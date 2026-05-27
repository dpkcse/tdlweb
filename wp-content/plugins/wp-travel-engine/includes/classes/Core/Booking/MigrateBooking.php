<?php
/**
 * Booking Migration.
 *
 * @since 6.8.0
 */

namespace WPTravelEngine\Core\Booking;

use WPTravelEngine\Core\Cart\Adjustments\CouponAdjustment;
use WPTravelEngine\Core\Cart\Adjustments\TaxAdjustment;
use WPTravelEngine\Core\Models\Post\Booking as BookingModel;
use WPTravelEngine\Helpers\CartInfoParser;
use WPTravelEngine\Utilities\PaymentCalculator;

class MigrateBooking {

	protected BookingModel $booking;

	protected int $new_booking_id = 0;

	public function __construct( int $booking_id ) {
		$booking = wptravelengine_get_booking( $booking_id );
		if ( ! $booking ) {
			return;
		}
		$this->booking = $booking;
		$this->migrate();
	}

	public function get_new_booking_id(): int {
		return $this->new_booking_id;
	}

	/**
	 * Checks whether _migrated_to / _migrated_from post IDs still exist.
	 *
	 * @param int $booking_id
	 * @return array Keys: migrated_to (int|null), migrated_to_exists (bool|null), migrated_from (int|null), migrated_from_exists (bool|null)
	 */
	public static function validate_migration_links( int $booking_id ): array {
		$migrated_to   = absint( get_post_meta( $booking_id, '_migrated_to', true ) );
		$migrated_from = absint( get_post_meta( $booking_id, '_migrated_from', true ) );

		return array(
			'migrated_to'          => $migrated_to ?: null,
			'migrated_to_exists'   => $migrated_to ? (bool) get_post( $migrated_to ) : null,
			'migrated_from'        => $migrated_from ?: null,
			'migrated_from_exists' => $migrated_from ? (bool) get_post( $migrated_from ) : null,
		);
	}

	protected function migrate(): void {
		if ( $this->booking->is_curr_cart() ) {
			return;
		}

		$existing_id = absint( $this->booking->get_meta( '_migrated_to' ) );
		$cart_info   = $this->booking->get_cart_info();
		if ( empty( $cart_info ) ) {
			return;
		}

		$parser = new CartInfoParser( $cart_info );

		$new_cart_info                     = $cart_info;
		$new_cart_info['version']          = '4.0';
		$new_cart_info['fees']             = $this->normalize_adjustments( $parser->get_fees(), TaxAdjustment::class );
		$new_cart_info['deductible_items'] = $this->normalize_adjustments( $parser->get_deductible_items(), CouponAdjustment::class );
		$new_cart_info['totals']           = $this->compute_v4_totals(
			$parser->get_totals(),
			$this->resolve_currency( $cart_info ),
			$cart_info['payment_type'] ?? ''
		);

		if ( $existing_id ) {
			$new_booking_id = $this->update_migrated_booking( $existing_id, $new_cart_info );
			if ( ! $new_booking_id ) {
				// Migrated target post was deleted — remove stale pointer and re-create.
				delete_post_meta( $this->booking->get_id(), '_migrated_to' );
				$new_booking_id = $this->create_migrated_booking( $new_cart_info );
			}
		} else {
			$new_booking_id = $this->create_migrated_booking( $new_cart_info );
		}

		if ( $new_booking_id ) {
			$this->new_booking_id = $new_booking_id;
		}
	}

	/**
	 * Re-applies migrated cart_info to an already-migrated booking in place.
	 */
	protected function update_migrated_booking( int $existing_id, array $new_cart_info ): int {
		$existing_booking = wptravelengine_get_booking( $existing_id );
		if ( ! $existing_booking ) {
			return 0;
		}

		$existing_booking->set_cart_info( $new_cart_info )->save();

		if ( 'publish' !== get_post_status( $existing_id ) ) {
			wp_update_post(
				array(
					'ID'          => $existing_id,
					'post_status' => 'publish',
				)
			);
		}

		$this->sync_payment_cart_totals( $existing_id, $new_cart_info );
		$this->sync_booking_payment_totals( $existing_id, $new_cart_info );

		return $existing_id;
	}

	/**
	 * Creates a new booking post, copies all meta from the original, and
	 * applies the migrated cart_info to the new post only.
	 */
	protected function create_migrated_booking( array $new_cart_info ): int {
		$old_post = get_post( $this->booking->get_id() );
		if ( ! $old_post ) {
			return 0;
		}

		$new_id = wp_insert_post(
			array(
				'post_type'   => $old_post->post_type,
				'post_status' => $old_post->post_status,
				'post_title'  => $old_post->post_title,
				'post_author' => $old_post->post_author,
				'post_date'   => $old_post->post_date,
			),
			true
		);

		if ( is_wp_error( $new_id ) ) {
			return 0;
		}

		// Replace old booking ID with new ID in the post title.
		$new_title = str_replace( (string) $old_post->ID, (string) $new_id, $old_post->post_title );
		if ( $new_title !== $old_post->post_title ) {
			wp_update_post(
				array(
					'ID'         => $new_id,
					'post_title' => $new_title,
				)
			);
		}

		$this->copy_post_meta( $old_post->ID, $new_id );

		// migrate_payments returns [['new_id' => X, 'old_id' => Y], ...].
		$payment_map     = $this->migrate_payments( $old_post->ID, $new_id, $new_cart_info );
		$new_payment_ids = array_column( $payment_map, 'new_id' );

		$new_booking = wptravelengine_get_booking( $new_id );
		if ( $new_booking ) {
			$new_booking->set_cart_info( $new_cart_info )
						->set_meta( '_migrated_from', $old_post->ID );

			if ( ! empty( $new_payment_ids ) ) {
				$new_booking->set_meta( 'payments', $new_payment_ids );
			}

			$new_booking->save();
		}

		// Re-fetch so get_payments() sees the saved payment IDs.
		$new_booking = wptravelengine_get_booking( $new_id );

		if ( $new_booking && ! empty( $payment_map ) ) {
			// Preserve booking status set by copy_post_meta — sync methods will overwrite it.
			$original_booking_status         = get_post_meta( $new_id, 'wp_travel_engine_booking_status', true );
			$original_booking_payment_status = get_post_meta( $new_id, 'wp_travel_engine_booking_payment_status', true );

			foreach ( $payment_map as $entry ) {
				$this->sync_migrated_payment( $new_booking, $entry['new_id'], $entry['old_id'] );
			}

			// Restore original booking status — sync methods reflect payment-level state
			// but the copied status already represents the correct booking state.
			if ( $original_booking_status ) {
				$new_booking->sync_metas(
					array(
						'wp_travel_engine_booking_status' => $original_booking_status,
						'wp_travel_engine_booking_payment_status' => $original_booking_payment_status,
					)
				);
			}
		}

		// Track on the original booking — points forward to the new booking.
		$this->booking->sync_metas( array( '_migrated_to' => $new_id ) );

		// Register original booking ID in the global migrated-bookings index.
		$migrated_ids   = get_option( 'wptravelengine_migrated_booking_ids', array() );
		$migrated_ids[] = $old_post->ID;
		update_option( 'wptravelengine_migrated_booking_ids', array_unique( $migrated_ids ), false );

		return $new_id;
	}

	/**
	 * Calls the appropriate sync_payment_*_metas method on the booking based on
	 * the original payment's status — reuses existing business logic for all
	 * payment meta updates instead of re-implementing them.
	 */
	protected function sync_migrated_payment( BookingModel $booking, int $new_payment_id, int $old_payment_id ): void {
		$status      = get_post_meta( $old_payment_id, 'payment_status', true );
		$payable_raw = get_post_meta( $old_payment_id, 'payable', true );
		// v3.0 stored payable as a plain scalar; v4.0 uses ['amount' => ...].
		$payable_amount = is_array( $payable_raw )
			? (float) ( $payable_raw['amount'] ?? 0 )
			: (float) $payable_raw;

		// Reset payment_status so sync_payment_success_metas doesn't return early.
		delete_post_meta( $new_payment_id, 'payment_status' );

		switch ( $status ) {
			case 'completed':
			case 'captured':
				$payment_amount = get_post_meta( $old_payment_id, 'payment_amount', true );
				$amount_value   = is_array( $payment_amount )
					? (float) ( $payment_amount['value'] ?? 0 )
					: (float) $payment_amount;
				$gateway_fee    = (string) ( get_post_meta( $old_payment_id, 'gateway_fee', true ) ?: '0.00' );
				$booking->sync_payment_success_metas(
					$new_payment_id,
					$amount_value,
					array( 'payment_metadata' => array( 'gateway_fee' => $gateway_fee ) )
				);
				break;

			case 'pending':
				$booking->sync_payment_pending_metas( $new_payment_id, $payable_amount );
				break;

			case 'failed':
				$booking->sync_payment_failed_metas( $new_payment_id, $payable_amount );
				break;
		}
	}

	/**
	 * Duplicates each payment linked to the original booking, wires the copies
	 * to the new booking, and sets _migrated_from/_migrated_to on both sides.
	 * Returns [['new_id' => X, 'old_id' => Y], ...].
	 */
	protected function migrate_payments( int $old_booking_id, int $new_booking_id, array $new_cart_info = array() ): array {
		$payment_ids = get_post_meta( $old_booking_id, 'payments', true );
		if ( empty( $payment_ids ) || ! is_array( $payment_ids ) ) {
			return array();
		}

		$base_totals = $new_cart_info['totals'] ?? array();
		$calculator  = ! empty( $base_totals )
			? PaymentCalculator::for( $this->resolve_currency( $new_cart_info ) )
			: null;

		$payment_map = array();

		foreach ( $payment_ids as $old_payment_id ) {
			$old_payment = get_post( absint( $old_payment_id ) );
			if ( ! $old_payment ) {
				continue;
			}

			$new_payment_id = wp_insert_post(
				array(
					'post_type'   => $old_payment->post_type,
					'post_status' => $old_payment->post_status,
					'post_title'  => $old_payment->post_title,
					'post_author' => $old_payment->post_author,
					'post_date'   => $old_payment->post_date,
				),
				true
			);

			if ( is_wp_error( $new_payment_id ) ) {
				continue;
			}

			$this->copy_post_meta( $old_payment->ID, $new_payment_id );

			if ( $calculator ) {
				$this->apply_payment_cart_totals( $old_payment->ID, $new_payment_id, $base_totals, $calculator );
			}

			// Normalize v3.0 scalar payable to v4.0 array format so Payment::get_payable_amount() works.
			$payable_meta = get_post_meta( $new_payment_id, 'payable', true );
			if ( ! is_array( $payable_meta ) ) {
				$currency = $this->resolve_currency( $new_cart_info );
				update_post_meta(
					$new_payment_id,
					'payable',
					array(
						'amount'   => (string) ( $payable_meta ?: 0 ),
						'currency' => $currency,
					)
				);
			}

			update_post_meta( $new_payment_id, 'booking_id', $new_booking_id );
			update_post_meta( $new_payment_id, '_migrated_from', $old_payment->ID );
			update_post_meta( $old_payment->ID, '_migrated_to', $new_payment_id );

			$payment_map[] = array(
				'new_id' => $new_payment_id,
				'old_id' => $old_payment->ID,
			);
		}

		return $payment_map;
	}

	/**
	 * Re-computes cart_totals on each payment already linked to the migrated booking.
	 */
	protected function sync_payment_cart_totals( int $booking_id, array $new_cart_info ): void {
		$payment_ids = get_post_meta( $booking_id, 'payments', true );
		if ( empty( $payment_ids ) || ! is_array( $payment_ids ) ) {
			return;
		}

		$base_totals = $new_cart_info['totals'] ?? array();
		if ( empty( $base_totals ) ) {
			return;
		}

		$calculator = PaymentCalculator::for( $this->resolve_currency( $new_cart_info ) );

		foreach ( $payment_ids as $payment_id ) {
			$payment_id = absint( $payment_id );
			$this->apply_payment_cart_totals( $payment_id, $payment_id, $base_totals, $calculator );
		}
	}

	/**
	 * Re-computes total_paid_amount and total_due_amount on the booking from its payments.
	 * Used on re-migration where payment statuses don't change.
	 */
	protected function sync_booking_payment_totals( int $booking_id, array $new_cart_info ): void {
		$payment_ids = get_post_meta( $booking_id, 'payments', true );
		if ( empty( $payment_ids ) || ! is_array( $payment_ids ) ) {
			return;
		}

		$base_totals         = $new_cart_info['totals'] ?? array();
		$calculator          = PaymentCalculator::for( $this->resolve_currency( $new_cart_info ) );
		$total_paid_amount   = '0.00';
		$total_extra_charges = '0.00';
		$total_gateway_fee   = '0.00';

		foreach ( $payment_ids as $payment_id ) {
			$payment_id     = absint( $payment_id );
			$payment_status = get_post_meta( $payment_id, 'payment_status', true );

			if ( in_array( $payment_status, array( 'completed', 'captured' ), true ) ) {
				$payment_amount    = get_post_meta( $payment_id, 'payment_amount', true );
				$paid_value        = is_array( $payment_amount )
					? (string) ( $payment_amount['value'] ?? 0 )
					: (string) ( $payment_amount ?: 0 );
				$total_paid_amount = $calculator->add( $total_paid_amount, $paid_value );
			}

			$cart_totals         = get_post_meta( $payment_id, 'cart_totals', true );
			$total_extra_charges = $calculator->add( $total_extra_charges, (string) ( $cart_totals['total_extra_charges'] ?? 0 ) );
			$total_gateway_fee   = $calculator->add( $total_gateway_fee, (string) ( get_post_meta( $payment_id, 'gateway_fee', true ) ?: 0 ) );
		}

		$total            = (string) ( $base_totals['total'] ?? 0 );
		$total_due_amount = $calculator->subtract(
			$calculator->add( $total, $total_extra_charges ),
			$calculator->subtract( $total_paid_amount, $total_gateway_fee )
		);

		$booking = wptravelengine_get_booking( $booking_id );
		if ( $booking ) {
			$booking->sync_metas(
				array(
					'total_paid_amount' => $total_paid_amount,
					'total_due_amount'  => $total_due_amount,
				)
			);
		}
	}

	/**
	 * Reads payable.amount from $source_id and writes computed cart_totals to $target_id.
	 */
	protected function apply_payment_cart_totals( int $source_id, int $target_id, array $base_totals, PaymentCalculator $calculator ): void {
		$payable     = get_post_meta( $source_id, 'payable', true );
		$base_amount = is_array( $payable )
			? (string) ( $payable['amount'] ?? 0 )
			: (string) ( $payable ?: 0 );

		if ( ! $calculator->is( $base_amount, '>', '0.00' ) ) {
			$base_amount = (string) ( $base_totals['payable_now'] ?? $base_totals['total'] ?? 0 );
		}

		update_post_meta( $target_id, 'cart_totals', $this->compute_proportional_totals( $base_amount, $base_totals, $calculator ) );
	}

	/**
	 * Derives the four v4.0-only totals from v3.0 totals.
	 *
	 * v3.0: total = subtotal − discounts + ALL_FEES (fees baked in).
	 * v4.0: total = subtotal − discounts (pre-fee base); fees are stored separately.
	 */
	protected function compute_v4_totals( array $totals, string $currency, string $payment_type = '' ): array {
		$calculator     = PaymentCalculator::for( $currency );
		$v3_total       = (string) ( $totals['total'] ?? 0 );
		$partial_total  = (string) ( $totals['partial_total'] ?? 0 );
		$due_total      = (string) ( $totals['due_total'] ?? 0 );
		$subtotal       = (string) ( $totals['subtotal'] ?? 0 );
		$discount_total = (string) ( $totals['discount_total'] ?? 0 );

		// v4.0 pre-fee base = subtotal − discounts.
		// Fallback: if subtotal absent, subtract only tax (last-resort approximation).
		if ( $calculator->is( $subtotal, '>', '0.00' ) ) {
			$v4_base = $calculator->subtract( $subtotal, $discount_total );
			if ( ! $calculator->is( $v4_base, '>', '0.00' ) ) {
				$v4_base = '0.00';
			}
		} else {
			$total_tax = (string) ( $totals['total_tax'] ?? 0 );
			$v4_base   = $calculator->is( $total_tax, '>', '0.00' )
				? $calculator->subtract( $v3_total, $total_tax )
				: $v3_total;
		}

		// Store v3.0 full total so compute_proportional_totals can derive total_fees accurately.
		$totals['total']          = $v4_base;
		$totals['_v3_full_total'] = $v3_total;

		// payable_now = v3.0 fee-inclusive amount for the given payment_type.
		switch ( $payment_type ) {
			case 'partial':
				$base_amount = $calculator->is( $partial_total, '>', '0.00' ) ? $partial_total : $v3_total;
				break;
			case 'due':
				$base_amount = $calculator->is( $due_total, '>', '0.00' ) ? $due_total : $v3_total;
				break;
			case 'full_payment':
			case 'full':
				$base_amount = $v3_total;
				break;
			default:
				// Very old bookings may not have payment_type; infer from totals.
				$base_amount = $calculator->is( $partial_total, '>', '0.00' ) ? $partial_total : $v3_total;
				break;
		}

		return $this->compute_proportional_totals( $base_amount, $totals, $calculator );
	}

	/**
	 * Merges payable_now, deposit, and total_extra_charges into $base_totals.
	 *
	 * When _v3_full_total is present (migration path), fee-rate = (v3_total − v4_base) / v3_total
	 * so that ALL baked-in fees (tax, booking fees, etc.) are proportionally extracted.
	 * Fallback uses total_tax / total for non-migrated invocations.
	 */
	protected function compute_proportional_totals( string $base_amount, array $base_totals, PaymentCalculator $calculator ): array {
		$v3_full_total = (string) ( $base_totals['_v3_full_total'] ?? 0 );
		$v4_base       = (string) ( $base_totals['total'] ?? 0 );

		$total_extra_charges = '0.00';

		if ( $calculator->is( $v3_full_total, '>', '0.00' ) ) {
			// Migration path: total_fees = all fees baked into v3.0 total.
			$total_fees = $calculator->subtract( $v3_full_total, $v4_base );
			if ( $calculator->is( $total_fees, '>', '0.00' ) ) {
				$total_extra_charges = $calculator->normalize(
					$calculator->divide(
						$calculator->multiply( $base_amount, $total_fees ),
						$v3_full_total
					)
				);
			}
		} else {
			// Non-migration fallback: use total_tax / total as proxy.
			$total     = (string) ( $base_totals['total'] ?? 0 );
			$total_tax = (string) ( $base_totals['total_tax'] ?? 0 );
			if ( $calculator->is( $total, '>', '0.00' ) && $calculator->is( $total_tax, '>', '0.00' ) ) {
				$total_extra_charges = $calculator->normalize(
					$calculator->divide(
						$calculator->multiply( $base_amount, $total_tax ),
						$total
					)
				);
			}
		}

		return array_merge(
			$base_totals,
			array(
				'payable_now'         => $base_amount,
				'deposit'             => $calculator->subtract( $base_amount, $total_extra_charges ),
				'total_extra_charges' => $total_extra_charges,
			)
		);
	}

	/**
	 * Normalizes an array of adjustment items — sets _class_name to $default_class
	 * if absent or pointing to a non-existent class.
	 */
	protected function normalize_adjustments( array $items, string $default_class ): array {
		return array_map(
			function ( $item ) use ( $default_class ) {
				if ( ! isset( $item['_class_name'] ) || ! class_exists( $item['_class_name'] ) ) {
					$item['_class_name'] = $default_class;
				}
				return $item;
			},
			$items
		);
	}

	/**
	 * Copies all post meta from one post to another.
	 */
	protected function copy_post_meta( int $from_id, int $to_id ): void {
		foreach ( get_post_meta( $from_id ) as $meta_key => $meta_values ) {
			foreach ( $meta_values as $meta_value ) {
				add_post_meta( $to_id, $meta_key, wptravelengine_maybe_unserialize( $meta_value ) );
			}
		}
	}

	/**
	 * Returns the currency from cart_info, falling back to the site default.
	 */
	protected function resolve_currency( array $cart_info ): string {
		return $cart_info['currency'] ?? wptravelengine_settings()->get( 'currency_code', 'USD' );
	}
}
