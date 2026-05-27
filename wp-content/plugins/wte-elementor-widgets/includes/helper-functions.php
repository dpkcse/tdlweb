<?php
/**
 * Reusuable functions for WP Travel Engine Elementor Widgets.
 */
use WPTravelEngine\Core\Models\Post\Trip as TripData;

/**
 * Convert the html tag to lowercase and return the normalized html tag.
 *
 * @param string $html_tag
 * @return string
 * @since 1.4.3
 */
function wptravelengineeb_normalize_html_tag( $html_tag = 'h3' ): string {
	$html = strtolower( $html_tag );
	return in_array( $html, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ), true ) ? $html : 'h3';
}

/**
 * Get the maximum altitude of the trip.
 *
 * @param array $trip_facts
 * @return string
 */
function wptravelengineeb_get_altitude( $trip_facts ) {
	if ( ! is_array( $trip_facts ) ) {
		return ''; // Return empty string if $trip_facts is not an array
	}

	foreach ( $trip_facts as $fact ) {
		if ( isset( $fact['field_title'] ) && $fact['field_title'] === 'Maximum Altitude' ) {
			return isset( $fact['field_content'] ) ? $fact['field_content'] : '';
		}
	}

	return ''; // Return emptys string if 'Maximum Altitude' is not found
}

/**
 * Get the count of taxonomy terms.
 *
 * @param int    $trip_id
 * @param string $taxonomy
 * @return string|int
 */
function wptravelengineeb_get_tax_count( $trip_id, $taxonomy ) {
	$trip_types_term = wp_get_post_terms( (int) $trip_id, $taxonomy );
	if ( ! is_wp_error( $trip_types_term ) && count( $trip_types_term ) === 1 ) {
		return $trip_types_term[0]->name;
	} else {
		return ! is_wp_error( $trip_types_term ) ? (int) count( $trip_types_term ) : '';
	}
}

/**
 * Return meta data for the trip.
 *
 * @param int    $trip_id
 * @param array  $trip_settings
 * @param string $item
 * @return string
 */
function wptravelengineeb_get_trip_metadata( $trip_id, $item ) {

	if ( class_exists( 'WPTravelEngine\Core\Models\Post\Trip' ) ) {
		$trip_data       = new TripData( $trip_id );
		$group_size      = $trip_data->get_group_size();
		$difficulty_data = $trip_data->get_trip_difficulty_term();
		$difficulty      = isset( $difficulty_data[0] ) ? $difficulty_data[0]['difficulty_name'] : '';
		$trip_facts      = $trip_data->get_trip_facts();
		$altitude        = \wptravelengineeb_get_altitude( $trip_facts );

		// Calculate Age Group
		$group_age   = array();
		$group_age[] = isset( $trip_facts['minimum-age']['field_content'] ) ? (int) $trip_facts['minimum-age']['field_content'] : '';
		$group_age[] = isset( $trip_facts['maximum-age']['field_content'] ) ? (int) $trip_facts['maximum-age']['field_content'] : '';
		$age_data    = ! empty( $group_age ) ? implode( ' - ', $group_age ) : '';

	}

	$data = array(
		'age-group'  => isset( $age_data ) ? $age_data : '',
		'group-size' => isset( $group_size ) ? $group_size : '',
		'difficulty' => isset( $difficulty ) ? $difficulty : '',
		'altitude'   => isset( $altitude ) ? $altitude : '',
		'activity'   => wptravelengineeb_get_tax_count( $trip_id, 'activities' ),
		'trip-types' => wptravelengineeb_get_tax_count( $trip_id, 'trip_types' ),
	);

	return isset( $data[ $item ] ) && $data[ $item ] ? $data[ $item ] : '';
}

/**
 * Display WishList for trips
 *
 * @param int     $trip_id
 * @param boolean $display_wishlist
 * @return void
 */
function wptravelengineeb_get_wishlist( $trip_id ) {

	if ( ! function_exists( 'wptravelengine_user_wishlists' ) ) {
		return;
	}
	$user_wishlists = \wptravelengine_user_wishlists();

	if ( is_array( $user_wishlists ?? '' ) ) {
		$active_class    = in_array( $trip_id, $user_wishlists ) ? ' active' : '';
		$title_attribute = in_array( $trip_id, $user_wishlists ) ? __( 'Already in wishlist', 'wptravelengine-elementor-widgets' ) : __( 'Add to wishlist', 'wptravelengine-elementor-widgets' );
	}

	?>
	
	<div class="category-trips-single">
		<span class="wishlist-title"><?php __( 'Add to wishlist', 'wptravelengine-elementor-widgets' ); ?></span>
		<a class="wishlist-toggle<?php echo esc_attr( $active_class ?? '' ); ?>" data-product="<?php echo esc_attr( $trip_id ); ?>" title="<?php echo esc_attr( $title_attribute ?? '' ); ?>">
			<svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M10 19L8.55 17.7C6.86667 16.1834 5.475 14.875 4.375 13.775C3.275 12.675 2.4 11.6874 1.75 10.812C1.1 9.93736 0.646 9.13336 0.388 8.40002C0.129333 7.66669 0 6.91669 0 6.15002C0 4.58336 0.525 3.27502 1.575 2.22502C2.625 1.17502 3.93333 0.650024 5.5 0.650024C6.36667 0.650024 7.19167 0.833358 7.975 1.20002C8.75833 1.56669 9.43333 2.08336 10 2.75002C10.5667 2.08336 11.2417 1.56669 12.025 1.20002C12.8083 0.833358 13.6333 0.650024 14.5 0.650024C16.0667 0.650024 17.375 1.17502 18.425 2.22502C19.475 3.27502 20 4.58336 20 6.15002C20 6.91669 19.871 7.66669 19.613 8.40002C19.3543 9.13336 18.9 9.93736 18.25 10.812C17.6 11.6874 16.725 12.675 15.625 13.775C14.525 14.875 13.1333 16.1834 11.45 17.7L10 19Z" fill="#C6C6C6" />
			</svg>
		</a>
	</div>
	<?php
}

/**
 * Display icons for the Trip Fact as per the given slug
 *
 * @param string $slug
 * @return void
 */
function wptravelengineeb_get_icon_by_slug( $slug ) {

	switch ( $slug ) {
		case 'altitude':
			?>
			<span class="wpte-card__icon">
				<svg 
					fill="currentColor" 
					data-prefix="fas" 
					data-icon="mountain" 
					xmlns="http://www.w3.org/2000/svg" 
					class="svg-inline--fa" 
					viewBox="0 0 512 512" 
					height="24" 
					width="24">
					<path d="M503.2 393.8L280.1 44.25c-10.42-16.33-37.73-16.33-48.15 0L8.807 393.8c-11.11 17.41-11.75 39.42-1.666 57.45C17.07 468.1 35.92 480 56.31 480h399.4c20.39 0 39.24-11.03 49.18-28.77C514.9 433.2 514.3 411.2 503.2 393.8zM256 111.8L327.8 224H256L208 288L177.2 235.3L256 111.8z"></path>
				</svg>
			</span>
			<?php
			break;
		case 'location':
			?>
			<span class="wpte-card__icon">
				<svg
					data-prefix="fas" 
					data-icon="map-marker"  
					width="12" 
					height="15" 
					viewBox="0 0 12 15" 
					fill="none" 
					xmlns="http://www.w3.org/2000/svg">
					<path d="M6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 10.05 5.2875 14.625 5.5125 14.82C5.64835 14.9362 5.82124 15 6 15C6.17877 15 6.35165 14.9362 6.4875 14.82C6.75 14.625 12 10.05 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0ZM6 13.2375C4.4025 11.7375 1.5 8.505 1.5 6C1.5 4.80653 1.97411 3.66193 2.81802 2.81802C3.66193 1.97411 4.80653 1.5 6 1.5C7.19347 1.5 8.33807 1.97411 9.18198 2.81802C10.0259 3.66193 10.5 4.80653 10.5 6C10.5 8.505 7.5975 11.745 6 13.2375ZM6 3C5.40666 3 4.82664 3.17595 4.33329 3.50559C3.83994 3.83524 3.45542 4.30377 3.22836 4.85195C3.0013 5.40013 2.94189 6.00333 3.05764 6.58527C3.1734 7.16721 3.45912 7.70176 3.87868 8.12132C4.29824 8.54088 4.83279 8.8266 5.41473 8.94236C5.99667 9.05811 6.59987 8.9987 7.14805 8.77164C7.69623 8.54458 8.16477 8.16006 8.49441 7.66671C8.82405 7.17336 9 6.59334 9 6C9 5.20435 8.68393 4.44129 8.12132 3.87868C7.55871 3.31607 6.79565 3 6 3ZM6 7.5C5.70333 7.5 5.41332 7.41203 5.16665 7.2472C4.91997 7.08238 4.72771 6.84811 4.61418 6.57403C4.50065 6.29994 4.47094 5.99834 4.52882 5.70736C4.5867 5.41639 4.72956 5.14912 4.93934 4.93934C5.14912 4.72956 5.41639 4.5867 5.70737 4.52882C5.99834 4.47094 6.29994 4.50065 6.57403 4.61418C6.84811 4.72771 7.08238 4.91997 7.2472 5.16665C7.41203 5.41332 7.5 5.70333 7.5 6C7.5 6.39782 7.34197 6.77936 7.06066 7.06066C6.77936 7.34196 6.39783 7.5 6 7.5Z" fill="currentColor"></path>
				</svg>
			</span>
			<?php
			break;
		case 'duration':
			?>
			<span class="wpte-card__icon">
				<svg width="24" 
					height="25" 
					viewBox="0 0 24 25" 
					fill="none" 
					xmlns="http://www.w3.org/2000/svg">
					<g opacity="1">
					<path d="M21 10.3018H3M16 2.30176V6.30176M8 2.30176V6.30176M7.8 22.3018H16.2C17.8802 22.3018 18.7202 22.3018 19.362 21.9748C19.9265 21.6872 20.3854 21.2282 20.673 20.6637C21 20.022 21 19.1819 21 17.5018V9.10176C21 7.4216 21 6.58152 20.673 5.93979C20.3854 5.3753 19.9265 4.91636 19.362 4.62874C18.7202 4.30176 17.8802 4.30176 16.2 4.30176H7.8C6.11984 4.30176 5.27976 4.30176 4.63803 4.62874C4.07354 4.91636 3.6146 5.3753 3.32698 5.93979C3 6.58152 3 7.4216 3 9.10176V17.5018C3 19.1819 3 20.022 3.32698 20.6637C3.6146 21.2282 4.07354 21.6872 4.63803 21.9748C5.27976 22.3018 6.11984 22.3018 7.8 22.3018Z" stroke="currentColor" stroke-width="1.39" stroke-linecap="round" stroke-linejoin="round"/>
					</g>
				</svg>
			</span>
			<?php
			break;
		default:
			if ( ! function_exists( 'wptravelengine_get_trip_facts_default_options' ) || ! function_exists( 'wptravelengine_svg_by_fa_icon' ) ) {
				return;
			}

			$trip_facts = wptravelengine_get_trip_facts_default_options();

			if ( isset( $trip_facts[ $slug ]['field_icon'] ) ) {
				?>
				<span class="wpte-card__icon">
					<?php wptravelengine_svg_by_fa_icon( $trip_facts[ $slug ]['field_icon'] ); ?>
				</span>
				<?php
			}
			break;
	}
}

function wptravelengineeb_get_rating( $trip_id, $rating_layout = '1' ) {
	if ( ! class_exists( 'Wte_Trip_Review_Init' ) ) {
		return;
	}
	$review_obj              = new Wte_Trip_Review_Init();
	$comment_datas           = $review_obj->pull_comment_data( $trip_id );
	$icon_type               = '';
	$icon_fill_color         = '#F39C12';
	$review_icon_type        = apply_filters( 'trip_rating_icon_type', $icon_type );
	$review_icon_fill_colors = apply_filters( 'trip_rating_icon_fill_color', $icon_fill_color );
	if ( ! empty( $comment_datas ) ) {
		if ( $rating_layout === '1' ) {
			?>
			<div class="wpte-card__rating">
				<div
					class="agg-rating trip-review-stars <?php echo ! empty( $review_icon_type ) ? 'svg-trip-adv' : 'trip-review-default'; ?>"
					data-icon-type='<?php echo esc_attr( $review_icon_type ); ?>'
					data-rating-value="<?php echo esc_attr( $comment_datas['aggregate'] ); ?>"
					data-rateyo-rated-fill="<?php echo esc_attr( $review_icon_fill_colors ); ?>"
					data-rateyo-read-only="true"
				>
				</div>
				<span><?php printf( esc_html( _nx( '%1$s of %2$s review', '%1$s of %2$s reviews', absint( $comment_datas['i'] ), 'review count', 'wptravelengine-elementor-widgets' ) ), '<span>' . $comment_datas['aggregate'] . '</span>', esc_html( number_format_i18n( $comment_datas['i'] ) ) ); ?></span>
			</div>
			<?php
		} else {
			?>
			<div class="wpte-card__rating">
				<svg width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M11 0.672607L14.0949 7.05811L21.125 8.02926L16.0085 12.9466L17.2572 19.932L11 16.5849L4.74191 19.932L5.9915 12.9466L0.875 8.02926L7.90512 7.05811L11 0.672607Z" fill="#FFAE34"/>
				</svg>
				<span>
					<?php
					if ( $rating_layout === '2' ) {
						printf( esc_html( _nx( '%1$s (%2$s review)', '%1$s (%2$s reviews)', absint( $comment_datas['i'] ), 'review count', 'wptravelengine-elementor-widgets' ) ), '<span>' . $comment_datas['aggregate'] . '</span>', esc_html( number_format_i18n( $comment_datas['i'] ) ) );
					} else {
						echo '<span>' . esc_html( $comment_datas['aggregate'] ) . '</span>';
					}
					?>
				</span>
			</div>
			<?php
		}
	}
}

/**
 * Generate a random ID.
 */
if ( ! function_exists( 'wptravelengineeb_rand_md5' ) ) {
	function wptravelengineeb_rand_md5( $slug = null ) {
		if ( $slug ) {
			return md5( $slug );
		}
		return md5( time() . '-' . uniqid( wp_rand(), true ) . '-' . wp_rand() );
	}
}

/**
 * Get custom filter options from WP Travel Engine.
 *
 * @return array Custom filter options for the select control.
 */
function wptravelengineeb_get_custom_filter_options() {
	$custom_filters = \get_option( 'wte_custom_filters', array() );
	$options        = array();

	foreach ( $custom_filters as $slug => $filter ) {
		if ( is_array( $filter ) && isset( $filter['label'] ) ) {
			$options[ $slug ] = $filter['label'];
		}
	}

	// Fallback prompt when there are no custom filters
	if ( empty( $options ) ) {
		$options[''] = \__( 'No Custom Filters found', 'wptravelengine-elementor-widgets' );
	}

	return $options;
}

/**
 * Get all WP Travel Engine taxonomies dynamically.
 *
 * Returns an array of all taxonomies registered for the WP Travel Engine post type,
 * including both default taxonomies and any custom taxonomies created by users.
 *
 * @since 1.4.8
 * @return array Associative array of taxonomy slugs and labels.
 */
function wptravelengineeb_get_trip_taxonomies() {
	// Check if WP Travel Engine is active and post type constant is defined
	if ( ! defined( 'WP_TRAVEL_ENGINE_POST_TYPE' ) ) {
		// Return default taxonomies as fallback
		return array(
			'destination' => __( 'Destination', 'wptravelengine-elementor-widgets' ),
			'activities'  => __( 'Activities', 'wptravelengine-elementor-widgets' ),
			'trip_types'  => __( 'Trip Types', 'wptravelengine-elementor-widgets' ),
			'difficulty'  => __( 'Difficulty', 'wptravelengine-elementor-widgets' ),
			'trip_tag'    => __( 'Trip Tag', 'wptravelengine-elementor-widgets' ),
		);
	}

	// Get all taxonomies registered for WP Travel Engine post type
	$taxonomies = get_object_taxonomies( WP_TRAVEL_ENGINE_POST_TYPE, 'objects' );

	$taxonomy_list = array();

	if ( is_array( $taxonomies ) && ! empty( $taxonomies ) ) {
		foreach ( $taxonomies as $taxonomy_slug => $taxonomy_object ) {
			// Only include public taxonomies
			if ( isset( $taxonomy_object->public ) && $taxonomy_object->public ) {
				$taxonomy_list[ $taxonomy_slug ] = $taxonomy_object->label;
			}
		}
	}

	// If no taxonomies found, return default ones
	if ( empty( $taxonomy_list ) ) {
		return array(
			'destination' => __( 'Destination', 'wptravelengine-elementor-widgets' ),
			'activities'  => __( 'Activities', 'wptravelengine-elementor-widgets' ),
			'trip_types'  => __( 'Trip Types', 'wptravelengine-elementor-widgets' ),
			'difficulty'  => __( 'Difficulty', 'wptravelengine-elementor-widgets' ),
			'trip_tag'    => __( 'Trip Tag', 'wptravelengine-elementor-widgets' ),
		);
	}

	return $taxonomy_list;
}

/**
 * Get allowed HTML tags for trip content including iframes.
 *
 * Extends wp_kses_post() to allow iframe tags for embedding videos, maps, and other rich media.
 * Maintains security by allow-listing specific iframe attributes only.
 *
 * @since 1.5.1
 * @return array Allowed HTML tags and attributes.
 */
function wptravelengineeb_kses_allowed_html() {
	$allowed_html = wp_kses_allowed_html( 'post' );

	// Add iframe support with security attributes
	$allowed_html['iframe'] = array(
		'src'             => true,
		'width'           => true,
		'height'          => true,
		'frameborder'     => true,
		'allow'           => true,
		'allowfullscreen' => true,
		'title'           => true,
		'class'           => true,
		'id'              => true,
		'style'           => true,
		'loading'         => true,
		'name'            => true,
		'sandbox'         => true,
		'srcdoc'          => true,
		'referrerpolicy'  => true,
	);

	return $allowed_html;
}