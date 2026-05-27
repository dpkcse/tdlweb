<?php
/**
 * Itinerary Widget Template
 *
 * @since 1.3.0
 * @package wptravelengine-elementor-widgets
 */

global $post;

// Use trip ID passed from widget if available (handles Elementor editor context correctly)
// The $wte_trip_id variable is set by the widget's render() method using Elementor's document API
if ( isset( $wte_trip_id ) && $wte_trip_id > 0 ) {
	$trip_id = $wte_trip_id;
} elseif ( isset( $post->ID ) ) {
	$trip_id = $post->ID;
} else {
	$trip_id = get_the_ID();
}

$_tabs           = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
$show_tab_titles = apply_filters( 'wpte_show_tab_titles_inside_tabs', true );

if ( ! $show_tab_titles ) {
	return;
}

$tab_title                 = isset( $_tabs['trip_itinerary_title'] ) && ! empty( $_tabs['trip_itinerary_title'] ) ? $_tabs['trip_itinerary_title'] : '';
$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings', false );

// get the attributes from the widget.
$show_title      = isset( $attributes['show_title'] ) ? $attributes['show_title'] : 'yes';
$html_tag        = wptravelengineeb_normalize_html_tag( $attributes['html_tag'] ?? 'h3' );
$show_expand_all = isset( $attributes['show_expand_all'] ) ? $attributes['show_expand_all'] : 'yes';
$expand_all_text = isset( $attributes['expand_all_text'] ) ? $attributes['expand_all_text'] : '';
$expand_all      = isset( $attributes['expand_all'] ) ? $attributes['expand_all'] : 'yes';
$first_day_icon  = isset( $attributes['first_day_icon']['value'] ) && ! empty( $attributes['first_day_icon']['value'] ) ? 'has-custom-icon' : '';
$last_day_icon   = isset( $attributes['last_day_icon']['value'] ) && ! empty( $attributes['last_day_icon']['value'] ) ? ' has-custom-icon' : '';
$expand_on_icon  = isset( $attributes['expand_on_icon']['value'] ) && ! empty( $attributes['expand_on_icon']['value'] ) ? 'custom-expand-on-icon' : '';
$expand_off_icon = isset( $attributes['expand_off_icon']['value'] ) && ! empty( $attributes['expand_off_icon']['value'] ) ? ' custom-expand-off-icon' : '';
$show_chart      = isset( $attributes['show_chart'] ) ? $attributes['show_chart'] : 'yes';
// Additional itinerary info position.
$additional_itinerary_info_position = $wp_travel_engine_settings['wte_advance_itinerary']['info_display_position'] ?? 'below_title';

?>
	<div id="wte-itinerary" class="wte-itinerary-header-wrapper">
		<div class="wp-travel-engine-itinerary-header">
			<?php
			printf( '<%1$s class="wpte-itinerary-title">%2$s</%1$s>', esc_html( $html_tag ), esc_html( ( $show_title && $tab_title ) ? esc_html( $tab_title ) : '' ) );
			if ( $show_expand_all && ! empty( $_tabs['itinerary'] ) ) {
				?>
			<div class="aib-button-toggle toggle-button expand-all-button">
				<label for="itinerary-toggle-button" class="aib-button-label"><?php echo esc_html( $expand_all_text ); ?></label>
				<input id="itinerary-toggle-button" type="checkbox" class="checkbox" <?php echo '' !== $expand_all ? 'checked' : ''; ?>>
			</div>
			<?php } ?>
		</div>
		<?php
		if ( defined( 'WTEAI_VERSION' ) && $show_chart ) {
			do_action( 'wte_after_itinerary_header' );
		}
		?>
	</div>
<?php

if ( ! defined( 'WTEAI_VERSION' ) ) {
	// Get itinerary data like WP Travel Engine does
	$inner_content = ! empty( $_tabs['itinerary']['itinerary_content_inner'] ) ? $_tabs['itinerary']['itinerary_content_inner'] : ( isset( $_tabs['itinerary']['itinerary_content'] ) ? $_tabs['itinerary']['itinerary_content'] : array() );
	$itineraries   = isset( $_tabs['itinerary']['itinerary_title'] ) ? $_tabs['itinerary']['itinerary_title'] : array();
	?>
	<div class="post-data itinerary wte-trip-itinerary-v2">
		<?php
		if ( empty( $itineraries ) ) {
			return;
		}
		$total_items   = count( $itineraries );
		$current_index = 0;

		// Loop directly over itineraries like WP Travel Engine does
		foreach ( $itineraries as $key => $title ) {
			$is_first = ( $current_index === 0 );
			$is_last  = ( $current_index === $total_items - 1 );
			?>
			<div class="itinerary-row <?php echo ( $expand_all === 'yes' ) ? 'active' : ''; ?>">
				<div class="wte-itinerary-head-wrap">
					<div class="title
					<?php
					echo $is_first ? esc_attr( $first_day_icon ) : '';
					echo $is_last ? esc_attr( $last_day_icon ) : '';
					?>
					">
					<?php
					if ( isset( $attributes['first_day_icon']['value'] ) && '' !== $attributes['first_day_icon']['value'] && $is_first ) {
						echo "<span class='custom-icon'>";
						\Elementor\Icons_Manager::render_icon( $attributes['first_day_icon'] );
						echo '</span>';
					}
					if ( isset( $attributes['last_day_icon']['value'] ) && '' !== $attributes['last_day_icon']['value'] && $is_last ) {
						echo "<span class='custom-icon'>";
						\Elementor\Icons_Manager::render_icon( $attributes['last_day_icon'] );
						echo '</span>';
					}
					// Use custom day label if available, otherwise default to "Day X"
					if ( isset( $_tabs['itinerary']['itinerary_days_label'][ $key ] ) && ! empty( $_tabs['itinerary']['itinerary_days_label'][ $key ] ) ) {
						echo esc_attr( $_tabs['itinerary']['itinerary_days_label'][ $key ] ) . ' : ';
					} else {
						printf( esc_html__( 'Day %s : ', 'wptravelengine-elementor-widgets' ), esc_attr( $key ) );
					}
					?>
					</div>
					<a class="accordion-tabs-toggle <?php echo $expand_all === 'yes' ? 'active' : ''; ?>" href="javascript:void(0);">
						<?php if ( ! empty( $inner_content[ $key ] ) ) : ?>
							<span class="dashicons dashicons-arrow-down custom-toggle-tabs rotator <?php echo $expand_all === 'yes' ? 'open' : ''; ?>
							<?php
							echo esc_attr( $expand_on_icon );
							echo esc_attr( $expand_off_icon );
							?>
							">
							<?php
							if ( isset( $attributes['expand_on_icon']['value'] ) && '' !== $attributes['expand_on_icon']['value'] ) {
								echo "<span class='icon-on'>";
								\Elementor\Icons_Manager::render_icon( $attributes['expand_on_icon'] );
								echo '</span>';
							}
							if ( isset( $attributes['expand_off_icon']['value'] ) && '' !== $attributes['expand_off_icon']['value'] ) {
								echo "<span class='icon-off'>";
								\Elementor\Icons_Manager::render_icon( $attributes['expand_off_icon'] );
								echo '</span>';
							}
							?>
							</span>
						<?php endif; ?>
						<div class="itinerary-title">
							<span>
							<?php
							echo wp_kses(
								$title,
								array(
									'span'   => array(),
									'strong' => array(),
								)
							);
							?>
							</span>
						</div>
					</a>
				</div>

				<div class="itinerary-content <?php echo $expand_all === 'yes' ? 'show' : ''; ?>">
					<div class="content" style="<?php echo empty( $inner_content[ $key ] ) ? 'margin: 0;' : ''; ?>">
						<?php echo wp_kses( isset( $inner_content[ $key ] ) ? $inner_content[ $key ] : '', wptravelengineeb_kses_allowed_html() ); ?>
					</div>
				</div>
			</div>
			<?php
			++$current_index;
		}
		?>
	</div>
	<?php
} else {
	?>
	<div class="post-data itinerary">
		<?php

		$wte_advanced_itinerary = get_post_meta( $trip_id, 'wte_advanced_itinerary', true );
		$arr_keys               = array();
		if ( isset( $_tabs['itinerary'] ) && ! empty( $_tabs['itinerary'] ) ) {
			$arr_keys = array_keys( $_tabs['itinerary']['itinerary_title'] );
		}
		foreach ( $arr_keys as $key => $value ) {
			if ( array_key_exists( $value, $_tabs['itinerary']['itinerary_title'] ) ) {
				?>
		<div id="advanced-itinerary-tabs<?php echo esc_attr( $value ); ?>" data-id="<?php echo esc_attr( $value ); ?>"
			class="itinerary-row advanced-itinerary-row <?php echo ! empty( $expand_all === 'yes' ) ? 'active' : ''; ?>">
			<div class="wte-itinerary-head-wrap">
				<div class="title
				<?php
				echo 0 === $key ? esc_attr( $first_day_icon ) : '';
				echo count( $arr_keys ) - 1 === $key ? esc_attr( $last_day_icon ) : '';
				?>
				">
				<?php
				if ( isset( $attributes['first_day_icon']['value'] ) && '' !== $attributes['first_day_icon']['value'] && 0 === $key ) {
					echo "<span class='custom-icon'>";
					\Elementor\Icons_Manager::render_icon( $attributes['first_day_icon'] );
					echo '</span>';
				}
				if ( isset( $attributes['last_day_icon']['value'] ) && '' !== $attributes['last_day_icon']['value'] && count( $arr_keys ) - 1 === $key ) {
					echo "<span class='custom-icon'>";
					\Elementor\Icons_Manager::render_icon( $attributes['last_day_icon'] );
					echo '</span>';
				}
				?>
					<span class="itinerary-day">
					<?php
					if ( isset( $_tabs['itinerary']['itinerary_days_label'][ $value ] ) && ! empty( $_tabs['itinerary']['itinerary_days_label'][ $value ] ) ) {
						echo $itinerary_days_label_header = esc_attr( $_tabs['itinerary']['itinerary_days_label'][ $value ] );
					} else {
						esc_html_e( 'Day ', 'wptravelengine-elementor-widgets' ) . ' ' . esc_html_e( esc_attr( str_pad( $value, 2, '0', STR_PAD_LEFT ) ), 'wte-advanced-itinerary', 'wptravelengine-elementor-widgets' );
					}
					?>
						:</span>
				</div>
				<a class="accordion-tabs-toggle <?php echo $expand_all === 'yes' ? 'active' : ''; ?>" href="javascript:void(0);">
					<span class="dashicons dashicons-arrow-down custom-toggle-tabs rotator <?php echo $expand_all === 'yes' ? 'open' : ''; ?>
					<?php
					echo esc_attr( $expand_on_icon );
					echo esc_attr( $expand_off_icon );
					?>
				">
					<?php
					if ( isset( $attributes['expand_on_icon']['value'] ) && '' !== $attributes['expand_on_icon']['value'] ) {
						echo "<span class='icon-on'>";
						\Elementor\Icons_Manager::render_icon( $attributes['expand_on_icon'] );
						echo '</span>';
					}
					if ( isset( $attributes['expand_off_icon']['value'] ) && '' !== $attributes['expand_off_icon']['value'] ) {
						echo "<span class='icon-off'>";
						\Elementor\Icons_Manager::render_icon( $attributes['expand_off_icon'] );
						echo '</span>';
					}
					?>
					</span>
					<div class="itinerary-title">
						<span><?php echo ( isset( $_tabs['itinerary']['itinerary_title'][ $value ] ) ? esc_attr( $_tabs['itinerary']['itinerary_title'][ $value ] ) : '' ); ?></span>
					</div>
				</a>
			</div>
			<div class="itinerary-content <?php echo ( $expand_all == 'yes' ) ? 'show' : ''; ?>">
				<div class="content">
					<?php
					if ( $additional_itinerary_info_position === 'below_title' ) :
						include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/itinerary/additional-info-fields-template.php';
					endif;
					?>
					<p>
					<?php
					if ( isset( $_tabs['itinerary']['itinerary_content_inner'][ $value ] ) && '' !== $_tabs['itinerary']['itinerary_content_inner'][ $value ] ) {
								$content_itinerary = wpautop( $_tabs['itinerary']['itinerary_content_inner'][ $value ] );
					} else {
						$content_itinerary = wpautop( $_tabs['itinerary']['itinerary_content'][ $value ] );
					}
					echo wp_kses( wpautop( $content_itinerary ), wptravelengineeb_kses_allowed_html() );
					?>
							</p>
				</div>
				<?php
				if ( $additional_itinerary_info_position === 'below_description' ) :
					include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/itinerary/additional-info-fields-template.php';
				endif;
				$itinerary_galleries_ids = isset( $wte_advanced_itinerary['advanced_itinerary']['itinerary_image'][ $value ] ) && ! empty( $wte_advanced_itinerary['advanced_itinerary']['itinerary_image'][ $value ] ) ? $wte_advanced_itinerary['advanced_itinerary']['itinerary_image'][ $value ] : '';
				if ( isset( $itinerary_galleries_ids ) && is_array( $itinerary_galleries_ids ) && ! empty( $itinerary_galleries_ids ) ) {
					?>
				<div class="itenary-detail-gallery">
					<?php
					foreach ( $itinerary_galleries_ids as $keys => $_id ) :
						$image_thumbnail = wp_get_attachment_image_src( $_id, 'wteai-gallery-thumbnail' );
						$image_full      = wp_get_attachment_image_src( $_id, 'large' );
						if ( ! empty( $image_thumbnail ) ) :
							?>
						<a class="itinerary-gallery-link" href="<?php echo esc_url( $image_full[0] ); ?>" data-fancybox="itinerary-gallery">
							<img class='itinerary-indv-image' src='<?php echo esc_attr( $image_thumbnail[0] ); ?>'>
						</a>
							<?php
						endif;
					endforeach;
					?>
				</div>
				<?php } ?>
				<?php
				if ( isset( $wte_advanced_itinerary['advanced_itinerary']['itinerary_duration'][ $value ] ) && ! empty( $wte_advanced_itinerary['advanced_itinerary']['itinerary_duration'][ $value ] )
					|| ( isset( $wte_advanced_itinerary['advanced_itinerary']['meals_included'][ $value ] ) )
					|| ( isset( $wte_advanced_itinerary['advanced_itinerary']['sleep_modes'][ $value ] ) && ! empty( $wte_advanced_itinerary['advanced_itinerary']['sleep_modes'][ $value ] ) )
					|| ( isset( $wte_advanced_itinerary['advanced_itinerary']['itinerary_image'][ $value ] ) && ! empty( $wte_advanced_itinerary['advanced_itinerary']['itinerary_image'][ $value ] ) )
					) {
					?>
				<div class="itinerary-detail-additional-info">
					<?php
					if ( isset( $wte_advanced_itinerary['advanced_itinerary']['itinerary_duration'][ $value ] ) && ! empty( $wte_advanced_itinerary['advanced_itinerary']['itinerary_duration'][ $value ] ) ) {
						if ( isset( $wte_advanced_itinerary['advanced_itinerary']['itinerary_duration'][ $value ] ) ) {
							$duration_type_text  = isset( $wte_advanced_itinerary['advanced_itinerary']['itinerary_duration_type'][ $value ] ) ? esc_attr( $wte_advanced_itinerary['advanced_itinerary']['itinerary_duration_type'][ $value ] ) : '';
							$duration_type_text .= ( $wte_advanced_itinerary['advanced_itinerary']['itinerary_duration'][ $value ] > 1 ) ? 's' : '';
						} else {
							$duration_type_text = '';
						}
						?>
					<div class="itinerary-duration">
						<span class="itinierary-icon-wrap"><svg xmlns="http://www.w3.org/2000/svg" width="16.44" height="14.807" viewBox="0 0 16.44 14.807"><g id="time" transform="translate(0)"><path id="Path_23383" data-name="Path 23383" d="M-283.058-26.585h.095c.442,0,.883,0,1.325,0,.08,0,.1-.023.1-.1-.006-.148,0-.3,0-.445a5.067,5.067,0,0,1,.063-.64,5.429,5.429,0,0,1,.153-.77,4.837,4.837,0,0,1,.161-.541,8.685,8.685,0,0,1,.364-.9,9.969,9.969,0,0,1,.544-.911c.1-.16.253-.292.351-.455a3.335,3.335,0,0,1,.475-.535,6.516,6.516,0,0,1,1.077-.92,9.043,9.043,0,0,1,.885-.528,7.044,7.044,0,0,1,1.547-.577c.269-.07.548-.1.822-.154a7.193,7.193,0,0,1,1.413-.068c.169,0,.337.05.507.059a2.536,2.536,0,0,1,.5.078c.139.036.283.053.422.091.242.066.485.131.72.216a6.1,6.1,0,0,1,1.157.539c.273.17.541.347.808.527a2.225,2.225,0,0,1,.3.245c.284.276.558.561.843.836a4.736,4.736,0,0,1,.607.806,6.27,6.27,0,0,1,.673,1.3c.062.166.115.334.169.5s.112.33.15.5c.051.229.086.462.126.694.023.133.043.267.063.4a.652.652,0,0,1,.008.1c0,.231.005.463,0,.694s-.014.472-.038.706a4.532,4.532,0,0,1-.09.476c-.038.181-.068.366-.122.543-.089.3-.181.592-.3.879a7.062,7.062,0,0,1-.408.858c-.164.286-.36.554-.549.826a7.633,7.633,0,0,1-1.137,1.2,5.54,5.54,0,0,1-.925.652,8.162,8.162,0,0,1-1.027.523,10.633,10.633,0,0,1-1.031.342c-.18.055-.366.092-.55.13-.115.024-.232.035-.349.05a11.682,11.682,0,0,1-1.489.032,3.787,3.787,0,0,1-.524-.062c-.157-.022-.313-.051-.47-.077a1.655,1.655,0,0,1-.2-.041c-.321-.1-.649-.179-.961-.3a6.637,6.637,0,0,1-1.266-.638c-.194-.129-.4-.249-.578-.394a6.543,6.543,0,0,1-.537-.49.463.463,0,0,1-.122-.313.887.887,0,0,1,.1-.433.441.441,0,0,1,.283-.211.936.936,0,0,1,.453-.056.3.3,0,0,1,.132.069c.226.176.445.361.676.53a5.6,5.6,0,0,0,1.369.727,4.811,4.811,0,0,0,.459.161c.228.058.46.1.69.146a1.713,1.713,0,0,0,.191.035c.164.015.329.031.493.034.284.005.569.009.854,0a3.244,3.244,0,0,0,.533-.064c.32-.066.641-.131.953-.227a6.234,6.234,0,0,0,.742-.3,5.556,5.556,0,0,0,1.024-.629,5.113,5.113,0,0,0,.558-.505,7.591,7.591,0,0,0,.64-.7,5.234,5.234,0,0,0,.71-1.209,9.23,9.23,0,0,0,.347-1.05,4.675,4.675,0,0,0,.135-.79c.024-.255.008-.514.014-.772a4.477,4.477,0,0,0-.067-.773,5.5,5.5,0,0,0-.267-1.062,5.036,5.036,0,0,0-.52-1.088,8.549,8.549,0,0,0-.612-.867,8.788,8.788,0,0,0-.782-.748,3.173,3.173,0,0,0-.4-.3,5.373,5.373,0,0,0-.994-.551c-.231-.088-.459-.188-.694-.261a6.522,6.522,0,0,0-.652-.153c-.2-.041-.4-.07-.608-.1a.5.5,0,0,0-.08,0c-.16,0-.32,0-.48,0a5.579,5.579,0,0,0-1.116.1,6.441,6.441,0,0,0-.963.26,6.235,6.235,0,0,0-1.543.812,5.93,5.93,0,0,0-.979.9,6.5,6.5,0,0,0-.611.8,4.418,4.418,0,0,0-.492.965c-.106.306-.215.614-.292.928a3.273,3.273,0,0,0-.123.864,4.183,4.183,0,0,1-.034.5c-.008.082.025.1.1.1.439,0,.878,0,1.316,0a.116.116,0,0,1,.076.027c.009.009,0,.049-.018.068q-.265.393-.534.785c-.154.224-.313.444-.464.669-.112.166-.211.341-.322.507-.091.137-.194.266-.288.4-.135.193-.267.389-.4.584-.015.022-.03.044-.045.066-.059.079-.082.079-.135,0-.158-.236-.314-.473-.474-.708-.284-.417-.572-.833-.856-1.25q-.34-.5-.677-1c-.029-.043-.06-.085-.09-.127Z" transform="translate(283.072 34.13)" fill="currentColor"/><path id="Path_23384" data-name="Path 23384" d="M150.372,112.235c0,.584,0,1.168,0,1.752a.193.193,0,0,0,.077.158,4,4,0,0,1,.319.308.2.2,0,0,0,.166.077q1.44,0,2.881,0a.587.587,0,0,1,.554.36.662.662,0,0,1-.46.957,1.212,1.212,0,0,1-.288.033q-1.343,0-2.685,0a.221.221,0,0,0-.193.1,1.345,1.345,0,0,1-.682.455,1.287,1.287,0,0,1-1.12-2.277.194.194,0,0,0,.08-.185q0-1.707,0-3.415a.7.7,0,0,1,.661-.653.661.661,0,0,1,.678.485.435.435,0,0,1,.013.114Q150.372,111.368,150.372,112.235Z" transform="translate(-140.729 -107.341)" fill="currentColor"/></g></svg></span>
						<span><?php echo ( isset( $wte_advanced_itinerary['advanced_itinerary']['itinerary_duration'][ $value ] ) ? esc_attr( $wte_advanced_itinerary['advanced_itinerary']['itinerary_duration'][ $value ] ) : '' ); ?>
							<?php echo esc_html( $duration_type_text ); ?></span>
					</div>
					<?php } ?>
					<?php if ( isset( $wte_advanced_itinerary['advanced_itinerary']['meals_included'][ $value ] ) ) : ?>
					<div class="itinerary-meals">

						<span class="itinierary-icon-wrap"><svg xmlns="http://www.w3.org/2000/svg" width="18.933" height="15.3" viewBox="0 0 18.933 15.3"><g id="tray" transform="translate(0 -26.502)" opacity="0.9"><path id="Path_23622" data-name="Path 23622" d="M21.531,208.212H4.427a.586.586,0,0,0-.509.338.852.852,0,0,0-.033.7l.537,1.341a1.509,1.509,0,0,0,1.356,1.024h14.4a1.509,1.509,0,0,0,1.356-1.024l.537-1.341a.851.851,0,0,0-.033-.7A.586.586,0,0,0,21.531,208.212Z" transform="translate(-3.512 -169.812)" fill="currentColor"/><path id="Path_23623" data-name="Path 23623" d="M18.931,36.377c-.374-3.812-4.2-6.839-8.95-7.044v-.552a1.191,1.191,0,0,0,.766-1.089,1.283,1.283,0,0,0-2.559,0,1.191,1.191,0,0,0,.766,1.089v.552C4.2,29.538.377,32.565,0,36.377a.538.538,0,0,0,.156.43.626.626,0,0,0,.446.184H18.329a.626.626,0,0,0,.447-.183A.539.539,0,0,0,18.931,36.377ZM5.6,32.336a5.025,5.025,0,0,0-2.6,3.017.509.509,0,0,1-.5.358.55.55,0,0,1-.129-.015A.476.476,0,0,1,2,35.112,5.984,5.984,0,0,1,5.09,31.5a.535.535,0,0,1,.7.179A.461.461,0,0,1,5.6,32.336Z" transform="translate(0)" fill="currentColor"/></g></svg></span>
						<?php
						// Empty for default case.
						$before_meal_string = '';
						$before_meal_string = apply_filters( 'wte_filtered_advanced_itinerary_meal_before_text', $before_meal_string );
						echo wp_kses_post( $before_meal_string );
						?>
						<span>
							<?php
							$iti_meals_array = apply_filters(
								'wpte_ai_trip_meals_array',
								array(
									'breakfast' => __( 'Breakfast', 'wptravelengine-elementor-widgets' ),
									'lunch'     => __( 'Lunch', 'wptravelengine-elementor-widgets' ),
									'dinner'    => __( 'Dinner', 'wptravelengine-elementor-widgets' ),
								)
							);

							$cloned_meals_inc = $wte_advanced_itinerary['advanced_itinerary']['meals_included'][ $value ];
							$count            = count( $cloned_meals_inc );
							$i                = 1;
							$selected_meals   = array_map( 'strtolower', $cloned_meals_inc );
							foreach ( $selected_meals as $kkey => $vval ) :
								if ( in_array( $vval, $cloned_meals_inc ) ) {
									echo esc_html( $iti_meals_array[ $vval ] );
									if ( $i < $count && $i !== $count ) :
										echo ', ';
										endif;
								}
								++$i;
							endforeach;
							?>
						</span>
					</div>
					<?php endif; ?>
					<?php if ( isset( $wte_advanced_itinerary['advanced_itinerary']['sleep_modes'][ $value ] ) && ! empty( $wte_advanced_itinerary['advanced_itinerary']['sleep_modes'][ $value ] ) ) { ?>
					<div class="itinerary-sleep-mode">
						<span class="itinierary-icon-wrap"><svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 11V8M21 8H1M21 8H11V3H18C18.7957 3 19.5587 3.31607 20.1213 3.87868C20.6839 4.44129 21 5.20435 21 6V8ZM1 2V11M4 3C4 3.53043 4.21071 4.03914 4.58579 4.41421C4.96086 4.78929 5.46957 5 6 5C6.53043 5 7.03914 4.78929 7.41421 4.41421C7.78929 4.03914 8 3.53043 8 3C8 2.46957 7.78929 1.96086 7.41421 1.58579C7.03914 1.21071 6.53043 1 6 1C5.46957 1 4.96086 1.21071 4.58579 1.58579C4.21071 1.96086 4 2.46957 4 3Z" stroke="currentColor" stroke-width="1.39" stroke-linecap="round" stroke-linejoin="round"/></svg></span> <span class="label">
							<?php
							if ( isset( $wte_advanced_itinerary['advanced_itinerary']['itinerary_sleep_mode_description'][ $value ] ) && $wte_advanced_itinerary['advanced_itinerary']['itinerary_sleep_mode_description'][ $value ] != '' ) {
								echo '<a href="JavaScript:void(0);">' . esc_attr( $wte_advanced_itinerary['advanced_itinerary']['sleep_modes'][ $value ] ) . '<span>';
								echo '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M20 10C20 15.5228 15.5228 20 10 20C4.47715 20 0 15.5228 0 10C0 4.47715 4.47715 0 10 0C15.5228 0 20 4.47715 20 10ZM10 5C9.44771 5 9 5.44772 9 6C9 6.55228 9.44771 7 10 7H10.01C10.5623 7 11.01 6.55228 11.01 6C11.01 5.44772 10.5623 5 10.01 5H10ZM11 10C11 9.44772 10.5523 9 10 9C9.44771 9 9 9.44772 9 10V14C9 14.5523 9.44771 15 10 15C10.5523 15 11 14.5523 11 14V10Z" fill="currentColor"/></svg>';
								echo '</span></a>';
							} else {
								echo esc_attr( $wte_advanced_itinerary['advanced_itinerary']['sleep_modes'][ $value ] );
							}
							?>
						</span>
					</div>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
				<?php if ( isset( $wte_advanced_itinerary['advanced_itinerary']['itinerary_sleep_mode_description'][ $value ] ) && '' !== $wte_advanced_itinerary['advanced_itinerary']['itinerary_sleep_mode_description'][ $value ] ) { ?>
			<div class="content-additional-sleep-mode" id="content-additional-sleep-mode-<?php echo esc_attr( $value ); ?>"
				style="display:none;">
				<div class="additional-sleep-mode-inner">
					<a href="javascript:void(0);"
						class="wte-ai-close-button"><?php esc_html_e( 'Close', 'wptravelengine-elementor-widgets' ); ?></a>
					<div class="advanced-sleep-mode-content">
						<p>
							<?php
							if ( isset( $wte_advanced_itinerary['advanced_itinerary']['itinerary_sleep_mode_description'][ $value ] ) && '' !== $wte_advanced_itinerary['advanced_itinerary']['itinerary_sleep_mode_description'][ $value ] ) {
								$content_sleep_mode = $wte_advanced_itinerary['advanced_itinerary']['itinerary_sleep_mode_description'][ $value ];
							} else {
								$content_sleep_mode = '';
							}
							echo wp_kses_post( wpautop( $content_sleep_mode ) );
							?>
						</p>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
				<?php
			}
		}
		?>
	</div>
	<div class="wte-ai-overlay"></div>
	<?php
}
?>