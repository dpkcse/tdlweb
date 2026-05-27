<?php
/**
 * Dummy Tags for Email Template Preview.
 *
 * @since 6.7.9
 */

namespace WPTravelEngine\Booking\Email\Dummy;

use WPTravelEngine\Abstracts\EmailTags;

/**
 * DummyTags class.
 *
 * @since 6.7.9
 */
class DummyTags extends EmailTags {

	/** @var array */
	private array $billing;

	/** @var object */
	private object $trip;

	/** @var array */
	private array $cart;

	/** @var \stdClass */
	private \stdClass $booking;

	/** @var \stdClass */
	private \stdClass $payment;

	/** @var string */
	private string $currency;

	/** @var float */
	private float $total_paid;

	/** @var float */
	private float $total_due;

	public function __construct() {
		parent::__construct(); // inherits {sitename}, {site_admin_email}, {ip_address}
		$this->billing    = DummyDataProvider::get_billing_info();
		$this->trip       = (object) DummyDataProvider::get_order_trip();
		$this->cart       = DummyDataProvider::get_cart_info();
		$this->booking    = DummyDataProvider::get_booking();
		$this->payment    = DummyDataProvider::get_payment();
		$this->currency   = $this->cart['currency'];
		$this->total_paid = (float) ( $this->cart['totals']['partial_total'] ?? 0 );
		$this->total_due  = (float) ( $this->cart['totals']['due_total'] ?? 0 );
	}

	/**
	 * Build the callback registry.
	 *
	 * @return array<string, callable>
	 */
	protected function build_callbacks(): array {
		$dummy_tags = array(
			// Customer / billing tags.
			'{billing_address}'           => array( $this, 'get_billing_address' ),
			'{city}'                      => array( $this, 'get_city' ),
			'{country}'                   => array( $this, 'get_country' ),
			'{customer_first_name}'       => array( $this, 'get_name' ),
			'{customer_last_name}'        => array( $this, 'get_last_name' ),
			'{customer_full_name}'        => array( $this, 'get_fullname' ),
			'{customer_email}'            => array( $this, 'get_user_email' ),

			// Trip tags.
			'{trip_url}'                  => array( $this, 'get_trip_url' ),
			'{booked_trip_name}'          => array( $this, 'get_booked_trip_name' ),
			'{trip_code}'                 => array( $this, 'get_trip_code' ),
			'{trip_start_date}'           => array( $this, 'get_trip_start_date' ),
			'{trip_end_date}'             => array( $this, 'get_trip_end_date' ),
			'{no_of_travellers}'          => array( $this, 'get_traveler_count' ),
			'{no_of_travelers}'           => array( $this, 'get_traveler_count' ),
			'{tprice}'                    => array( $this, 'get_tprice' ),

			// Booking tags.
			'{booking_id}'                => array( $this, 'get_booking_id' ),
			'{booking_url}'               => array( $this, 'get_booking_url' ),
			'{booking_trips_count}'       => array( $this, 'get_booking_trips_count' ),
			'{trip_booked_date}'          => array( $this, 'get_trip_booked_date' ),

			// Payment / price tags.
			'{payment_id}'                => array( $this, 'get_payment_id' ),
			'{payment_method}'            => array( $this, 'get_payment_method' ),
			'{payment_link}'              => array( $this, 'get_payment_link' ),
			'{subtotal}'                  => array( $this, 'get_subtotal' ),
			'{total}'                     => array( $this, 'get_total' ),
			'{paid_amount}'               => array( $this, 'get_paid_amount' ),
			'{trip_total_price}'          => array( $this, 'get_total' ),
			'{trip_paid_amount}'          => array( $this, 'get_price' ),
			'{trip_due_amount}'           => array( $this, 'get_due' ),
			'{trip_extra_fee}'            => array( $this, 'get_trip_extra_fee' ),
			'{total_gateway_fee}'         => array( $this, 'get_total_gateway_fee' ),

			// Discount tags.
			'{discount_name}'             => array( $this, 'get_discount_name' ),
			'{discount_amount}'           => array( $this, 'get_discount_amount' ),
			'{discount_sign}'             => array( $this, 'get_discount_sign' ),
			'{discount_value}'            => array( $this, 'get_discount_value' ),

			// Payment gateway detail tags — empty, dummy uses PayPal.
			'{bank_details}'              => array( $this, 'get_bank_details' ),
			'{check_payment_instruction}' => array( $this, 'get_check_payment_instruction' ),

			// Complex HTML block tags.
			'{trip_booking_summary}'      => array( $this, 'get_trip_booking_summary' ),
			'{trip_payment_details}'      => array( $this, 'get_trip_payment_details' ),
			'{trip_booking_details}'      => array( $this, 'get_trip_booking_details' ),
			'{traveller_details}'         => array( $this, 'get_traveler_details' ),
			'{traveler_details}'          => array( $this, 'get_traveler_details' ),
			'{emergency_details}'         => array( $this, 'get_emergency_details' ),
			'{billing_details}'           => array( $this, 'get_billing_details' ),
			'{additional_note}'           => array( $this, 'get_additional_note' ),

			// Depreacted Tags
			'{booking_details}'           => array( $this, 'get_booking_details' ),
			'{name}'                      => array( $this, 'get_name' ),
			'{fullname}'                  => array( $this, 'get_fullname' ),
			'{user_email}'                => array( $this, 'get_user_email' ),
			'{tdate}'                     => array( $this, 'get_trip_start_date' ),
			'{date}'                      => array( $this, 'get_date' ),
			'{traveler}'                  => array( $this, 'get_traveler_count' ),
			'{price}'                     => array( $this, 'get_price' ),
			'{due}'                       => array( $this, 'get_due' ),
			'{total_cost}'                => array( $this, 'get_total_cost' ),
			'{traveler_data}'             => array( $this, 'get_empty' ),
		);

		return apply_filters( 'wptravelengine_dummy_email_tags_callback', $dummy_tags, $this );
	}

	public function get_empty(): string {
		return '';
	}

	public function get_name(): string {
		return $this->billing['fname'];
	}

	public function get_last_name(): string {
		return $this->billing['lname'];
	}

	public function get_fullname(): string {
		return $this->billing['fname'] . ' ' . $this->billing['lname'];
	}

	public function get_user_email(): string {
		return $this->billing['email'];
	}

	public function get_billing_address(): string {
		return $this->billing['address'];
	}

	public function get_city(): string {
		return $this->billing['city'];
	}

	public function get_country(): string {
		return 'Nepal';
	}

	public function get_trip_url(): string {
		return esc_url( home_url( '/trips/everest-base-camp-trek/' ) );
	}

	public function get_booked_trip_name(): string {
		return $this->trip->title;
	}

	public function get_trip_code(): string {
		return 'WTE-2026';
	}

	public function get_trip_start_date(): string {
		return wptravelengine_format_trip_datetime( $this->trip->datetime );
	}

	public function get_trip_end_date(): string {
		return wptravelengine_format_trip_datetime( $this->trip->end_datetime );
	}

	public function get_traveler_count(): int {
		return array_sum( $this->trip->pax );
	}

	public function get_tprice(): string {
		return wte_get_formated_price( $this->trip->cost, $this->currency );
	}

	public function get_booking_id(): string {
		return sprintf( __( 'Booking #%1$s', 'wp-travel-engine' ), $this->booking->ID );
	}

	public function get_booking_url(): string {
		return admin_url( 'post.php?post=' . $this->booking->ID . '&action=edit' );
	}

	public function get_booking_trips_count(): int {
		return 1;
	}

	public function get_date(): string {
		return wp_date( get_option( 'date_format', 'Y-m-d' ) . ' ' . get_option( 'time_format', 'H:i:s' ) );
	}

	public function get_trip_booked_date(): string {
		return wp_date( get_option( 'date_format', 'Y-m-d' ) );
	}

	public function get_payment_id(): int {
		return $this->payment->ID;
	}

	public function get_payment_method(): string {
		return 'Stripe Payment';
	}

	public function get_payment_link(): string {
		return esc_url( home_url( '/?dummy-due-payment-link' ) );
	}

	public function get_price(): string {
		return wte_get_formated_price( $this->total_paid, $this->currency );
	}

	public function get_total_cost(): string {
		return wptravelengine_the_price_with_decimal( $this->cart['total'], false );
	}

	public function get_subtotal(): string {
		return wptravelengine_the_price_with_decimal( $this->cart['subtotal'], false );
	}

	public function get_total(): string {
		return wptravelengine_the_price_with_decimal( $this->cart['total'], false );
	}

	public function get_paid_amount(): string {
		return wptravelengine_the_price_with_decimal( $this->total_paid, false );
	}

	public function get_due(): string {
		return wptravelengine_the_price_with_decimal( $this->total_due, false );
	}

	public function get_trip_extra_fee(): string {
		$total = 0.0;
		foreach ( $this->cart['items'][0]['line_items'] ?? array() as $section_items ) {
			foreach ( $section_items as $item ) {
				$total += $item['total'] ?? ( + $item['quantity'] * + $item['price'] );
			}
		}
		return wptravelengine_the_price_with_decimal( $total, false );
	}

	public function get_total_gateway_fee(): string {
		return wptravelengine_the_price_with_decimal( 0.00, false );
	}

	public function get_discount_name(): string {
		return $this->cart['discounts']['labels'] ?? '';
	}

	public function get_discount_amount(): string {
		$amount = $this->cart['discounts']['amounts'] ?? 0;
		return $amount ? wptravelengine_the_price_with_decimal( $amount, false ) : '';
	}

	public function get_discount_sign(): string {
		return '%';
	}

	public function get_discount_value(): string {
		return '';
	}

	public function get_booking_details(): string {
		$trip     = $this->trip;
		$cart     = $this->cart;
		$currency = $this->currency;

		ob_start();
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
				<td class="alignright"><?php echo esc_html( wp_date( get_option( 'date_format', 'Y-m-d' ), strtotime( $trip->datetime ), new \DateTimeZone( 'utc' ) ) ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Trip End Date', 'wp-travel-engine' ); ?></td>
				<td class="alignright"><?php echo esc_html( wp_date( get_option( 'date_format', 'Y-m-d' ), strtotime( $trip->end_datetime ), new \DateTimeZone( 'utc' ) ) ); ?></td>
			</tr>
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
						$pax_sum = 0;
						foreach ( $trip->pax as $category => $tcount ) :
							if ( + $tcount < 1 ) {
								continue; }
							$pax_unit_cost = + $trip->pax_cost[ $category ] / + $tcount;
							$pax_sum      += + $trip->pax_cost[ $category ];
							?>
							<tr>
								<td class="alignright"><?php echo esc_html( $category ); ?></td>
								<td><?php echo (int) $tcount . ' X ' . wte_esc_price( wte_get_formated_price( $pax_unit_cost, $currency, '', ! 0 ) ) . ' = ' . wte_esc_price( wte_get_formated_price( $trip->pax_cost[ $category ], $currency, '', ! 0 ) ); ?></td>
							</tr>
						<?php endforeach; ?>
						<tr>
							<td width="50%"><?php esc_html_e( 'Subtotal', 'wp-travel-engine' ); ?></td>
							<td width="50%"><?php echo wte_esc_price( wte_get_formated_price( + $pax_sum, $currency, '', ! 0 ) ); ?></td>
						</tr>
					</table>
				</td>
			</tr>

			<?php if ( ! empty( $trip->trip_extras ) ) : ?>
				<tr>
					<td colspan="2"><?php esc_html_e( 'Extra Services:', 'wp-travel-engine' ); ?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="alignright">
						<table width="100%" cellpadding="0" cellspacing="0">
							<?php
							$extras_sum = 0;
							foreach ( $trip->trip_extras as $tx ) :
								$tx_total     = + $tx['qty'] * + $tx['price'];
								$extras_sum  += $tx_total;
								$service_name = $tx['extra_service'] ?? $tx['accommodation'] ?? $tx['pickupoints'] ?? $tx['travel_insurance'] ?? '';
								?>
								<tr>
									<td><?php echo esc_html( $service_name ); ?></td>
									<td><?php echo (int) $tx['qty'] . ' X ' . wte_esc_price( wte_get_formated_price( + $tx['price'], $currency, '', ! 0 ) ) . ' = ' . wte_esc_price( wte_get_formated_price( + $tx_total, $currency, '', ! 0 ) ); ?></td>
								</tr>
							<?php endforeach; ?>
							<tr>
								<td width="50%"><?php esc_html_e( 'Subtotal', 'wp-travel-engine' ); ?></td>
								<td width="50%"><?php echo wte_esc_price( wte_get_formated_price( + $extras_sum, $currency, '', ! 0 ) ); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			<?php endif; ?>
		</table>
		<hr/>
		<table width="100%">
			<tr>
				<td width="50%">&nbsp;</td>
				<td width="50%">
					<table width="100%">
						<tr>
							<td><?php esc_html_e( 'Subtotal', 'wp-travel-engine' ); ?></td>
							<td class="alignright"><?php echo wptravelengine_the_price_with_decimal( $cart['subtotal'], false ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Total', 'wp-travel-engine' ); ?></td>
							<td class="alignright"><?php echo wptravelengine_the_price_with_decimal( $cart['total'], false ); ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php
		return ob_get_clean();
	}

	public function get_trip_booking_summary(): string {
		$trip     = $this->trip;
		$cart     = $this->cart;
		$currency = $this->currency;

		$travelers_count = isset( $cart['items'][0]['travelers'] )
			? array_sum( $cart['items'][0]['travelers'] )
			: ( $cart['items'][0]['travelers_count'] ?? 0 );

		ob_start();
		echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
		?>
		<tr>
			<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Booking Details:', 'wp-travel-engine' ); ?></td>
		</tr>
		<tr>
			<td style="color: #566267;"><?php esc_html_e( 'Package Name', 'wp-travel-engine' ); ?>:</td>
			<td style="width: 50%;text-align: right;"><strong><?php echo esc_html( $trip->package_name ); ?></strong></td>
		</tr>
		<tr>
			<td style="color: #566267;"><?php esc_html_e( 'Trip Date', 'wp-travel-engine' ); ?>:</td>
			<td style="width: 50%;text-align: right;"><strong><?php echo wptravelengine_format_trip_datetime( $trip->datetime ); ?></strong></td>
		</tr>
		<tr>
			<td style="color: #566267;"><?php esc_html_e( 'Trip End Date', 'wp-travel-engine' ); ?>:</td>
			<td style="width: 50%;text-align: right;"><strong><?php echo wptravelengine_format_trip_datetime( $trip->end_datetime ); ?></strong></td>
		</tr>
		<tr>
			<td style="color: #566267;"><?php esc_html_e( 'Travellers', 'wp-travel-engine' ); ?>:</td>
			<td style="width: 50%;text-align: right;"><strong><?php echo esc_html( $travelers_count ); ?></strong></td>
		</tr>
		<tr>
			<td colspan="2"><strong><?php esc_html_e( 'Traveller(s):', 'wp-travel-engine' ); ?></strong></td>
		</tr>
		<?php
		foreach ( $trip->pax as $category => $tcount ) :
			if ( + $tcount < 1 ) {
				continue; }
			$pax_unit = + $trip->pax_cost[ $category ] / + $tcount;
			?>
			<tr>
				<td style="color: #566267;"><?php echo esc_html( $category ) . ': ' . $tcount . ' x ' . wte_esc_price( wte_get_formated_price( $pax_unit, $currency ) ); ?></td>
				<td style="width: 50%;text-align: right;"><strong><?php echo wte_esc_price( wte_get_formated_price( + $trip->pax_cost[ $category ], $currency ) ); ?></strong></td>
			</tr>
		<?php endforeach; ?>
		<?php
		$section_labels = array(
			'accommodations'   => __( 'Accommodation(s):', 'wp-travel-engine' ),
			'pickup_points'    => __( 'Pickup Point(s):', 'wp-travel-engine' ),
			'extra_services'   => __( 'Extra Service(s):', 'wp-travel-engine' ),
			'travel_insurance' => __( 'Travel Insurance:', 'wp-travel-engine' ),
		);
		$line_items     = $cart['items'][0]['line_items'] ?? array();
		foreach ( $line_items as $section_key => $section_items ) :
			if ( empty( $section_items ) ) {
				continue; }
			$section_label = $section_labels[ $section_key ] ?? ( str_replace( '_', ' ', ucwords( $section_key ) ) . ':' );
			?>
			<tr>
				<td colspan="2"><strong><?php echo esc_html( $section_label ); ?></strong></td>
			</tr>
			<?php
			foreach ( $section_items as $item ) :
				$item_total = $item['total'] ?? ( + $item['quantity'] * + $item['price'] );
				?>
				<tr>
					<td style="color: #566267;"><?php echo esc_html( $item['label'] . ': ' ) . $item['quantity'] . ' x ' . wte_esc_price( wte_get_formated_price( + $item['price'], $currency ) ); ?></td>
					<td style="width: 50%;text-align: right;"><strong><?php echo wte_esc_price( wte_get_formated_price( $item_total, $currency ) ); ?></strong></td>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
		<?php
		echo '</table>';
		return ob_get_clean();
	}

	public function get_trip_payment_details(): string {
		ob_start();
		echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
		echo $this->render_payment_rows( false );
		echo '</table>';
		return ob_get_clean();
	}

	public function get_trip_booking_details(): string {
		ob_start();
		echo $this->get_trip_booking_summary();
		?>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="2"><hr style="border: none;border-top: 1px solid #DCDFEA;"></td>
			</tr>
			<tr>
				<td>
					<?php echo $this->get_trip_payment_details(); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2"><hr style="border: none;border-top: 1px solid #DCDFEA;"></td>
			</tr>
			<tr>
				<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Additional Notes:', 'wp-travel-engine' ); ?></td>
			</tr>
			<tr>
				<td colspan="2" style="color: #566267;"><?php echo esc_html( $this->booking->wptravelengine_additional_note ); ?></td>
			</tr>
		</table>
		<?php
		return ob_get_clean();
	}

	public function get_additional_note(): string {
		$note = $this->booking->wptravelengine_additional_note;
		if ( empty( $note ) ) {
			return '';
		}
		ob_start();
		?>
		<table width="100%">
			<tr>
				<td class="title-holder" style="margin: 0;" valign="top">
					<h3 class="alignleft"><?php esc_html_e( 'Additional Note', 'wp-travel-engine' ); ?></h3>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html( $note ); ?></td>
			</tr>
		</table>
		<?php
		return ob_get_clean();
	}

	public function get_billing_details(): string {
		$key_map = array(
			'fname'   => __( 'First Name', 'wp-travel-engine' ),
			'lname'   => __( 'Last Name', 'wp-travel-engine' ),
			'email'   => __( 'Email', 'wp-travel-engine' ),
			'phone'   => __( 'Phone', 'wp-travel-engine' ),
			'address' => __( 'Address', 'wp-travel-engine' ),
			'city'    => __( 'City', 'wp-travel-engine' ),
			'country' => __( 'Country', 'wp-travel-engine' ),
		);
		ob_start();
		?>
		<table width="100%">
			<tr>
				<td class="title-holder" style="margin: 0;" valign="top">
					<h3 class="alignleft"><?php esc_html_e( 'Billing Details', 'wp-travel-engine' ); ?></h3>
				</td>
			</tr>
			<?php foreach ( $this->billing as $key => $value ) : ?>
				<tr>
					<td><?php echo esc_html( $key_map[ $key ] ?? ucfirst( $key ) ); ?></td>
					<td><?php echo esc_html( $key === 'country' ? 'Nepal' : $value ); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
		return ob_get_clean();
	}

	public function get_traveler_details(): string {
		$traveller = DummyDataProvider::get_traveller();
		ob_start();
		?>
		<table width="100%">
			<tr>
				<td class="title-holder" style="margin: 0;" valign="top">
					<h3 class="alignleft"><?php esc_html_e( 'Traveller Details', 'wp-travel-engine' ); ?></h3>
				</td>
			</tr>
			<tr>
				<td><h3><?php esc_html_e( 'Traveller 1 (Lead Traveller)', 'wp-travel-engine' ); ?></h3></td>
			</tr>
			<?php foreach ( $traveller as $key => $value ) : ?>
				<?php
				if ( empty( $value ) ) {
					continue; }
				?>
				<tr>
					<td>
						<?php
							echo esc_html( str_replace( '_', ' ', ucfirst( $key ) ) );
						?>
					</td>
					<td><strong><?php echo esc_html( $value ); ?></strong></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
		return ob_get_clean();
	}

	public function get_emergency_details(): string {
		$emergency = DummyDataProvider::get_emergency_contact();
		$key_map   = array(
			'fname'    => 'First Name',
			'lname'    => 'Last Name',
			'email'    => 'Email',
			'phone'    => 'Phone',
			'relation' => 'Relation',
		);
		ob_start();
		?>
		<table width="100%">
			<tr>
				<td class="title-holder" style="margin: 0;" valign="top">
					<h3 class="alignleft"><?php esc_html_e( 'Emergency Details', 'wp-travel-engine' ); ?></h3>
				</td>
			</tr>
			<?php foreach ( $emergency as $key => $value ) : ?>
				<tr>
					<td><?php echo esc_html( $key_map[ $key ] ?? ucfirst( $key ) ); ?></td>
					<td><strong><?php echo esc_html( $value ); ?></strong></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
		return ob_get_clean();
	}

	public function get_check_payment_instruction(): string {
		ob_start();
		?>
		<table class="invoice-items">
			<tr>
				<td colspan="2">
					<h3><?php esc_html_e( 'Check Payment Instructions:', 'wp-travel-engine' ); ?></h3>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php echo wp_kses_post( __( 'Please make your check payable to <strong>Demo Travel Agency</strong> and mail it to 123 Adventure Lane, Kathmandu, Nepal. Include your booking reference <strong>#12345</strong> on the memo line. Payment must be received within 7 business days to confirm your booking.', 'wp-travel-engine' ) ); ?>
				</td>
			</tr>
		</table>
		<?php
		return ob_get_clean();
	}

	public function get_bank_details(): string {
		$account = DummyDataProvider::get_bank_account();
		$labels  = array(
			'account_number' => __( 'Account Number', 'wp-travel-engine' ),
			'bank_name'      => __( 'Bank Name', 'wp-travel-engine' ),
			'sort_code'      => __( 'Sort Code', 'wp-travel-engine' ),
			'iban'           => __( 'IBAN', 'wp-travel-engine' ),
			'swift'          => __( 'BIC/Swift', 'wp-travel-engine' ),
		);

		ob_start();
		echo '<table class="invoice-items">';
		echo '<tr><td colspan="2"><h3>' . esc_html__( 'Bank Details:', 'wp-travel-engine' ) . '</h3></td></tr>';
		echo '<tr><td colspan="2"><h5>' . esc_html( $account['account_name'] ) . '</h5></td></tr>';
		foreach ( $labels as $key => $label ) {
			?>
			<tr>
				<td><?php echo esc_html( $label ); ?></td>
				<td class="alignright"><?php echo esc_html( $account[ $key ] ?? '' ); ?></td>
			</tr>
			<?php
		}
		echo '</table>';

		return ob_get_clean();
	}

	/**
	 * Shared payment + billing rows HTML.
	 * Used by both get_trip_payment_details() and get_trip_booking_details().
	 *
	 * @param bool $hide_header True when rendered inside trip_booking_details.
	 */
	private function render_payment_rows( bool $hide_header ): string {
		$cart            = $this->cart;
		$billing         = $this->billing;
		$currency        = $this->currency;
		$subtotal        = $cart['subtotal'] ?? 0;
		$total           = $cart['total'] ?? 0;
		$deposit         = $cart['totals']['partial_total'] ?? $this->total_paid;
		$due             = $cart['totals']['due_total'] ?? $this->total_due;
		$discount_label  = $cart['discounts']['labels'] ?? '';
		$discount_amount = $cart['discounts']['amounts'] ?? 0;
		$tax_amount      = $cart['totals']['total_tax'] ?? 0;
		$tax_percentage  = $cart['tax_percentage'] ?? 0;

		ob_start();
		if ( ! $hide_header ) :
			?>
			<tr>
				<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Payment Details:', 'wp-travel-engine' ); ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td><strong><?php esc_html_e( 'Subtotal', 'wp-travel-engine' ); ?></strong></td>
			<td style="text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $subtotal, false ); ?></strong></td>
		</tr>
		<?php if ( $discount_amount ) : ?>
		<tr style="color: #12B76A;">
			<td><strong><?php echo esc_html( $discount_label ?: __( 'Discount', 'wp-travel-engine' ) ); ?></strong></td>
			<td style="width: 50%;text-align: right;"><strong>-<?php echo wptravelengine_the_price_with_decimal( $discount_amount, false ); ?></strong></td>
		</tr>
		<?php endif; ?>
		<tr>
			<td colspan="2">
				<span style="display: block;padding: 8px 16px;background-color: rgba(15, 29, 35, 0.04);border-radius: 4px;margin: 0 -16px; font-size: 0px;">
					<strong style="width: 50%;display: inline-block;font-size: 16px;"><?php esc_html_e( 'Total', 'wp-travel-engine' ); ?></strong>
					<strong style="width: 50%;text-align: right;display: inline-block;font-size: 16px;"><?php echo wptravelengine_the_price_with_decimal( $total, false ); ?></strong>
				</span>
			</td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Initial Deposit', 'wp-travel-engine' ); ?></strong></td>
			<td style="width: 50%;text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $deposit, false ); ?></strong></td>
		</tr>
		<?php if ( $tax_amount > 0 ) : ?>
		<tr style="color: #F79009;">
			<td><strong><?php echo esc_html( $tax_percentage ? sprintf( __( 'Tax (%s%%)', 'wp-travel-engine' ), $tax_percentage ) : __( 'Tax', 'wp-travel-engine' ) ); ?></strong></td>
			<td style="width: 50%;text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $tax_amount, false ); ?></strong></td>
		</tr>
		<?php endif; ?>
		<tr>
			<td><strong><?php esc_html_e( 'Amount Due (excl. Tax) ', 'wp-travel-engine' ); ?></strong></td>
			<td style="width: 50%;text-align: right;"><strong><?php echo wptravelengine_the_price_with_decimal( $due, false ); ?></strong></td>
		</tr>
		<tr>
			<td colspan="2" style="font-size: 16px;line-height: 1.75;font-weight: bold;padding: 8px 0 4px;"><?php esc_html_e( 'Billing Details:', 'wp-travel-engine' ); ?></td>
		</tr>
		<tr>
			<td style="color: #566267;"><?php esc_html_e( 'Booking Name:', 'wp-travel-engine' ); ?></td>
			<td style="width: 50%;text-align: right;"><strong><?php echo esc_html( $billing['fname'] . ' ' . $billing['lname'] ); ?></strong></td>
		</tr>
		<tr>
			<td style="color: #566267;"><?php esc_html_e( 'Booking Email:', 'wp-travel-engine' ); ?></td>
			<td style="width: 50%;text-align: right;"><strong><?php echo esc_html( $billing['email'] ); ?></strong></td>
		</tr>
		<tr>
			<td style="color: #566267;"><?php esc_html_e( 'Booking Address:', 'wp-travel-engine' ); ?></td>
			<td style="width: 50%;text-align: right;"><strong><?php echo esc_html( $billing['address'] ); ?></strong></td>
		</tr>
		<tr>
			<td style="color: #566267;"><?php esc_html_e( 'City:', 'wp-travel-engine' ); ?></td>
			<td style="width: 50%;text-align: right;"><strong><?php echo esc_html( $billing['city'] ); ?></strong></td>
		</tr>
		<tr>
			<td style="color: #566267;"><?php esc_html_e( 'Country:', 'wp-travel-engine' ); ?></td>
			<td style="width: 50%;text-align: right;"><strong><?php esc_html_e( 'Nepal', 'wp-travel-engine' ); ?></strong></td>
		</tr>
		<?php
		return ob_get_clean();
	}
}
