<?php

/**
 * Upcoming Tours Header.
 *
 * @since 6.4.3
 *
 * @var array $dates
 * @var array $valid_statuses
 * @var array $destinations
 * @var array $activities
 */
?>

<svg width="0" height="0" class="hidden">
	<symbol id="calendar" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M21 10H3M16 2V6M8 2V6M7.8 22H16.2C17.8802 22 18.7202 22 19.362 21.673C19.9265 21.3854 20.3854 20.9265 20.673 20.362C21 19.7202 21 18.8802 21 17.2V8.8C21 7.11984 21 6.27976 20.673 5.63803C20.3854 5.07354 19.9265 4.6146 19.362 4.32698C18.7202 4 17.8802 4 16.2 4H7.8C6.11984 4 5.27976 4 4.63803 4.32698C4.07354 4.6146 3.6146 5.07354 3.32698 5.63803C3 6.27976 3 7.11984 3 8.8V17.2C3 18.8802 3 19.7202 3.32698 20.362C3.6146 20.9265 4.07354 21.3854 4.63803 21.673C5.27976 22 6.11984 22 7.8 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
	</symbol>
	<symbol id="marker-pin" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		<path d="M12 22C16 18 20 14.4183 20 10C20 5.58172 16.4183 2 12 2C7.58172 2 4 5.58172 4 10C4 14.4183 8 18 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
	</symbol>
	<symbol id="compas" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		<path d="M14.7221 8.26596C15.2107 8.10312 15.4549 8.02169 15.6174 8.07962C15.7587 8.13003 15.87 8.24127 15.9204 8.38263C15.9783 8.54507 15.8969 8.78935 15.734 9.27789L14.2465 13.7405C14.2001 13.8797 14.1769 13.9492 14.1374 14.007C14.1024 14.0582 14.0582 14.1024 14.007 14.1374C13.9492 14.1769 13.8797 14.2001 13.7405 14.2465L9.27789 15.734C8.78935 15.8969 8.54507 15.9783 8.38263 15.9204C8.24127 15.87 8.13003 15.7587 8.07962 15.6174C8.02169 15.4549 8.10312 15.2107 8.26596 14.7221L9.75351 10.2595C9.79989 10.1203 9.82308 10.0508 9.8626 9.99299C9.8976 9.94182 9.94182 9.8976 9.99299 9.8626C10.0508 9.82308 10.1203 9.79989 10.2595 9.75351L14.7221 8.26596Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
	</symbol>
</svg>

<header class="wpte-upcoming-tours-header">
	<h1 class="wpte-upcoming-tours-header-title"><?php esc_html_e( 'Upcoming Tours', 'wp-travel-engine' ); ?></h1>
	<div class="wpte-dates-filter" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wte_filter_upcoming_tours' ) ); ?>">
		<div class="wpte-field">
			<select id="wpte-filter-date-select" class="wpte-filter-date-select">
				<option value="<?php echo esc_attr( 'all' ); ?>"><?php esc_html_e( 'All', 'wp-travel-engine' ); ?></option>
				<option value="<?php echo esc_attr( wp_json_encode( $dates['today'] ) ); ?>"><?php esc_html_e( 'Today', 'wp-travel-engine' ); ?></option>
				<option value="<?php echo esc_attr( wp_json_encode( $dates['this_week'] ) ); ?>"><?php esc_html_e( 'This Week', 'wp-travel-engine' ); ?></option>
				<option value="<?php echo esc_attr( wp_json_encode( $dates['next_15_days'] ) ); ?>"><?php esc_html_e( 'Next 15 Days', 'wp-travel-engine' ); ?></option>
				<option value="<?php echo esc_attr( wp_json_encode( $dates['this_month'] ) ); ?>"><?php esc_html_e( 'This Month', 'wp-travel-engine' ); ?></option>
				<option value="<?php echo esc_attr( wp_json_encode( $dates['next_month'] ) ); ?>"><?php esc_html_e( 'Next Month', 'wp-travel-engine' ); ?></option>
				<option value="<?php echo esc_attr( wp_json_encode( $dates['this_year'] ) ); ?>"><?php esc_html_e( 'This Year', 'wp-travel-engine' ); ?></option>
				<option value="<?php echo esc_attr( wp_json_encode( $dates['next_year'] ) ); ?>"><?php esc_html_e( 'Next Year', 'wp-travel-engine' ); ?></option>
			</select>
		</div>
		<div class="wpte-date-range">
			<span class="wpte-date-icon">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M17.5 8.33333H2.5M13.3333 1.66667V5M6.66667 1.66667V5M6.5 18.3333H13.5C14.9001 18.3333 15.6002 18.3333 16.135 18.0609C16.6054 17.8212 16.9878 17.4387 17.2275 16.9683C17.5 16.4335 17.5 15.7335 17.5 14.3333V7.33333C17.5 5.9332 17.5 5.23314 17.2275 4.69836C16.9878 4.22795 16.6054 3.8455 16.135 3.60582C15.6002 3.33333 14.9001 3.33333 13.5 3.33333H6.5C5.09987 3.33333 4.3998 3.33333 3.86502 3.60582C3.39462 3.8455 3.01217 4.22795 2.77248 4.69836C2.5 5.23314 2.5 5.9332 2.5 7.33333V14.3333C2.5 15.7335 2.5 16.4335 2.77248 16.9683C3.01217 17.4387 3.39462 17.8212 3.86502 18.0609C4.3998 18.3333 5.09987 18.3333 6.5 18.3333Z" stroke="#859094" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</span>
			<input id="wpte-custom-filter-date" class="wte-flatpickr" placeholder="Select Date Range">
		</div>
	</div>
</header>
<div class="wpte-upcoming-tours-search-area">
	<div class="wpte-field wpte-upcoming-tours-header-left">
		<input id="wpte-upcoming-tours-search" type="search" placeholder="<?php esc_attr_e( 'Search Trips', 'wp-travel-engine' ); ?>" />
	</div>
	<div class="wpte-field wpte-upcoming-tours-header-right">
		<?php
		$filters = array(
			array(
				'id'         => 'status',
				'aria_label' => __( 'Filter by status', 'wp-travel-engine' ),
				'options'    => $valid_statuses,
				'show_all'   => false,
				'display'    => class_exists( 'WTE_Fixed_Departure_Dates\Frontend\HookCallbacks' ) && method_exists( 'WTE_Fixed_Departure_Dates\Frontend\HookCallbacks', 'get_data_of' ),
			),
			array(
				'id'         => 'destination',
				'aria_label' => __( 'Filter by destination', 'wp-travel-engine' ),
				'options'    => $destinations,
				'show_all'   => true,
				'all_label'  => __( 'All Destinations', 'wp-travel-engine' ),
				'display'    => true,
			),
			array(
				'id'         => 'activity',
				'aria_label' => __( 'Filter by activity', 'wp-travel-engine' ),
				'options'    => $activities,
				'show_all'   => true,
				'all_label'  => __( 'All Activities', 'wp-travel-engine' ),
				'display'    => true,
			),
		);

		foreach ( $filters as $filter ) :
			if ( $filter['display'] === false ) {
				continue;
			}
			?>
			<select id="<?php echo esc_attr( $filter['id'] ); ?>" aria-label="<?php echo esc_attr( $filter['aria_label'] ); ?>">
				<?php if ( $filter['show_all'] ) : ?>
					<option value=""><?php echo esc_html( $filter['all_label'] ); ?></option>
				<?php endif; ?>
				<?php if ( ! empty( $filter['options'] ) ) : ?>
					<?php foreach ( $filter['options'] as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		<?php endforeach; ?>
		<button class="wpte-clear-filters" type="button">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M2 10C2 10 4.00498 7.26822 5.63384 5.63824C7.26269 4.00827 9.5136 3 12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21C7.89691 21 4.43511 18.2543 3.35177 14.5M2 10V4M2 10H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
			<?php esc_html_e( 'Reset', 'wp-travel-engine' ); ?>
		</button>
	</div>
</div>
