<?php
namespace WPTRAVELENGINEEB;

/**
 * Trip Slider Widget 3 Thumbs markup
 */
list( $settings, $trip, $index ) = $args;
$position                        = wte_array_get( $settings, 'loc_position', 'top' );
$show_meta_data                  = wte_array_get( $settings, 'showTripMeta', array( 'showDuration', 'showDifficulty', 'showActivities' ) );
$showButton                      = wte_array_get( $settings, 'showViewMoreButton', true );

$meta_data = array( 'group-size', 'age-group', 'difficulty', 'activity', 'trip-types', 'altitude' );

$_meta_data = array();
foreach ( $meta_data as $item ) {
	$_meta_data[ $item ] = wptravelengineeb_get_trip_metadata( $trip_id, $item );
}
?>
<div class="swiper-slide">
	<div class="wpte-thumbs-details wpte-card">
		<?php
		if ( 'top' === $position && wte_array_get( $settings, 'showLocation', false ) ) :
			$terms = wte_get_the_tax_term_list( $trip->ID, 'destination', '', ', ', '' );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
				?>
					<div class="wpte-card__location">
					<?php wptravelengineeb_get_icon_by_slug( 'location' ); ?>
						<span><?php echo wp_kses_post( $terms ); ?></span>
					</div>
					<?php
				endif;
			endif;
		if ( wte_array_get( $settings, 'showTitle', true ) ) :
			?>
				<h2 class="wpte-card__title" itemprop="name">
					<a itemprop="url" href="<?php echo esc_url( get_the_permalink( $trip ) ); ?>"><?php echo esc_html( $trip->post_title ); ?></a>
				</h2>
			<?php
			endif;
		if ( 'bottom' === $position && wte_array_get( $settings, 'showLocation', false ) ) :
			$terms = wte_get_the_tax_term_list( $trip->ID, 'destination', '', ', ', '' );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
				?>
					<span class="wpte-card__location">
					<?php wptravelengineeb_get_icon_by_slug( 'location' ); ?>
						<span><?php echo wp_kses_post( $terms ); ?></span>
					</span>
					<?php
				endif;
			endif;
		?>
		<div class="wpte-card__meta-wrapper">
			<div class="wpte-card__meta">
				<?php
				if ( in_array( 'showDuration', $show_meta_data ) && $meta->duration['days'] != 0 ) :
					$trip_duration_unit   = $meta->duration['duration_unit'];
					$trip_duration_nights = $meta->duration['nights'];
					$set_duration_types   = $settings['durationType'];
					$duration_label       = array();
					?>

					<div class="category trip-dur">
						<?php wptravelengineeb_get_icon_by_slug( 'duration' ); ?>
						<div class="wpte-card__meta-content">
							<?php
							if ( ( 'days' !== $trip_duration_unit ) || ( 'days' === $trip_duration_unit && $meta->duration['days'] && in_array( $set_duration_types, array( 'both', 'days' ) ) ) ) {
								$duration_label[] = sprintf(
									_nx( '%1$d %2$s', '%1$d %3$s', (int) $meta->duration['days'], 'trip duration', 'wptravelengine-elementor-widgets' ),
									(int) $meta->duration['days'],
									$results['duration'][ $trip_duration_unit ][0],
									$results['duration'][ $trip_duration_unit ][1]
								);
							}
							if ( 'days' === $trip_duration_unit && $trip_duration_nights && in_array( $set_duration_types, array( 'both', 'nights' ) ) ) {
								$duration_label[] = sprintf( _nx( '%1$d Night', '%1$d Nights', (int) $trip_duration_nights, 'trip duration night', 'wptravelengine-elementor-widgets' ), (int) $trip_duration_nights );
							}
							$duration_label = apply_filters( 'wptravelengine_trip_duration_arr', $duration_label, $trip->ID, $set_duration_types );
							?>
								<span class="wpte-card__meta-title"><?php \esc_html_e( 'Duration', 'wptravelengine-elementor-widgets' ); ?></span>
								<span class="wpte-card__meta-value"><?php echo esc_html( implode( ' - ', $duration_label ) ); ?></span>
						</div>
					</div>
					<?php
				endif;

				if ( in_array( 'showDifficulty', $show_meta_data ) && ! empty( $_meta_data['difficulty'] ) ) :
					?>
					<div class="category trip-diff">
						<?php wptravelengineeb_get_icon_by_slug( 'difficulties' ); ?>
						<div class="wpte-card__meta-content">
							<span class="wpte-card__meta-title"><?php \esc_html_e( 'Difficulty', 'wptravelengine-elementor-widgets' ); ?></span>
							<span class="wpte-card__meta-value"><?php echo esc_html( $_meta_data['difficulty'] ); ?></span>
						</div>
					</div>
					<?php
				endif;

				if ( in_array( 'showActivities', $show_meta_data ) && ! empty( $_meta_data['activity'] ) ) :
					?>
					<div class="category trip-act">
						<?php wptravelengineeb_get_icon_by_slug( 'activities' ); ?>
						<div class="wpte-card__meta-content">
							<span class="wpte-card__meta-title"><?php \esc_html_e( 'Activity', 'wptravelengine-elementor-widgets' ); ?></span>
							<span class="wpte-card__meta-value">
								<?php
									echo esc_html( $_meta_data['activity'] );
								if ( gettype( $_meta_data['activity'] ) === 'integer' ) {
									esc_html_e( ' Activities', 'wptravelengine-elementor-widgets' );
								}
								?>
							</span>
						</div>
					</div>
					<?php
				endif;

				if ( in_array( 'showGroupSize', $show_meta_data ) && ! empty( $_meta_data['group-size'] ) ) :
					?>
					<div class="category trip-group">
						<?php wptravelengineeb_get_icon_by_slug( 'group-size' ); ?>
						<div class="wpte-card__meta-content">
							<span class="wpte-card__meta-title"><?php \esc_html_e( 'Group Size', 'wptravelengine-elementor-widgets' ); ?></span>
							<span class="wpte-card__meta-value">
							<?php
							echo esc_html( $_meta_data['group-size'] );
							esc_html_e( ' People', 'wptravelengine-elementor-widgets' );
							?>
							</span>
						</div>
					</div>
					<?php
				endif;

				if ( in_array( 'showAgeGroup', $show_meta_data ) && ! empty( $_meta_data['age-group'] ) ) :
					?>
					<div class="category trip-age">
						<?php wptravelengineeb_get_icon_by_slug( 'maximum-age' ); ?>
						<div class="wpte-card__meta-content">
							<span class="wpte-card__meta-title"><?php \esc_html_e( 'Age Group', 'wptravelengine-elementor-widgets' ); ?></span>
							<span class="wpte-card__meta-value"><?php echo esc_html( $_meta_data['age-group'] ); ?></span>
						</div>
					</div>
					<?php
				endif;

				if ( in_array( 'showTripType', $show_meta_data ) && ! empty( $_meta_data['trip-types'] ) ) :
					?>
					<div class="category trip-act">
						<?php wptravelengineeb_get_icon_by_slug( 'trip_types' ); ?>
						<div class="wpte-card__meta-content">
							<span class="wpte-card__meta-title"><?php \esc_html_e( 'Trip Type', 'wptravelengine-elementor-widgets' ); ?></span>
							<span class="wpte-card__meta-value">
								<?php
									echo esc_html( $_meta_data['trip-types'] );
								if ( gettype( $_meta_data['trip-types'] ) === 'integer' ) {
									esc_html_e( ' Trip Types', 'wptravelengine-elementor-widgets' );
								}
								?>
							</span>
						</div>
					</div>
					<?php
				endif;

				if ( in_array( 'showAltitude', $show_meta_data ) && ! empty( $_meta_data['altitude'] ) ) :
					?>
					<div class="category trip-altitude">
						<?php wptravelengineeb_get_icon_by_slug( 'altitude' ); ?>
						<div class="wpte-card__meta-content">
							<span class="wpte-card__meta-title"><?php \esc_html_e( 'Altitude', 'wptravelengine-elementor-widgets' ); ?></span>
							<span class="wpte-card__meta-value"><?php echo esc_html( $_meta_data['altitude'] ); ?></span>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( $showButton ) : ?>
				<div class="wpte-card__button-wrap">
					<a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>" class="wpte-card__button"><?php echo esc_html( wte_array_get( $settings, 'viewMoreButtonText', __( 'View Details', 'wptravelengine-elementor-widgets' ) ) ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>