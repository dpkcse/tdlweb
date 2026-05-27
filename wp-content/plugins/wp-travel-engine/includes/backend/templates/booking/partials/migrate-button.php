<?php
/**
 * Migrate Button Partial.
 *
 * Renders a navigation link between an original booking and its migrated counterpart.
 * - Migrated booking (has _migrated_from): shows "View Original Booking" link.
 * - Original booking (has _migrated_to): shows "View Migrated Booking" link.
 *
 * @var \WPTravelEngine\Core\Models\Post\Booking $booking
 * @since 6.8.0
 */

$original_booking_id = absint( $booking->get_meta( '_migrated_from' ) );
$migrated_booking_id = absint( $booking->get_meta( '_migrated_to' ) );
$svg                 = '<svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
	<path
		d="M17.5 7.50001L17.5 2.50001M17.5 2.50001H12.5M17.5 2.50001L10 10M8.33333 2.5H6.5C5.09987 2.5 4.3998 2.5 3.86502 2.77248C3.39462 3.01217 3.01217 3.39462 2.77248 3.86502C2.5 4.3998 2.5 5.09987 2.5 6.5V13.5C2.5 14.9001 2.5 15.6002 2.77248 16.135C3.01217 16.6054 3.39462 16.9878 3.86502 17.2275C4.3998 17.5 5.09987 17.5 6.5 17.5H13.5C14.9001 17.5 15.6002 17.5 16.135 17.2275C16.6054 16.9878 16.9878 16.6054 17.2275 16.135C17.5 15.6002 17.5 14.9001 17.5 13.5V11.6667"
		stroke="currentcolor" stroke-width="1.39" stroke-linecap="round" stroke-linejoin="round" />
</svg>';

$original_post = $original_booking_id ? get_post( $original_booking_id ) : null;
$migrated_post = $migrated_booking_id ? get_post( $migrated_booking_id ) : null;

if ( $original_post && 'trash' !== $original_post->post_status ) {
	printf(
		'<a href="%1$s" class="wpte-button wpte-outlined wpte-migrate-nav-button">%2$s%3$s</a>',
		esc_url( admin_url( "post.php?post={$original_booking_id}&action=edit" ) ),
		esc_html__( 'View Original Booking', 'wp-travel-engine' ),
		$svg
	);
} elseif ( $migrated_post && 'trash' !== $migrated_post->post_status ) {
	printf(
		'<a href="%1$s" class="wpte-button wpte-outlined wpte-migrate-nav-button">%2$s%3$s</a>',
		esc_url( admin_url( "post.php?post={$migrated_booking_id}&action=edit" ) ),
		esc_html__( 'View Migrated Booking', 'wp-travel-engine' ),
		$svg
	);
}
