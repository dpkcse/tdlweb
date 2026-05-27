<?php
/**
 * Save Booking logic.
 *
 * @package WPTravelEngine/Core/Booking
 * @since 6.8.0
 */
namespace WPTravelEngine\Core\Booking;

use WPTravelEngine\Core\Tax;
use WPTravelEngine\Core\Cart\Item;
use WPTravelEngine\Filters\Events;
use WPTravelEngine\Helpers\Functions;
use WPTravelEngine\Abstracts\CartItem;
use WPTravelEngine\Validator\Validator;
use WPTravelEngine\Core\Models\Post\Trip;
use WPTravelEngine\Utilities\ArrayUtility;
use WPTravelEngine\Core\Models\Post\Payment;
use WPTravelEngine\Core\Models\Post\Customer;
use WPTravelEngine\Utilities\PaymentCalculator;
use WPTravelEngine\Core\Cart\Items\ExtraService;
use WPTravelEngine\Core\Models\Post\TripPackage;
use WPTravelEngine\Core\Cart\Items\PricingCategory;
use WPTravelEngine\Core\Cart\Adjustments\TaxAdjustment;
use WPTravelEngine\Core\Models\Post\Booking as BookingModel;

/**
 * Class SaveBooking
 * This class handles the saving logic for trip bookings.
 *
 * @since 6.8.0
 */
class SaveBooking {

	/**
	 * Booking model.
	 *
	 * @var BookingModel|null
	 */
	public ?BookingModel $booking;

	/**
	 * Request object.
	 *
	 * @var \WP_REST_Request|null
	 */
	public $request = null;

	/**
	 * Form validator.
	 *
	 * @var Validator
	 * @since 6.8.0
	 */
	protected Validator $form_validator;

	/**
	 * Saved cart_info meta key value.
	 *
	 * @var array
	 * @since 6.8.0
	 */
	protected array $saved_cart_info = array();

	/**
	 * Is new booking.
	 *
	 * @var bool
	 * @since 6.8.0
	 */
	protected bool $is_new_booking = false;

	/**
	 * Set fees.
	 *
	 * @var array
	 */
	private array $set_fees = array();

	/**
	 * Constructor.
	 *
	 * @param int $booking_id Booking post ID.
	 * @since 6.8.0
	 */
	public function __construct( int $booking_id ) {
		$this->booking        = wptravelengine_get_booking( $booking_id );
		$this->is_new_booking = $this->booking && ! $this->booking->has_meta( 'order_trips' );
	}

	/**
	 * Process saving.
	 *
	 * @return void
	 * @since 6.8.0
	 */
	public function process() {

		if ( ! $this->booking ) {
			return;
		}

		$this->form_validator = new Validator();
		$this->request        = Functions::create_request( 'POST' );

		if ( $this->is_new_booking ) {
			$this->booking->update_post( array( 'post_status' => 'publish' ) );
			$this->booking->post->post_status = 'publish';
		}

		$this->saved_cart_info = $this->booking->get_cart_info();

		$this->request->set_body_params( wptravelengine_sanitize_params_recursive( $this->request->get_body_params() ) );

		$this->booking->set_meta( '_user_edited', 'yes' );

		$this->booking->with_delayed_save(
			function () {
				$this->dispatch_sections();
				$this->post_sets();
				$cart_info = $this->booking->get_changes()['cart_info'] ?? $this->saved_cart_info;
				do_action( 'wptravelengine_save_addon_meta', $this->request, $this->booking, $cart_info, $this->is_new_booking );
			}
		);

		$this->booking->maybe_update_inventory();

		if ( $this->is_new_booking ) {
			Events::booking_created( $this->booking );
		} else {
			Events::booking_updated( $this->booking );
			/**
			 * @param array $data Booking Data.
			 * @param BookingModel $booking Booking Object.
			 *
			 * @since 6.5.2
			 */
			do_action( 'wptravelengine.booking.updated', $this->booking->get_data(), $this->booking );
		}
	}

	/**
	 * Dispatches processing handlers for each changed section.
	 *
	 * Each section_map entry supports:
	 *   - 'callable'  : single PHP callable.
	 *   - 'callables' : array of PHP callables (each deduplicated independently).
	 * Every callable is invoked as callable( $booking, $request ).
	 * An optional 'unconditional' bool key forces the section to always run.
	 *
	 * @return void
	 * @since 6.8.0
	 */
	protected function dispatch_sections(): void {
		$section_map = apply_filters(
			'wptravelengine.booking_save.section_map',
			array(
				'notes'             => array(
					'callable'      => array( $this, 'process_admin_notes' ),
					'unconditional' => true,
				),
				'additional-notes'  => array(
					'callable'      => array( $this, 'process_user_notes' ),
					'unconditional' => true,
				),
				'billing'           => array(
					'callable'      => array( $this, 'process_billing_details' ),
					'unconditional' => true,
				),
				'trip-details'      => array( 'callable' => array( $this, 'process_trip_details' ) ),
				'travellers'        => array(
					'callables' => array(
						array( $this, 'process_traveller_details' ),
						array( $this, 'process_cart_data' ),
					),
				),
				'accommodation'     => array( 'callable' => array( $this, 'process_cart_data' ) ),
				'extra-services'    => array( 'callable' => array( $this, 'process_cart_data' ) ),
				'payments'          => array( 'callable' => array( $this, 'process_cart_data' ) ),
				'travel-insurance'  => array(
					'callables' => array(
						array( $this, 'process_insurance_details' ),
						array( $this, 'process_cart_data' ),
					),
				),
				'emergency-contact' => array( 'callable' => array( $this, 'process_emergency_contacts' ) ),
				'booking-summary'   => array( 'callable' => array( $this, 'process_cart_data' ) ),
			),
			$this
		);

		$already_processed = array();
		$is_unconditional  = (bool) ( $this->request['order_trip']['id'] ?? false );

		foreach ( $section_map as $tab => $value ) :
			$unconditional = $value['unconditional'] ?? $is_unconditional;

			if ( ! $this->is_new_booking && ( ! $unconditional || ! $this->is_section_changed( $tab ) ) ) {
				continue;
			}

			$callables = isset( $value['callable'] ) ? array( $value['callable'] ) : ( $value['callables'] ?? array() );

			foreach ( $callables as $callable ) {
				if ( ! is_callable( $callable ) ) {
					continue;
				}

				$key = is_array( $callable ) ? ( ( is_string( $callable[0] ) ? $callable[0] : get_class( $callable[0] ) ) . '::' . $callable[1] ) : ( is_string( $callable ) ? $callable : null );

				if ( null !== $key && ( $already_processed[ $key ] ?? false ) ) {
					continue;
				}

				call_user_func( $callable, $this->booking, $this->request );

				if ( null !== $key ) {
					$already_processed[ $key ] = true;
				}
			}
		endforeach;
	}

	/**
	 * Checks if sections is changed.
	 *
	 * @return bool
	 * @since 6.8.0
	 */
	protected function is_section_changed( string $id ): bool {
		return $this->request && wptravelengine_toggled( $this->request->get_param( "__changed_{$id}" ) );
	}

	/**
	 * Process user notes.
	 *
	 * @return void
	 * @since 6.7.0
	 * @since 6.8.0 Split from process_notes(); moved to SaveBooking.
	 */
	protected function process_user_notes(): void {
		if ( $additional_note = $this->request->get_param( 'additional_details' ) ) {
			$this->booking->set_meta( 'wptravelengine_additional_note', $additional_note );
		}
	}

	/**
	 * Process admin notes.
	 *
	 * @return void
	 * @since 6.7.0
	 * @since 6.8.0 Split from process_notes(); moved to SaveBooking.
	 */
	protected function process_admin_notes(): void {
		if ( $admin_notes = $this->request->get_param( 'admin_notes' ) ) {
			$this->booking->set_meta( 'wptravelengine_admin_notes', $admin_notes );
		}
	}

	/**
	 * Process emergency contacts.
	 *
	 * @return void
	 * @since 6.7.0
	 * @since 6.8.0 Moved from PostTypes\Booking; parameters replaced by class properties.
	 */
	protected function process_emergency_contacts(): void {
		$emergency_contacts = $this->request->get_param( 'emergency_contacts' ) ?? array();

		$this->booking->set_emergency_contact_details( empty( $emergency_contacts ) ? array() : array_values( ArrayUtility::normalize( $emergency_contacts ) ) );
	}

	/**
	 * Process traveller details.
	 *
	 * @return void
	 * @since 6.7.0
	 * @since 6.8.0 Moved from PostTypes\Booking; parameters replaced by class properties.
	 */
	protected function process_traveller_details(): void {
		$travellers = $this->request->get_param( 'travellers' ) ?? array();

		$this->booking->set_traveller_details( empty( $travellers ) ? array() : array_values( ArrayUtility::normalize( $travellers ) ) );
	}

	/**
	 * Process travel insurance details.
	 *
	 * @return void
	 * @since 6.7.0
	 * @since 6.8.0 Renamed from save_travel_insurance_meta() and moved to SaveBooking.
	 */
	protected function process_insurance_details(): void {
		if ( $travel_insurance = $this->request->get_param( 'travel_insurance_meta' ) ) {
			$this->booking->set_meta( 'wptravelengine_travel_insurance', $travel_insurance );
		}
	}

	/**
	 * Process billing details.
	 *
	 * @return void
	 * @since 6.7.0
	 * @since 6.8.0 Moved from PostTypes\Booking; parameters replaced by class properties.
	 */
	protected function process_billing_details(): void {
		$billing_details = $this->request->get_param( 'billing' );
		if ( ! $billing_details ) {
			return;
		}

		$billing_email = $billing_details['email'] ?? '';

		if ( ! empty( $billing_email ) && is_email( $billing_email ) ) {
			$customer_id    = Customer::is_exists( $billing_email );
			$customer_model = null;

			if ( $customer_id ) {
				try {
					$customer_model = new Customer( $customer_id );
				} catch ( \Exception $e ) {
					$customer_model = null;
				}
			} else {
				$customer_model = Customer::create_post(
					array(
						'post_status' => 'publish',
						'post_type'   => 'customer',
						'post_title'  => sanitize_email( $billing_email ),
					)
				);
			}

			if ( $customer_model instanceof Customer ) {
				if ( ! email_exists( $billing_email ) ) {
					/**
					 * Filters whether to force user account creation when saving a booking from the admin.
					 *
					 * Defaults to true (current behaviour). Return false to respect the
					 * "Create Account Automatically" setting (WTE → Settings → User Dashboard).
					 *
					 * @param bool     $force          Whether to force account creation.
					 * @param Customer $customer_model Customer model instance.
					 * @param BookingModel $booking    Booking model instance.
					 *
					 * @since 6.8.0
					 */
					$force = apply_filters( 'wptravelengine_admin_booking_force_register_user', true, $customer_model, $this->booking );
					$customer_model->maybe_register_as_user( $force );
				}

				do_action( 'wptravelengine_after_customer_created', $customer_model->ID );

				$customer_model->update_customer_bookings( $this->booking->ID );
				$customer_model->update_customer_meta( $this->booking->ID );
				$customer_model->save();
			}
		}

		$sanitized_billing = array();
		foreach ( $billing_details as $field => $value ) {
			if ( is_array( $value ) ) {
				$sanitized_billing[ $field ] = array_map( 'sanitize_text_field', $value );
				continue;
			}
			if ( is_string( $value ) && filter_var( $value, FILTER_VALIDATE_URL ) ) {
				$sanitized_billing[ $field ] = basename( $value );
				continue;
			}
			$sanitized_billing[ $field ] = $this->sanitize_traveller_field( $field, $value );
		}

		$this->booking->set_billing_info( $sanitized_billing );
	}

	/**
	 * Process trip details.
	 *
	 * @return void
	 * @since 6.8.0
	 */
	protected function process_trip_details(): void {
		$trip_info = &$this->request['order_trip'];

		$trip = wptravelengine_get_trip( $trip_info['id'] );

		if ( in_array( $trip_info['id'], array( 'other', '', 0 ), true ) ) {
			$trip            = Trip::create( sanitize_text_field( $trip_info['custom_trip'] ?: 'Untitled' ) );
			$trip_info['id'] = $trip->ID;
		}

		if ( ! $trip ) {
			return;
		}

		$trip_id = $trip->ID;

		$start_datetime = $trip_info['start_date'] . ( ( $trip_info['start_time'] ?? '' ) ? ( 'T' . $trip_info['start_time'] ) : '' );

		$this->booking->set_meta( 'trip_datetime', $start_datetime );

		$package_id = intval( $trip_info['package_id'] ?? 0 );

		if ( 0 === $package_id ) {
			$name = sanitize_text_field( $trip_info['custom_package'] ?? 'Custom Package' );

			// This for backward compatibility as previously we were saving custom package name only.
			foreach ( $trip->packages() as $package ) {
				/** @var TripPackage $package */
				if ( $package->get_title() === $name ) {
					$package_id = $package->ID;
					break;
				}
			}

			if ( 0 === $package_id ) {
				$prices = array();
				foreach ( $this->request['line_items']['pricing_category'] ?? array() as $cat_key => $cat_item ) {
					$prices[ $cat_key ] = (float) ( $cat_item['price'][0] ?? 0 );
				}

				$package_id = $trip->create_manual_package( compact( 'name', 'prices' ) );

				$trip_info['custom_package'] = $name;
				$trip_info['package_name']   = $name;
			}
		} elseif ( ! $trip::package_exists( $trip_id, $package_id, true ) ) {
			$trip->update_manual_package( $package_id );
		}

		$trip_info['package_id'] = $package_id;

		$this->process_cart_items();
	}

	/**
	 * Sanitize traveller field.
	 *
	 * @param string $field Field name.
	 * @param mixed  $value Field value.
	 *
	 * @return mixed Sanitized value.
	 * @since 6.4.0
	 * @since 6.8.0 Moved from PostTypes\Booking; form_validator accessed via class property.
	 */
	private function sanitize_traveller_field( string $field, $value ) {
		// Handle null or empty values.
		if ( $value === null ) {
			return '';
		}

		switch ( $field ) {
			case 'email':
				return sanitize_email( $value );
			case 'phone':
				return $this->form_validator->sanitize_phone( $value );
			case 'country':
				return $this->form_validator->sanitize_country( $value );
			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Process cart data.
	 *
	 * @return void
	 * @since 6.8.0
	 */
	protected function process_cart_data(): void {
		$cart_info = $this->booking->get_cart_info();
		if ( $this->booking->get_meta( '_initial_cart_info' ) === '' ) {
			$this->booking->set_meta( '_initial_cart_info', wp_json_encode( $cart_info ) );
		}

		$order_items = $this->booking->get_order_items();
		if ( $this->booking->get_meta( '_initial_order_items' ) === '' ) {
			$this->booking->set_meta( '_initial_order_items', wp_json_encode( $order_items ) );
		}

		$this->process_cart_items();

		$this->process_payments();
	}

	/**
	 * Get subtotal reservations.
	 *
	 * @return array<array<array{id: mixed, price: float, quantity: int|array{id: null, label: mixed, manual: bool, price: float, quantity: int, total: float}>>}
	 * @since 6.7.0
	 * @since 6.8.0 Renamed from create_subtotal_reservations(); returns data instead of mutating cart_info in-place.
	 */
	final private function get_subtotal_reservations(): array {
		$currency   = $this->booking->get_cart_info()['currency'] ?? wptravelengine_settings()->get( 'currency_code', 'USD' );
		$calc       = PaymentCalculator::for( $currency );
		$line_items = $this->request['line_items'] ?? array();

		$subtotal_reservations = array();

		foreach ( $line_items['pricing_category'] ?? array() as $cat_key => $cat_item ) {
			$price    = $cat_item['price'][0] ?? 0;
			$quantity = $cat_item['quantity'][0] ?? 0;

			$subtotal_reservations['travelers'][ $cat_key ] = array(
				'id'       => $cat_key,
				'quantity' => (int) $quantity,
				'price'    => (float) $price,
				'total'    => $calc->multiply( (string) $quantity, (string) $price ),
			);
		}
		unset( $line_items['pricing_category'] );

		$key_map = array(
			'extra_service' => 'extraServices',
		);

		foreach ( $line_items as $param_key => $val ) {

			if ( empty( $val ) ) {
				continue;
			}

			$param_key = $key_map[ $param_key ] ?? $param_key;

			$items = ArrayUtility::normalize( $val );

			foreach ( $items as $item ) {
				$args = wp_parse_args(
					$item,
					array(
						'id'       => null,
						'manual'   => true,
						'label'    => 'Manual ' . $param_key,
						'quantity' => 1,
						'price'    => 0,
					)
				);

				$args['price']    = (float) $args['price'];
				$args['quantity'] = (int) $args['quantity'];
				$args['total']    = $calc->multiply( (string) $args['quantity'], (string) $args['price'] );

				$subtotal_reservations[ $param_key ][] = $args;
			}
		}

		return $subtotal_reservations;
	}

	/**
	 * Process cart items and sets them to allocated positions of booking instance.
	 *
	 * @return void
	 * @since 6.7.0
	 * @since 6.8.0 Refactored from process_line_items(); uses get_subtotal_reservations() and BookingProcess::prepare_items().
	 */
	protected function process_cart_items(): void {
		$trip_infos = $this->request['order_trip'] ?? array();

		if ( ! $trip_infos ) {
			return;
		}

		$subtotal_reservations = $this->get_subtotal_reservations();
		$end_datetime          = $trip_infos['end_date'] . ( ( $trip_infos['end_time'] ?? '' ) ? ( 'T' . $trip_infos['end_time'] ) : '' );
		$start_datetime        = $trip_infos['start_date'] . ( ( $trip_infos['start_time'] ?? '' ) ? ( 'T' . $trip_infos['start_time'] ) : '' );

		$package_id   = (int) ( $trip_infos['package_id'] ?? 0 );
		$package_name = $package_id === 0 ? ( $trip_infos['custom_package'] ?? '' ) : ( $trip_infos['package_name'] ?? '' );

		$trip_price    = .0;
		$travelers_pax = array();
		$_line_items   = array();
		$pax_cost      = array();

		// Process Line Items: start
		$i               = 0;
		$_metakey_prefix = 'cart_info.items.0.line_items';
		foreach ( $subtotal_reservations['travelers'] ?? array() as $travellers ) {
			$term                = get_term( $travellers['id'] );
			$curr_metakey_prefix = "{$_metakey_prefix}.pricing_category.{$i}";

			$travellers['_class_name'] = wp_slash( PricingCategory::class );
			$travellers['pricingType'] = $this->booking->get_nested_meta( "{$curr_metakey_prefix}.pricingType", 'per-person' );
			$travellers['label']       = $term ? $term->name : ( 'Category ' . $travellers['id'] );

			$_line_items['pricing_category'][ $i ] = $travellers;

			$travelers_pax[ $travellers['id'] ] = $travellers['quantity'];
			$pax_cost[ $travellers['id'] ]      = $travellers['price'];

			$trip_price += $travellers['total'];

			++$i;
		}

		foreach ( $subtotal_reservations as $key => $vals ) {
			switch ( $key ) :
				case 'extraServices':
					$key         = 'extra_service';
					$_class_name = ExtraService::class;
					break;
				default:
					$_class_name = false;
			endswitch;

			$_class_name = apply_filters( 'wptravelengine_custom_line_item_class', $_class_name, $key );

			if ( ! is_subclass_of( $_class_name, CartItem::class ) ) {
				$_class_name = $this->booking->get_nested_meta( "{$_metakey_prefix}.{$key}.0._class_name" );
				if ( null === $_class_name ) {
					continue;
				}
			}

			foreach ( $vals as $val ) {
				$val['_class_name']    = wp_slash( $_class_name ); // phpcs:igonre
				$_line_items[ $key ][] = $val;
			}
		}

		$key_map = array(
			'pricing_category' => 'travelers',
			'extra_service'    => 'extraServices',
		);

		$cart_items = $this->booking->get_nested_meta( "{$_metakey_prefix}" );
		unset( $cart_items['pricing_category'] );

		foreach ( $cart_items as $key => $vals ) {
			if ( isset( $_line_items[ $key ] ) || ( wptravelengine_is_addon_active( $key ) ?? false ) ) {
				continue;
			}
			$_line_items[ $key ] = $this->booking->get_nested_meta( "{$_metakey_prefix}.{$key}" );

			$sub_value = $this->booking->get_nested_meta( "cart_info.items.0.subtotal_reservations.{$key}" );
			if ( $sub_value ) {
				$subtotal_reservations[ $key_map[ $key ] ?? $key ] = $sub_value;
			}
		}
		// Process Line Items: end

		$cart_item_1 = array(
			'id'                    => Item::get_item_id( $this->booking->get_trip_id(), $package_id, $start_datetime, $trip_infos['start_date'] ),
			'trip_id'               => $trip_infos['id'],
			'trip_price'            => $trip_price,
			'trip_date'             => $trip_infos['start_date'],
			'trip_time'             => $start_datetime,
			'trip_end_date'         => $trip_infos['end_date'],
			'trip_end_time'         => $end_datetime,
			'trip_time_range'       => array( $start_datetime, $end_datetime ),
			'price_key'             => $package_id,
			'package_name'          => $package_name,
			'pax'                   => $travelers_pax,
			'pax_labels'            => array(),
			// 'category_info'         => $travelers['info'],
			'pax_cost'              => $pax_cost,
			'multi_pricing_used'    => false,
			'trip_extras'           => $subtotal_reservations['extraServices'] ?? array(),
			'datetime'              => $trip_infos['start_date'],
			// 'tax_amount'            => $tax->get_tax_percentage(),
			'travelers'             => $travelers_pax,
			'subtotal_reservations' => $subtotal_reservations,
			'pricingCategories'     => $this->request['line_items']['pricing_category'] ?? array(),
			'travelers_count'       => array_sum( $travelers_pax ),
			'line_items'            => $_line_items,
		);

		$cart_info = $this->booking->get_cart_info();

		$item1 = wp_parse_args( $cart_item_1, $cart_info['items'][0] ?? array() );

		$items = array( $item1 );

		$this->booking->set_nested_meta( 'cart_info.items', $items );

		$prepared_items = BookingProcess::prepare_items( $items );

		$settings_order = $prepared_items['settings']['place_order'] ?? array();

		$this->booking->set_order_items( $prepared_items['items'] );

		$this->booking->set_nested_meta( 'wp_travel_engine_booking_setting.place_order', $settings_order, ! empty( $settings_order ) );
	}

	/**
	 * Process No Payments.
	 *
	 * @return void
	 */
	private function process_no_payments(): void {
		$this->process_cart_fees();
		$this->booking->set_meta( 'total_due_amount', $this->request->get_param( 'total' ) );
		$this->booking->set_meta( 'total_paid_amount', '0.00' );
		$this->booking->set_meta( 'payments', array() );
		$this->booking->set_meta( 'wp_travel_engine_booking_payment_status', 'pending' );
	}

	/**
	 * Process Payments.
	 *
	 * @return void
	 * @since 6.7.0
	 * @since 6.8.0 Moved from PostTypes\Booking; fee handling extracted to process_cart_fees().
	 */
	protected function process_payments(): void {
		$req = $this->request;

		$payments = $req->get_param( 'payments' );
		$fees     = $req->get_param( 'fees' );
		$fees     = $fees ? ( $fees['slug'] ?? array() ) : array();

		$cart_info = $this->booking->get_cart_info();

		$total = $this->request->get_param( 'total' );
		if ( is_numeric( $total ) ) {
			$cart_info['total']           = $total;
			$cart_info['totals']['total'] = $total;

			$this->booking->set_nested_meta( 'cart_info.total', $total );
			$this->booking->set_nested_meta( 'cart_info.totals.total', $total );
		}

		$subtotal = $this->request->get_param( 'subtotal' );
		if ( is_numeric( $subtotal ) ) {
			$cart_info['sub_total']          = $subtotal;
			$cart_info['totals']['subtotal'] = $subtotal;

			$this->booking->set_nested_meta( 'cart_info.sub_total', $subtotal );
			$this->booking->set_nested_meta( 'cart_info.totals.subtotal', $subtotal );
		}

		$due_amount = $req->get_param( 'due_amount' );
		if ( is_numeric( $due_amount ) ) {
			$cart_info['due_total']           = $due_amount;
			$cart_info['totals']['due_total'] = $due_amount;

			$this->booking->set_nested_meta( 'cart_info.due_total', $due_amount );
			$this->booking->set_nested_meta( 'cart_info.totals.due_total', $due_amount );
		}

		$this->booking->set_nested_meta( 'cart_info.version', $this->request['wptravelengine_cart_version'] ?? '4.0', false );

		if ( ! $payments ) {
			array_map(
				function ( $fee ) {
					$this->set_fees[ $fee ] = true;
				},
				$fees
			);
			$this->process_no_payments();
			return;
		}

		$items = ArrayUtility::normalize( $payments, 'gateway' );
		$calc  = PaymentCalculator::for( $cart_info['currency'] ?? wptravelengine_settings()->get( 'currency_code', 'USD' ) );

		$_payments = array();

		$actual_deposit_                            = '0.00';
		$cart_info['totals']['deposit']             = '0.00';
		$cart_info['totals']['total_extra_charges'] = '0.00';

		$success_status = wptravelengine_success_payment_status();

		foreach ( $items as $payment_data ) {
			try {
				$payment_model = new Payment( (int) $payment_data['id'] );
				Events::payment_updated( $payment_model );
			} catch ( \Exception $e ) {
				$payment_model = Payment::create_post(
					array(
						'post_type'   => 'wte-payments',
						'post_status' => 'publish',
						'post_title'  => 'Payment',
					)
				);
				Events::payment_created( $payment_model );
			}

			if ( $status = $payment_data['status'] ?? null ) {
				$payment_model->set_status( sanitize_text_field( $status ) );
			} else {
				$status = $payment_model->get_meta( 'payment_status' );
			}

			$payment_cart_total = ArrayUtility::make( $payment_model->get_cart_totals() ?: $cart_info['totals'] );

			if ( is_numeric( $p_deposit = $payment_data['deposit'] ?? null ) ) {
				$p_deposit = $calc->normalize( (string) $p_deposit );
				$payment_cart_total->set( 'deposit', $p_deposit );
				$cart_info['totals']['deposit'] = $calc->add(
					(string) $cart_info['totals']['deposit'],
					$p_deposit
				);
				if ( isset( $success_status[ $status ] ) ) {
					$actual_deposit_ = $calc->add( $actual_deposit_, $p_deposit );
				}
			}

			$extra_fee = '0';
			foreach ( $fees as $fee ) {
				if ( 'gateway_fee' === $fee ) {
					continue;
				}
				$p_fee = $payment_data[ $fee ] ?? '';
				if ( is_numeric( $p_fee ) ) {
					$p_fee = floatval( $p_fee );
					if ( $p_fee >= 0.00 ) {
						$p_fee = $calc->normalize( (string) $p_fee );
						$payment_cart_total->set( 'total_' . $fee, $p_fee );
						$extra_fee              = $calc->add( $extra_fee, $p_fee );
						$this->set_fees[ $fee ] = true;
					}
				} else {
					$payment_cart_total->remove( 'total_' . $fee );
				}
			}

			if ( '0' !== $extra_fee ) {
				$payment_cart_total->set( 'total_extra_charges', $extra_fee );
				$cart_info['totals']['total_extra_charges'] = $extra_fee;
			}

			$payment_model->set_meta( 'cart_totals', $payment_cart_total->value() );

			if ( $gateway = $payment_data['gateway'] ?? null ) {
				$payment_model->set_meta( 'payment_gateway', sanitize_text_field( $gateway ) );
				$this->booking->set_meta( 'wp_travel_engine_booking_payment_gateway', sanitize_text_field( $gateway ) );
			}

			$payment_currency = sanitize_text_field( $payment_data['currency'] ?? '' );
			if ( '' === $payment_currency ) {
				$payment_currency = $payment_model->get_currency();
			}
			if ( '' === $payment_currency ) {
				$payment_currency = $cart_info['currency'] ?? wptravelengine_settings()->get( 'currency_code', 'USD' );
			}
			$payment_currency = $payment_currency ?: 'USD';

			$paid_amount = $payment_data['amount'] ?? null;
			$payment_model->set_meta(
				'payment_amount',
				array(
					'value'    => is_numeric( $paid_amount ) ? (float) $paid_amount : 0,
					'currency' => $payment_currency,
				)
			);

			if ( is_numeric( $due_amount = $this->request->get_param( 'due_amount' ) ) ) {
				$payment_model->set_meta(
					'payable',
					array(
						'currency' => $payment_currency,
						'amount'   => (float) $due_amount,
					)
				);
			}

			if ( $transaction_id = $payment_data['transaction_id'] ?? null ) {
				$payment_model->set_transaction_id( sanitize_text_field( $transaction_id ) );
			}

			$transaction_date = $payment_data['date'] ?? $payment_data['transaction_date'] ?? null;
			if ( $transaction_date ) {
				$payment_model->set_transaction_date( sanitize_text_field( $transaction_date ) );
			}

			if ( $gateway_response = $payment_data['gateway_response'] ?? null ) {
				$payment_model->set_meta( 'gateway_response', sanitize_text_field( $gateway_response ) );
			}

			if ( empty( $payment_model->get_meta( 'booking_id' ) ) ) {
				$payment_model->set_meta( 'booking_id', $this->booking->ID );
			}

			if ( empty( $payment_model->get_meta( 'payment_source' ) ) ) {
				$payment_model->set_meta( 'payment_source', $payment_model->get_payment_source() );
			}

			$payment_model->save();
			$_payments[] = $payment_model->get_id();
		}

		if ( is_numeric( $total = $this->request->get_param( 'total' ) ) ) {
			$due_exclusive = $calc->subtract( (string) $total, (string) $actual_deposit_ );
			$this->booking->set_meta( 'total_due_amount', $due_exclusive );

			if ( $calc->is( $due_exclusive, '==', '0.00' ) ) {
				$this->booking->set_status( 'booked' );
			}
		}

		$last_status = $payment_model->get_meta( 'payment_status' );

		$this->booking->set_meta( 'payments', $_payments );
		$this->booking->set_meta( 'wp_travel_engine_booking_payment_status', $last_status );

		/**
		 * Moved from update_cart_totals to process_payments.
		 *
		 * @since 6.7.6
		 */
		$this->booking->set_meta( 'total_paid_amount', (float) $paid_amount );

		if ( is_numeric( $paid_amount = $this->request->get_param( 'paid_amount' ) ) ) {
			$this->booking->set_meta( 'paid_amount', (float) $paid_amount );
		}

		if ( is_numeric( $due_amount = $this->request->get_param( 'due_amount' ) ) ) {
			$this->booking->set_meta( 'due_amount', (float) $due_amount );
		}

		$this->booking->set_nested_meta( 'cart_info.totals', $cart_info['totals'] );

		$this->process_cart_fees();
	}

	/**
	 * Process and returns cart fees.
	 *
	 * @return void
	 * @since 6.7.0
	 * @since 6.8.0 Renamed from process_fees() and moved to SaveBooking.
	 */
	private function process_cart_fees(): void {
		$fees  = $this->request->get_param( 'fees' ) ?? array();
		$items = empty( $fees ) ? array() : ArrayUtility::normalize( $fees, 'label' );

		$_items    = array();
		$def_class = TaxAdjustment::class;
		$tax       = new Tax();

		foreach ( $items as $index => $item ) {

			if ( isset( $item['slug'] ) && ! isset( $this->set_fees[ $item['slug'] ] ) ) {
				continue;
			}

			$percentage = '';
			if ( preg_match( '/(\d+)%/', $item['label'], $matches ) ) {
				$percentage = $matches[1];
			}

			$_items[ $index ] = wp_parse_args(
				$item,
				array(
					'order'                    => $index,
					'description'              => '',
					'apply_to_actual_subtotal' => false,
					'type'                     => 'fee',
				)
			);

			$item_class = ( $item['_class_name'] ?? '' ) ?: null;

			if ( ! isset( $item_class ) ) {
				$percentage = $tax->get_tax_percentage();
				$item_class = $def_class;
			}

			$_items[ $index ]['name']            = ( $item['slug'] ?? '' ) ?: ( '_fee' . $index );
			$_items[ $index ]['label']           = ( $item['label'] ?? '' ) ?: '';
			$_items[ $index ]['value']           = ( $item['value'] ?? '' ) ?: 0;
			$_items[ $index ]['_class_name']     = wp_slash( $item_class );
			$_items[ $index ]['adjustment_type'] = ( $item['adjustment_type'] ?? '' ) ?: 'percentage';
			$_items[ $index ]['percentage']      = ( $item['percentage'] ?? '' ) ?: $percentage;
			$_items[ $index ]['apply_tax']       = wptravelengine_toggled( ( $item['apply_tax'] ?? '' ) ?: true );
			$_items[ $index ]['apply_upfront']   = wptravelengine_toggled( ( $item['apply_upfront'] ?? '' ) ?: false );

			// $cart_info['totals'][ 'total_fee' . $index ] = $item['value'];
		}

		$this->booking->set_nested_meta( 'cart_info.fees', $_items );
	}

	/**
	 * Post Sets.
	 *
	 * @since 6.8.0
	 */
	private function post_sets() {
		// Booking Status
		$this->booking->set_status( $this->request->get_param( 'wp_travel_engine_booking_status' ) );
	}
}
