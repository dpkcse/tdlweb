<?php
/**
 * Upcoming Tours Admin Page.
 *
 * @package WPTravelEngine\Pages\Admin
 * @since 6.4.3
 */

namespace WPTravelEngine\Pages\Admin;

use WPTravelEngine\Interfaces\AdminPage;
use WPTravelEngine\Core\Models\Post\Trip;

/**
 * Upcoming Tours Class
 */
class UpcomingTours implements AdminPage {

	/**
	 * Parent slug.
	 *
	 * @var string
	 */
	public string $parent_slug;

	/**
	 * Page title.
	 *
	 * @var string
	 */
	public string $page_title;

	/**
	 * Menu title.
	 *
	 * @var string
	 */
	public string $menu_title;

	/**
	 * Capability.
	 *
	 * @var string
	 */
	public string $capability;

	/**
	 * Position.
	 *
	 * @var int
	 */
	public int $position;

	/**
	 * Cached cache version for the current request (avoids repeated get_transient calls).
	 *
	 * @var int|null
	 */
	private static ?int $cache_version = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->parent_slug = 'edit.php?post_type=booking';
		$this->page_title  = __( 'Upcoming Tours', 'wp-travel-engine' );
		$this->menu_title  = __( 'Upcoming Tours', 'wp-travel-engine' );
		$this->capability  = 'manage_options';
		$this->position    = 1;
	}

	/**
	 * Register hooks that clear the upcoming tours transient cache.
	 *
	 * @return void
	 * @since 6.7.10
	 */
	public static function register_cache_hooks() {
		add_action( 'wptravelengine.booking.created', array( __CLASS__, 'clear_cache' ), 10 );
		add_action( 'wptravelengine.booking.updated', array( __CLASS__, 'clear_cache' ), 10 );
		add_action( 'created_term', array( __CLASS__, 'clear_cache' ), 10 );
		add_action( 'update_option_WPLANG', array( __CLASS__, 'clear_cache' ), 10 );
	}

	/**
	 * Render the page of Upcoming Tours.
	 */
	public function view() {
		wp_enqueue_script( 'wptravelengine-upcoming-tours' );
		wptravelengine_get_admin_template( 'upcoming-tours/index.php', self::get_template_args() );
	}

	/**
	 * Get upcoming tours html.
	 *
	 * @param array $args Arguments.
	 *
	 * @return string
	 */
	public static function get_upcoming_tours_html( array $args = array() ) {
		ob_start();
		wptravelengine_get_admin_template( 'upcoming-tours/partials/content.php', self::get_template_args( $args ) );

		return ob_get_clean();
	}

	/**
	 * Get formatted date.
	 * Uses UTC for output so the displayed time does not change when WordPress timezone is changed.
	 *
	 * @param string $datetime Datetime.
	 *
	 * @return array
	 */
	public static function get_formatted_date( $datetime ) {
		if ( ! is_string( $datetime ) || '' === trim( $datetime ) ) {
			return array(
				'date_time' => '',
				'year'      => '',
				'month'     => '',
				'day'       => '',
				'time'      => '',
			);
		}
		$time_stamp = strtotime( $datetime );
		if ( false === $time_stamp ) {
			return array(
				'date_time' => '',
				'year'      => '',
				'month'     => '',
				'day'       => '',
				'time'      => '',
			);
		}
		$tz_utc = new \DateTimeZone( 'UTC' );
		$time   = strpos( $datetime, 'T' ) !== false ? wp_date( get_option( 'time_format', 'g:i a' ), $time_stamp, $tz_utc ) : '';

		return array(
			'date_time' => wp_date( 'M d, Y', $time_stamp, $tz_utc ) . $time,
			'year'      => wp_date( 'Y', $time_stamp, $tz_utc ),
			'month'     => wp_date( 'M', $time_stamp, $tz_utc ),
			'day'       => wp_date( 'd', $time_stamp, $tz_utc ),
			'time'      => $time,
		);
	}

	/**
	 * Get filtered terms.
	 *
	 * @param string $taxonomy Taxonomy.
	 *
	 * @return array
	 */
	public static function get_filtered_terms( $taxonomy ) {
		$cache_key = 'wptravelengine_upcoming_tours_terms_' . $taxonomy . '_' . self::get_cache_version();

		// Try to get from cache first
		$cached = get_transient( $cache_key );
		if ( false !== $cached ) {
			return $cached;
		}

		$terms = get_terms(
			array(
				'taxonomy' => $taxonomy,
			)
		);

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return array();
		}

		$grouped_terms = array();
		$term_filtered = array();

		// Group terms by parent
		foreach ( $terms as $term ) {
			$grouped_terms[ $term->parent ][] = $term;
		}
		if ( isset( $grouped_terms[0] ) ) {
			foreach ( $grouped_terms[0] as $parent ) {
				$term_filtered[ $parent->slug ] = $parent->name;
				if ( isset( $grouped_terms[ $parent->term_id ] ) ) {

					foreach ( $grouped_terms[ $parent->term_id ] as $child ) {
						$term_filtered[ $child->slug ] = '- ' . $child->name;
					}
				}
			}
		}

		// Cache for 1 hour (terms change infrequently)
		set_transient( $cache_key, $term_filtered, HOUR_IN_SECONDS );

		return $term_filtered;
	}

	/**
	 * Get filtered dates For Header Filter Buttons.
	 *
	 * @return array
	 */
	public static function get_filtered_dates() {
		return array(
			'today'        => array(
				'from' => wp_date( 'Y-m-d' ),
				'to'   => wp_date( 'Y-m-d' ),
			),
			'this_week'    => array(
				'from' => wp_date( 'Y-m-d', strtotime( 'last sunday' ) ),
				'to'   => wp_date( 'Y-m-d', strtotime( 'next saturday' ) ),
			),
			'next_15_days' => array(
				'from' => wp_date( 'Y-m-d' ),
				'to'   => wp_date( 'Y-m-d', strtotime( '+15 days' ) ),
			),
			'this_month'   => array(
				'from' => wp_date( 'Y-m-d', strtotime( 'first day of this month' ) ),
				'to'   => wp_date( 'Y-m-d', strtotime( 'last day of this month' ) ),
			),
			'next_month'   => array(
				'from' => wp_date( 'Y-m-d', strtotime( 'first day of next month' ) ),
				'to'   => wp_date( 'Y-m-d', strtotime( 'last day of next month' ) ),
			),
			'this_year'    => array(
				'from' => wp_date( 'Y-m-d', strtotime( 'first day of january this year' ) ),
				'to'   => wp_date( 'Y-m-d', strtotime( 'last day of december this year' ) ),
			),
			'next_year'    => array(
				'from' => wp_date( 'Y-m-d', strtotime( 'first day of january next year' ) ),
				'to'   => wp_date( 'Y-m-d', strtotime( 'last day of december next year' ) ),
			),
		);
	}

	/**
	 * Get filtered statuses.
	 *
	 * @return array
	 * @since 6.7.10
	 */
	public static function get_filtered_statuses() {
		return apply_filters(
			'wptravelengine_upcoming_tours_status_options',
			array(
				'all'          => __( 'All Status (Default)', 'wp-travel-engine' ),
				'available'    => __( 'Available', 'wp-travel-engine' ),
				'fully_booked' => __( 'Fully Booked', 'wp-travel-engine' ),
			)
		);
	}

	/**
	 * Get template args.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 * @since 6.7.10
	 */
	public static function get_template_args( array $args = array() ) {
		$request = wp_parse_args( // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_wp_parse_args
			$args,
			array(
				'date'        => 'all',
				'count'       => 10,
				'offset'      => 0,
				'status'      => 'all',
				'keywords'    => '',
				'destination' => '',
				'activity'    => '',
			)
		);
		$trips   = array();

		$status      = $request['status'];
		$keywords    = $request['keywords'];
		$destination = $request['destination'];
		$activity    = $request['activity'];

		// Parse date filter for database query
		$booking_args = array();
		if ( isset( $request['date'] ) && $request['date'] !== 'all' ) {
			$date_range = json_decode( stripslashes( $request['date'] ), true );
			if ( is_array( $date_range ) && isset( $date_range['from'], $date_range['to'] ) ) {
				$booking_args['date_from'] = $date_range['from'];
				$booking_args['date_to']   = $date_range['to'];
			}
		}

		$trips_cache_key = 'wptravelengine_upcoming_tours_trips_' . self::get_cache_version() . '_' . md5( wp_json_encode( $booking_args ) );
		$trips           = get_transient( $trips_cache_key );
		if ( false === $trips ) {
			$trips = array();
		}

		if ( empty( $trips ) && in_array( $status, array_keys( self::get_filtered_statuses() ), true ) ) {
			// get_post_bookings() already primes the booking meta cache
			$bookings = self::get_post_bookings( $booking_args );

			$trip_ids     = array();
			$booking_data = array();
			foreach ( $bookings as $booking ) {
				$trip_id       = $booking['trip_id'];
				$trip_datetime = $booking['trip_datetime'];

				$trip_ids[] = $trip_id;

				$u_id = $trip_id . '_' . $trip_datetime;

				if ( ! isset( $booking_data[ $u_id ] ) ) {
					$booking_data[ $u_id ] = array(
						'trip_id'       => $trip_id,
						'trip_datetime' => $trip_datetime,
						'travellers'    => 0,
					);
				}
				$booking_data[ $u_id ]['travellers'] += $booking['travellers'];
			}

			$unique_trip_ids = array_unique( $trip_ids );
			if ( ! empty( $unique_trip_ids ) ) {
				update_postmeta_cache( $unique_trip_ids );

				update_object_term_cache( $unique_trip_ids, 'trip' );
			}

			$trip_instances = array();
			foreach ( $booking_data as $u_id => $data ) {
				$trip_id       = $data['trip_id'];
				$trip_datetime = $data['trip_datetime'];
				$travellers    = $data['travellers'];

				if ( isset( $trips[ $u_id ] ) ) {
					// Trip already exists, just add travellers
					$trips[ $u_id ]['travellers'] += $travellers;
					continue;
				}

				if ( ! isset( $trip_instances[ $trip_id ] ) ) {
					try {
						$trip_instances[ $trip_id ] = new Trip( $trip_id );
					} catch ( \Exception $e ) {
						continue;
					}
				}
				$trip = $trip_instances[ $trip_id ];

				$destinations_data = array();
				$destinations      = get_the_terms( $trip_id, 'destination' );
				if ( is_array( $destinations ) ) {
					foreach ( $destinations as $term ) {
						$destinations_data[ $term->slug ] = $term->name;
					}
				}

				$activities_data = array();
				$activities      = get_the_terms( $trip_id, 'activities' );
				if ( is_array( $activities ) ) {
					foreach ( $activities as $term ) {
						$activities_data[ $term->slug ] = $term->name;
					}
				}

				// Calculation of Booking Seats for Booking Progress Bar.
				$capacity_data = $trip->get_trip_capacity( $trip_datetime );
				$bar           = null;
				$is_sold_out   = false;

				if ( false !== $capacity_data && $travellers > 0 ) {
					if ( ! empty( $capacity_data ) ) {
						$capacity     = $capacity_data['total']['capacity'] ?? '';
						$booked_seats = ( (int) $capacity_data['total']['booked_seats'] === $travellers ) ? (int) $capacity_data['total']['booked_seats'] : $travellers;
					} else {
						$capacity = $booked_seats = $travellers;
					}
					$is_unlimited = '' === $capacity || (int) $capacity <= 0;
					$is_sold_out  = ! $is_unlimited && (int) $capacity > 0 && $booked_seats >= (int) $capacity;

					$bar = array(
						'capacity'                => $is_unlimited ? '' : max( (int) $capacity, (int) $booked_seats ),
						'booked_seats'            => $booked_seats,
						'progress'                => $is_unlimited ? 100 : ( $is_sold_out ? 100 : min( 100, round( $booked_seats / (int) $capacity * 100 ) ) ),
						'has_capacity_adjustment' => ! $is_unlimited && ( (int) $capacity < (int) $travellers || ( $capacity_data['includes_deleted'] ?? false ) ),
					);
				}

				$trips[ $u_id ] = array(
					'trip_id'      => $trip_id,
					'permalink'    => $trip->get_permalink(),
					'title'        => $trip->get_title(),
					'timestamp'    => strtotime( $trip_datetime ),
					'datetime'     => self::get_formatted_date( $trip_datetime ),
					'travellers'   => $travellers,
					'image'        => $trip->get_gallery_images()[0]['src'] ?? '',
					'duration'     => self::get_trip_duration( $trip ),
					'destinations' => $destinations_data,
					'activities'   => $activities_data,
					'is_sold_out'  => $is_sold_out,
					'bar'          => $bar,
				);
			}

			// Cache the complete trips data for 15 minutes
			set_transient( $trips_cache_key, $trips, 15 * MINUTE_IN_SECONDS );
		}

		/**
		 * Allow plugins to modify trips array (e.g., add custom entries, filter by status, etc.).
		 *
		 * @param array $trips Trips array.
		 * @param string $status Current status filter.
		 * @param array $request Request arguments.
		 *
		 * @return array Modified trips array.
		 * @since 6.7.0
		 */
		$trips = apply_filters( 'wptravelengine_upcoming_tours_modify_trips', $trips, $status, $request );

		// Apply search filter
		if ( ! empty( $keywords ) ) {
			$trips = array_filter(
				$trips,
				function ( $trip ) use ( $keywords ) {
					return stripos( $trip['title'], $keywords ) !== false;
				}
			);
		}

		// Apply destination filter (check if slug exists as key)
		if ( ! empty( $destination ) ) {
			$trips = array_filter(
				$trips,
				function ( $trip ) use ( $destination ) {
					return isset( $trip['destinations'][ $destination ] );
				}
			);
		}

		// Apply activity filter (check if slug exists as key)
		if ( ! empty( $activity ) ) {
			$trips = array_filter(
				$trips,
				function ( $trip ) use ( $activity ) {
					return isset( $trip['activities'][ $activity ] );
				}
			);
		}

		// Apply status filter
		if ( $status !== 'all' ) {
			$trips = array_filter(
				$trips,
				function ( $trip ) use ( $status ) {
					switch ( $status ) {
						case 'available':
							return ! ( $trip['is_sold_out'] ?? false );

						case 'fully_booked':
							return $trip['is_sold_out'] ?? false;

						default:
							return true;
					}
				}
			);
		}

		foreach ( array_keys( $trips ) as $key ) {
			if ( ! isset( $trips[ $key ]['timestamp'] ) ) {
				$datetime                   = substr( $key, strpos( $key, '_' ) + 1 );
				$trips[ $key ]['timestamp'] = (int) strtotime( $datetime );
			}
		}

		uasort(
			$trips,
			function ( $a, $b ) {
				return (int) $a['timestamp'] <=> (int) $b['timestamp'];
			}
		);

		$count         = $request['count'];
		$offset        = max( 0, (int) $request['offset'] );
		$total         = count( $trips );
		$show_more_btn = $total > $offset + $count;
		$trips         = array_slice( $trips, $offset, $count );
		$show_less_btn = ( $offset + count( $trips ) ) > 10;

		$dates          = self::get_filtered_dates();
		$valid_statuses = self::get_filtered_statuses();
		$destinations   = self::get_filtered_terms( 'destination' );
		$activities     = self::get_filtered_terms( 'activities' );

		return compact(
			'dates',
			'trips',
			'show_more_btn',
			'show_less_btn',
			'count',
			'valid_statuses',
			'destinations',
			'activities',
		);
	}

	/**
	 * Get upcoming tours details Html.
	 *
	 * @param int $id Unique Trip DateTime & Booking Id.
	 *
	 * @return string
	 */
	public static function get_details_html( $id ) {
		$bookings_total = 0;
		$bookings       = array();
		$trip_details   = array();

		// Parse ID to extract trip_id and datetime ({trip_id}_{datetime})
		$id_parts       = explode( '_', $id, 2 );
		$trip_id        = isset( $id_parts[0] ) ? absint( $id_parts[0] ) : 0;
		$start_datetime = $id_parts[1] ?? '';

		if ( ! $trip_id || ! $start_datetime ) {
			ob_start();
			wptravelengine_get_admin_template( 'upcoming-tours/partials/details.php', compact( 'trip_details', 'bookings' ) );

			return ob_get_clean();
		}

		// Fetch only bookings for this specific trip (much faster than all bookings)
		$all_bookings = self::get_post_bookings( array( 'trip_id' => $trip_id ) );

		foreach ( $all_bookings as $booking ) {
			// Only process bookings matching the exact datetime
			if ( $id !== $booking['trip_id'] . '_' . $booking['trip_datetime'] ) {
				continue;
			}

			$bookings[ $booking['booking_id'] ] = array(
				'id'           => $booking['booking_id'],
				'billing_info' => $booking['billing_info'],
				'travellers'   => $booking['travellers'],
			);
			$bookings_total                    += $booking['total'];

			// Accumulate total travellers
			if ( ! isset( $trip_details['travellers'] ) ) {
				$trip_details['travellers'] = 0;
			}
			$trip_details['travellers'] += $booking['travellers'];
		}

		// Build trip details once if we found matching bookings
		if ( $trip_id && $start_datetime ) {
			try {
				$trip = new Trip( $trip_id );

				$trip_details['title']      = $trip->get_title();
				$trip_details['image']      = $trip->get_gallery_images()[0]['src'] ?? '';
				$trip_details['duration']   = self::get_trip_duration( $trip );
				$trip_details['start_date'] = wptravelengine_format_trip_datetime( $start_datetime );
				$trip_details['end_date']   = wptravelengine_format_trip_end_datetime( $start_datetime, $trip );
				$trip_details['total']      = wte_get_formated_price( $bookings_total );
			} catch ( \Exception $e ) {
				// Handle invalid trip
			}
		}

		ob_start();
		wptravelengine_get_admin_template( 'upcoming-tours/partials/details.php', compact( 'trip_details', 'bookings' ) );

		return ob_get_clean();
	}

	/**
	 * Get post bookings filtered by date range and optionally by trip_id.
	 * Note: Since datetime is stored in serialized order_trips meta for older bookings,
	 * we can't filter datetime at database level. We fetch bookings and filter in PHP.
	 *
	 * @param array $args Optional query arguments including 'date_from', 'date_to', and 'trip_id'.
	 *
	 * @return array
	 */
	private static function get_post_bookings( array $args = array() ) {
		$cache_key = 'wptravelengine_upcoming_tours_bookings_' . self::get_cache_version() . '_' . md5( wp_json_encode( $args ) );

		// Try to get from cache first (15 minute cache)
		$cached = get_transient( $cache_key );
		if ( false !== $cached ) {
			return $cached;
		}
		$query_args = array(
			'post_type'      => 'booking',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'     => 'wp_travel_engine_booking_status',
					'value'   => array( 'booked' ),
					'compare' => 'IN',
				),
				array(
					'relation' => 'OR',
					array(
						'key'     => 'trip_datetime',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => 'trip_datetime',
						'value'   => wp_date( 'Y-m-d' ),
						'compare' => '>=',
						'type'    => 'DATE',
					),
				),
			),
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		// Filter by trip_id at database level if provided ( either from wp_travel_engine_booking_setting | cart_info ).
		if ( ! empty( $args['trip_id'] ) ) {
			$trip_id   = absint( $args['trip_id'] );
			$trip_id_s = (string) $trip_id;
			// Use serialized string format s:LENGTH:"VALUE" to avoid LIKE false positives.
			// e.g. s:1:"1" won't match s:2:"10" or s:3:"100".
			$serialized = 's:' . strlen( $trip_id_s ) . ':"' . $trip_id_s . '"';

			$query_args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'wp_travel_engine_booking_setting',
					'value'   => $serialized,
					'compare' => 'LIKE',
				),
				array(
					'key'     => 'cart_info',
					'value'   => '"trip_id";i:' . $trip_id . ';',
					'compare' => 'LIKE',
				),
			);
		}

		$bookings = get_posts( $query_args );

		// Prime post meta cache to avoid N+1 queries
		if ( ! empty( $bookings ) ) {
			update_postmeta_cache( wp_list_pluck( $bookings, 'ID' ) );
		}

		// Filter by date in PHP since datetime is in serialized order_trips
		$filtered_bookings = array();
		$today             = wp_date( 'Y-m-d' );
		$date_from         = $args['date_from'] ?? null;
		$date_to           = $args['date_to'] ?? null;

		foreach ( $bookings as $booking ) {
			try {
				$booking_model = wptravelengine_get_booking( $booking );
				$trip_id       = $booking_model->get_trip_id();
				$trip_datetime = $booking_model->get_trip_datetime();

				if ( ! $trip_id || ! $trip_datetime ) {
					continue;
				}

				// Extract date part only for comparison (remove time if present)
				$trip_date_only = strpos( $trip_datetime, 'T' ) !== false ? substr( $trip_datetime, 0, 10 ) : $trip_datetime;

				// Filter: Apply date range if specified
				if ( $date_from && $date_to ) {
					if ( $trip_date_only < $date_from || $trip_date_only > $date_to ) {
						continue;
					}
				}

				// Filter: Only future dates (from today onwards)
				if ( $trip_date_only < $today ) {
					continue;
				}

				$travellers = 0;
				foreach ( $booking_model->get_trip_pax() as $pax ) {
					$travellers += (int) $pax;
				}
				if ( $travellers <= 0 ) {
					$data       = $booking_model->get_data();
					$travellers = $data['booked_trips'][0]['number_of_travellers'] ?? 0;
				}

				$filtered_bookings[] = array(
					'booking_id'    => $booking->ID,
					'trip_id'       => $trip_id,
					'trip_datetime' => $trip_datetime,
					'travellers'    => $travellers,
					'billing_info'  => trim( $booking_model->get_billing_fname() . ' ' . $booking_model->get_billing_lname() ),
					'total'         => $booking_model->get_total(),
				);
			} catch ( \Exception $e ) {
				continue;
			}
		}

		// Cache for 15 minutes
		set_transient( $cache_key, $filtered_bookings, 15 * MINUTE_IN_SECONDS );

		return $filtered_bookings;
	}

	/**
	 * Render collapsible meta (destinations/activities) with "show more" functionality.
	 *
	 * @param array  $items Array of items to display.
	 * @param string $icon_id SVG icon ID (e.g., 'marker-pin', 'compas').
	 * @param string $label_singular Singular label for aria-label (e.g., 'destinations', 'activities').
	 *
	 * @return string HTML output.
	 * @since 6.7.10
	 */
	public static function render_collapsible_meta( $items, $icon_id, $label_singular ) {
		if ( empty( $items ) ) {
			return '';
		}

		$items_count = count( $items );
		$output      = '<span class="wpte-trip-meta">';
		$output     .= sprintf(
			'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><use href="#%s"></use></svg>',
			esc_attr( $icon_id )
		);

		if ( $items_count > 2 ) {
			$full_text  = implode( ', ', $items );
			$short_text = implode( ', ', array_slice( $items, 0, 2 ) );
			$more_count = $items_count - 2;
			$label      = sprintf(
			/* translators: %s: singular label (e.g., destinations, activities) */
				__( 'Show more %s', 'wp-travel-engine' ),
				$label_singular
			);

			$output .= sprintf(
				'<span class="wpte-trip-collapsible-meta" data-full-text="%s" data-short-text="%s" data-more-count="%d">',
				esc_attr( $full_text ),
				esc_attr( $short_text ),
				esc_attr( $more_count )
			);
			$output .= esc_html( $short_text );
			$output .= sprintf(
				' <span class="wpte-trip-collapsible-meta-more" role="button" aria-expanded="false" tabindex="0" aria-label="%s">+ %d more</span>',
				esc_attr( $label ),
				esc_html( $more_count )
			);
			$output .= '</span>';
		} else {
			$output .= esc_html( implode( ', ', $items ) );
		}

		$output .= '</span>';

		return $output;
	}

	/**
	 * Get trip duration.
	 *
	 * @param Trip $trip Trip.
	 *
	 * @return string
	 */
	private static function get_trip_duration( $trip ) {
		$trip_duration         = $trip->get_trip_duration() . ' ' . $trip->get_trip_duration_unit();
		$trip_duration_minutes = '';
		$trip_nights           = '';

		if ( $trip->get_meta( 'trip_type' ) === 'single' ) {
			$minutes = $trip->get_meta( 'trip_duration_minutes' );
			if ( ! empty( $minutes ) && (int) $minutes > 0 ) {
				$trip_duration_minutes = $minutes . ' minutes';
			}
		} elseif ( $trip->get_trip_duration_unit() === 'days' ) {
			$nights = $trip->get_setting( 'trip_duration_nights' );
			if ( ! empty( $nights ) && (int) $nights > 0 ) {
				$trip_nights = $nights . ' ' . __( 'Nights', 'wp-travel-engine' );
			}
		}

		return trim( $trip_duration . ' ' . $trip_nights . ' ' . $trip_duration_minutes );
	}

	/**
	 * Get cache version, fetching from object cache only once per request.
	 *
	 * @return int
	 */
	private static function get_cache_version(): int {
		if ( null === self::$cache_version ) {
			$ver                 = get_transient( 'wptravelengine_upcoming_tours_version' );
			self::$cache_version = false === $ver ? 1 : (int) $ver;
			if ( false === $ver ) {
				set_transient( 'wptravelengine_upcoming_tours_version', self::$cache_version, 30 * DAY_IN_SECONDS );
			}
		}

		return self::$cache_version;
	}

	/**
	 * Clear cache for upcoming tours.
	 *
	 * @return void
	 * @since 6.7.0
	 */
	public static function clear_cache() {
		$current_version     = get_transient( 'wptravelengine_upcoming_tours_version' );
		$new_version         = ( false !== $current_version ) ? (int) $current_version + 1 : 1;
		self::$cache_version = $new_version;
		set_transient( 'wptravelengine_upcoming_tours_version', $new_version, 30 * DAY_IN_SECONDS );
	}
}
