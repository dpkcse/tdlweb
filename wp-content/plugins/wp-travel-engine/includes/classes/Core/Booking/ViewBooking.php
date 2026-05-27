<?php
/**
 * Booking admin view renderer.
 * Handles meta box rendering for the booking post edit screen.
 * Refined and migrated methods from WPTravelEngine\Core\PostTypes\Booking class.
 *
 * @package WPTravelEngine/Core/Booking
 * @since 6.8.0
 */

namespace WPTravelEngine\Core\Booking;

use WPTravelEngine\Core\Cart\Cart;
use WPTravelEngine\Helpers\BookedItem;
use WPTravelEngine\Helpers\CartInfoParser;
use WPTravelEngine\Core\Models\Post\Payment;
use WPTravelEngine\Utilities\PaymentCalculator;
use WPTravelEngine\Core\Models\Post\Booking as BookingModel;
use WPTravelEngine\Builders\FormFields\BillingEditFormFields;
use WPTravelEngine\Builders\FormFields\PaymentEditFormFields;
use WPTravelEngine\Builders\FormFields\EmergencyEditFormFields;
use WPTravelEngine\Builders\FormFields\OrderTripEditFormFields;
use WPTravelEngine\Builders\FormFields\TravellerEditFormFields;

/**
 * Renders the booking admin meta box for view and edit actions.
 *
 * @since 6.8.0
 */
class ViewBooking {

	/**
	 * Booking instance.
	 *
	 * @var ?BookingModel $booking
	 */
	protected $booking = null;

	/**
	 * Is new booking.
	 *
	 * @var bool $is_new_booking
	 */
	protected bool $is_new_booking = false;

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
	 * Register Hooks.
	 *
	 * @since 6.8.0
	 */
	public static function register_hooks(): void {
		add_action(
			'current_screen',
			static function ( \WP_Screen $screen ): void {
				if ( 'booking' !== $screen->id ) {
					return;
				}
				remove_all_actions( 'admin_notices' );
				remove_all_actions( 'all_admin_notices' );
				add_filter( 'screen_options_show_screen', '__return_false' );
				add_action( 'admin_notices', array( __CLASS__, 'dashboard_notice' ) );
			}
		);
	}

	/**
	 * Prints an info notice on the booking edit screen warning about manual edits.
	 *
	 * @return void
	 * @since 6.4.0
	 * @since 6.8.0 Migrated from Plugin class.
	 */
	public static function dashboard_notice(): void {
		$screen = get_current_screen();
		if ( 'booking' !== $screen->id || 'edit' !== ( $_GET['wptravelengine_action'] ?? '' ) ) {
			return;
		}

		printf(
			'<div class="%1$s"><p><strong>%2$s</strong></p></div>',
			esc_attr( 'notice notice-info is-dismissible' ),
			esc_html__( 'Notice: Please be aware that you are responsible for any mistakes, payment issues, or customer concerns that may arise when editing the booking summary. Double-check your changes, update payment settings, and contact support if you need assistance.', 'wp-travel-engine' )
		);
	}

	/**
	 * Entry point called from the meta box callback.
	 * Enqueues scripts, resolves action, and dispatches to view/create.
	 *
	 * @return void
	 * @since 6.8.0
	 */
	public function process_markup(): void {
		if ( null === $this->booking ) {
			echo esc_html__( 'Booking not found.', 'wp-travel-engine' );
			return;
		}

		global $current_screen;

		$this->register_addon_hooks();

		if ( $this->is_new_booking ) {
			$this->booking->post->post_title = 'Booking #' . $this->booking->ID;
		}

		$method = 'view';
		if ( ! $this->is_new_booking && ! $this->booking->is_curr_cart() ) {
			wp_enqueue_script( 'wptravelengine-booking-legacy-edit' );
		} else {
			if ( 'booking' === $current_screen->id && 'add' === $current_screen->action ) {
				$method = 'create';
			} elseif ( ( $_GET['wptravelengine_action'] ?? '' ) === 'edit' ) {
				$method = 'create'; // update
			}

			wp_enqueue_script( 'wptravelengine-booking-edit' );
			wp_localize_script(
				'wptravelengine-booking-edit',
				'wptravelengineBookingEdit',
				array(
					'activePlugins' => array(
						'pricing_category' => true,
						'accommodation'    => wptravelengine_is_addon_active( 'accommodation' ),
						'extra_service'    => wptravelengine_is_addon_active( 'extra_service' ),
						'pickup_point'     => wptravelengine_is_addon_active( 'pickup_point' ),
						'travel_insurance' => wptravelengine_is_addon_active( 'travel_insurance' ),
					),
				)
			);
		}

		$this->{$method}();
	}

	/**
	 * View booking details.
	 *
	 * @return void
	 * @since 6.4.0
	 * @since 6.8.0 Moved from PostTypes\Booking; uses class property instead of parameter.
	 */
	protected function view(): void {
		$args = $this->get_template_args();
		wptravelengine_get_admin_template( $this->get_path_prefix( $args['cart_info'] ) . 'index.php', $args );
	}

	/**
	 * Create new booking.
	 *
	 * @return void
	 * @since 6.4.0
	 * @since 6.8.0 Moved from PostTypes\Booking; uses class property instead of parameter.
	 */
	protected function create(): void {
		$args = $this->get_template_args( 'edit' );
		wptravelengine_get_admin_template( $this->get_path_prefix( $args['cart_info'] ) . 'create.php', $args );
	}

	/**
	 * Get path prefix for template.
	 *
	 * @param CartInfoParser $cart_info
	 * @return string
	 * @since 6.7.0
	 * @since 6.8.0 Moved from PostTypes\Booking.
	 */
	private function get_path_prefix( CartInfoParser $cart_info ): string {
		return $cart_info->is_curr_cart_ver( '>=' ) ? 'booking/' : 'booking/legacy/';
	}

	/**
	 * Prepares template arguments for Booking Page.
	 *
	 * @param string $mode
	 * @return array
	 * @since 6.4.0
	 * @since 6.8.0 Moved from PostTypes\Booking; uses class property instead of parameter.
	 */
	protected function get_template_args( string $mode = 'view' ): array {
		$cart_info = $this->booking->get_cart_info() ?? array();

		if ( empty( $cart_info ) ) {
			$cart_info['version'] = Cart::CURRENT_VERSION;
			$this->booking->sync_metas( array( 'cart_info' => $cart_info ) );
		}

		$cart_info['items'][0]['package_name'] ??= $this->booking->get_order_items()[0]['package_name'] ?? '';

		$cart_info = new CartInfoParser( $cart_info );

		$mode = 'view' === $mode ? 'readonly' : 'edit';

		/** @since 6.7.1 filter: wptravelengine_booking_form_field_modes( array $field_modes, string $mode, BookingModel $booking ) */
		$field_modes = apply_filters(
			'wptravelengine_booking_form_field_modes',
			array_fill_keys( array( 'template_mode', 'order_trip', 'travellers', 'emergency_contacts', 'billing', 'payments', 'admin_notes' ), $mode ),
			$mode,
			$this->booking
		);

		$currency_code = $cart_info->get_currency();
		$payment_data  = $cart_info->is_curr_cart_ver( '>=' ) ? $this->booking->get_payments_data( false )['payments'] ?? array() : array();

		return array(
			'booking'                        => $this->booking,
			'cart_info'                      => $cart_info,
			'template_mode'                  => $field_modes['template_mode'],
			'order_trip_form_fields'         => $this->get_order_trip_form_fields( $field_modes['order_trip'], $cart_info->get_item() ),
			'travellers_form_fields'         => $this->get_travellers_form_fields( $field_modes['travellers'] ),
			'emergency_contacts_form_fields' => $this->get_emergency_contacts_form_fields( $field_modes['emergency_contacts'] ),
			'billing_edit_form_fields'       => $this->get_billing_edit_form_fields( $field_modes['billing'] ),
			'admin_notes_edit_form_fields'   => $field_modes['admin_notes'] ?? 'readonly',
			'payments_edit_form_fields'      => $this->get_payments_edit_form_fields( $field_modes['payments'], $payment_data ),
			'pricing_arguments'              => compact( 'currency_code' ),
			'calculator'                     => PaymentCalculator::for( $currency_code ),
		);
	}

	/**
	 * Builds the order trip form fields object.
	 *
	 * @param string     $mode
	 * @param BookedItem $order_trip
	 *
	 * @return OrderTripEditFormFields
	 * @since 6.8.0
	 */
	private function get_order_trip_form_fields( string $mode, BookedItem $order_trip ): OrderTripEditFormFields {

		$split_datetime_value = fn( $v ) => array(
			'date' => ( $e = explode( 'T', $v ) )[0],
			'time' => $e[1] ?? '',
		);

		$order_trips  = $this->booking->get_meta( 'order_trips' );
		$start_parts  = $split_datetime_value( (string) ( $this->booking->get_meta( 'trip_datetime' ) ?: $order_trip->get_trip_date() ) );
		$end_parts    = $split_datetime_value( (string) ( $this->booking->get_meta( 'end_datetime' ) ?: ( ( $order_trips[ $order_trip->get_id() ]['end_datetime'] ?? '' ) ?: $order_trip->get_end_date() ) ) );
		$package_name = $order_trip->get_package_name();
		$package_id   = $order_trip->get_trip_package_id();

		$defaults = array(
			'id'                  => $order_trip->get_trip_id(),
			'booked_date'         => $this->booking->post->post_date ?? '',
			'start_date'          => $start_parts['date'],
			'start_time'          => $start_parts['time'],
			'end_date'            => $end_parts['date'],
			'end_time'            => $end_parts['time'],
			'trip_code'           => $order_trip->get_trip_code() ?: ( $order_trip->get_trip_id() ? 'WTE-' . $order_trip->get_trip_id() : '' ),
			'number_of_travelers' => $order_trip->travelers_count(),
			'package_id'          => $package_id,
			'package_name'        => $package_name,
			'is_new_booking'      => $this->is_new_booking,
		);

		return new OrderTripEditFormFields( $defaults, $mode );
	}

	/**
	 * Builds traveller form field objects mapped to their pricing categories.
	 *
	 * @param string $mode
	 * @return TravellerEditFormFields[]
	 * @since 6.8.0
	 */
	private function get_travellers_form_fields( string $mode ): array {
		$travellers = $this->booking->get_travelers();

		if ( empty( $travellers ) ) {
			return $travellers;
		}

		$pricing_categories = get_terms(
			array(
				'taxonomy'   => 'trip-packages-categories',
				'hide_empty' => false,
				'orderby'    => 'term_id',
				'fields'     => 'id=>name',
			)
		);

		if ( is_wp_error( $pricing_categories ) ) {
			$pricing_categories = array();
		}

		$category_name_to_id = array_flip( $pricing_categories );

		$result = array();
		foreach ( $travellers as $index => $traveller ) {
			$traveller['index'] = $index;
			$category           = $traveller['pricing_category'] ?? '';
			if ( isset( $category_name_to_id[ $category ] ) ) {
				$traveller['pricing_category'] = $category_name_to_id[ $category ];
			}
			$result[] = new TravellerEditFormFields( $traveller, $mode, $this->booking );
		}

		return $result;
	}

	/**
	 * Builds emergency contact form field objects.
	 *
	 * @param string $mode
	 * @return EmergencyEditFormFields[]
	 * @since 6.8.0
	 */
	private function get_emergency_contacts_form_fields( string $mode ): array {
		$contacts = $this->booking->get_emergency_contacts();

		$result = array();
		foreach ( $contacts as $index => $contact ) {
			$contact['index'] = $index;
			$result[]         = new EmergencyEditFormFields( $contact, $mode, $this->booking );
		}

		return $result;
	}

	/**
	 * Builds the billing edit form fields object.
	 *
	 * @param string $mode
	 * @return BillingEditFormFields
	 * @since 6.8.0
	 */
	private function get_billing_edit_form_fields( string $mode ): BillingEditFormFields {
		return new BillingEditFormFields( $this->booking->get_billing_info(), $mode );
	}

	/**
	 * Builds payment edit form field objects for all payments on the booking.
	 *
	 * @param string $mode
	 * @param array  $payment_data
	 * @return PaymentEditFormFields[]
	 * @since 6.8.0
	 */
	private function get_payments_edit_form_fields( string $mode, array $payment_data ): array {

		$payment_status = array(
			'success' => wptravelengine_success_payment_status(),
			'pending' => wptravelengine_pending_payment_status(),
			'failed'  => wptravelengine_failed_payment_status(),
		);

		$result   = array();
		$payments = $this->booking->get_payments();

		foreach ( $payments as $payment ) :
			if ( ! $payment instanceof Payment ) {
				continue;
			}

			$p_id          = $payment->get_id();
			$_payment_data = $payment_data[ $p_id ] ?? array();

			$status = $payment->get_payment_status();

			if ( isset( $payment_status['success'][ $status ] ) ) {
				$status = 'completed';
			} elseif ( isset( $payment_status['pending'][ $status ] ) ) {
				$status = 'pending';
			} elseif ( isset( $payment_status['failed'][ $status ] ) ) {
				$status = 'failed';
			}

			$labels   = array();
			$defaults = array(
				'id'               => $p_id,
				'status'           => $status,
				'gateway'          => $payment->get_payment_gateway(),
				'deposit'          => $_payment_data['deposit'] ?? 0,
				'amount'           => $payment->get_amount(),
				'date'             => $payment->get_transaction_date() ?: ( $payment->post->post_date ?? '' ),
				'currency'         => $payment->get_payable_currency(),
				'transaction_id'   => $payment->get_transaction_id(),
				'gateway_response' => $this->format_gateway_response( $payment->get_gateway_response() ),
				'payment_source'   => $payment->get_payment_source(),
				'gateway_fee'      => $payment->get_gateway_fee(),
				'payable'          => $payment->get_payable_amount(),
			);

			foreach ( $this->booking->get_fees() as $value ) {
				$slug = $value['name'];
				if ( isset( $_payment_data[ $slug ] ) ) {
					$labels[ $slug ]   = $value['label'];
					$defaults[ $slug ] = $_payment_data[ $slug ];
				}
			}

			$defaults = apply_filters( 'wptravelengine_payment_edit_form_fields', $defaults, $payment );

			$result[] = new PaymentEditFormFields( $defaults, $mode, $labels );
		endforeach;

		return $result;
	}

	/**
	 * Serializes a payment gateway response value to a display string.
	 *
	 * @param mixed $gateway_response
	 * @return string
	 * @since 6.8.0
	 */
	private function format_gateway_response( $gateway_response ): string {
		if ( empty( $gateway_response ) ) {
			return '';
		}
		if ( \is_array( $gateway_response ) || \is_object( $gateway_response ) ) {
			return wp_json_encode( $gateway_response, JSON_PRETTY_PRINT );
		}
		return $gateway_response;
	}

	/**
	 * Registers addon display hooks for the booking edit screen.
	 *
	 * @return void
	 * @since 6.7.0
	 * @since 6.8.0 Renamed and refactored from init_addon_display_hooks(); moved from PostTypes\Booking.
	 */
	protected function register_addon_hooks(): void {
		$addons = apply_filters(
			'wptravelengine_booking_edit_meta_boxes',
			array(
				'accommodation'    => array(
					'p'     => 5,
					'key'   => 'accommodation',
					'label' => __( 'Accommodation', 'wp-travel-engine' ),
				),
				'extra-services'   => array(
					'p'     => 10,
					'key'   => 'extra_service',
					'label' => __( 'Extra Services', 'wp-travel-engine' ),
				),
				'travel-insurance' => array(
					'p'     => 15,
					'key'   => 'travel_insurance',
					'label' => __( 'Travel Insurance', 'wp-travel-engine' ),
				),
			)
		);

		$hooks = array(
			'line_items'      => '',
			'tabs'            => 'tabs/',
			'edit_line_items' => 'edit/',
		);

		foreach ( $addons as $slug => $cfg ) {
			$check_display = function ( $booking ) use ( $slug, $cfg ) {
				if ( wptravelengine_is_addon_active( $slug ) ) {
					return true;
				}
				$items = $booking->get_cart_info()['items'][0]['line_items'] ?? array();
				return ! empty( $items[ $cfg['key'] ] );
			};

			foreach ( $hooks as $hook => $path ) {
				add_action(
					"wptravelengine_booking_details_{$hook}",
					function ( $booking ) use ( $slug, $path, $check_display ) {
						if ( $check_display( $booking ) ) {
							wptravelengine_get_admin_template( "booking/partials/{$path}{$slug}.php" );
						}
					},
					$cfg['p']
				);
			}
		}
	}
}
