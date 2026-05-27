<?php
/**
 * Email Tags.
 *
 * @since 5.5.3
 */

namespace WPTravelEngine\Booking\Email;

use WPTravelEngine\Core\Models\Post\Trip;
use WPTravelEngine\Core\Models\Settings\Options;
use WPTravelEngine\Builders\FormFields\TravellerFormFields;
use WPTravelEngine\Builders\FormFields\BillingFormFields;
use WPTravelEngine\Builders\FormFields\EmergencyFormFields;
use WPTravelEngine\Email\TemplateTags;
use WPTravelEngine\Helpers\Countries;
use WPTravelEngine\Helpers\CartInfoParser;
use WPTravelEngine\Core\Models\Post\Booking;
use WPTravelEngine\Core\Models\Post\Payment;
use WPTravelEngine\Utilities\PaymentCalculator;

#[AllowDynamicProperties]
class Template_Tags extends TemplateTags {

	/**
	 * Currency code.
	 *
	 * @var string
	 */
	protected $currency;

	/**
	 * Called from booking details.
	 *
	 * @var bool
	 * @since 6.7.1
	 */
	public $called_from_booking_details = false;

	/**
	 * Called from payment details.
	 *
	 * @var bool
	 * @since 6.7.1
	 */
	public $called_from_payment_details = false;

	/**
	 * Constructor.
	 *
	 * @since 6.8.0 Use get_post_meta() directly for traveller_details to bypass magic property caching.
	 *
	 * @param int $booking_id Booking post ID.
	 * @param int $payment_id Payment post ID.
	 */
	public function __construct( $booking_id, $payment_id ) {
		$this->booking = get_post( $booking_id );
		$this->payment = get_post( $payment_id );

		$this->order_trips = (array) ( $this->booking->order_trips ?? array() );

		$this->billing_info = (array) ( $this->booking->billing_info ?? array() );

		$this->cart_info = (array) ( $this->booking->cart_info ?? array() );

		$this->trip = ! empty( $this->order_trips ) ? (object) ( array_values( $this->order_trips )[0] ?? array() ) : (object) array();

		$this->billing_details = (array) ( $this->booking->wptravelengine_billing_details ?? array() );

		$this->traveller_details = (array) ( get_post_meta( $booking_id, 'wptravelengine_travelers_details', true ) ?: array() );

		$this->emergency_details = (array) ( $this->booking->wptravelengine_emergency_details ?? array() );

		$this->additional_notes = $this->booking->wptravelengine_additional_note ?? '';

		$this->cart_info_parser = new CartInfoParser( (array) $this->cart_info );

		$this->currency = $this->payment->payable['currency'] ?? $this->cart_info['currency'] ?? $this->cart_info->currency ?? 'USD';

		$this->called_from_booking_details = false;
		$this->called_from_payment_details = false;
		parent::__construct();
	}

	/**
	 * Get the trip URL.
	 *
	 * @return string
	 * @since 6.7.6 Updated: only get url from this function.
	 */
	public function get_trip_url() {
		$order_trip = $this->cart_info_parser->get_item();

		if ( ! empty( $order_trip ) ) {
			return esc_url( get_permalink( $order_trip->get_trip_id() ) );
		}

		return esc_url( get_permalink( $this->trip->ID ) );
	}

	public function get_billing_first_name() {
		if ( isset( $this->billing_info['fname'] ) ) {
			return $this->billing_info['fname'];
		}

		return wte_array_get( $this->billing_info, 'booking_first_name', '' );
	}

	public function get_billing_last_name() {
		if ( isset( $this->billing_info['lname'] ) ) {
			return $this->billing_info['lname'];
		}

		return wte_array_get( $this->billing_info, 'booking_last_name', '' );
	}

	public function get_billing_fullname() {
		return implode( ' ', array( $this->get_billing_first_name(), $this->get_billing_last_name() ) );
	}

	public function get_billing_email() {
		if ( isset( $this->billing_info['email'] ) ) {
			return $this->billing_info['email'];
		}

		return wte_array_get( $this->billing_info, 'booking_email', '' );
	}

	public function get_billing_address() {
		if ( isset( $this->billing_info['address'] ) ) {
			return $this->billing_info['address'];
		}

		return wte_array_get( $this->billing_info, 'booking_address', '' );
	}

	public function get_billing_city() {
		if ( isset( $this->billing_info['city'] ) ) {
			return $this->billing_info['city'];
		}

		return wte_array_get( $this->billing_info, 'booking_city', '' );
	}

	public function get_billing_country() {

		$countries_list = Countries::list();
		if ( isset( $countries_list[ $this->billing_info['country'] ] ) ) {
			return $countries_list[ $this->billing_info['country'] ];
		}
		if ( isset( $this->billing_info['country'] ) ) {
			return $this->billing_info['country'];
		}

		return wte_array_get( $this->billing_info, 'booking_country', '' );
	}

	public function get_due_amount() {
		$cart_info      = (array) $this->cart_info ?? array();
		$payment_type   = $cart_info['payment_type'] ?? '';
		$is_due_payment = $payment_type === 'partial' || $payment_type === 'due' || $payment_type === 'remaining_payment';
		/**
		 * Check if payment is due.
		 *
		 * @since 6.5.1
		 */
		if ( $is_due_payment ) {
			$booking_id   = $this->booking->ID;
			$booking_post = Booking::make( $booking_id );
			$due_amount   = $booking_post->get_total_due_amount();
		} else {
			$due_amount = $this->booking->due_amount;
		}
		return wptravelengine_the_price_with_decimal( $due_amount, false );
	}

	/**
	 * Get the current date.
	 *
	 * @since 6.5.0
	 * @return string
	 * @since 6.7.10 Updated booked date calculation.
	 */
	public function get_current_date() {
		$date_to_format = $this->booking->post_date_gmt ?: $this->booking->post_date;
		$timestamp      = strtotime( $date_to_format );

		return wp_date(
			get_option( 'date_format', 'Y-m-d' ) . ' ' . get_option( 'time_format', 'H:i:s' ),
			$timestamp
		);
	}

	/**
	 * Get the total amount.
	 *
	 * @since 6.5.0
	 * @return string
	 */
	public function get_total_amount() {
		return wptravelengine_the_price_with_decimal( $this->cart_info['total'], false );
	}

	/**
	 * Get the subtotal amount.
	 *
	 * @since 6.5.0
	 * @return string
	 */
	public function get_subtotal() {
		return wptravelengine_the_price_with_decimal( $this->cart_info['subtotal'], false );
	}

	/**
	 * Get the paid amount.
	 *
	 * @since 6.5.0
	 * @return string
	 */
	public function get_paid_amount() {
		$cart_info      = (array) $this->cart_info ?? array();
		$payment_type   = $cart_info['payment_type'] ?? '';
		$is_due_payment = $payment_type === 'partial' || $payment_type === 'due' || $payment_type === 'remaining_payment';
		/**
		 * Check if payment is due.
		 *
		 * @since 6.5.1
		 */
		if ( $is_due_payment ) {
			$booking_id   = $this->booking->ID;
			$booking_post = Booking::make( $booking_id );
			$paid_amount  = $booking_post->get_total_paid_amount();
		} else {
			$paid_amount = $this->payment->paid_amount;
		}
		return wptravelengine_the_price_with_decimal( $paid_amount, false );
	}

	public function get_bank_details() {
		if ( $this->payment && 'direct_bank_transfer' === $this->payment->payment_gateway ) {
			$bank_details_labels = array(
				'account_name'   => __( 'Account Name', 'wp-travel-engine' ),
				'account_number' => __( 'Account Number', 'wp-travel-engine' ),
				'bank_name'      => __( 'Bank Name', 'wp-travel-engine' ),
				'sort_code'      => __( 'Sort Code', 'wp-travel-engine' ),
				'iban'           => __( 'IBAN', 'wp-travel-engine' ),
				'swift'          => __( 'BIC/Swift', 'wp-travel-engine' ),
			);

			$settings = get_option( 'wp_travel_engine_settings', array() );

			$bank_accounts     = wte_array_get( $settings, 'bank_transfer.accounts', array() );
			$bank_instructions = wte_array_get( $settings, 'bank_transfer.instruction', '' );
			ob_start();
			?>
			<table cellspacing="0" cellpadding="0" style="width: 100%;">
				<tr>
					<td colspan="2"><hr style="border: none;border-top: 1px solid #DCDFEA;"></td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Bank Details:', 'wp-travel-engine' ); ?></td>
				</tr>
				<?php if ( ! empty( $bank_instructions ) ) : ?>
				<tr>
					<td colspan="2" style="color: #566267;"><?php echo wp_kses( nl2br( $bank_instructions ), array( 'br' => array() ) ); ?></td>
				</tr>
				<tr>
					<td colspan="2" style="padding: 4px 0;"></td>
				</tr>
					<?php
				endif;
				$is_first = true;
				foreach ( $bank_accounts as $account ) :
					if ( ! $is_first ) {
						?>
						<tr><td colspan="2"><hr style="border: none;border-top: 1px solid #DCDFEA;"></td></tr>
						<?php
					}
					$is_first = false;
					foreach ( $bank_details_labels as $key => $label ) :
						?>
						<tr>
							<td style="color: #566267;"><?php echo esc_html( $label ); ?></td>
							<td style="width: 50%;text-align: right;"><strong><?php echo isset( $account[ $key ] ) ? esc_html( $account[ $key ] ) : ''; ?></strong></td>
						</tr>
						<?php
					endforeach;
				endforeach;
				?>
			</table>
			<?php
			return ob_get_clean();
		}

		return '';
	}

	public function get_check_payment_details() {
		if ( $this->payment && 'check_payments' === $this->payment->payment_gateway ) {
			ob_start();
			?>
			<table class="invoice-items">
				<tr>
					<td colspan="2">
						<h3><?php echo esc_html__( 'Check Payment Instructions:', 'wp-travel-engine' ); ?></h3>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php echo wp_kses_post( wte_array_get( get_option( 'wp_travel_engine_settings', array() ), 'check_payment.instruction', '' ) ); ?>
					</td>
				</tr>
			</table>
			<?php
			return ob_get_clean();
		}

		return '';
	}

	public function get_booking_details() {
		$order_trips     = $this->order_trips;
		$cart_info       = (array) $this->cart_info;
		$global_settings = get_option( 'wp_travel_engine_settings', array() );

		if ( is_array( $order_trips ) ) :
			ob_start();
			$count              = 1;
			$pricing_categories = get_terms(
				array(
					'taxonomy'   => 'trip-packages-categories',
					'hide_empty' => false,
					'orderby'    => 'term_id',
					'fields'     => 'id=>name',
				)
			);
			foreach ( $order_trips as $trip ) :
				$trip = (object) $trip;
				?>
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2"><b><?php echo esc_html( $trip->title ); ?></b></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Package Name', 'wp-travel-engine' ); ?></td>
						<td class="alignright"><?php echo esc_html( $trip->package_name ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Trip Date', 'wp-travel-engine' ); ?></td>
						<td class="alignright"><?php echo esc_html( $trip->has_time ? wp_date( 'Y-m-d H:i', strtotime( $trip->datetime ), new \DateTimeZone( 'utc' ) ) : wp_date( get_option( 'date-format', 'Y-m-d' ), strtotime( $trip->datetime ), new \DateTimeZone( 'utc' ) ) ); ?></td>
					</tr>
					<?php if ( isset( $trip->end_datetime ) ) : ?>
						<tr>
							<td><?php esc_html_e( 'Trip End Date', 'wp-travel-engine' ); ?></td>
							<td class="alignright"><?php echo esc_html( $trip->has_time ? wp_date( 'Y-m-d H:i', strtotime( $trip->end_datetime ), new \DateTimeZone( 'utc' ) ) : wp_date( get_option( 'date-format', 'Y-m-d' ), strtotime( $trip->end_datetime ), new \DateTimeZone( 'utc' ) ) ); ?></td>
						</tr>

					<?php endif; ?>
					<tr>
						<td><?php esc_html_e( 'Travellers', 'wp-travel-engine' ); ?></td>
						<td class="alignright"><?php echo esc_html( array_sum( $trip->pax ) ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Trip Cost', 'wp-travel-engine' ); ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td class="alignright">
							<table width="100%" cellpadding="0" cellspacing="0">
								<?php
								$sum = 0;
								foreach ( $trip->pax as $pricing_category_id => $tcount ) {
									if ( ! isset( $trip->pax_cost ) || ! is_array( $trip->pax_cost ) || ! isset( $trip->pax_cost[ $pricing_category_id ] ) || +$tcount < 1 ) {
										continue;
									}
									$pax_cost = + $trip->pax_cost[ $pricing_category_id ] / + $tcount;
									$sum     += + $trip->pax_cost[ $pricing_category_id ];

									$label = isset( $pricing_categories[ $pricing_category_id ] ) ? $pricing_categories[ $pricing_category_id ] : $pricing_category_id;
									?>
										<tr>
											<td class="alignright"><?php echo esc_html( $label ); ?></td>
											<td><?php echo (int) $tcount . ' X ' . wptravelengine_the_price_with_decimal( $pax_cost, false ) . ' = ' . wptravelengine_the_price_with_decimal( $trip->pax_cost[ $pricing_category_id ] ?? 0, false ); ?></td>
										</tr>
										<?php
								}
								?>
								<tr>
									<td width="50%"><?php esc_html_e( 'Subtotal', 'wp-travel-engine' ); ?></td>
									<td width="50%"><?php echo wptravelengine_the_price_with_decimal( + $sum, false ); ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<?php do_action( 'wptravelengine_email_template_before_extra_services', $cart_info ); ?>
					<?php if ( $trip->trip_extras && is_array( $trip->trip_extras ) ) : ?>
						<tr>
							<td colspan="2"><?php echo esc_html( $global_settings['extra_service_title'] ?? __( 'Extra Services:', 'wp-travel-engine' ) ); ?></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td class="alignright">
								<table width="100%" cellpadding="0" cellspacing="0">
									<?php
									$sum = 0;
									foreach ( $trip->trip_extras as $index => $tx ) {
										$tx_total = + $tx['qty'] * + $tx['price'];
										$sum     += $tx_total;
										?>
										<tr>
											<td><?php echo esc_html( $tx['extra_service'] ); ?></td>
											<td><?php echo (int) $tx['qty'] . ' X ' . wptravelengine_the_price_with_decimal( + $tx['price'], false ) . ' = ' . wptravelengine_the_price_with_decimal( + $tx_total, false ); ?></td>
										</tr>
										<?php
									}
									?>
									<tr>
										<td width="50%"><?php esc_html_e( 'Subtotal', 'wp-travel-engine' ); ?></td>
										<td widht="50%"><?php echo wptravelengine_the_price_with_decimal( + $sum, false ); ?></td>
									</tr>
								</table>
							</td>
						</tr>
					<?php endif; ?>
				</table>
				<?php
				++$count;
			endforeach;
			echo '<hr/>';
			?>
			<table width="100%">
				<tr>
					<td width="50%">&nbsp;</td>
					<td width="50%">
						<table width="100%">
							<tr>
								<td><?php esc_html_e( 'Subtotal', 'wp-travel-engine' ); ?></td>
								<td class="alignright"><?php echo wptravelengine_the_price_with_decimal( + $cart_info['subtotal'], false ); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e( 'Discount', 'wp-travel-engine' ); ?></td>
								<?php
								$discount_figure = 0;
								if ( ! empty( $cart_info['discounts'] ) ) {
									$discounts       = $cart_info['discounts'];
									$discount        = array_shift( $discounts );
									$discount_figure = 'percentage' === $discount['type'] ? + $cart_info['subtotal'] * ( + $discount['value'] / 100 ) : $discount['value'];
								}
								?>
								<td class="alignright">
									<?php echo wptravelengine_the_price_with_decimal( + $discount_figure, false ); ?>
								</td>
							</tr>
							<?php do_action( 'wptravelengine_email_template_before_tax_amount', $cart_info ); ?>
							<?php if ( ! empty( $cart_info['tax_amount'] ) ) { ?>
								<tr>
									<td><?php echo esc_html( wptravelengine_get_tax_label( $cart_info['tax_amount'] ) ); ?></td>
									<?php
									$tax_figure = 0;
									$tax_amount = wp_travel_engine_get_tax_detail( $cart_info );
									?>
									<td class="alignright">
										<?php echo wptravelengine_the_price_with_decimal( + $tax_amount['tax_actual'], false ); ?>
									</td>
								</tr>
							<?php } ?>
							<?php
							// Add new row before total amount calculation on email template.
							do_action( 'wptravelengine_email_template_trip_cost_rows', $cart_info );
							?>
							<tr>
								<td><?php esc_html_e( 'Total', 'wp-travel-engine' ); ?></td>
								<td class="alignright">
									<?php echo wptravelengine_the_price_with_decimal( $cart_info['total'], false ); ?>
									<?php
									$global_settings = get_option( 'wp_travel_engine_settings', array() );
									$tax_enable      = isset( $global_settings['tax_enable'] ) && 'yes' === $global_settings['tax_enable'];
									if ( $tax_enable == 'yes' && isset( $global_settings['tax_type_option'] ) && 'inclusive' === $global_settings['tax_type_option'] ) {
										$tax_percentage = $global_settings['tax_percentage'];
										printf( '<span class="wpte-inclusive-tax-label">%s</span>', sprintf( __( '(%s%% Incl. tax)', 'wp-travel-engine' ), esc_html( $tax_percentage ) ) );
									}
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php
		endif;

		return ob_get_clean();
	}

	/**
	 * Get trip booking Summary details.
	 *
	 * @since 6.5.0
	 * @since 6.7.11 Skip empty line item groups; added wptravelengine_email_manual_trigger_{type} action.
	 * @since 6.8.0 Escape trip date output; end date reads from $trip->end_datetime instead of wptravelengine_format_trip_end_datetime().
	 *
	 * @return string
	 */
	public function get_trip_booking_summary() {

		$pricing_categories = get_terms(
			array(
				'taxonomy'   => 'trip-packages-categories',
				'hide_empty' => false,
				'orderby'    => 'term_id',
				'fields'     => 'id=>name',
			)
		);

		$line_items      = array();
		$travelers_count = 0;

		$booking_id      = $this->booking->ID;
		$booking         = Booking::make( $booking_id );
		$cart_info       = $booking->get_cart_info();
		$global_settings = get_option( 'wp_travel_engine_settings', array() );

		if ( is_array( $cart_info ) ) {
			$line_items      = $cart_info['items'][0]['line_items'] ?? array();
			$travelers_count = isset( $cart_info['items'][0]['travelers'] ) ? array_sum( $cart_info['items'][0]['travelers'] ) : ( $cart_info['items'][0]['travelers_count'] ?? 0 );
		}

		$is_triggered_manually = 'wte_resend_purchase_receipt' === ( sanitize_text_field( $_REQUEST['action'] ?? '' ) );

		ob_start();
		echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
		foreach ( $this->order_trips as $trip ) :
			$trip       = (object) $trip;
			$trip_modal = new Trip( $trip->ID );
			?>
			<tr>
				<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Booking Details:', 'wp-travel-engine' ); ?></td>
			</tr>
			<tr>
				<td style="color: #566267;"><?php esc_html_e( 'Package Name:', 'wp-travel-engine' ); ?></td>
				<td style="width: 50%;text-align: right;"><strong><?php echo esc_html( $trip->package_name ); ?></strong></td>
			</tr>
			<tr>
				<td style="color: #566267;"><?php esc_html_e( 'Trip Date:', 'wp-travel-engine' ); ?></td>
				<td style="width: 50%;text-align: right;"><strong><?php echo esc_html( wptravelengine_format_trip_datetime( $trip->datetime ) ); ?></strong></td>
			</tr>
			<tr>
				<td style="color: #566267;"><?php esc_html_e( 'Trip End Date:', 'wp-travel-engine' ); ?></td>
				<td style="width: 50%;text-align: right;"><strong>
				<?php
					echo esc_html( wptravelengine_format_trip_datetime( $trip->end_datetime ) );
				?>
					</strong>
				</td>
			</tr>
			<tr>
				<td style="color: #566267;"><?php esc_html_e( 'Travellers:', 'wp-travel-engine' ); ?></td>
				<td style="width: 50%;text-align: right;"><strong><?php echo esc_html( $travelers_count ); ?></strong></td>
			</tr>
			<tr>
				<td colspan="2"><strong><?php esc_html_e( 'Traveller(s):', 'wp-travel-engine' ); ?></strong></td>
			</tr>
			<?php
			if ( ! $is_triggered_manually ) {
				$sum = 0;
				foreach ( $trip->pax as $pricing_category_id => $tcount ) :
					if ( ! isset( $trip->pax_cost ) || ! is_array( $trip->pax_cost ) || ! isset( $trip->pax_cost[ $pricing_category_id ] ) || + $tcount < 1 ) {
						continue;
					}
					$pax_cost = + $trip->pax_cost[ $pricing_category_id ] / + $tcount;
					$sum     += + $trip->pax_cost[ $pricing_category_id ];
					$label    = isset( $pricing_categories[ $pricing_category_id ] ) ? $pricing_categories[ $pricing_category_id ] : $pricing_category_id;
					?>
						<tr>
							<td style="color: #566267;"><?php echo esc_html( $label ) . ': ' . $tcount . ' x ' . wptravelengine_the_price( $pax_cost, false ); ?></td>
							<td style="width: 50%;text-align: right;"><strong><?php echo wptravelengine_the_price( + $trip->pax_cost[ $pricing_category_id ], false ); ?></strong></td>
						</tr>
					<?php
				endforeach;

				do_action( 'wptravelengine_email_template_before_extra_services', (array) $this->cart_info );

				if ( $trip->trip_extras && is_array( $trip->trip_extras ) ) :
					?>
					<tr>
						<td colspan="2"><strong><?php echo esc_html( $global_settings['extra_service_title'] ?? __( 'Extra Services:', 'wp-travel-engine' ) ); ?></strong></td>
					</tr>
					<tr>
							<?php
							$sum = 0;
							foreach ( $trip->trip_extras as $index => $tx ) {
								$tx_total = + $tx['qty'] * + $tx['price'];
								$sum     += $tx_total;
								?>
								<tr>
									<td style="color: #566267;">
									<?php
									echo esc_html( $tx['extra_service'] . ': ' );
									echo (int) $tx['qty'] . ' x ' . wptravelengine_the_price( + $tx['price'], false );
									?>
									</td>
									<td style="width: 50%;text-align: right;"><strong><?php echo wptravelengine_the_price( + $tx_total, false ); ?></strong></td>
								</tr>
								<?php
							}
							?>
					</tr>
						<?php
				endif;

				do_action( 'wptravelengine_email_template_after_extra_services', (array) $this->cart_info );

			} else {
				foreach ( $line_items as $line_item => $_items ) {
					if ( empty( $_items ) ) {
						continue;
					}

					if ( ! has_action( 'wptravelengine_email_manual_trigger_' . $line_item ) ) {
						$label = $this->get_line_item_label( $line_item );
						if ( $label ) {
							?>
							<tr>
								<td colspan="2"><strong><?php echo esc_html( $label ); ?></strong></td>
							</tr>
							<?php
						}
						foreach ( $_items as $item ) {
							?>
							<tr>
								<td style="color: #566267;"><?php echo esc_html( $item['label'] ?? '' ) . ': ' . esc_html( $item['quantity'] ) . ' x ' . wptravelengine_the_price( $item['price'], false ); ?></td>
								<td style="width: 50%;text-align: right;"><strong><?php echo wptravelengine_the_price( $item['total'], false ); ?></strong></td>
							</tr>
							<?php
						}
					}

					do_action( 'wptravelengine_email_manual_trigger_' . $line_item, $_items, (array) $cart_info );
				}
			}
		endforeach;
		echo '</table>';

		return ob_get_clean();
	}

	/**
	 * Get trip booking Payment details.
	 *
	 * @since 6.5.0
	 * @return string
	 * @updated 6.7.0
	 */
	public function get_trip_booking_payment() {
		$booking = Booking::for( $this->booking->ID );

		$this->called_from_payment_details = true;

		// Get payment content based on cart version.
		$content = $booking->get_cart_version() === '4.0'
			? $this->get_cart_v4_booking_payment_details( $booking )
			: $this->get_cart_v3_booking_payment_details( $booking );

		ob_start();
		do_action( 'wptravelengine_email_template_after_billing_details', (array) $this->cart_info );
		$content .= ob_get_clean();

		return $content;
	}

	/**
	 * Get old cart booking payment details.
	 *
	 * @param Booking $booking
	 * @return string
	 *
	 * @since 6.7.0
	 * @since 6.7.8 Updated amount calculation.
	 */
	protected function get_cart_v3_booking_payment_details( $booking ) {
		$cart_info                 = (array) $this->cart_info;
		$is_customized_reservation = get_post_meta( $this->booking->ID, '_user_edited', true );
		$deductible_items          = array();
		$fees                      = array();
		$subtotal                  = $this->cart_info['subtotal'] ?? 0;
		$total                     = $booking->get_total();
		$amount_paid               = $booking->get_total_paid_amount();
		$amount_due                = $booking->get_total_due_amount();
		$spacer_row                = "<tr>
			<td colspan='2' style='padding: 4px 0;'></td>
		</tr>";

		if ( $is_customized_reservation ) {
			$cart_info = $booking->get_cart_info();
			if ( is_array( $cart_info ) ) {
				$deductible_items = $cart_info['deductible_items'] ?? array();
				$fees             = $cart_info['fees'] ?? array();
				$subtotal         = $cart_info['subtotal'] ?? 0;
				$total            = $cart_info['total'] ?? 0;
			}
		}

		$global_settings = Options::get( 'wp_travel_engine_settings', array() );
		$tax_enable      = isset( $global_settings['tax_enable'] ) && 'yes' === $global_settings['tax_enable'];
		$tax_percentage  = $global_settings['tax_percentage'] ?? 0;
		$tax_label       = $tax_enable && isset( $global_settings['tax_type_option'] ) && 'inclusive' === $global_settings['tax_type_option'] ? sprintf( __( '(%s%% Incl. tax)', 'wp-travel-engine' ), esc_html( $tax_percentage ) ) : '';
		$total_row       = '<tr style="font-size: 16px;">
					<td colspan="2">
						<span style="display: block;padding: 8px 16px;background-color: rgba(15, 29, 35, 0.04);border-radius: 4px;margin: 0 -16px;">
							<strong style="width: 50%;display: inline-block;">' . __( 'Total', 'wp-travel-engine' ) . '</strong>
							<strong style="width: 49%;text-align: right;display: inline-block;">
								' . wptravelengine_the_price_with_decimal( $total, false ) . '<span class="wpte-inclusive-tax-label">' . $tax_label . '</span>' . '
							</strong>
						</span>
					</td>
				</tr>';
		ob_start();
		?>
		<tr>
			<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Payment Details:', 'wp-travel-engine' ); ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Subtotal', 'wp-travel-engine' ); ?></strong></td>
			<td style="text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $subtotal, false ); ?></strong></td>
		</tr>
		<?php if ( ! $is_customized_reservation ) { ?>
			<?php
			echo $spacer_row;
			$discount_figure = 0;
			if ( ! empty( $cart_info['discounts'] ) ) {
				$discounts       = $cart_info['discounts'];
				$discount        = array_shift( $discounts );
				$discount_figure = 'percentage' === $discount['type'] ? + $cart_info['subtotal'] * ( + $discount['value'] / 100 ) : $discount['value'];
				?>
				<tr style="color: #12B76A;">
					<td><?php esc_html_e( 'Discount', 'wp-travel-engine' ); ?> <?php echo 'percentage' === $discount['type'] ? esc_html( '(' . $discount['name'] . ' ' . $discount['value'] ) . '%)' : esc_html( '(' . $discount['name'] . ')' ); ?></td>
					<td style="text-align: right;"><strong>-<?php echo wptravelengine_the_price_with_decimal( + $discount_figure, false ); ?></strong></td>
				</tr>
				<?php
			}
			?>

			<?php
			echo $total_row;
			echo $spacer_row;
			// Hooks for addon.
			do_action( 'wptravelengine_email_template_before_tax_amount', $cart_info );

			if ( isset( $cart_info['tax_amount'] ) && $cart_info['tax_amount'] > 0 ) {
				?>
		<tr style="color: #F79009;">
				<?php $tax_amount = wp_travel_engine_get_tax_detail( $cart_info ); ?>
			<td><?php echo esc_html( wptravelengine_get_tax_label( $cart_info['tax_amount'] ) ); ?></td>
			<td style="text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( + $tax_amount['tax_actual'], false ); ?></strong></td>
		</tr>
				<?php
			}
			// Add new row before total amount calculation on email template.
			do_action( 'wptravelengine_email_template_trip_cost_rows', $cart_info );
		} else {
			foreach ( $deductible_items as $deductible_item ) {
				?>
					<tr style="color: #12B76A;">
						<td><strong><?php echo esc_html( $deductible_item['label'] ); ?></strong></td>
						<td style="text-align: right;"><strong>-<?php echo wptravelengine_the_price_with_decimal( $deductible_item['value'], false ); ?></strong></td>
					</tr>
				<?php
			}
			echo $total_row;
			echo $spacer_row;
			foreach ( $fees as $fee ) {
				if ( isset( $fee['value'] ) && $fee['value'] > 0 ) {
					?>
						<tr>
							<td><strong><?php echo esc_html( $fee['label'] ); ?></strong></td>
							<td style="text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $fee['value'], false ); ?></strong></td>
						</tr>
					<?php
				}
			}
		}
		?>
			<tr>
				<td colspan="2" style="padding: 4px 0;"></td>
			</tr>
			<tr>
				<td><strong><?php esc_html_e( 'Amount Paid', 'wp-travel-engine' ); ?></strong></td>
				<td style="text-align: right;font-size: 16px;"><strong><?php echo wptravelengine_the_price_with_decimal( $amount_paid, false ); ?></strong></td>
			</tr>
			<?php if ( $amount_due > 0 ) { ?>
				<tr>
					<td><strong><?php esc_html_e( 'Amount Due', 'wp-travel-engine' ); ?></strong></td>
					<td style="text-align: right;font-size: 16px;"><strong><?php echo wptravelengine_the_price_with_decimal( $amount_due, false ); ?></strong></td>
				</tr>
			<?php } ?>
		<tr>
			<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Billing Details:', 'wp-travel-engine' ); ?></td>
		</tr>
		<?php
		$billing_fields = array(
			__( 'Booking Name:', 'wp-travel-engine' )    => $this->get_billing_fullname(),
			__( 'Booking Email:', 'wp-travel-engine' )   => $this->get_billing_email(),
			__( 'Booking Address:', 'wp-travel-engine' ) => $this->get_billing_address(),
			__( 'City:', 'wp-travel-engine' )            => $this->get_billing_city(),
			__( 'Country:', 'wp-travel-engine' )         => $this->get_billing_country(),
		);
		foreach ( $billing_fields as $label => $value ) {
			if ( empty( trim( $value ) ) ) {
				continue;
			}
			?>
			<tr>
				<td style="color: #566267;"><?php echo esc_html( $label ); ?></td>
				<td style="width: 50%;text-align: right;"><strong><?php echo esc_html( $value ); ?></strong></td>
			</tr>
			<?php
		}
		?>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get new cart booking payment details.
	 *
	 * @param Booking $booking
	 * @return string
	 *
	 * @since 6.7.0
	 */
	protected function get_cart_v4_booking_payment_details( $booking ) {
		global $wte_cart;

		$p_data  = $booking->get_payments_data( false );
		$_totals = $p_data['totals'] ?? array();

		$cart_info         = new CartInfoParser( $booking->get_cart_info() );
		$payment_type      = $cart_info->__get( 'payment_type' ) ?? 'full';
		$is_manual_trigger = apply_filters( 'wptravelengine_email_template_is_manual_trigger', 'wte_resend_purchase_receipt' === ( $_REQUEST['action'] ?? '' ), $booking );

		/**
		 * @var Payment $payment_model
		 */
		$payment_model   = Payment::make( $this->payment->ID );
		$payment_gateway = $payment_model->get_payment_gateway();

		$amounts = array();
		if ( $is_manual_trigger ) {
			$amounts = array(
				'subtotal'        => $_totals['subtotal'],
				'deposit'         => $_totals['total_paid'],
				'due'             => $_totals['due_exclusive'] ?? 0,
				'tax'             => $_totals['tax']['value'] ?? 0,
				'total'           => $_totals['total_exclusive'],
				'initial_deposit' => $_totals['total_deposit'] ?? $cart_info->get_totals( 'partial_total' ),
				'remaining_total' => max( $_totals['due_exclusive'], 0 ),
				'gateway_fee'     => $_totals['gateway_fee'] ?? 0,
			);

			$payment_type = $amounts['due'] > 0 ? 'partial' : 'full';
		} else {
			$amounts = array(
				'subtotal'        => $cart_info->get_totals( 'subtotal' ),
				'deposit'         => $payment_model->get_amount(),
				'due'             => $_totals['due_exclusive'] ?? 0,
				'tax'             => $cart_info->get_totals( 'total_tax' ),
				'total'           => $cart_info->get_totals( 'total' ),
				'initial_deposit' => $cart_info->get_totals( 'partial_total' ),
				'remaining_total' => $cart_info->get_totals( 'due_total' ),
				'gateway_fee'     => $payment_model->get_gateway_fee(),
			);
		}

		$global_settings  = Options::get( 'wp_travel_engine_settings', array() );
		$tax_enable       = isset( $global_settings['tax_enable'] ) && 'yes' === $global_settings['tax_enable'];
		$is_inclusive_tax = isset( $global_settings['tax_type_option'] ) && 'inclusive' === $global_settings['tax_type_option'];
		$excl_label       = $wte_cart->get_exclusion_label( $cart_info->get_fees() );

		ob_start();
		?>
		<?php if ( ! $this->called_from_booking_details ) { ?>
			<tr>
				<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Payment Details:', 'wp-travel-engine' ); ?></td>
			</tr>
			<?php
		}
		$deductible_items = $cart_info->get_deductible_items() ?? array();
		if ( $deductible_items ) {
			foreach ( $deductible_items as $line_item ) {
				printf(
					'<tr style="color: #12B76A;"><td>%1$s</td><td style="text-align: right;"><strong>%2$s</strong></td></tr>',
					esc_html( $line_item['label'] ?? '' ),
					wptravelengine_the_price_with_decimal( $line_item['value'] && $line_item['value'] > 0 ? $line_item['value'] : $cart_info->get_totals( 'total_' . $line_item['name'] ) ?? 0, false )
				);
			}
		}

			// Add new row before total amount calculation on email template.
			do_action( 'wptravelengine_email_template_trip_cost_rows', $cart_info );
		?>
		<tr>
			<td colspan="2">
				<span style="display: flex;padding: 8px 16px;background-color: rgba(15, 29, 35, 0.04);border-radius: 4px;margin: 0 -16px; font-size: 0px;">
					<strong style="width: 50%;display: inline-block; font-size: 16px;"><?php esc_html_e( 'Total', 'wp-travel-engine' ); ?></strong>
					<strong style="width: 50%;text-align: right;display: inline-block; font-size: 16px;">
					<?php
					echo wptravelengine_the_price_with_decimal( $amounts['total'], false );
					if ( $tax_enable == 'yes' && isset( $global_settings['tax_type_option'] ) && 'inclusive' === $global_settings['tax_type_option'] ) {
						$tax_percentage = $global_settings['tax_percentage'];
						printf( '<span class="wpte-inclusive-tax-label">%s</span>', sprintf( __( '(%s%% Incl. tax)', 'wp-travel-engine' ), esc_html( $tax_percentage ) ) );
					}
					?>
					</strong>
				</span>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding: 4px 0;"></td>
		</tr>

		<?php
		$initial_condition = $payment_type !== 'full' && $amounts['initial_deposit'] > 0;
		if ( $initial_condition && apply_filters( 'wptravelengine_email_template_initial_deposit_row', true, $cart_info ) ) {
			?>
			<tr>
				<td><?php esc_html_e( 'Initial Deposit', 'wp-travel-engine' ); ?></td>
				<td style="text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $amounts['initial_deposit'], false ); ?></strong></td>
			</tr>
			<?php
		}

		if ( $payment_type === 'due' && $amounts['remaining_total'] > 0 ) {
			?>
			<tr>
				<td><?php esc_html_e( 'Remaining Amount', 'wp-travel-engine' ); ?></td>
				<td style="text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $amounts['remaining_total'], false ); ?></strong></td>
			</tr>
			<?php
		}

		if ( $is_manual_trigger ) :

			foreach ( $_totals['tax_inclusive'] ?? array() as $tax_inclusive ) {
				?>
				<tr>
					<td><?php echo esc_html( $tax_inclusive['label'] ); ?></td>
					<td style="text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $tax_inclusive['value'], false ); ?></strong></td>
				</tr>
				<?php
			}

			if ( isset( $_totals['tax'] ) ) {
				?>
				<tr style="color: #F79009;">
					<td><?php echo esc_html( wptravelengine_get_tax_label() ); ?></td>
					<td style="text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $_totals['tax']['value'], false ); ?></strong></td>
				</tr>
				<?php
			}

			foreach ( $_totals['tax_exclusive'] ?? array() as $tax_exclusive ) {
				?>
				<tr>
					<td><?php echo esc_html( $tax_exclusive['label'] ); ?></td>
					<td style="text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $tax_exclusive['value'], false ); ?></strong></td>
				</tr>
				<?php
			}

		else :
			// Hooks for addon.
			do_action( 'wptravelengine_email_template_before_tax_amount', $this->cart_info );

			if ( $amounts['tax'] > 0 && ! $is_inclusive_tax ) {
				?>
					<tr style="color: #F79009;">
						<td><?php echo esc_html( wptravelengine_get_tax_label() ); ?></td>
						<td style="text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $amounts['tax'], false ); ?></strong></td>
					</tr>
					<?php
			}

			do_action( 'wptravelengine_email_template_after_tax_amount', $this->cart_info );
		endif;
		ob_start();
		?>
		<?php if ( 'booking_only' === $payment_gateway || 'check_payments' === $payment_gateway || 'direct_bank_transfer' === $payment_gateway ) { ?>
		<tr>
			<td colspan="2">
				<span style="display: flex;padding: 8px 16px;background-color: #147dfe1a;border-radius: 4px;margin: 0 -16px;">
					<strong style="width: 50%;display: inline-block;"><?php esc_html_e( 'Payable Amount', 'wp-travel-engine' ); ?></strong>
					<strong style="width: 50%;text-align: right;display: inline-block;">
					<?php
					echo wptravelengine_the_price_with_decimal( $payment_model->get_payable_amount(), false );
					?>
					</strong>
				</span>
			</td>
		</tr>
			<?php
		} else {
			if ( $amounts['gateway_fee'] > 0 ) {
				?>
				<tr>
					<td><?php esc_html_e( 'Gateway Fee', 'wp-travel-engine' ); ?></td>
					<td style="text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $amounts['gateway_fee'], false ); ?></strong></td>
				</tr>
				<?php
			}
			?>
			<tr>
			<td colspan="2">
				<span style="display: flex;padding: 8px 16px;background-color: #147dfe1a;border-radius: 4px;margin: 0 -16px;">
					<strong style="width: 50%;display: inline-block;"><?php esc_html_e( 'Amount Paid', 'wp-travel-engine' ); ?></strong>
					<strong style="width: 50%;text-align: right;display: inline-block;">
					<?php
					echo wptravelengine_the_price_with_decimal( $amounts['deposit'], false );
					?>
					</strong>
				</span>
			</td>
		</tr>
			<?php if ( $amounts['due'] > 0 ) { ?>
			<tr>
				<td><strong><?php esc_html_e( 'Amount Due', 'wp-travel-engine' ); ?> </strong> <?php echo ( ! empty( $excl_label ) ? '(excl. ' . $excl_label . ')' : '' ); ?></td>
				<td style="text-align: right;font-size: 16px;"><strong><?php echo wptravelengine_the_price_with_decimal( $amounts['due'], false ); ?></strong></td>
			</tr>
				<?php
			}
		}
		/**
		 * @since 6.7.1
		 * @description This is the html for the payable amount on email template that can be overwritten by the filters.
		 * @param string $totals_html The html for the payable amount.
		 * @param object $this The object of the class.
		 * @param object $booking The object of the booking.
		 * @param object $payment_model The object of the payment model.
		 * @return string
		 */
		$totals_html = ob_get_clean();
		$htmls       = apply_filters( 'wptravelengine_email_template_payable_amount_html', $totals_html, $this, $booking, $payment_model );
		echo $htmls;
		?>
		<tr>
			<td colspan="2">
				<?php
				echo $this->get_billing_details();
				?>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get trip booking details.
	 *
	 * @since 6.5.0
	 *
	 * @return string
	 */
	public function get_trip_booking_details() {
		ob_start();
		if ( is_array( $this->order_trips ) ) :
			// Trip Booking Summary.
			echo $this->get_trip_booking_summary();
			?>
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="2"><hr style="border: none;border-top: 1px solid #DCDFEA;"></td>
				</tr>
				<?php
				// Set called from booking details to true.
				$this->called_from_booking_details = true;
				// Trip Booking Payment Details.
				echo $this->get_trip_booking_payment();

				// Bank Details.
				if ( $this->payment && 'direct_bank_transfer' === $this->payment->payment_gateway ) :
					$bank_details_labels = array(
						'account_name'   => __( 'Account Name', 'wp-travel-engine' ),
						'account_number' => __( 'Account Number', 'wp-travel-engine' ),
						'bank_name'      => __( 'Bank Name', 'wp-travel-engine' ),
						'sort_code'      => __( 'Sort Code', 'wp-travel-engine' ),
						'iban'           => __( 'IBAN', 'wp-travel-engine' ),
						'swift'          => __( 'BIC/Swift', 'wp-travel-engine' ),
					);

					$settings = get_option( 'wp_travel_engine_settings', array() );

					$bank_accounts     = wte_array_get( $settings, 'bank_transfer.accounts', array() );
					$bank_instructions = wte_array_get( $settings, 'bank_transfer.instruction', '' );
					?>
					<tr>
						<td colspan="2"><hr style="border: none;border-top: 1px solid #DCDFEA;"></td>
					</tr>
					<tr>
						<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Bank Details:', 'wp-travel-engine' ); ?></td>
					</tr>
					<?php if ( ! empty( $bank_instructions ) ) : ?>
					<tr>
						<td colspan="2" style="color: #566267;"><?php echo wp_kses( nl2br( $bank_instructions ), array( 'br' => array() ) ); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding: 4px 0;"></td>
					</tr>
						<?php
					endif;
					$is_first = true;
					foreach ( $bank_accounts as $account ) :
						if ( ! $is_first ) {
							?>
							<tr><td colspan="2"><hr style="border: none;border-top: 1px solid #DCDFEA;"></td></tr>
							<?php
						}
						$is_first = false;
						foreach ( $bank_details_labels as $key => $label ) :
							?>
							<tr>
								<td style="color: #566267;"><?php echo esc_html( $label ); ?></td>
								<td style="width: 50%;text-align: right;"><strong><?php echo isset( $account[ $key ] ) ? esc_html( $account[ $key ] ) : ''; ?></strong></td>
							</tr>
							<?php
						endforeach;
					endforeach;
				endif;

				// Check Payment Details.
				if ( $this->payment && 'check_payments' === $this->payment->payment_gateway ) :
					?>
					<tr>
						<td colspan="2"><hr style="border: none;border-top: 1px solid #DCDFEA;"></td>
					</tr>
					<tr>
						<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Check Payment Instructions:', 'wp-travel-engine' ); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="color: #566267;"><?php echo esc_html( wptravelengine_settings()->get( 'check_payment.instruction', '' ) ); ?></td>
					</tr>
					<?php
				endif;

				// Additional Notes.
				if ( ! empty( $this->additional_notes ) ) :
					?>
					<tr>
						<td colspan="2"><hr style="border: none;border-top: 1px solid #DCDFEA;"></td>
					</tr>
					<tr>
						<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Additional Notes:', 'wp-travel-engine' ); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="color: #566267;"><?php echo esc_html( $this->additional_notes ); ?></td>
					</tr>
					<?php
				endif;
				do_action( 'wptravelengine_email_template_after_additional_notes', $this );
			endif;
		echo '</table>';
		return ob_get_clean();
	}

	public function discount_amount() {
		$cart_info = (object) $this->cart_info;

		if ( isset( $cart_info->discounts ) && is_array( $cart_info->discounts ) ) {
			$discounts = $cart_info->discounts;
			$discount  = array_shift( $discounts );
			if ( ! is_array( $discount ) ) {
				return 0;
			}

			extract( $discount );
			if ( 'percentage' === $type ) {
				return + $cart_info->subtotal * ( + $value / 100 );
			}

			return $value;
		}
	}

	/**
	 * Get traveller email template.
	 */
	public function get_traveller_template( $type, $pno, $personal_options ) {

		ob_start();
		$args = array(
			'data'    => $personal_options,
			'numbers' => $pno,
		);

		// Email Content.
		wte_get_template( "emails/{$type}.php", $args );

		$template = ob_get_clean();

		return $template;
	}

	/**
	 * Gets Booking Payment method by Payment ID.
	 *
	 * @param int $payment_id
	 *
	 * @return string
	 */
	public function get_payment_method( $payment_id ) {
		$payment_method = get_post_meta( $payment_id, 'payment_gateway', true );

		return empty( $payment_method ) ? __( 'N/A', 'wp-travel-engine' ) : $payment_method;
	}

	/**
	 * Get trip code for the booked trip.
	 *
	 * @since 6.6.10
	 * @return string
	 */
	public function get_trip_code() {
		$trip = wptravelengine_get_trip( $this->trip->ID );

		return is_null( $trip ) ? '' : $trip->get_trip_code();
	}

	/**
	 * Build and register email template tags.
	 */
	public function get_email_tags() {

		$trip = $this->trip;

		$booking_id        = $this->booking->ID;
		$_booking          = new Booking( $booking_id );
		$edit_booking_link = admin_url() . 'post.php?post=' . $booking_id . '&action=edit';

		$traveller_data = $_booking->get_travelers();

		$pno = ( isset( $trip->pax ) ) ? array_sum( $trip->pax ) : 0;
		if ( ! empty( $traveller_data ) ) :
			$traveller_email_template_content = $this->get_traveller_template( 'traveller-data', $pno, $traveller_data );
		else :
			$traveller_email_template_content = '';
		endif;

		$order_trip = reset( $this->order_trips );

		$payments_data = $_booking->get_payments_data();

		$traveller_details = $this->get_traveller_details();

		$meta_tags = array(
			'{trip_url}'                  => $this->get_trip_url(),
			'{name}'                      => $this->get_billing_first_name(),
			'{fullname}'                  => $this->get_billing_fullname(),
			'{user_email}'                => $this->get_billing_email(),
			'{billing_address}'           => $this->get_billing_address(),
			'{city}'                      => $this->get_billing_city(),
			'{country}'                   => $this->get_billing_country(),
			'{tdate}'                     => wptravelengine_format_trip_datetime( $trip->datetime ),
			'{traveler}'                  => isset( $trip->pax ) ? array_sum( $trip->pax ) : 0,
			// '{child-traveler}'            => $trip->pax['child'],
			'{tprice}'                    => wptravelengine_the_price_with_decimal( isset( $trip->cost ) ? $trip->cost : 0, false ),
			'{price}'                     => wptravelengine_the_price_with_decimal( $_booking->get_total_paid_amount() == 0 ? 0 : $_booking->get_total_paid_amount(), false ),
			'{total_cost}'                => wptravelengine_the_price_with_decimal( $this->cart_info->total ?? $this->cart_info['total'] ?? 0, false ),
			'{due}'                       => wptravelengine_the_price_with_decimal( max( 0, $_booking->get_total_due_amount() ), false ),
			'{booking_url}'               => $edit_booking_link,
			'{date}'                      => $this->get_current_date(),
			'{booking_id}'                => sprintf( __( 'Booking #%1$s', 'wp-travel-engine' ), $this->booking->ID ),
			'{bank_details}'              => $this->get_bank_details(),
			'{check_payment_instruction}' => $this->get_check_payment_details(),
			'{booking_details}'           => $this->get_booking_details(),
			'{booking_trips_count}'       => isset( $this->booking->order_trips ) ? count( $this->booking->order_trips ) : 1,
			'{payment_id}'                => $this->payment->ID,
			'{subtotal}'                  => $this->get_subtotal(),
			'{total}'                     => $this->get_total_amount(),
			'{paid_amount}'               => $this->get_paid_amount(),
			'{traveler_data}'             => $traveller_email_template_content,
			'{payment_method}'            => $this->get_payment_method( $this->payment->ID ),
			'{billing_details}'           => $this->get_billing_details(),
			'{additional_note}'           => $this->get_additional_note(),
			'{traveller_details}'         => $traveller_details, // @deprecated 6.7.9 This is deprecated tags that need to remove after addon fixed typos.
			'{traveler_details}'          => $traveller_details,
			'{emergency_details}'         => $this->get_emergency_details(),
			'{trip_booking_summary}'      => $this->get_trip_booking_summary(),
			'{trip_payment_details}'      => $this->get_trip_booking_payment(),
			'{trip_booking_details}'      => $this->get_trip_booking_details(),
			'{booked_trip_name}'          => html_entity_decode( $this->trip->title ?: 'Untitled Trip', ENT_QUOTES, 'UTF-8' ),
			'{customer_first_name}'       => $this->get_billing_first_name(),
			'{customer_last_name}'        => $this->get_billing_last_name(),
			'{customer_full_name}'        => $this->get_billing_fullname(),
			'{customer_email}'            => $this->get_billing_email(),
			'{trip_booked_date}'          => $this->get_current_date(),
			'{trip_start_date}'           => wptravelengine_format_trip_datetime( $trip->datetime ),
			'{trip_end_date}'             => wptravelengine_format_trip_datetime( $this->cart_info_parser->get_item()->get_end_date() ),
			'{no_of_travellers}'          => $this->get_no_of_travellers(), // @deprecated 6.8.0 This is a deprecated tag; remove after addons (automator, waitlist) are updated.
			'{no_of_travelers}'           => $this->get_no_of_travellers(),
			'{trip_total_price}'          => $this->get_total_amount(),
			'{trip_paid_amount}'          => wptravelengine_the_price_with_decimal( $_booking->get_total_paid_amount(), false ),
			'{trip_due_amount}'           => wptravelengine_the_price_with_decimal( max( 0, $_booking->get_total_due_amount() ), false ),
			'{payment_link}'              => $_booking->get_due_payment_link(),
			'{trip_code}'                 => $this->get_trip_code(),
			'{trip_extra_fee}'            => wptravelengine_the_price_with_decimal( $this->get_trip_extra_fee( $_booking ), false ),
			'{total_gateway_fee}'         => wptravelengine_the_price_with_decimal( $payments_data['totals']['gateway_fee'] ?? 0, false ),
		);

		$this->add_billing_details_mail_tags( $meta_tags, $_booking );
		$this->add_discount_mail_tags( $meta_tags, $_booking );

		parent::set_tags(
			apply_filters(
				'wte_booking_mail_tags',
				$meta_tags,
				$this->payment->ID,
				$this->booking->ID
			)
		);

		return $this->tags;
	}

	/**
	 * Add billing details mail tags to email templates.
	 *
	 * Enriches email templates with booking data including billing details
	 * and discount information.
	 *
	 * @TODO: following mail tags are for form editor move following block of code to FormEditor plugin.
	 *
	 * @param array   $mail_tags Existing mail tags.
	 * @param Booking $booking Booking object.
	 * @return void
	 * @since 6.7.8
	 */
	private function add_billing_details_mail_tags( &$mail_tags, Booking $booking ) {
		$additional_fields = wte_array_get( $booking->get_billing_info(), null, array() );

		foreach ( $additional_fields as $field_name => $field_value ) {
			if ( is_array( $field_value ) ) {
				$field_value = implode( ',', $field_value );
			}
			$mail_tags[ '{' . $field_name . '}' ] = $field_value;
		}
	}

	/**
	 * Add discount mail tags to email templates.
	 *
	 * @param array   $mail_tags Existing mail tags.
	 * @param Booking $booking Booking object.
	 * @return void
	 * @since 6.7.8
	 */
	private function add_discount_mail_tags( &$mail_tags, Booking $booking ) {
		$mail_tags['{discount_name}']   = '';
		$mail_tags['{discount_amount}'] = '';
		$mail_tags['{discount_sign}']   = '';
		$mail_tags['{discount_value}']  = '';

		if ( isset( $this->cart_info['discounts'] ) ) {
			$discounts = $this->cart_info['discounts'];
			if ( ! is_array( $discounts ) || empty( $discounts ) ) {
				return;
			}
			$discount                       = (object) array_shift( $discounts );
			$discount_amount                = wptravelengine_the_price( $this->cart_info['totals']['total_coupon'] ?? 0, false );
			$mail_tags['{discount_name}']   = $discount->name;
			$mail_tags['{discount_amount}'] = $discount_amount;
			$mail_tags['{discount_sign}']   = 'percentage' === $discount->type ? '%' : $this->currency;
			$mail_tags['{discount_value}']  = 'percentage' === $discount->type ? $discount->value : $discount_amount;
		}
	}

	/**
	 * Get the trip extra fee.
	 * This function consists the extra fee..
	 *
	 * @param Booking $booking
	 * @return mixed
	 * @since 6.7.6
	 */
	public function get_trip_extra_fee( Booking $booking ) {
		$payment_data      = $booking->get_payments_data( false );
		$total_paid_amount = $booking->get_total_paid_amount();
		if ( $total_paid_amount === 0.0 ) {
			return isset( $payment_data['totals']['extra_charges'] ) ? $payment_data['totals']['extra_charges'] : 0;
		}
		// Addition of extra fee for the trip addition of ( Tax, Booking Fee, Payment Gateway Fee ).
		$payment_calculator = PaymentCalculator::for( $booking->get_currency() );
		$payable            = $payment_calculator->subtract( $payment_data['totals']['payable'] ?? 0, $total_paid_amount );
		// Make sure payable is positive number because payment transaction fee can be added to paid amount directly which can be negative after sub.
		$payable   = (string) abs( floatval( $payable ) );
		$extra_fee = $payment_calculator->add( $payable, $payment_data['totals']['extra_charges'] ?? 0 );
		return $extra_fee;
	}

	/**
	 * Get the number of travellers.
	 *
	 * @return int
	 * @since 6.7.6
	 */
	public function get_no_of_travellers(): int {
		if ( ! empty( $this->trip->pax ) ) {
			return array_sum( $this->trip->pax );
		} elseif ( ! empty( $this->cart_info['items'][0]['travelers_count'] ) ) {
			return (int) $this->cart_info['items'][0]['travelers_count'];
		}
		return 0;
	}

	/**
	 * Set default tags.
	 *
	 * @param array $tags The tags.
	 *
	 * @return $this
	 * @since 6.5.0
	 */
	public function set_tags( array $tags = array() ) {
		$this->get_email_tags();
		parent::set_tags( $tags );
		return $this;
	}

	/**
	 * Get additional note.
	 *
	 * @return string
	 */
	public function get_additional_note() {
		if ( empty( $this->additional_notes ) ) {
			return '';
		}
		ob_start();
		?>
		<table width="100%">
			<tr>
				<td class="title-holder" style="margin: 0;" valign="top">
					<h3 class="alignleft"><?php echo esc_html__( 'Additional Note', 'wp-travel-engine' ); ?></h3>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html( $this->additional_notes ); ?></td>
			</tr>
		</table>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get the Billing details.
	 *
	 * @return string
	 * @since 6.7.12 Updated billing details with other updated form fields.
	 */
	public function get_billing_details() {
		if ( empty( $this->billing_details ) ) {
			return '';
		}
		$billing_form_fields = new BillingFormFields();
		$fields              = $billing_form_fields->with_values( $this->billing_details );
		ob_start();
		?>
		<table width="100%">
			<tr>
				<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Billing Details:', 'wp-travel-engine' ); ?></td>
			</tr>
			<?php
			foreach ( $fields as $field ) :
				if ( empty( $field['value'] ) ) {
					continue;
				}
				$value          = $field['value'] ?? '';
				$countries_list = Countries::list();
				if ( $field['type'] == 'country_dropdown' ) {
					$value = $countries_list[ $value ] ?? '';
				}
				?>
				<tr>
					<td style="color: #566267;"><?php echo esc_html( ucfirst( $field['field_label'] ) ); ?></td>
					<td style="width: 50%;text-align: right;">
						<?php
						if ( filter_var( $value, FILTER_VALIDATE_URL ) ) :
							?>
							<a href="<?php echo esc_url( $value ); ?>"
								target="_blank"><?php echo esc_html( basename( $value ) ); ?></a>
						<?php else : ?>
							<strong><?php echo is_array( $value ) ? esc_html( implode( ', ', $value ) ) : esc_html( $value ?? '' ); ?></strong>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get the Emergency details.
	 *
	 * @return string
	 * @since 6.7.12 Updated EmergencyFormFields Dynamic form fields get to display dynamic fields.
	 */
	public function get_emergency_details() {
		if ( empty( $this->emergency_details ) ) {
			return '';
		}
		$emergency_form_fields = new EmergencyFormFields();
		$fields                = $emergency_form_fields->with_values( $this->emergency_details, new Booking( $this->booking->ID ) );
		ob_start();
		?>
		<table width="100%">
			<tr>
				<td class="title-holder" style="margin: 0;" valign="top">
					<h3 class="alignleft"><?php echo esc_html__( 'Emergency Details', 'wp-travel-engine' ); ?></h3>
				</td>
			</tr>
			<?php
			foreach ( $fields as $field ) :
				if ( empty( $field['value'] ) ) {
					continue;
				}
				$value          = $field['value'];
				$countries_list = Countries::list();
				if ( $field['type'] == 'country_dropdown' ) {
					$value = $countries_list[ $value ] ?? '';
				}
				?>
				<tr>
					<td><?php echo esc_html( ucfirst( $field['field_label'] ) ); ?></td>
					<td>
						<?php
						if ( filter_var( $value, FILTER_VALIDATE_URL ) ) :
							?>
							<a href="<?php echo esc_url( $value ); ?>"
								target="_blank"><?php echo esc_html( basename( $value ) ); ?></a>
						<?php else : ?>
							<strong><?php echo is_array( $value ) ? esc_html( implode( ', ', $value ) ) : esc_html( $value ?? '' ); ?></strong>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get the Traveller details.
	 *
	 * @return string
	 */
	public function get_traveller_details() {
		if ( empty( $this->traveller_details ) ) {
			return '';
		}
		ob_start();
		?>
		<table width="100%">
			<tr>
				<td class="title-holder" style="margin: 0;" valign="top">
					<h3 class="alignleft"><?php echo esc_html__( 'Traveller Details', 'wp-travel-engine' ); ?></h3>
				</td>
			</tr>
			<?php
			$traveller_details     = array();
			$traveller_form_fields = new TravellerFormFields();
			foreach ( $this->traveller_details as $details ) {
				$traveller_details[] = $traveller_form_fields->with_values( $details, new Booking( $this->booking->ID ) );
			}
			foreach ( $traveller_details as $index => $traveller_detail ) :
				$traveller_label = sprintf( __( 'Traveller %1$d%2$s', 'wp-travel-engine' ), $index + 1, $index === 0 ? __( ' (Lead Traveller)', 'wp-travel-engine' ) : '' );
				?>
				<tr>
					<td>
						<h3><?php echo esc_html( $traveller_label ); ?></h3>
					</td>
				</tr>
				<?php
				foreach ( $traveller_detail as $field ) :
					if ( empty( $field['value'] ) ) {
						continue;
					}
					$value          = isset( $field['value'] ) ? $field['value'] : '';
					$countries_list = Countries::list();
					if ( $field['type'] == 'country_dropdown' ) {
						$value = $countries_list[ $value ] ?? '';
					}
					?>
						<tr>
							<td><?php echo esc_html( ucfirst( $field['field_label'] ) ); ?></td>
							<td>
							<?php
							if ( filter_var( $value, FILTER_VALIDATE_URL ) ) :
								?>
									<a href="<?php echo esc_url( $value ); ?>" target="_blank"><?php echo esc_html( basename( $value ) ); ?></a>
								<?php else : ?>
									<strong><?php echo is_array( $value ) ? esc_html( implode( ', ', $value ) ) : esc_html( $value ?? '' ); ?></strong>
								<?php endif; ?>
							</td>
						</tr>
					<?php
					endforeach;
			endforeach;
			?>
					</table>
		<?php
		return ob_get_clean();
	}

	/**
	 * This function returns label for line items of cart.
	 *
	 * @since 6.7.9
	 * @since 6.7.11 Changed visibility from public to protected.
	 */
	protected function get_line_item_label( $line_item ) {

		switch ( $line_item ) :
			case 'pricing_category':
				$label = '';
				break;
			case 'extra_service':
				$label = __( 'Extra Service(s):', 'wp-travel-engine' );
				break;
			case 'accommodation':
				$label = __( 'Accommodation(s):', 'wp-travel-engine' );
				break;
			case 'pickup_point':
				$label = __( 'Pickup Points:', 'wp-travel-engine' );
				break;
			case 'travel_insurance':
				$label = __( 'Travel Insurance:', 'wp-travel-engine' );
				break;
			default:
				$label = ucwords( str_replace( '_', ' ', $line_item ) ) . ':';
				break;
		endswitch;
		return apply_filters( 'wptravelengine_mail_line_item_label', $label, $line_item );
	}
}
