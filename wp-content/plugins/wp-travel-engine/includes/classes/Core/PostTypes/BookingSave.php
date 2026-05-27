<?php
/**
 * Post Type Booking.
 *
 * @package WPTravelEngine/Core/PostTypes
 * @since 6.0.0
 */

namespace WPTravelEngine\Core\PostTypes;

use DateTime;
use WPTravelEngine\Abstracts\PostType;
use WPTravelEngine\Core\Cart\Adjustments\CouponAdjustment;
use WPTravelEngine\Core\Cart\Adjustments\TaxAdjustment;
use WPTravelEngine\Core\Cart\Items\PricingCategory;
use WPTravelEngine\Core\Cart\Items\ExtraService;
use WPTravelEngine\Core\Models\Post\Booking as BookingModel;
use WPTravelEngine\Core\Models\Post\Payment;
use WPTravelEngine\Helpers\Functions;
use WPTravelEngine\Utilities\ArrayUtility;
use WPTravelEngine\Core\Models\Post\Customer;
use WPTravelEngine\Validator\Validator;
use WPTravelEngine\Core\Booking\BookingProcess;
use WPTravelEngine\Core\Models\Post\TripPackageIterator;
use WPTravelEngine\Core\Models\Post\TripPackage;
use WPTravelEngine\Core\Models\Post\Trip;
use WPTravelEngine\Abstracts\CartItem;
use WPTravelEngine\Core\Cart\Cart;
use WPTravelEngine\Utilities\PaymentCalculator;
use WPTravelEngine\Core\Tax;
use WPTravelEngine\Filters\Events;

/**
 * Class Booking
 * This class represents a trip booking to the WP Travel Engine plugin.
 *
 * @since 6.0.0
 */
class BookingSave extends PostType {

	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected string $post_type = 'booking';

	/**
	 * Date format constant.
	 *
	 * @var string
	 * @since 6.7.0
	 */
	protected const DATE_FORMAT = 'Y-m-d';

	/**
	 * DateTime format constant.
	 *
	 * @var string
	 * @since 6.7.0
	 */
	private const DATETIME_FORMAT = 'Y-m-d\TH:i';

	/**
	 * Request object.
	 *
	 * @var \WP_REST_Request|null
	 * @since 6.7.0
	 */
	protected $request = null;

	/**
	 * Set fees.
	 *
	 * @var array
	 * @since 6.7.0
	 */
	private array $set_fees = array();

	/**
	 * Save booking data.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post Post object.
	 * @param bool     $update Whether this is an update.
	 * @return void
	 * @since 6.4.0
	 */
	public function save( $post_id, $post, $update = false ) {
		// Verify nonce.
		if ( $this->post_type !== $post->post_type || ! isset( $_POST['wptravelengine_new_booking_nonce'] ) || ! wp_verify_nonce( $_POST['wptravelengine_new_booking_nonce'], 'wptravelengine_new_booking' ) ) {
			return;
		}
		$this->handle_save( $post_id, $post, $update );
	}

	/**
	 * Handle save.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post Post object.
	 * @param bool     $update Whether this is an update.
	 * @return void
	 */
	private function handle_save( $post_id, $post, $update = false ) {

		$booking        = new BookingModel( $post_id );
		$this->request  = Functions::create_request( 'POST' );
		$form_validator = new Validator();

		// Ensure draft posts are published.
		$this->maybe_publish_draft( $post_id, $post );

		$booking->set_meta( '_user_edited', 'yes' );

		if ( $this->is_section_changed( 'billing' ) ) {
			$this->process_billing_details( $booking, $post_id, $form_validator );
		}

		if ( $this->is_section_changed( 'notes' ) || $this->is_section_changed( 'additional-notes' ) ) {
			$this->process_notes( $booking );
		}

		// Get and validate trip info.
		$trip_info = $this->request->get_param( 'order_trip' ) ?? array();
		if ( is_array( $trip_info ) && isset( $trip_info['id'] ) ) {
			$this->handle_booking_save( $booking, $trip_info, $form_validator, $update, $post_id );
		}

		$booking->save();

		if ( ! $update ) {
			Events::booking_created( $booking );
		} else {
			Events::booking_updated( $booking );
		}
	}

	/**
	 * Handle booking save with trip info.
	 *
	 * @param BookingModel $booking Booking model.
	 * @param array        $trip_info Trip info.
	 * @param Validator    $form_validator Form validator.
	 * @param bool         $update Update.
	 * @param int          $post_id Post ID.
	 * @return void
	 */
	protected function handle_booking_save( $booking, $trip_info, $form_validator, $update, $post_id ) {
		global $wte_cart;

		// Resolve custom trip first so $_POST is patched before request object is built.
		$trip_info     = $this->resolve_trip_info( $trip_info, $booking );
		$this->request = Functions::create_request( 'POST' );

		if ( isset( $trip_info['start_date'] ) ) {
			// $booking->set_meta( 'trip_datetime', sanitize_text_field( $trip_info['start_date'] ) );
			$merged_start = $trip_info['start_date'];
			if ( ! empty( $trip_info['start_time'] ) ) {
				$merged_start .= 'T' . $trip_info['start_time'];
			}
			$booking->set_meta( 'trip_datetime', sanitize_text_field( $merged_start ) );
		}

		if ( isset( $trip_info['end_date'] ) ) {
			$merged_end = $trip_info['end_date'];
			if ( ! empty( $trip_info['end_time'] ) ) {
				$merged_end .= 'T' . $trip_info['end_time'];
			}
			$booking->set_meta( 'end_datetime', sanitize_text_field( $merged_end ) );
		}

		$trip_id      = $trip_info['id'];
		$package_name = $this->resolve_package_name( $trip_info );

		// Process cart info and line items.
		$cart_info = $this->process_cart_line_items( $booking->get_cart_info() ?? array() );

		// Get price key for cart item.
		$price_key = $this->get_price_key( $trip_id, $trip_info, $package_name );

		// Save travel insurance meta.
		if ( $this->is_section_changed( 'travel-insurance' ) ) {
			$this->save_travel_insurance_meta( $booking );
		}

		// Store initial booking data for first update.
		$this->store_initial_booking_data( $booking, $update );

		// Process traveller details.
		if ( $this->is_section_changed( 'travellers' ) ) {
			$this->process_traveller_details( $booking, $form_validator );
		}

		// Process emergency contacts.
		if ( $this->is_section_changed( 'emergency-contact' ) ) {
			$this->process_emergency_contacts( $booking, $form_validator );
		}

		// Process payment amounts.
		if ( $this->is_section_changed( 'payments' ) ) {
			$this->process_payment_amounts( $booking );
		}

		// Update cart info with trip details.
		$cart_info = $this->update_cart_trip_info( $booking, $cart_info );

		// Process discounts/deductible items.
		if ( $booking->is_curr_cart( '<' ) ) {
			$cart_info = $this->process_discounts( $cart_info );
		}

		// Process commission.
		$this->process_commission( $booking );

		// Process line items.
		$cart_info = $this->process_line_items( $cart_info );

		// Update cart totals.
		$cart_info = $this->update_cart_totals( $booking, $cart_info );

		// Prepare and process cart items (moved here to use updated line items and subtotal_reservations).
		$cart_items = $this->prepare_cart_items( $trip_info, $trip_id, $price_key, $cart_info );
		$this->process_cart_and_booking( $wte_cart, $cart_items, $post_id, $booking );

		$cart_info['version'] ??= $this->request->get_param( 'wptravelengine_cart_version' );

		// Process payment records.
		$this->process_payments( $booking, $cart_info );

		// Process fees.
		$cart_info = $this->process_fees( $cart_info );

		// Save cart info to booking.
		$booking->set_cart_info( $cart_info );

		$booking->ensure_payment_key();

		do_action( 'wptravelengine_save_addon_meta', $this->request, $booking, $cart_info, $update );
	}

	/**
	 * Maybe publish draft post.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post Post object.
	 * @return void
	 * @since 6.4.0
	 */
	private function maybe_publish_draft( int $post_id, \WP_Post $post ): void {
		if ( isset( $post->ID ) && $post->post_status === 'draft' ) {
			wp_update_post(
				array(
					'ID'          => $post_id,
					'post_status' => 'publish',
				)
			);
			clean_post_cache( $post_id );
			$post->post_status = 'publish';
		}
	}

	/**
	 * Create subtotal reservations for line items.
	 *
	 * @param string $key Line item key (e.g., 'extra_service', 'accommodation').
	 * @param array  $items Line items array.
	 * @return array Array with [reservation_key, subtotal_reservations].
	 * @since 6.7.0
	 */
	private function create_subtotal_reservations( string $key, array $items ): array {
		if ( 'extra_service' === $key ) {
			$subtotal_reservations = array_map(
				function ( $extra_service ) {
					return array(
						'id'       => $extra_service['id'] ?? 'es_' . uniqid(),
						'quantity' => (int) ( $extra_service['quantity'] ?? 1 ),
					);
				},
				$items
			);
			return array( 'extraServices', $subtotal_reservations );
		} else {
			$subtotal_reservations = array_map(
				function ( $acc_item ) use ( $key ) {
					return array(
						'id'       => null,
						'manual'   => true,
						'label'    => $acc_item['label'] ?? 'Manual ' . $key,
						'quantity' => (int) ( $acc_item['quantity'] ?? 1 ),
						'price'    => (float) ( $acc_item['price'] ?? 0 ),
						'total'    => (float) ( $acc_item['total'] ?? 0 ),
					);
				},
				$items
			);
			return array( $key, $subtotal_reservations );
		}
	}

	/**
	 * Assign subtotal reservations to cart info.
	 *
	 * @param array  $cart_info Cart info array (passed by reference).
	 * @param string $reservation_key Reservation key.
	 * @param array  $subtotal_reservations Subtotal reservations array.
	 * @return void
	 * @since 6.7.0
	 */
	private function assign_subtotal_reservations( array &$cart_info, string $reservation_key, array $subtotal_reservations ): void {
		$cart_info['items'][0]['subtotal_reservations'][ $reservation_key ] = $subtotal_reservations;
		$cart_info['subtotal_reservations'][ $reservation_key ]             = $subtotal_reservations;
	}

	/**
	 * Remove line item and its reservations from cart.
	 *
	 * @param array  $cart_info Cart info array (passed by reference).
	 * @param string $key Line item key to remove.
	 * @return void
	 * @since 6.7.0
	 */
	private function remove_line_item_from_cart( array &$cart_info, string $key ): void {
		unset( $cart_info['items'][0]['line_items'][ $key ] );
		unset( $cart_info['items'][0]['subtotal_reservations'][ $key ] );
		unset( $cart_info['subtotal_reservations'][ $key ] );
	}

	/**
	 * Process cart line items and subtotal reservations.
	 *
	 * @param array $cart_info Cart info array.
	 * @return array Modified cart info.
	 * @since 6.7.0
	 */
	private function process_cart_line_items( array $cart_info ): array {
		if ( ! isset( $cart_info['items'][0]['line_items'] ) || ! is_array( $cart_info['items'][0]['line_items'] ) ) {
			return $cart_info;
		}

		foreach ( $cart_info['items'][0]['line_items'] as $key => $line_item ) {
			if ( 'pricing_category' === $key || empty( $line_item ) ) {
				continue;
			}

			list( $reservation_key, $subtotal_reservations ) = $this->create_subtotal_reservations( $key, $line_item );
			$this->assign_subtotal_reservations( $cart_info, $reservation_key, $subtotal_reservations );
		}

		return $cart_info;
	}

	/**
	 * Get price key for cart item.
	 *
	 * @param string|int $trip_id Trip ID or 'other'.
	 * @param array      $trip_info Trip info array.
	 * @param string     $package_name Package name.
	 * @return string|int Price key.
	 * @since 6.7.0
	 */
	private function get_price_key( $trip_id, array $trip_info, string $package_name ) {
		if ( ! $trip_id ) {
			return 0;
		}

		if ( $trip_id === 'other' ) {
			return $trip_info['custom_trip'] ?? 'other';
		}

		if ( ! get_post( $trip_id ) ) {
			return $trip_id;
		}

		$trip          = new Trip( $trip_id );
		$trip_packages = new TripPackageIterator( $trip );

		foreach ( $trip_packages as $trip_package ) {
			/** @var TripPackage $trip_package */
			if ( isset( $trip_package->post->post_title ) && $trip_package->post->post_title === $package_name ) {
				return $trip_package->post->ID ?? $trip_id;
			}
		}

		return $trip_id;
	}

	/**
	 * Resolve package name from trip info.
	 *
	 * @param array $trip_info Trip info array.
	 * @return string Resolved package name.
	 * @since 6.8.0
	 */
	private function resolve_package_name( array $trip_info ): string {
		$raw = $trip_info['package_name'] ?? '';
		if ( is_numeric( $raw ) ) {
			return get_the_title( (int) $raw );
		}
		if ( 'other' === $raw ) {
			return sanitize_text_field( $trip_info['custom_package'] ?? '' );
		}
		return sanitize_text_field( $raw );
	}

	/**
	 * Save travel insurance meta.
	 *
	 * @param BookingModel $booking Booking model.
	 * @return void
	 * @since 6.7.0
	 */
	private function save_travel_insurance_meta( BookingModel $booking ): void {
		if ( $travel_insurance = $this->request->get_param( 'travel_insurance_meta' ) ) {
			$booking->set_meta( 'wptravelengine_travel_insurance', $travel_insurance );
		}
	}

	/**
	 * Prepare cart items array.
	 *
	 * @param array      $trip_info Trip info.
	 * @param string|int $trip_id Trip ID.
	 * @param string|int $price_key Price key.
	 * @param array      $cart_info Cart info.
	 * @return array Cart items array.
	 * @since 6.7.0
	 */
	private function prepare_cart_items( array $trip_info, $trip_id, $price_key, array $cart_info ): array {
		$line_items = $this->request->get_param( 'line_items' );
		$pax        = array();
		if ( is_array( $line_items ) && isset( $line_items['pricing_category']['quantity'] ) ) {
			$pax = $line_items['pricing_category']['quantity'];
		}

		$start_date_raw = (string) ( $trip_info['start_date'] ?? '' );
		$end_date_raw   = (string) ( $trip_info['end_date'] ?? '' );
		$start_time_raw = trim( (string) ( $trip_info['start_time'] ?? '' ) );
		$end_time_raw   = trim( (string) ( $trip_info['end_time'] ?? '' ) );

		// Merge explicit time fields so cart item datetime values retain time.
		if ( $start_date_raw !== '' && $start_time_raw !== '' && ! preg_match( '/[T\s]\d{2}:\d{2}/', $start_date_raw ) ) {
			$start_date_raw .= 'T' . $start_time_raw;
		}
		if ( $end_date_raw !== '' && $end_time_raw !== '' && ! preg_match( '/[T\s]\d{2}:\d{2}/', $end_date_raw ) ) {
			$end_date_raw .= 'T' . $end_time_raw;
		}

		$start_date = $this->format_datetime_for_storage( $start_date_raw, self::DATE_FORMAT );
		$start_time = $this->format_datetime_for_storage( $start_date_raw, self::DATETIME_FORMAT );
		$end_time   = $this->format_datetime_for_storage( $end_date_raw, self::DATETIME_FORMAT );

		$cart_items = array(
			'trip_id'               => $trip_id,
			'trip_date'             => $start_date,
			'trip_time'             => $start_time,
			'price_key'             => $price_key ?? 0,
			'pax'                   => $pax ?? array(),
			'pax_cost'              => array(),
			'trip_price'            => 0,
			'multi_pricing_used'    => false,
			'trip_extras'           => array(),
			'package_name'          => $trip_info['package_name'] ?? '',
			'subtotal_reservations' => $cart_info['subtotal_reservations'] ?? array(),
			'line_items'            => $cart_info['items'][0]['line_items'] ?? array(),
			'travelers_count'       => $trip_info['number_of_travelers'] ?? $this->get_travelers_count(),
			'trip_end_date'         => $end_time,
			'trip_end_time'         => $end_time,
		);

		if ( isset( $trip_info['end_date'] ) ) {
			$cart_items['trip_time_range'] = array(
				$start_time,
				$end_time,
			);
		}

		return $cart_items;
	}

	/**
	 * Format date/datetime input for storage using WP site timezone.
	 *
	 * @param string $value Raw date/datetime value.
	 * @param string $format Target format.
	 * @return string
	 * @since 6.8.0
	 */
	private function format_datetime_for_storage( string $value, string $format ): string {
		$value = trim( $value );
		if ( '' === $value ) {
			return '';
		}
		if ( preg_match( '/^(\d{4}-\d{2}-\d{2})(?:[T\s](\d{2}):(\d{2}))?$/', $value, $matches ) ) {
			$date = $matches[1];
			if ( self::DATE_FORMAT === $format ) {
				return $date;
			}
			if ( ! isset( $matches[2], $matches[3] ) ) {
				return $date;
			}
			$hour = $matches[2];
			$min  = $matches[3];
			return "{$date}T{$hour}:{$min}";
		}

		return $value;
	}

	/**
	 * Process cart and booking.
	 *
	 * @param object       $wte_cart Cart object.
	 * @param array        $cart_items Cart items.
	 * @param int          $post_id Post ID.
	 * @param BookingModel $booking Booking model.
	 * @return void
	 * @since 6.7.0
	 */
	private function process_cart_and_booking( $wte_cart, array $cart_items, int $post_id, BookingModel $booking ): void {
		$wte_cart->clear();

		if ( is_numeric( $cart_items['trip_id'] ) ) {
			$item = new \WPTravelEngine\Core\Cart\Item( $wte_cart, $cart_items );
			$wte_cart->setItems( array( $cart_items['trip_id'] => $item ) );
			$wte_cart->add( $item );
			$wte_cart->set_booking_ref( $post_id );
		}

		$booking_process = new BookingProcess( $this->request, $wte_cart );

		if ( ! empty( $wte_cart->getItems( true ) ) && ! empty( $post_id ) && get_post( $post_id ) ) {
			$booking_post = BookingModel::make( $post_id );
			$booking_post->set_order_items( $wte_cart->getItems( true ) );
			$booking_process->set_order_items( $booking_post );
		}
	}

	/**
	 * Store initial booking data.
	 *
	 * @param BookingModel $booking Booking model.
	 * @param bool         $update Whether this is an update.
	 * @return void
	 * @since 6.7.0
	 */
	private function store_initial_booking_data( BookingModel $booking, bool $update ): void {
		if ( ! $update ) {
			return;
		}

		$cart_info = $booking->get_cart_info();
		if ( $booking->get_meta( '_initial_cart_info' ) === '' ) {
			$booking->set_meta( '_initial_cart_info', wp_json_encode( $cart_info ) );
		}

		$order_items = $booking->get_order_items();
		if ( $booking->get_meta( '_initial_order_items' ) === '' ) {
			$booking->set_meta( '_initial_order_items', wp_json_encode( $order_items ) );
		}
	}

	/**
	 * Process traveller details.
	 *
	 * @param BookingModel $booking Booking model.
	 * @param Validator    $form_validator Form validator.
	 * @return void
	 * @since 6.7.0
	 */
	private function process_traveller_details( BookingModel $booking, Validator $form_validator ): void {
		$travellers = $this->request->get_param( 'travellers' );

		if ( ! $travellers ) {
			$booking->set_traveller_details( array() );
			return;
		}

		$sanitized_data = array();

		foreach ( $travellers as $field => $values ) {
			if ( ! is_array( $values ) || 'pricing_category' === $field ) {
				continue;
			}

			foreach ( $values as $index => $value ) {
				if ( ! isset( $sanitized_data[ $index ] ) ) {
					$sanitized_data[ $index ] = array();
				}

				if ( is_array( $value ) ) {
					$sanitized_data[ $index ][ $field ] = $value;
					continue;
				}

				if ( ! empty( $value ) || $value === '' ) {
					$sanitized_data[ $index ][ $field ] = $this->sanitize_traveller_field( $field, $value, $form_validator );
				}
			}
		}

		$booking->set_traveller_details( array_values( $sanitized_data ) );
	}

	/**
	 * Sanitize traveller field.
	 *
	 * @param string    $field Field name.
	 * @param mixed     $value Field value.
	 * @param Validator $form_validator Form validator.
	 * @return mixed Sanitized value.
	 * @since 6.4.0
	 */
	private function sanitize_traveller_field( string $field, $value, Validator $form_validator ) {
		// Handle null or empty values.
		if ( $value === null ) {
			return '';
		}

		switch ( $field ) {
			case 'email':
				return sanitize_email( $value );
			case 'phone':
				return $form_validator->sanitize_phone( $value );
			case 'country':
				return $form_validator->sanitize_country( $value );
			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Process emergency contacts.
	 *
	 * @param BookingModel $booking Booking model.
	 * @param Validator    $form_validator Form validator.
	 * @return void
	 * @since 6.7.0
	 */
	private function process_emergency_contacts( BookingModel $booking, Validator $form_validator ): void {
		$emergency_contacts = $this->request->get_param( 'emergency_contacts' );
		if ( ! $emergency_contacts ) {
			return;
		}

		$data = array();
		foreach ( array_keys( $emergency_contacts ) as $entity ) {
			foreach ( $emergency_contacts[ $entity ] as $index => $value ) {
				$data[ $index ][ $entity ] = $value;
			}
		}

		$sanitized_data = array_map(
			function ( $emergency_contact ) use ( $form_validator ) {
				$sanitized = array();
				foreach ( $emergency_contact as $field => $value ) {
					if ( is_array( $value ) ) {
						$sanitized[ $field ] = $value;
						continue;
					}
					$sanitized[ $field ] = $this->sanitize_traveller_field( $field, $value, $form_validator );
				}
				return $sanitized;
			},
			$data
		);

		$booking->set_emergency_contact_details( $sanitized_data );
	}

	/**
	 * Process billing details.
	 *
	 * @param BookingModel $booking Booking model.
	 * @param int          $post_id Post ID.
	 * @param Validator    $form_validator Form validator.
	 * @return void
	 * @since 6.7.0
	 */
	private function process_billing_details( BookingModel $booking, int $post_id, Validator $form_validator ): void {
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
					$customer_model->maybe_register_as_user( true );
				}

				do_action( 'wptravelengine_after_customer_created', $customer_model->ID );

				$customer_model->update_customer_bookings( $post_id );
				$customer_model->update_customer_meta( $post_id );
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
			$sanitized_billing[ $field ] = $this->sanitize_traveller_field( $field, $value, $form_validator );
		}

		$booking->set_meta( 'wptravelengine_billing_details', $sanitized_billing );
		$booking->set_meta( 'billing_info', $sanitized_billing );
	}

	/**
	 * Process payment amounts.
	 *
	 * @param BookingModel $booking Booking model.
	 * @return void
	 * @since 6.7.0
	 */
	private function process_payment_amounts( BookingModel $booking ): void {
		if ( is_numeric( $paid_amount = $this->request->get_param( 'paid_amount' ) ) ) {
			$booking->set_meta( 'paid_amount', (float) $paid_amount );
		}

		if ( is_numeric( $due_amount = $this->request->get_param( 'due_amount' ) ) ) {
			$booking->set_meta( 'due_amount', (float) $due_amount );
		}
	}

	/**
	 * Process notes.
	 *
	 * @param BookingModel $booking Booking model.
	 * @return void
	 * @since 6.7.0
	 */
	private function process_notes( BookingModel $booking ): void {
		if ( $additional_note = $this->request->get_param( 'additional_details' ) ) {
			$booking->set_additional_details( sanitize_text_field( $additional_note ) );
		}

		if ( $admin_notes = $this->request->get_param( 'admin_notes' ) ) {
			$booking->set_notes( sanitize_text_field( $admin_notes ) );
		}
	}

	/**
	 * Update cart info with trip details.
	 *
	 * @param BookingModel $booking Booking model.
	 * @return array Modified cart info.
	 * @since 6.7.0
	 */
	private function update_cart_trip_info( BookingModel $booking, array $cart_info ): array {
		$trip_info = $this->request->get_param( 'order_trip' );
		if ( ! $trip_info ) {
			return $cart_info;
		}

		$sanitized_trip_info = array(
			'id'                  => absint( $trip_info['id'] ?? 0 ),
			'start_date'          => sanitize_text_field( $trip_info['start_date'] ?? '' ),
			'start_time'          => sanitize_text_field( $trip_info['start_time'] ?? '' ),
			'end_date'            => sanitize_text_field( $trip_info['end_date'] ?? '' ),
			'end_time'            => sanitize_text_field( $trip_info['end_time'] ?? '' ),
			'trip_code'           => sanitize_text_field( $trip_info['trip_code'] ?? '' ),
			'number_of_travelers' => absint( $trip_info['number_of_travelers'] ?? $this->get_travelers_count() ),
			'package_name'        => $this->resolve_package_name( $trip_info ),
			'custom_trip'         => sanitize_text_field( $trip_info['custom_trip'] ?? '' ),
		);

		// If id resolved to 0 (e.g., custom trip with request created before id override), preserve existing numeric trip_id from cart_info
		if ( 0 === $sanitized_trip_info['id'] && isset( $cart_info['items'][0]['trip_id'] ) && is_numeric( $cart_info['items'][0]['trip_id'] ) ) {
			$sanitized_trip_info['id'] = (int) $cart_info['items'][0]['trip_id'];
		}

		$start_has_time = date_parse( $sanitized_trip_info['start_date'] )['hour'] !== false;
		$end_has_time   = date_parse( $sanitized_trip_info['end_date'] )['hour'] !== false;

		$start_date = $start_has_time
			? ( DateTime::createFromFormat( 'Y-m-d H:i', $sanitized_trip_info['start_date'] )
				?: DateTime::createFromFormat( 'Y-m-d\TH:i', $sanitized_trip_info['start_date'] ) )
			: DateTime::createFromFormat( 'Y-m-d', $sanitized_trip_info['start_date'] );

		$end_date = $end_has_time
			? ( DateTime::createFromFormat( 'Y-m-d H:i', $sanitized_trip_info['end_date'] )
				?: DateTime::createFromFormat( 'Y-m-d\TH:i', $sanitized_trip_info['end_date'] ) )
			: DateTime::createFromFormat( 'Y-m-d', $sanitized_trip_info['end_date'] );

		$sanitized_trip_info['start_date'] = $start_date
			? $start_date->format( $start_has_time ? self::DATETIME_FORMAT : self::DATE_FORMAT )
			: current_time( self::DATETIME_FORMAT );
		$sanitized_trip_info['end_date']   = $end_date
			? $end_date->format( $end_has_time ? self::DATETIME_FORMAT : self::DATE_FORMAT )
			: current_time( self::DATETIME_FORMAT );

		// Merge separate time fields back into combined datetime for backward-compatible storage.
		// Guard against double-appending if the submitted date already contained a time component.
		if ( ! empty( $sanitized_trip_info['start_time'] ) && ! $start_has_time ) {
			$sanitized_trip_info['start_date'] .= 'T' . $sanitized_trip_info['start_time'];
		}
		if ( ! empty( $sanitized_trip_info['end_time'] ) && ! $end_has_time ) {
			$sanitized_trip_info['end_date'] .= 'T' . $sanitized_trip_info['end_time'];
		}

		// Ensure items array exists.
		if ( ! isset( $cart_info['items'][0] ) ) {
			$cart_info['items'][0] = array();
		}

		$cart_info['items'][0]['trip_id']          = $sanitized_trip_info['id'];
		$cart_info['items'][0]['trip_date']        = $sanitized_trip_info['start_date'];
		$cart_info['items'][0]['trip_time']        = $sanitized_trip_info['start_date'];
		$cart_info['items'][0]['end_date']         = $sanitized_trip_info['end_date'];
		$cart_info['items'][0]['trip_endtime']     = $sanitized_trip_info['end_date'];
		$cart_info['items'][0]['travelers_count']  = $sanitized_trip_info['number_of_travelers'];
		$cart_info['items'][0]['trip_package']     = $sanitized_trip_info['package_name'];
		$cart_info['items'][0]['custom_trip_name'] = $sanitized_trip_info['custom_trip'];

		return $cart_info;
	}

	/**
	 * Process discounts.
	 *
	 * @param array $cart_info Cart info.
	 * @return array Modified cart info.
	 * @since 6.7.0
	 */
	private function process_discounts( array $cart_info ): array {
		if ( ! $this->request->get_param( 'discounts' ) ) {
			unset( $cart_info['totals']['total_discount'] );
			unset( $cart_info['deductible_items'] );
			return $cart_info;
		}

		$deductible_items = $this->request->get_param( 'discounts' );
		$items            = ArrayUtility::normalize( $deductible_items, 'label' );
		$_items           = array();

		foreach ( $items as $index => $item ) {
			$percentage = '';
			if ( preg_match( '/(\d+)%/', $item['label'], $matches ) ) {
				$percentage = $matches[1];
			}

			$_items[] = wp_parse_args(
				$item,
				array(
					'name'                     => 'discount' . $index,
					'order'                    => $index,
					'label'                    => $item['label'],
					'description'              => '',
					'adjustment_type'          => 'percentage',
					'apply_to_actual_subtotal' => false,
					'percentage'               => $percentage,
					'value'                    => $item['value'],
					'_class_name'              => CouponAdjustment::class,
					'type'                     => 'deductible',
				)
			);
			$cart_info['totals'][ 'total_discount' . $index ] = $item['value'];
		}

		$cart_info['deductible_items'] = $_items;
		return $cart_info;
	}

	/**
	 * Process commission.
	 *
	 * @param BookingModel $booking Booking model.
	 * @return void
	 * @since 6.7.0
	 */
	private function process_commission( BookingModel $booking ): void {
		$commission_amount = $this->request->get_param( 'wptravelengine_commission_amount' );
		if ( ! $commission_amount || ! function_exists( 'slicewp_get_commissions' ) ) {
			return;
		}

		$commissions = slicewp_get_commissions(
			array(
				'reference' => $booking->ID,
				'origin'    => 'wptravelengine',
				'number'    => 1,
			)
		);

		if ( ! empty( $commissions ) && function_exists( 'slicewp_update_commission' ) ) {
			slicewp_update_commission(
				$commissions[0]->get( 'id' ),
				array(
					'amount' => $commission_amount,
				)
			);
		}
	}

	/**
	 * Process fees.
	 *
	 * @param array $cart_info Cart info.
	 * @return array Modified cart info.
	 * @since 6.7.0
	 */
	private function process_fees( array $cart_info ): array {
		if ( ! $this->request->get_param( 'fees' ) ) {
			unset( $cart_info['tax_amount'] );
			unset( $cart_info['totals']['total_fee'] );
			unset( $cart_info['fees'] );
			return $cart_info;
		}

		$fees  = $this->request->get_param( 'fees' );
		$items = ArrayUtility::normalize( $fees, 'label' );

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
			$_items[ $index ]['_class_name']     = $item_class;
			$_items[ $index ]['adjustment_type'] = ( $item['adjustment_type'] ?? '' ) ?: 'percentage';
			$_items[ $index ]['percentage']      = ( $item['percentage'] ?? '' ) ?: $percentage;
			$_items[ $index ]['apply_tax']       = wptravelengine_toggled( ( $item['apply_tax'] ?? '' ) ?: true );
			$_items[ $index ]['apply_upfront']   = wptravelengine_toggled( ( $item['apply_upfront'] ?? '' ) ?: false );

			$cart_info['totals'][ 'total_fee' . $index ] = $item['value'];
		}

		$cart_info['fees'] = $_items;
		return $cart_info;
	}

	/**
	 * Process line items.
	 *
	 * @param array $cart_info Cart info.
	 * @return array Modified cart info.
	 * @since 6.7.0
	 */
	private function process_line_items( array $cart_info ): array {
		$line_items = $this->request->get_param( 'line_items' );

		// If no line items submitted, clear all non-pricing_category line items from cart
		if ( ! $line_items ) {
			if ( isset( $cart_info['items'][0]['line_items'] ) ) {
				foreach ( $cart_info['items'][0]['line_items'] as $key => $value ) {
					if ( 'pricing_category' !== $key ) {
						unset( $cart_info['items'][0]['line_items'][ $key ] );
					}
				}
			}
			return $cart_info;
		}

		// Remove line items from cart that weren't submitted in the form
		if ( isset( $cart_info['items'][0]['line_items'] ) ) {
			foreach ( $cart_info['items'][0]['line_items'] as $key => $value ) {
				if ( 'pricing_category' !== $key && ! isset( $line_items[ $key ] ) ) {
					// This line item type exists in cart but wasn't submitted - remove it
					$this->remove_line_item_from_cart( $cart_info, $key );
					error_log( "REMOVED LINE ITEM TYPE '$key' - not in form submission" );
				}
			}
		}

		foreach ( $line_items as $key => $item ) {
			if ( 'pricing_category' === $key && ! isset( $item['label'] ) ) {
				// Get existing pricing category data from cart
				$existing_items = $cart_info['items'][0]['line_items'][ $key ] ?? array();

				$_items = array();

				// Build a lookup map: category_id => label from term taxonomy
				$category_labels = array();
				foreach ( $item as $cat_id => $cat_data ) {
					if ( is_numeric( $cat_id ) && $cat_id > 0 ) {
						$term = get_term( $cat_id, 'trip-packages-categories' );
						if ( $term && ! is_wp_error( $term ) ) {
							$category_labels[ $cat_id ] = $term->name;
						}
					}
				}

				// New format: line_items[pricing_category][category_id][price][] and [quantity][]
				foreach ( $item as $category_id => $category_data ) {
					if ( ! is_array( $category_data ) ) {
						continue;
					}

					$cart_info['items'][0]['travelers'][ $category_id ] = $category_data['quantity'][0] ?? $cart_info['items'][0]['travelers'][ $category_id ] ?? 0;

					// Get price and quantity from the new structure
					$price    = isset( $category_data['price'][0] ) ? (float) $category_data['price'][0] : 0;
					$quantity = isset( $category_data['quantity'][0] ) ? (float) $category_data['quantity'][0] : 1;

					// Find existing item - first try by category_id, then by label
					$existing_item = null;
					$label         = $category_labels[ $category_id ] ?? '';

					foreach ( $existing_items as $existing ) {
						// Try matching by category_id first
						if ( isset( $existing['category_id'] ) && $existing['category_id'] == $category_id ) {
							$existing_item = $existing;
							break;
						}
						// Fall back to matching by label if category_id not present
						if ( ! empty( $label ) && isset( $existing['label'] ) && $existing['label'] === $label ) {
							$existing_item = $existing;
							break;
						}
					}

					if ( $existing_item ) {
						// Preserve existing data and update price, quantity, and add category_id
						$_items[] = array_merge(
							$existing_item,
							array(
								'category_id' => $category_id, // Add category_id for future lookups
								'price'       => $price,
								'quantity'    => $quantity,
								'total'       => $price * $quantity,
							)
						);
					} else {
						// No existing item found - create new item with minimal required data
						error_log( "INFO: Creating new pricing category item for category_id: $category_id (label: $label)" );
						$_items[] = array(
							'label'       => $label ?: 'Category ' . $category_id,
							'category_id' => $category_id,
							'quantity'    => $quantity,
							'price'       => $price,
							'total'       => $price * $quantity,
							'pricingType' => 'per-person', // Default pricing type
							'_class_name' => PricingCategory::class,
						);
					}
				}

				if ( ! empty( $_items ) ) {
					$cart_info['items'][0]['line_items'][ $key ] = $_items;
				}
				continue;
			}

			// First check if the raw item data is empty (all arrays are empty)
			$is_empty_submission = true;
			if ( is_array( $item ) ) {
				foreach ( $item as $field_values ) {
					if ( is_array( $field_values ) && ! empty( $field_values ) ) {
						// Check if any value in the array is non-empty
						foreach ( $field_values as $value ) {
							if ( ! empty( trim( $value ) ) ) {
								$is_empty_submission = false;
								break 2; // Break out of both loops
							}
						}
					}
				}
			}

			// If submission is completely empty, remove from cart and skip
			if ( $is_empty_submission ) {
				$this->remove_line_item_from_cart( $cart_info, $key );
				continue;
			}

			// Normalize the item to get proper structure
			$items = ArrayUtility::normalize( $item, 'label' );

			$_items = array();

			foreach ( $items as $_item ) {
				if ( ! is_numeric( $_item['price'] ?: '' ) || empty( $_item['label'] ) ) {
					continue;
				}

				$_class_name = apply_filters(
					'wptravelengine_custom_line_item_class',
					$key === 'pricing_category' ? PricingCategory::class : ( $key === 'extra_service' ? ExtraService::class : false ),
					$key
				);

				if ( ! is_subclass_of( $_class_name, CartItem::class ) ) {
					continue;
				}

				$_item['total'] = (float) $_item['price'] * (float) $_item['quantity'];

				$_items[] = wp_parse_args(
					$_item,
					array(
						'label'       => $_item['label'],
						'quantity'    => $_item['quantity'],
						'price'       => $_item['price'],
						'_class_name' => $_class_name,
					)
				);
			}

			$cart_info['items'][0]['line_items'][ $key ] = $_items;

			// Process subtotal_reservations
			if ( 'pricing_category' === $key || empty( $_items ) ) {
				continue;
			}

			list( $reservation_key, $subtotal_reservations ) = $this->create_subtotal_reservations( $key, $_items );
			$this->assign_subtotal_reservations( $cart_info, $reservation_key, $subtotal_reservations );
		}

		return $cart_info;
	}

	/**
	 * Update cart totals.
	 *
	 * @param BookingModel $booking Booking model.
	 * @param array        $cart_info Cart info.
	 * @since 6.7.0
	 * @since 6.7.6 - removed partial total from cart info.
	 */
	private function update_cart_totals( BookingModel $booking, array $cart_info ): array {
		if ( is_numeric( $total = $this->request->get_param( 'total' ) ) ) {
			$cart_info['total']           = (float) $total;
			$cart_info['totals']['total'] = (float) $total;
		}

		if ( is_numeric( $subtotal = $this->request->get_param( 'subtotal' ) ) ) {
			$cart_info['subtotal']           = (float) $subtotal;
			$cart_info['totals']['subtotal'] = (float) $subtotal;
		}

		if ( is_numeric( $due_amount = $this->request->get_param( 'due_amount' ) ) ) {
			$booking->set_total_due_amount( (float) $due_amount );
			$cart_info['totals']['due_total'] = (float) $due_amount;
		}

		return $cart_info;
	}

	/**
	 * Process payments.
	 *
	 * @param BookingModel $booking Booking model.
	 * @param array        $cart_info Booking Cart Info.
	 * @return void
	 */
	private function process_payments( BookingModel $booking, array &$cart_info ): void {
		$payments = $this->request->get_param( 'payments' );
		$fees     = $this->request->get_param( 'fees' );
		$fees     = $fees ? ( $fees['slug'] ?? array() ) : array();

		if ( ! $payments ) {
			array_map(
				function ( $fee ) {
					$this->set_fees[ $fee ] = true;
				},
				$fees
			);
			return;
		}

		$items = ArrayUtility::normalize( $payments, 'gateway' );
		$calc  = PaymentCalculator::for( 'USD' );

		$_payments = array();

		$actual_deposit_                            = '0.00';
		$cart_info['totals']['deposit']             = '0.00';
		$cart_info['totals']['total_extra_charges'] = '0.00';

		$success_status = wptravelengine_success_payment_status();

		foreach ( $items as $payment_data ) {
			try {
				$payment_model = new Payment( (int) $payment_data['id'] );
			} catch ( \Exception $e ) {
				$payment_model = Payment::create_post(
					array(
						'post_type'   => 'wte-payments',
						'post_status' => 'publish',
						'post_title'  => 'Payment',
					)
				);
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
				$p_fee = floatval( $payment_data[ $fee ] ?? '' );
				if ( $p_fee > 0.00 ) {
					$p_fee = $calc->normalize( (string) $p_fee );
					$payment_cart_total->set( 'total_' . $fee, $p_fee );
					$extra_fee              = $calc->add( $extra_fee, $p_fee );
					$this->set_fees[ $fee ] = true;
				}
			}

			if ( '0' !== $extra_fee ) {
				$payment_cart_total->set( 'total_extra_charges', $extra_fee );
				$cart_info['totals']['total_extra_charges'] = $extra_fee;
			}

			$payment_model->set_meta( 'cart_totals', $payment_cart_total->value() );

			if ( $gateway = $payment_data['gateway'] ?? null ) {
				$payment_model->set_meta( 'payment_gateway', sanitize_text_field( $gateway ) );
				$booking->set_meta( 'wp_travel_engine_booking_payment_gateway', sanitize_text_field( $gateway ) );
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
				$payment_model->set_meta( 'booking_id', $booking->ID );
			}

			if ( empty( $payment_model->get_meta( 'payment_source' ) ) ) {
				$payment_model->set_meta( 'payment_source', $payment_model->get_payment_source() );
			}

			Events::payment_created( $payment_model );

			$payment_model->save();
			$_payments[] = $payment_model->get_id();
		}

		if ( version_compare( $cart_info['version'], '4.0', '>=' ) ) {
			if ( is_numeric( $total = $this->request->get_param( 'total' ) ) ) {
				$due_exclusive = $calc->subtract( (string) $total, (string) $actual_deposit_ );
				$booking->set_meta( 'total_due_amount', $due_exclusive );
			}
		}

		$last_status = $payment_model->get_meta( 'payment_status' );
		$booking->set_meta( 'wp_travel_engine_booking_payment_status', $last_status );

		$booking->set_meta( 'payments', $_payments );
		/*
		Moved from update_cart_totals to process_payments.
		*  @since 6.7.6
		*/
		$booking->set_total_paid_amount( (float) $paid_amount );
	}

	/**
	 * Get travelers count.
	 *
	 * @return int Travelers count.
	 * @since 6.7.0
	 */
	private function get_travelers_count(): int {
		$line_items = $this->request->get_param( 'line_items' );

		$travelers_count = 0;
		foreach ( $line_items['pricing_category'] ?? array() as $_val ) {
			$travelers_count += array_sum( array_map( 'intval', $_val['quantity'] ?? array() ) );
		}

		return $travelers_count;
	}

	/**
	 * Resolves trip info — returns existing trip or creates a new custom trip.
	 * Patches $_POST so Legacy request methods see the resolved trip ID.
	 *
	 * @param array $trip_info Raw trip info from request.
	 * @param mixed $booking   Booking model instance.
	 * @return array Resolved trip info.
	 * @since 6.8.0
	 */
	private function resolve_trip_info( array $trip_info, $booking ): array {
		$trip_id = $trip_info['id'] ?? '';

		if ( 'other' !== $trip_id && '' !== $trip_id && 0 !== (int) $trip_id ) {
			return $trip_info;
		}

		$custom_trip_title = sanitize_text_field( $trip_info['custom_trip'] ?? '' );
		if ( '' === $custom_trip_title ) {
			return $trip_info;
		}

		$resolved_id = $this->get_or_create_custom_trip( $custom_trip_title, $booking );
		if ( ! $resolved_id ) {
			return $trip_info;
		}

		$trip_code = 'WTE-' . $resolved_id;

		$_POST['order_trip']['id']        = $resolved_id; // phpcs:ignore
		$_POST['order_trip']['trip_code'] = $trip_code;   // phpcs:ignore

		return array_merge(
			$trip_info,
			array(
				'id'        => $resolved_id,
				'trip_code' => $trip_code,
			)
		);
	}

	/**
	 * Returns existing custom trip ID stored on booking, or creates a new draft trip.
	 *
	 * @param string $title   Sanitized trip title.
	 * @param mixed  $booking Booking model instance.
	 * @return int Trip post ID, or 0 on failure.
	 * @since 6.8.0
	 */
	private function get_or_create_custom_trip( string $title, $booking ): int {
		$stored_id = (int) $booking->get_meta( 'custom_trip_post_id' );
		if ( $stored_id && get_post( $stored_id ) ) {
			return $stored_id;
		}

		$new_id = wp_insert_post(
			array(
				'post_type'   => WP_TRAVEL_ENGINE_POST_TYPE,
				'post_title'  => wp_slash( $title ),
				'post_status' => 'draft',
				'post_author' => get_current_user_id(),
			)
		);

		if ( is_wp_error( $new_id ) || ! $new_id ) {
			return 0;
		}

		$trip_code = 'WTE-' . $new_id;

		update_post_meta(
			$new_id,
			'wp_travel_engine_setting',
			apply_filters(
				'wptravelengine_custom_trip_edit_settings',
				array(
					'trip_code'                  => $trip_code,
					'partial_payment_enable'     => 'yes',
					'partial_payment_use'        => 'global',
					'partial_payment_amount'     => 0,
					'partial_payment_percent'    => 0,
					'partial_payment_type'       => 'amount',
					'partial_payment_use_global' => 'yes',
				),
				$new_id
			)
		);

		update_post_meta( $new_id, 'is_created_from_booking', 'yes' );

		$booking->set_meta( '__customized', 'yes' );

		$custom_trips   = get_option( 'wptravelengine_custom_trips', array() );
		$custom_trips[] = $new_id;
		update_option( 'wptravelengine_custom_trips', $custom_trips );

		return $new_id;
	}

	/**
	 * Check if addon should be displayed
	 * Display if addon is active OR if there's existing data for the addon
	 *
	 * @param object $booking Booking object
	 * @param string $addon   Addon name
	 *
	 * @return bool
	 * @since 6.7.0
	 */
	protected function should_display_addon( $booking, $addon ) {
		// Always show if addon is active
		if ( wptravelengine_is_addon_active( $addon ) ) {
			return true;
		}

		// Check if there's existing data for this addon
		$cart_info = $booking->get_cart_info();
		if ( empty( $cart_info['items'][0]['line_items'] ) ) {
			return false;
		}

		$line_items = $cart_info['items'][0]['line_items'];

		// Map addon names to their line item keys
		$addon_line_item_map = array(
			'accommodation'    => 'accommodation',
			'extra-services'   => 'extra_service',
			'travel-insurance' => 'travel_insurance',
		);

		$line_item_key = $addon_line_item_map[ $addon ] ?? $addon;

		// Check if line items exist and are not empty
		return ! empty( $line_items[ $line_item_key ] ?? '' );
	}

	/**
	 * Checks if sections is changed.
	 *
	 * @since 6.8.0
	 */
	protected function is_section_changed( string $id ): bool {
		return $this->request && wptravelengine_toggled( $this->request->get_param( "__changed_{$id}" ) );
	}
}
