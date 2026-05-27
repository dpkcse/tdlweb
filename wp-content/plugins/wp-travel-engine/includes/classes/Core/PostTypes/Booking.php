<?php
/**
 * Post Type Booking.
 *
 * @package WPTravelEngine/Core/PostTypes
 * @since 6.0.0
 */

namespace WPTravelEngine\Core\PostTypes;

use WPTravelEngine\Helpers\Functions;
use WPTravelEngine\Helpers\BookedItem;
use WPTravelEngine\Abstracts\PostType;
use WPTravelEngine\Core\Booking\SaveBooking;
use WPTravelEngine\Core\Booking\ViewBooking;
use WPTravelEngine\Core\Models\Post\Booking as BookingModel;

/**
 * Class Booking
 * This class represents a trip booking to the WP Travel Engine plugin.
 *
 * @since 6.8.0
 */
class Booking extends PostType {

	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected string $post_type = 'booking';

	/**
	 * Serialized fragment identifying a v4.0 cart_info structure.
	 * Bookings missing this are treated as deprecated.
	 *
	 * @since 6.8.0
	 */
	private const CART_INFO_VERSION = 's:7:"version";s:3:"4.0";';

	/**
	 * SVG icon for the "migrated" booking state badge (double-arrow exchange).
	 *
	 * @since 6.8.0
	 */
	public const BADGE_ICON_MIGRATED = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h14M13 3l4 4-4 4M17 13H3M7 9l-4 4 4 4"/></svg>';

	/**
	 * SVG icon for the "deprecated" booking state badge (clock).
	 *
	 * @since 6.8.0
	 */
	public const BADGE_ICON_DEPRECATED = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="8"/><path d="M10 6v4l2.5 2.5"/></svg>';

	/**
	 * SVG icon for the "modified" booking state badge (pencil).
	 *
	 * @since 6.8.0
	 */
	public const BADGE_ICON_MODIFIED = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2.5a2.121 2.121 0 013 3L6 17l-4 1 1-4 11.5-11.5z"/></svg>';

	/**
	 * SVG icon for the "manual" booking state badge (person silhouette).
	 *
	 * @since 6.8.0
	 */
	public const BADGE_ICON_MANUAL = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="6" r="3"/><path d="M4 18v-1a6 6 0 0112 0v1"/></svg>';

	/**
	 * Constructor.
	 *
	 * @since 6.4.0
	 */
	public function __construct() {
		add_action( "add_meta_boxes_{$this->post_type}", array( $this, 'meta_box_booking' ) );
		add_action( 'restrict_manage_posts', array( $this, 'add_filter_options' ) );
		add_action( 'parse_query', array( $this, 'filter_bookings' ) );
		add_filter( 'disable_months_dropdown', array( $this, 'remove_date_filter' ) );

		add_filter( 'wptravelengine_booking_line_item_group_title', array( $this, 'add_booking_line_item_title' ), 10, 2 );
		add_filter( 'wptravelengine_booking_line_items', array( $this, 'add_booking_line_items' ), 10, 2 );
		add_filter( 'wp_travel_engine_traveller_info_fields_display', array( $this, 'add_pricing_category_field_to_traveller' ), 10, 1 );
		add_filter( 'wp_travel_engine_lead_traveller_info_fields_display', array( $this, 'add_pricing_category_field_to_lead_traveller' ), 10, 1 );
		add_filter( 'wptravelengine_form_field_options', array( $this, 'add_none_option_to_select_options' ), 10, 1 );
		add_action( 'wp_insert_post', array( $this, 'save' ), 10, 3 );
		add_action( 'load-post-new.php', array( $this, 'redirect_new_booking' ) );

		add_action( 'pre_get_posts', array( $this, 'exclude_migrated_bookings' ) );
		add_action( 'load-edit.php', array( $this, 'delete_auto_drafts' ) );
		add_filter( 'views_edit-booking', array( $this, 'fix_booking_view_counts' ) );
		add_filter( 'display_post_states', array( $this, 'append_booking_state_badges' ), 10, 2 );

		\WP_Travel_Engine_Booking_Export::register_hooks();
		ViewBooking::register_hooks();
	}

	/**
	 * Save booking data.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post Post object.
	 * @param bool     $update Whether this is an update.
	 * @return void
	 * @since 6.4.0
	 * @since 6.8.0 Refined and migrated from WPTravelEngine\Core\Booking\SaveBooking class.
	 */
	public function save( $post_id, $post, $update = false ) {
		static $processing = array();

		if ( isset( $processing[ $post_id ] ) ) {
			return;
		}

		// Verify nonce.
		if ( $this->post_type !== $post->post_type || ! wp_verify_nonce( $_POST['wptravelengine_new_booking_nonce'] ?? '', 'wptravelengine_new_booking' ) ) {
			return;
		}

		$processing[ $post_id ] = true;
		try {
			$booking = new SaveBooking( $post_id );
			$booking->process();
		} finally {
			unset( $processing[ $post_id ] );
		}
	}

	/**
	 * @return void
	 * @since 6.4.0
	 */
	public function meta_box_booking() {
		add_meta_box(
			'booking_details_id',
			__( 'Booking Details', 'wp-travel-engine' ),
			function () {
				global $post;
				$booking_view = new ViewBooking( $post->ID );
				$booking_view->process_markup();
			},
			'booking',
			'normal',
			'high'
		);
	}

	/**
	 * Add booking line item title.
	 *
	 * @param string $title Title.
	 * @param array  $item  Item.
	 *
	 * @return string
	 * @since 6.7.0
	 */
	public function add_booking_line_item_title( string $title, array $item ): string {
		if ( 'pricing_category' === $title ) {
			$title = __( 'Traveller(s)', 'wp-travel-engine' );
		}
		if ( 'extra_service' === $title ) {
			if ( wptravelengine_settings()->get( 'extra_service_title' ) !== '' ) {
				$title = wptravelengine_settings()->get( 'extra_service_title' );
			} else {
				$title = __( 'Extra Services', 'wp-travel-engine' );
			}
		}

		return $title;
	}

	/**
	 * Add booking line items.
	 *
	 * @param array      $line_items Line items.
	 * @param BookedItem $item       Item.
	 *
	 * @return array
	 * @since 6.4.0
	 */
	public function add_booking_line_items( array $line_items, BookedItem $item ): array {
		$line_items['pricing_category'] ??= array();
		if ( wptravelengine_is_addon_active( 'extra-services' ) ) {
			$line_items['extra_service'] ??= array();
		}
		if ( wptravelengine_is_addon_active( 'travel-insurance' ) ) {
			$line_items['travel_insurance'] ??= array();
		}

		return $line_items;
	}

	/**
	 * Retrieve the labels for the Booking post type.
	 *
	 * Returns an array containing the labels used for the Booking post type, including
	 * names for various elements such as the post type itself, singular and plural names,
	 * menu labels, and more.
	 *
	 * @return array An array containing the labels for the Booking post type.
	 */
	public function get_labels(): array {
		return array(
			'name'               => _x( 'Bookings', 'post type general name', 'wp-travel-engine' ),
			'singular_name'      => _x( 'Booking', 'post type singular name', 'wp-travel-engine' ),
			'menu_name'          => _x( 'WP Travel Engine', 'admin menu', 'wp-travel-engine' ),
			'name_admin_bar'     => _x( 'Booking', 'add new on admin bar', 'wp-travel-engine' ),
			'add_new'            => _x( 'Add New', 'Booking', 'wp-travel-engine' ),
			'add_new_item'       => esc_html__( 'Add New Booking', 'wp-travel-engine' ),
			'new_item'           => esc_html__( 'New Booking', 'wp-travel-engine' ),
			'edit_item'          => esc_html__( 'Edit Booking', 'wp-travel-engine' ),
			'view_item'          => esc_html__( 'View Booking', 'wp-travel-engine' ),
			'all_items'          => esc_html__( 'Bookings', 'wp-travel-engine' ),
			'search_items'       => esc_html__( 'Search Bookings', 'wp-travel-engine' ),
			'parent_item_colon'  => esc_html__( 'Parent Bookings:', 'wp-travel-engine' ),
			'not_found'          => esc_html__( 'No Bookings found.', 'wp-travel-engine' ),
			'not_found_in_trash' => esc_html__( 'No Bookings found in Trash.', 'wp-travel-engine' ),
		);
	}

	/**
	 * Retrieve the post type name.
	 *
	 * Returns the name of the post type.
	 *
	 * @return string The name of the post type.
	 */
	public function get_post_type(): string {
		return $this->post_type;
	}

	/**
	 * Retrieve the icon for the Booking post type.
	 *
	 * Returns the icon for the Booking post type.
	 *
	 * @return string The icon for the Booking post type.
	 */
	public function get_icon(): string {
		return 'data:image/svg+xml;base64,' . base64_encode( '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_60_548)"><path d="M22.8963 12.1856C23.1956 11.7415 22.7501 11.3673 22.7501 11.3673C22.7501 11.3673 22.2301 11.1051 21.9322 11.5491C21.633 11.9932 20.8789 13.1159 20.8789 13.1159L17.8029 13.1871L17.287 13.954L19.8988 14.572L18.7272 15.9741C19.0916 16.1151 19.4014 16.3747 19.7525 16.5486L20.863 15.2085L22.4442 17.359L22.9602 16.5921L21.8418 13.7524C21.8431 13.7524 22.5984 12.6297 22.8963 12.1856Z" fill="white"></path><path d="M11.9222 11.5544C12.8513 11.5544 13.6045 10.8081 13.6045 9.88745C13.6045 8.96683 12.8513 8.22052 11.9222 8.22052C10.9931 8.22052 10.2399 8.96683 10.2399 9.88745C10.2399 10.8081 10.9931 11.5544 11.9222 11.5544Z" fill="white"></path><path d="M21.2379 13.4954C20.9587 13.3215 20.589 13.4045 20.4134 13.6825C18.7032 16.3733 16.9172 17.8439 15.2482 17.9335C13.1351 18.0495 11.744 16.011 10.5299 14.6498C9.8862 13.9276 9.30105 13.1568 8.79038 12.3371C8.3861 11.6901 7.93927 10.9166 7.93927 10.1339C7.93794 7.95699 9.72528 6.18596 11.9222 6.18596C14.1178 6.18596 15.9052 7.95699 15.9052 10.1339C15.9052 11.4371 14.3226 13.5244 12.9635 15.0477C12.7494 15.2875 12.7733 15.6525 13.0114 15.87C13.0154 15.8726 13.018 15.8766 13.022 15.8792C13.2641 16.1006 13.6444 16.0795 13.8625 15.8357C15.2668 14.2716 17.1034 11.8904 17.1034 10.1326C17.1021 7.30208 14.7788 5 11.9222 5C9.06567 5 6.74106 7.30208 6.74106 10.1339C6.74106 11.7876 8.36749 13.9935 9.73326 15.555L9.72927 15.5511C10.091 15.8897 10.4022 16.2996 10.744 16.6593C11.4076 17.3551 12.0858 18.0969 12.9382 18.5634C12.9396 18.5647 12.9422 18.5647 12.9475 18.5687C13.5181 18.877 14.2375 19.1235 15.0807 19.1235C15.1511 19.1235 15.223 19.1221 15.2961 19.1182C17.4039 19.0141 19.4666 17.3972 21.4255 14.3137C21.6023 14.037 21.5172 13.6707 21.2379 13.4954Z" fill="white"></path><path d="M10.6349 17.7979C10.4607 17.6345 10.2054 17.5937 9.98463 17.6859C9.58567 17.852 9.11889 17.9626 8.59625 17.9337C6.92727 17.844 5.14126 16.3735 3.4377 13.6919L2.11049 11.5137C1.94027 11.233 1.57189 11.1434 1.28996 11.312C1.0067 11.482 0.914938 11.8457 1.08649 12.1264L2.41902 14.3138C4.37791 17.3973 6.44054 19.0142 8.54838 19.1183C8.62152 19.1222 8.69333 19.1236 8.76381 19.1236C9.40082 19.1236 9.96867 18.9826 10.4541 18.7796C10.8544 18.6123 10.9528 18.0957 10.6376 17.7992L10.6349 17.7979Z" fill="white"></path></g></svg>' ); // phpcs:ignore WordPress.WP.EnsuredPHPCS.Base64Encode.FileWithoutSafety
	}

	/**
	 * Retrieve the arguments for the Booking post type.
	 *
	 * Returns an array containing the arguments used to register the Booing post type.
	 *
	 * @return array An array containing the arguments for the Booking post type.
	 */
	public function get_args(): array {
		return array(
			'labels'             => $this->get_labels(),
			'description'        => esc_html__( 'Description.', 'wp-travel-engine' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'menu_icon'          => $this->get_icon(),
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'booking' ),
			'capability_type'    => 'post',
			'capabilities'       => $this->get_capabilities(),
			'map_meta_cap'       => true, // Set to `false`, if users are not allowed to edit/delete existing posts
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 31,
			'supports'           => array( '' ),
		);
	}

	/**
	 * Get capabilities.
	 *
	 * @return array
	 * @since 6.4.0
	 */
	public function get_capabilities(): array {
		// TODO: Add capabilities for the booking post type specifically once we define particular capabilities for the booking post type.
		return array(
			'edit_post'          => 'edit_trip',
			'read_post'          => 'read_trip',
			'delete_post'        => 'delete_trip',
			'edit_posts'         => 'edit_trips',
			'edit_others_posts'  => 'edit_others_trips',
			'publish_posts'      => 'publish_trips',
			'read_private_posts' => 'read_private_trips',
		);
	}

	/**
	 * Add filter options.
	 *
	 * @param string $post_type Post type.
	 *
	 * @since 5.7.4 - Booking Export button added.
	 * @modified_since 6.3.5 - Trip Name filter and Booking Status filter added.
	 */
	public function add_filter_options( $post_type ) {
		$current_screen = get_current_screen();
		if ( 'booking' !== $post_type && 'edit-booking' !== $current_screen->id ) {
			return;
		}
		remove_all_actions( 'admin_notices' );
		// Booking status and Trip Name filter options.
		$trips            = wp_travel_engine_get_trips_array();
		$status           = wp_travel_engine_get_booking_status();
		$booking_selected = isset( $_REQUEST['booking_status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['booking_status'] ) ) : 'all';
		$trip_selected    = isset( $_REQUEST['trip_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['trip_id'] ) ) : 'all';

		$mappings = array(
			'trip_id'        => array(
				'data'     => $trips,
				'label'    => __( 'Trip Name', 'wp-travel-engine' ),
				'selected' => $trip_selected,
			),
			'booking_status' => array(
				'data'     => $status,
				'label'    => __( 'Booking Status', 'wp-travel-engine' ),
				'selected' => $booking_selected,
			),
		);
		foreach ( $mappings as $id => $data ) { ?>
			<select id="<?php echo esc_attr( $id ); ?>_filter" name="<?php echo esc_attr( $id ); ?>">
				<option value="all"> <?php echo esc_html( $data['label'] ); ?> </option>
				<?php
				foreach ( $data['data'] as $key => $value ) :
					$display = 'booking_status' === $id ? $value['text'] : $value;
					?>
					<option value="<?php echo esc_html( $key ); ?>" <?php selected( $data['selected'], $key ); ?>>
						<?php echo esc_html( $display ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php
		}
	}

	/**
	 * Remove date filter given by WordPress
	 *
	 * @return bool
	 * @since 6.3.5
	 */
	public function remove_date_filter() {
		return 'booking' === ( $_GET['post_type'] ?? '' );
	}

	/**
	 * Add "None" as first option for select fields on backend (same as frontend FormField filter).
	 *
	 * @param array $options Select field options.
	 * @return array Modified options.
	 * @since 6.7.6
	 */
	public function add_none_option_to_select_options( $options ): array {
		if ( ! is_array( $options ) ) {
			return $options;
		}

		if ( ! is_admin() || get_post_type() !== 'booking' ) {
			return $options;
		}

		return Functions::add_none_option_to_select( $options, true );
	}

	/**
	 * Add pricing category field to traveller info fields.
	 *
	 * @param array $fields Traveller info fields.
	 * @return array Modified fields array.
	 * @since 6.7.0
	 */
	public function add_pricing_category_field_to_traveller( $fields ) {
		return $this->add_pricing_category_field( $fields, 'traveller_pricing_category' );
	}

	/**
	 * Add pricing category field to lead traveller info fields.
	 *
	 * @param array $fields Lead traveller info fields.
	 * @return array Modified fields array.
	 * @since 6.7.0
	 */
	public function add_pricing_category_field_to_lead_traveller( $fields ) {
		return $this->add_pricing_category_field( $fields, 'pricing_category' );
	}

	/**
	 * Add pricing category field to fields array.
	 *
	 * @param array  $fields    Fields array.
	 * @param string $field_key Key to use for the field.
	 * @return array Modified fields array.
	 * @since 6.7.0
	 */
	private function add_pricing_category_field( $fields, $field_key ) {
		// Only process on booking post type admin page.
		if ( get_post_type() !== 'booking' ) {
			return $fields;
		}
		global $post;

		$post_id = isset( $post->ID ) ? $post->ID : null;
		if ( $post_id ) {
			global $post;
			$booking      = BookingModel::for( $post_id, $post );
			$is_curr_cart = $booking->is_curr_cart();
			if ( ! $is_curr_cart ) {
				return $fields;
			}
		}

		// Ensure $fields is an array.
		if ( ! is_array( $fields ) ) {
			$fields = array();
		}

		// Return early if field already exists.
		if ( isset( $fields[ $field_key ] ) ) {
			return $fields;
		}

		// Get pricing categories.
		$pricing_categories = get_terms(
			array(
				'taxonomy'   => 'trip-packages-categories',
				'hide_empty' => false,
				'orderby'    => 'term_id',
				'fields'     => 'id=>name',
			)
		);

		// Handle get_terms error.
		if ( is_wp_error( $pricing_categories ) ) {
			$pricing_categories = array();
		}

		/**
		 * Sentinel to skip adding None option to the pricing category field.
		 */
		$pricing_categories['__skip_none_option__'] = true;
		// Create the pricing category field.
		$pricing_category_field = array(
			'type'          => 'select',
			'wrapper_class' => 'row-repeater',
			'field_label'   => __( 'Traveller(s)', 'wp-travel-engine' ),
			'name'          => 'travelers[pricing_category]',
			'id'            => 'pricing_category',
			'class'         => 'input',
			'default_field' => true,
			'options'       => $pricing_categories,
		);

		// Add the field at the beginning of the array.
		$fields = array( $field_key => $pricing_category_field ) + $fields;

		return $fields;
	}

	/**
	 * Return query after filtering bookings.
	 *
	 * @param object $query Query.
	 *
	 * @modified_since 6.4.0 Modified the query for the selected trip name and date range filter option.
	 *
	 * @return object $query
	 */
	public function filter_bookings( $query ) {
		// Modify the query only if it is admin and main query.
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return $query;
		}
		$current_screen = get_current_screen();
		$trip_id        = 'all';
		$booking_status = 'all';
		if ( isset( $_REQUEST['trip_id'] ) ) {
			$trip_id = sanitize_text_field( wp_unslash( $_REQUEST['trip_id'] ) );
		}
		if ( isset( $_REQUEST['booking_status'] ) ) {
			$booking_status = sanitize_text_field( wp_unslash( $_REQUEST['booking_status'] ) );
		}
		$date_range = isset( $_REQUEST['wte_booking_range'] ) ? sanitize_text_field( $_REQUEST['wte_booking_range'] ) : '';
		$dates      = explode( ' to ', $date_range );

		// Store the dates in separate variables.
		$start_date = isset( $dates[0] ) ? $dates[0] : '';
		$end_date   = isset( $dates[1] ) ? $dates[1] : '';

		// Modify the query for the targeted screen and filter option.
		if ( ( 'edit-booking' !== $current_screen->id ) || ( 'all' === $booking_status && 'all' === $trip_id && empty( $date_range ) ) ) {
			return $query;
		}
		$filter_ids = wptravelengine_get_booking_ids( (int) $trip_id );
		$filter_ids = empty( $filter_ids ) ? array( 0 ) : $filter_ids;

		// Add query for selected booking status.
		if ( 'all' !== $booking_status ) {
			$query->set(
				'meta_query',
				array(
					array(
						'key'     => 'wp_travel_engine_booking_status',
						'compare' => '=',
						'value'   => $booking_status,
						'type'    => 'string',
					),
				)
			);
		}

		// Add query for selected trip ids.
		if ( 'all' !== $trip_id ) {
			$query->set( 'post__in', $filter_ids );
		}

		// Add query for selected date range.
		if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
			$query->set(
				'date_query',
				array(
					array(
						'after'     => $start_date,
						'before'    => $end_date,
						'inclusive' => true,
					),
				)
			);
		} elseif ( ! empty( $start_date ) ) {
			$get_specific_date = explode( '-', $start_date );
			$query->set(
				'date_query',
				array(
					array(
						'year'  => $get_specific_date[0],
						'month' => $get_specific_date[1],
						'day'   => $get_specific_date[2],
					),
				)
			);
		}

		return $query;
	}

	/**
	 * Redirects post-new.php for bookings to a single auto-draft edit screen,
	 * preventing a new orphan post from being created on every page reload.
	 *
	 * @return void
	 * @since 6.8.0
	 */
	public function redirect_new_booking(): void {
		if ( ( $_GET['post_type'] ?? '' ) !== $this->post_type ) {
			return;
		}

		$booking    = BookingModel::create_booking( array( 'post_status' => 'auto-draft' ) );
		$booking_id = $booking->ID;

		if ( $booking_id ) {
			$booking->update_meta( '__is_manual', 'yes' );
			wp_safe_redirect( admin_url( "post.php?post={$booking_id}&action=edit&wptravelengine_action=edit" ) );
			exit;
		}
	}

	/**
	 * Add "Deprecated" view tab for bookings without cart_info version 4.0.
	 *
	 * @param array $views View links HTML keyed by status slug.
	 * @return array
	 * @since 6.8.0
	 */
	public function fix_booking_view_counts( array $views ): array {
		$is_deprecated = isset( $_GET['wte_deprecated'] );

		if ( $is_deprecated ) {
			foreach ( $views as &$link ) {
				$link = str_replace( array( ' class="current"', ' aria-current="page"' ), '', $link );
			}
			unset( $link );
		}

		$deprecated_ids = $this->get_deprecated_booking_ids();

		if ( empty( $deprecated_ids ) ) {
			return $views;
		}

		$deprecated_url          = add_query_arg(
			array(
				'post_type'      => 'booking',
				'wte_deprecated' => '1',
			),
			admin_url( 'edit.php' )
		);
		$views['wte_deprecated'] = sprintf(
			'<a href="%s"%s>%s <span class="count">(%s)</span></a>',
			esc_url( $deprecated_url ),
			$is_deprecated ? ' class="current" aria-current="page"' : '',
			esc_html__( 'Deprecated', 'wp-travel-engine' ),
			number_format_i18n( count( $deprecated_ids ) )
		);

		if ( ! empty( $deprecated_ids ) && isset( $views['publish'] ) ) {
			global $wpdb;
			$ids_str              = implode( ',', $deprecated_ids );
			$published_deprecated = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				"SELECT COUNT(*) FROM {$wpdb->posts} WHERE ID IN ({$ids_str}) AND post_status = 'publish'" // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			);

			if ( $published_deprecated > 0 ) {
				preg_match( '/<span class="count">\((\d+)\)<\/span>/', $views['publish'], $matches );
				if ( isset( $matches[1] ) ) {
					$new              = max( 0, (int) $matches[1] - $published_deprecated );
					$views['publish'] = preg_replace(
						'/<span class="count">\(\d+\)<\/span>/',
						'<span class="count">(' . number_format_i18n( $new ) . ')</span>',
						$views['publish']
					);
				}
			}
		}

		return $views;
	}

	/**
	 * Delete all auto-draft booking posts when the booking list screen loads.
	 *
	 * @return void
	 * @since 6.8.0
	 */
	public function delete_auto_drafts(): void {
		if ( ( $_GET['post_type'] ?? '' ) !== $this->post_type ) {
			return;
		}

		$deprecated_ids = $this->get_deprecated_booking_ids();

		$args = array(
			'post_type'      => $this->post_type,
			'post_status'    => 'auto-draft',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		);

		if ( ! empty( $deprecated_ids ) ) {
			$args['post__not_in'] = $deprecated_ids;
		}

		$auto_drafts = get_posts( $args );

		foreach ( $auto_drafts as $post_id ) {
			wp_delete_post( $post_id, true );
		}
	}

	/**
	 * Appends pill-style status badges to the booking title in the admin list.
	 * Only renders on the "All" and "Published" list tabs.
	 *
	 * @param string[] $states Existing post states.
	 * @param \WP_Post $post   Current post.
	 * @return string[]
	 * @since 6.8.0
	 */
	public function append_booking_state_badges( array $states, \WP_Post $post ): array {
		if ( 'booking' !== $post->post_type ) {
			return $states;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_status = sanitize_key( $_GET['post_status'] ?? '' );
		if ( $current_status && 'publish' !== $current_status ) {
			return $states;
		}

		// Maps badge type → wpte-tag modifier + icon + label.
		$state_badges = array(
			'migrated'   => array(
				'modifier' => 'migrated',
				'icon'     => self::BADGE_ICON_MIGRATED,
				'label'    => __( 'Migrated', 'wp-travel-engine' ),
			),
			'deprecated' => array(
				'modifier' => '',
				'icon'     => self::BADGE_ICON_DEPRECATED,
				'label'    => __( 'Deprecated', 'wp-travel-engine' ),
			),
			'modified'   => array(
				'modifier' => 'warning',
				'icon'     => self::BADGE_ICON_MODIFIED,
				'label'    => __( 'Modified', 'wp-travel-engine' ),
			),
			'manual'     => array(
				'modifier' => 'manual',
				'icon'     => self::BADGE_ICON_MANUAL,
				'label'    => __( 'Manual', 'wp-travel-engine' ),
			),
		);

		$badges              = array();
		$post_id             = $post->ID;
		$booking_state_badge = static function ( array $badge ): string {
			$class = trim( 'wpte-tag ' . $badge['modifier'] );
			return \sprintf(
				'<span class="%1$s">%2$s%3$s</span>',
				esc_attr( $class ),
				$badge['icon'],
				esc_html( $badge['label'] )
			);
		};

		$deprecated_ids = $this->get_deprecated_booking_ids();

		if ( absint( get_post_meta( $post_id, '_migrated_from', true ) ) ) {
			$badges[] = $booking_state_badge( $state_badges['migrated'] );
		}

		if ( in_array( $post_id, $deprecated_ids ) ) {
			$badges[] = $booking_state_badge( $state_badges['deprecated'] );
		}

		if ( wptravelengine_toggled( get_post_meta( $post_id, '__is_manual', true ) ) ) {
			$badges[] = $booking_state_badge( $state_badges['manual'] );
		} elseif ( wptravelengine_toggled( get_post_meta( $post_id, '_user_edited', true ) ) ) {
			$badges[] = $booking_state_badge( $state_badges['modified'] );
		}

		if ( ! empty( $badges ) ) {
			$states['wte_booking_states'] = '<span class="wte-booking-states">' . implode( '', $badges ) . '</span>';
		}

		return $states;
	}

	/**
	 * Exclude original bookings that have been migrated from the admin listing.
	 *
	 * @since 6.8.0
	 */
	public function exclude_migrated_bookings( \WP_Query $query ): void {
		global $pagenow;

		if ( ! is_admin() || ! $query->is_main_query() || 'edit.php' !== $pagenow ) {
			return;
		}

		if ( ( $_GET['post_type'] ?? '' ) !== 'booking' ) {
			return;
		}

		$deprecated_ids = $this->get_deprecated_booking_ids();

		if ( isset( $_GET['wte_deprecated'] ) ) {
			$query->set( 'post__in', ! empty( $deprecated_ids ) ? $deprecated_ids : array( 0 ) );
			$query->set( 'post_status', 'any' );
			return;
		}

		if ( ! isset( $_GET['post_status'] ) ) {
			return;
		}

		if ( empty( $deprecated_ids ) ) {
			return;
		}

		$existing = (array) $query->get( 'post__not_in' );
		$query->set( 'post__not_in', array_unique( array_merge( $existing, $deprecated_ids ) ) );
	}

	/**
	 * Get IDs of deprecated bookings — those whose cart_info lacks version 4.0.
	 *
	 * @return int[]
	 * @since 6.8.0
	 */
	private function get_deprecated_booking_ids(): array {
		static $ids = null;
		if ( null !== $ids ) {
			return $ids;
		}

		global $wpdb;
		$like = '%' . $wpdb->esc_like( self::CART_INFO_VERSION ) . '%';
		$ids  = array_map(
			'absint',
			$wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare(
					"SELECT p.ID FROM {$wpdb->posts} p
					LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'cart_info'
					WHERE p.post_type = 'booking'
					AND p.post_status = 'publish'
					AND (pm.meta_value IS NULL OR pm.meta_value NOT LIKE %s)",
					$like
				)
			)
		);

		return $ids;
	}
}
