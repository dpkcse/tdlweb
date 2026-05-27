<?php
/**
 * Upcoming Tours Content.
 *
 * @package wp-travel-engine
 * @since 6.4.3
 *
 * @var array $trips
 * @var int $count
 * @var bool $show_more_btn
 * @var bool $show_less_btn
 */
?>
<div class="wpte-upcoming-tours-list" data-items-count="<?php echo esc_attr( $count ); ?>">
	<?php if ( count( $trips ) === 0 ) : ?>
		<div class="wpte-not-found">
			<span class="wpte-not-found-title"><?php esc_html_e( 'No upcoming tours found', 'wp-travel-engine' ); ?></span>
			<span class="wpte-not-found-text"><?php esc_html_e( 'Try adjusting your search or filters.', 'wp-travel-engine' ); ?></span>
			<button class="wpte-clear-filters" type="button"><?php esc_html_e( 'Clear All Filters', 'wp-travel-engine' ); ?></button>
		</div>
		<?php
		else :
			foreach ( $trips as $key => $trip ) :
				?>
			<!-- Trip card component for upcoming tours -->
			<div class="wpte-trip-card">
				<div class="wpte-trip-date-info">
					<div class="wpte-trip-date">
						<span class="wpte-month"><?php echo esc_html( $trip['datetime']['month'] ); ?></span>
						<span class="wpte-day"><?php echo esc_html( $trip['datetime']['day'] ); ?></span>
						<span class="wpte-year"><?php echo esc_html( $trip['datetime']['year'] ); ?></span>
					</div>
					<div class="wpte-trip-time-travelers">
						<?php if ( ! empty( $trip['datetime']['time'] ) ) : ?>
							<div class="wpte-trip-time">
								<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M7.99998 4.50001V8.50001L10.6666 9.83334M14.6666 8.50001C14.6666 12.1819 11.6819 15.1667 7.99998 15.1667C4.31808 15.1667 1.33331 12.1819 1.33331 8.50001C1.33331 4.81811 4.31808 1.83334 7.99998 1.83334C11.6819 1.83334 14.6666 4.81811 14.6666 8.50001Z" stroke="#859094" stroke-width="1.336" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
								<span><?php echo esc_html( $trip['datetime']['time'] ); ?></span>
							</div>
						<?php endif; ?>
							<?php if ( ! empty( $trip['travellers'] ) && $trip['travellers'] > 0 ) : ?>
							<div class="wpte-trip-travelers">
								<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M2 13.8333C3.55719 12.1817 5.67134 11.1667 8 11.1667C10.3287 11.1667 12.4428 12.1817 14 13.8333M11 5.5C11 7.15685 9.65685 8.5 8 8.5C6.34315 8.5 5 7.15685 5 5.5C5 3.84315 6.34315 2.5 8 2.5C9.65685 2.5 11 3.84315 11 5.5Z" stroke="#859094" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
								<span>
								<?php
									$travellers_count = absint( $trip['travellers'] );
									echo esc_html( sprintf( _n( '%d Traveller', '%d Travellers', $travellers_count, 'wp-travel-engine' ), $travellers_count ) );
								?>
								</span>
							</div>
							<?php endif; ?>
							<?php do_action( 'wptravelengine_upcoming_tours_content', $trip ); ?>
					</div>
				</div>
				<div class="wpte-trip-content">
					<div class="wpte-trip-content-wrapper">
						<div class="wpte-trip-img">
							<?php if ( ! empty( $trip['image'] ) ) : ?>
								<img src="<?php echo esc_url( $trip['image'] ); ?>" alt="">
							<?php endif; ?>
						</div>
						<div class="wpte-trip-details">
							<a href="<?php echo esc_url( $trip['permalink'] ); ?>" target="_blank" class="wpte-trip-title"><?php echo esc_html( $trip['title'] ); ?></a>
							<div class="wpte-trip-metas">
								<?php if ( ! empty( $trip['duration'] ) ) : ?>
								<span class="wpte-trip-meta">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><use href="#calendar"></use></svg>
									<?php echo esc_html( $trip['duration'] ); ?>
								</span>
								<?php endif; ?>

								<?php
								// Render destinations with collapsible functionality
								// Extract names from ['slug' => 'Name'] array
								echo WPTravelEngine\Pages\Admin\UpcomingTours::render_collapsible_meta(
									array_values( $trip['destinations'] ?? array() ),
									'marker-pin',
									'destinations'
								);

								// Render activities with collapsible functionality
								// Extract names from ['slug' => 'Name'] array
								echo WPTravelEngine\Pages\Admin\UpcomingTours::render_collapsible_meta(
									array_values( $trip['activities'] ?? array() ),
									'compas',
									'activities'
								);
								?>
							</div>
							<div class="wpte-trip-progress-bars">
								<?php
								if ( ! empty( $trip['bar'] ) ) {
									$bar         = $trip['bar'];
									$is_sold_out = $trip['is_sold_out'];
									?>
									<div class="wpte-trip-progress-bar<?php echo $is_sold_out ? ' wpte-is-booked' : ''; ?>">
										<div class="wpte-trip-progress-labels">
											<span class="wpte-trip-progress-label">
												<?php
												echo $is_sold_out ? esc_html__( 'Sold Out', 'wp-travel-engine' ) : esc_html__( 'Available', 'wp-travel-engine' );
												if ( $bar['has_capacity_adjustment'] ) {
													?>
													<span class="wpte-upcoming-tour-warning-badge" data-content="<?php echo esc_attr( __( 'Total capacity has been recalculated because the trip setup was changed after travelers had already booked.', 'wp-travel-engine' ) ); ?>">
														<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M11.9998 8.99999V13M11.9998 17H12.0098M10.6151 3.89171L2.39019 18.0983C1.93398 18.8863 1.70588 19.2803 1.73959 19.6037C1.769 19.8857 1.91677 20.142 2.14613 20.3088C2.40908 20.5 2.86435 20.5 3.77487 20.5H20.2246C21.1352 20.5 21.5904 20.5 21.8534 20.3088C22.0827 20.142 22.2305 19.8857 22.2599 19.6037C22.2936 19.2803 22.0655 18.8863 21.6093 18.0983L13.3844 3.89171C12.9299 3.10654 12.7026 2.71396 12.4061 2.58211C12.1474 2.4671 11.8521 2.4671 11.5935 2.58211C11.2969 2.71396 11.0696 3.10655 10.6151 3.89171Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
														</svg>
														<?php echo esc_html__( 'Capacity Adjusted', 'wp-travel-engine' ); ?>
													</span>
													<?php
												}
												?>
											</span>
											<span class="wpte-trip-progress-value">
												<?php if ( $bar['capacity'] > 0 ) : ?>
													<strong><?php echo esc_html( $bar['booked_seats'] ); ?></strong> / <?php echo esc_html( $bar['capacity'] ); ?>
												<?php endif; ?>
											</span>
										</div>
										<div class="wpte-trip-progress">
											<div class="wpte-trip-progress-fill" style="width: <?php echo esc_attr( $bar['progress'] ); ?>%;"></div>
										</div>
									</div>
									<?php
								}
								do_action( 'wptravelengine_upcoming_tours_progress_bar', $trip );
								?>
							</div>
						</div>
					</div>
					<?php
					$trip_actions_classes = apply_filters( 'wptravelengine_upcoming_tours_classes', array( 'wpte-trip-actions' ), $trip );
					$trip_actions_classes = implode( ' ', $trip_actions_classes );
					?>
					<div class="<?php echo esc_attr( $trip_actions_classes ); ?>">
						<?php
						$show_view_details_btn = apply_filters( 'wptravelengine_upcoming_tours_show_view_details_btn', true, $trip, $key );
						if ( $show_view_details_btn ) :
							?>
							<button class="wpte-button wpte-solid wpte-btn-view-details" data-id="<?php echo esc_attr( $key ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wte_upcoming_tours_details' ) ); ?>"><?php esc_html_e( 'View Details', 'wp-travel-engine' ); ?></button>
						<?php endif; ?>
						<?php do_action( 'wptravelengine_upcoming_tours_view_details_btn', $trip, $key ); ?>
					</div>
				</div>
			</div>
				<?php
		endforeach;
			if ( $show_more_btn || $show_less_btn ) :
				?>
			<div class="wpte-load-more-btn-wrapper">
				<?php if ( $show_more_btn ) : ?>
					<button class="wpte-button wpte-load-more-btn"><?php esc_html_e( 'Load More', 'wp-travel-engine' ); ?></button>
					<?php
					endif;
				if ( $show_less_btn ) :
					?>
					<button class="wpte-button wpte-load-less-btn" style="display: flex;"><?php esc_html_e( 'Show Less', 'wp-travel-engine' ); ?></button>
				<?php endif; ?>
			</div>
				<?php
		endif;
	endif;
		?>
</div>
