<?php
/**
 * Booking Header.
 *
 * @since 6.4.0
 */

/**
 * @var Booking $booking
 */

use WPTravelEngine\Core\Models\Post\Booking;

?>
<!-- .wpte-page-header -->
<header class="wpte-page-header">
		<a href="<?php echo 'edit' === $template_mode ? esc_url( admin_url( "post.php?post={$booking->get_id()}&action=edit" ) ) : esc_url( admin_url( 'edit.php?post_type=booking' ) ); ?>" class="wpte-page-back-button">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
						stroke-linejoin="round" />
			</svg>
		</a>
	<?php if ( 'edit' === $template_mode ) : ?>
		<div class="wpte-fields-grid" data-columns="1">
			<div class="wpte-field">
				<input type="text" name="post_title" value="<?php echo $booking->post->post_title == '' ? esc_html( 'Booking #' . $booking->get_id() ) : esc_html( $booking->post->post_title ); ?>" />
			</div>
		</div>
	<?php else : ?>
		<h1><?php echo esc_html( $booking->get_title() ); ?></h1>
		<?php
	endif;

	$status_tag       = sprintf( '<span class="wpte-tag %1$s">%1$s</span>', $booking->get_booking_status() );
	$admin_edited_tag = wptravelengine_toggled( $booking->get_meta( '_user_edited' ) ) ? sprintf( '<span class="wpte-tag %1$s">%1$s</span>', __( 'Customized Reservation', 'wp-travel-engine' ) ) : '';
	?>
	<div class="wpte-page-header-content">
		<?php
		printf(
			'<div class="wpte-tags-wrap">%1$s%2$s</div>',
			$admin_edited_tag,
			$status_tag,
		);
		?>
	</div>
	<?php
	$_migrated_to_id   = absint( $booking->get_meta( '_migrated_to' ) );
	$_already_migrated = $_migrated_to_id && get_post( $_migrated_to_id );
	$_migrate_label    = $_already_migrated
		? __( 'Re-Migrate Booking', 'wp-travel-engine' )
		: __( 'Migrate to New Booking', 'wp-travel-engine' );
	?>
	<div class="wpte-button-group">
		<div>
			<button type="button" class="wpte-button wpte-outlined" id="wpte-migrate-booking"
					data-booking-id="<?php echo esc_attr( $booking->get_id() ); ?>"
					data-nonce="<?php echo wp_create_nonce( 'wptravelengine_migrate_booking' ); ?>"
					data-migrated-booking-id="<?php echo esc_attr( $_already_migrated ? $_migrated_to_id : '' ); ?>">
				<?php echo esc_html( $_migrate_label ); ?>
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M21 9H7.5C5.01472 9 3 11.0147 3 13.5C3 15.9853 5.01472 18 7.5 18H12M21 9L17 5M21 9L17 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
		</div>
	</div>
	<!-- </div> -->
</header> <!-- end .wpte-page-header -->
