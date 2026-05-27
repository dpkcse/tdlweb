<?php
namespace WPTRAVELENGINEEB;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Templates_Design setup
 *
 * @since 1.0
 */
class Templates_Design {

	/**
	 * The single instance of the class.
	 *
	 * @var Templates_Design|null
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Templates_Design Instance.
	 *
	 * Ensures only one instance of Templates_Design is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Templates_Design - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_render_templates_designs', array( $this, 'render_templates_designs' ) );
		add_action( 'wp_ajax_process_data_for_import', array( $this, 'process_data_for_import' ) );
		add_action( 'wp_ajax_fetch_required_plugins', array( $this, 'fetch_required_plugins' ) );
	}

	/*
	* Get the server list
	*
	* @return array
	*/
	public function get_server_list() {
		return array(
			array(
				'slug' => 'travel-monster',
				'name' => esc_html__( 'Default', 'wptravelengine-elementor-widgets' ),
			),
		);
	}

	/**
	 * Make remote request with retry logic and better error handling
	 *
	 * @param string $url The URL to make the request to.
	 * @param int    $retries The number of retries to make.
	 * @return array|WP_Error The response from the request.
	 */
	private function make_remote_request( $url, $retries = 3 ) {
		$attempt    = 0;
		$last_error = null;

		while ( $attempt < $retries ) {
			$response = wp_remote_get(
				$url,
				array(
					'timeout'     => 30,
					'httpversion' => '1.1',
					'sslverify'   => true,
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$response_code = wp_remote_retrieve_response_code( $response );

				if ( $response_code === 200 ) {
					return $response;
				}

				$last_error = new \WP_Error(
					'http_error',
					sprintf( 'HTTP %d: %s', $response_code, wp_remote_retrieve_response_message( $response ) )
				);
			} else {
				$last_error = $response;
			}

			++$attempt;

			// Exponential backoff: wait 1s, 2s, 4s
			if ( $attempt < $retries ) {
				sleep( pow( 2, $attempt - 1 ) );
			}
		}

		return $last_error;
	}

	/**
	 * Get the templates array from the API
	 *
	 * @param string $server
	 * @return array
	 */
	public function get_templates_array( $server = 'travel-monster' ) {
		$url = 'https://wptravelenginedemo.com/' . $server . '/wp-json/wpte-elementor-templates/v1/patterns/';

		// Check if external requests are blocked
		if ( $this->is_http_blocked( $url ) ) {
			error_log( 'WP Travel Engine Elementor: External requests to ' . $url . ' are blocked. Please add "wptravelenginedemo.com" to WP_ACCESSIBLE_HOSTS.' );
			return array( 'error' => __( 'External requests are blocked. Please contact your administrator.', 'wptravelengine-elementor-widgets' ) );
		}

		$apiData = $this->make_remote_request( $url );

		if ( ! is_wp_error( $apiData ) && wp_remote_retrieve_response_code( $apiData ) == 200 ) {
			$body     = wp_remote_retrieve_body( $apiData );
			$data     = json_decode( $body );
			$apiArray = array();
			if ( is_array( $data ) ) {
				$apiArray = $data;
			}
			return (array) $apiArray;
		}

		// Log the error for debugging
		if ( is_wp_error( $apiData ) ) {
			error_log( 'WP Travel Engine Elementor: Failed to fetch templates - ' . $apiData->get_error_message() );
			return array( 'error' => $apiData->get_error_message() );
		}

		return array();
	}

	/**
	 * Check if HTTP requests are blocked for the given URL
	 *
	 * @param string $url
	 * @return bool
	 */
	private function is_http_blocked( $url ) {
		if ( ! defined( 'WP_HTTP_BLOCK_EXTERNAL' ) || ! WP_HTTP_BLOCK_EXTERNAL ) {
			return false;
		}

		$host = parse_url( $url, PHP_URL_HOST );

		if ( defined( 'WP_ACCESSIBLE_HOSTS' ) ) {
			$accessible_hosts = preg_split( '|,\s*|', WP_ACCESSIBLE_HOSTS );
			if ( in_array( $host, $accessible_hosts ) || in_array( '*.' . $host, $accessible_hosts ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Validate URL against whitelist to prevent SSRF attacks
	 *
	 * @param string $url The URL to validate.
	 * @return true|WP_Error True if valid, WP_Error if invalid.
	 */
	private function validate_api_url( $url ) {
		// Parse URL
		$parsed = parse_url( $url );

		// Check if URL is valid
		if ( ! $parsed || ! isset( $parsed['host'] ) || ! isset( $parsed['scheme'] ) ) {
			return new \WP_Error(
				'invalid_url',
				__( 'Invalid URL format', 'wptravelengine-elementor-widgets' )
			);
		}

		// Only allow HTTP and HTTPS schemes
		if ( ! in_array( $parsed['scheme'], array( 'http', 'https' ), true ) ) {
			return new \WP_Error(
				'invalid_scheme',
				__( 'Only HTTP and HTTPS protocols are allowed', 'wptravelengine-elementor-widgets' )
			);
		}

		// Whitelist: Only allow requests to wptravelenginedemo.com
		$allowed_hosts = array( 'wptravelenginedemo.com', 'www.wptravelenginedemo.com' );
		if ( ! in_array( $parsed['host'], $allowed_hosts, true ) ) {
			return new \WP_Error(
				'invalid_host',
				sprintf(
					__( 'Requests are only allowed to %s', 'wptravelengine-elementor-widgets' ),
					'wptravelenginedemo.com'
				)
			);
		}

		// Additional security: Block private/internal IP addresses
		$ip = gethostbyname( $parsed['host'] );
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
			return new \WP_Error(
				'private_ip',
				__( 'Requests to private IP addresses are not allowed', 'wptravelengine-elementor-widgets' )
			);
		}

		return true;
	}

	/**
	 * Get the category list from the templates array
	 * with the count of each category
	 *
	 * @param array $data
	 * @return array
	 */
	protected function get_category_list( $data ) {
		$get_cat_data = array();
		foreach ( $data as $data_content ) {
			if ( isset( $data_content->cat ) ) {
				foreach ( $data_content->cat as $key => $item ) {
					$get_cat_data[ $item->slug ]['name'] = $item->name;
					if ( isset( $get_cat_data[ $item->slug ]['count'] ) ) {
						++$get_cat_data[ $item->slug ]['count'];
					} else {
						$get_cat_data[ $item->slug ]['count'] = 1;
					}
				}
			}
		}
		return $get_cat_data;
	}

	/**
	 * Get the total count for the category
	 *
	 * @param array $cat_list
	 * @return string
	 */
	public function get_total_count_for_category( $cat_list ) {
		$totalCount = 0;
		foreach ( $cat_list as $catData ) {
			if ( isset( $catData['count'] ) ) {
				$totalCount += $catData['count'];
			}
		}
		return '(' . absint( $totalCount ) . ')';
	}

	/**
	 * Render the templates designs as per the requested server
	 *
	 * @return void
	 */
	public function render_templates_designs() {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing.
		$server = isset( $_POST['server'] ) ? sanitize_text_field( $_POST['server'] ) : 'wptravelengine-elementor-widgets';
		$data   = $this->get_templates_array( $server );

		// Check if there was an error fetching templates
		if ( isset( $data['error'] ) ) {
			?>
			<div class="cw-error-message">
				<svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 20px; opacity: 0.5;">
					<path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<h3><?php esc_html_e( 'Unable to Load Templates', 'wptravelengine-elementor-widgets' ); ?></h3>
				<p><?php echo esc_html( $data['error'] ); ?></p>
				<details>
					<summary><?php esc_html_e( 'Troubleshooting Steps', 'wptravelengine-elementor-widgets' ); ?></summary>
					<ol>
						<li><?php esc_html_e( 'Check your internet connection', 'wptravelengine-elementor-widgets' ); ?></li>
						<li><?php esc_html_e( 'Verify your server can make outbound HTTP requests', 'wptravelengine-elementor-widgets' ); ?></li>
						<li><?php esc_html_e( 'Check if your firewall is blocking wptravelenginedemo.com', 'wptravelengine-elementor-widgets' ); ?></li>
						<li><?php esc_html_e( 'If WP_HTTP_BLOCK_EXTERNAL is enabled, add wptravelenginedemo.com to WP_ACCESSIBLE_HOSTS', 'wptravelengine-elementor-widgets' ); ?></li>
						<li><?php esc_html_e( 'Contact your hosting provider if the issue persists', 'wptravelengine-elementor-widgets' ); ?></li>
					</ol>
				</details>
			</div>
			<?php
			wp_die();
		}

		// Check if no templates were found
		if ( empty( $data ) ) {
			?>
			<div class="cw-error-message" style="padding: 40px 20px; text-align: center;">
				<h3 style="margin: 0 0 10px 0;"><?php esc_html_e( 'No Templates Found', 'wptravelengine-elementor-widgets' ); ?></h3>
				<p style="margin: 0; color: #646970;"><?php esc_html_e( 'No templates are currently available. Please try again later.', 'wptravelengine-elementor-widgets' ); ?></p>
			</div>
			<?php
			wp_die();
		}

		$cat_list = $this->get_category_list( $data );
		?>
		<div class="cw-pattern-library__content">
			<aside class="cw-pattern-library__sidebar">
				<div class='cw-pattern-library__sidebar-sticky'>
					<div class="cw-search-icon">
						<label for="cw-search-control" class="visually-hidden">
							<?php esc_html_e( 'Search', 'wptravelengine-elementor-widgets' ); ?>
						</label>
						<input type="search" id="cw-search-control" placeholder="<?php esc_attr_e( 'Search', 'wptravelengine-elementor-widgets' ); ?>">
						<i class="eicon-search"></i>
					</div>
					<div class="cw-pattern-library__category">
						<ul class="cw-pattern-library__category-list">
							<li>
								<button class="cw-cat-item tab-active transform-scale" data-filter="all">
									<?php esc_html_e( 'All', 'wptravelengine-elementor-widgets' ); ?>
									<span><?php echo $this->get_total_count_for_category( $cat_list ); ?></span>
								</button>
							</li>
							<?php
							foreach ( $cat_list as $slug => $cat_data ) {
								$count = isset( $cat_data['count'] ) ? $cat_data['count'] : '';
								?>
								<li>
									<button class="cw-cat-item transform-scale" data-filter="<?php echo esc_attr( $slug ); ?>">
										<?php echo esc_html( $cat_data['name'] ); ?>
										<span><?php echo '(' . esc_html( $count ) . ')'; ?></span>
									</button>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</aside>
			<div class="cw-pattern-library__main">
				<div class="cw-pattern-library__topbar">
					<div class='cw-pattern-library__btn-group'>
						<button class="cw-info-btn" aria-label="<?php esc_attr_e( 'Info Icon', 'wptravelengine-elementor-widgets' ); ?>">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12.5903 10.6919C12.5636 10.5444 12.4826 10.4122 12.3633 10.3214C12.244 10.2306 12.095 10.1877 11.9457 10.2013C11.7964 10.2148 11.6575 10.2838 11.5565 10.3946C11.4555 10.5054 11.3996 10.65 11.3999 10.7999V16.2023L11.4095 16.3103C11.4362 16.4578 11.5172 16.59 11.6365 16.6808C11.7558 16.7717 11.9048 16.8145 12.0541 16.801C12.2034 16.7874 12.3423 16.7184 12.4433 16.6076C12.5443 16.4968 12.6002 16.3522 12.5999 16.2023V10.7999L12.5903 10.6919ZM12.9587 8.0999C12.9587 7.86121 12.8639 7.63229 12.6951 7.46351C12.5263 7.29472 12.2974 7.1999 12.0587 7.1999C11.82 7.1999 11.5911 7.29472 11.4223 7.46351C11.2535 7.63229 11.1587 7.86121 11.1587 8.0999C11.1587 8.3386 11.2535 8.56752 11.4223 8.7363C11.5911 8.90508 11.82 8.9999 12.0587 8.9999C12.2974 8.9999 12.5263 8.90508 12.6951 8.7363C12.8639 8.56752 12.9587 8.3386 12.9587 8.0999ZM21.5999 11.9999C21.5999 9.45382 20.5885 7.01203 18.7881 5.21168C16.9878 3.41133 14.546 2.3999 11.9999 2.3999C9.45382 2.3999 7.01203 3.41133 5.21168 5.21168C3.41133 7.01203 2.3999 9.45382 2.3999 11.9999C2.3999 14.546 3.41133 16.9878 5.21168 18.7881C7.01203 20.5885 9.45382 21.5999 11.9999 21.5999C14.546 21.5999 16.9878 20.5885 18.7881 18.7881C20.5885 16.9878 21.5999 14.546 21.5999 11.9999ZM3.5999 11.9999C3.5999 10.8968 3.81717 9.8045 4.23931 8.78536C4.66145 7.76623 5.28019 6.84022 6.06021 6.06021C6.84022 5.28019 7.76623 4.66145 8.78536 4.23931C9.8045 3.81717 10.8968 3.5999 11.9999 3.5999C13.103 3.5999 14.1953 3.81717 15.2144 4.23931C16.2336 4.66145 17.1596 5.28019 17.9396 6.06021C18.7196 6.84022 19.3384 7.76623 19.7605 8.78536C20.1826 9.8045 20.3999 10.8968 20.3999 11.9999C20.3999 14.2277 19.5149 16.3643 17.9396 17.9396C16.3643 19.5149 14.2277 20.3999 11.9999 20.3999C9.77208 20.3999 7.63551 19.5149 6.06021 17.9396C4.4849 16.3643 3.5999 14.2277 3.5999 11.9999Z" fill="currentColor"/>
							</svg>
						</button>
						<span class="divider"></span>
						<button class="cw-layout-three transform-scale active" aria-label="<?php esc_attr_e( 'Change pattern\'s layout into 3 columns', 'wptravelengine-elementor-widgets' ); ?>" >
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M20.1819 9.54552H16.9091C16.091 9.54552 16.091 9.54552 16.091 10.3636V13.6364C16.091 14.4545 16.091 14.4545 16.9091 14.4545H20.1819C21 14.4545 21 14.4545 21 13.6364V10.3636C21 9.54552 21 9.54552 20.1819 9.54552ZM13.6364 3H10.3636C9.54552 3 9.54552 3 9.54552 3.8181V7.09086C9.54552 7.90896 9.54552 7.90896 10.3636 7.90896H13.6364C14.4545 7.90896 14.4545 7.90896 14.4545 7.09086V3.8181C14.4545 3 14.4545 3 13.6364 3ZM20.1819 16.0909H16.9091C16.091 16.0909 16.091 16.0909 16.091 16.909V20.1817C16.091 20.9998 16.091 20.9998 16.9091 20.9998H20.1819C21 21 21 21 21 20.1819V16.9091C21 16.0909 21 16.0909 20.1819 16.0909ZM13.6364 9.54552H10.3636C9.54552 9.54552 9.54552 9.54552 9.54552 10.3636V13.6364C9.54552 14.4545 9.54552 14.4545 10.3636 14.4545H13.6364C14.4545 14.4545 14.4545 14.4545 14.4545 13.6364V10.3636C14.4545 9.54552 14.4545 9.54552 13.6364 9.54552ZM7.09086 3H3.8181C3 3 3 3 3 3.8181V7.09086C3 7.90896 3 7.90896 3.8181 7.90896H7.09086C7.90896 7.90896 7.90896 7.90896 7.90896 7.09086V3.8181C7.90914 3 7.90914 3 7.09086 3ZM13.6364 16.0909H10.3636C9.54552 16.0909 9.54552 16.0909 9.54552 16.909V20.1817C9.54552 20.9998 9.54552 20.9998 10.3636 20.9998H13.6364C14.4545 20.9998 14.4545 20.9998 14.4545 20.1817V16.9091C14.4545 16.0909 14.4545 16.0909 13.6364 16.0909ZM7.09086 9.54552H3.8181C3 9.54552 3 9.54552 3 10.3636V13.6364C3 14.4545 3 14.4545 3.8181 14.4545H7.09086C7.90896 14.4545 7.90896 14.4545 7.90896 13.6364V10.3636C7.90914 9.54552 7.90914 9.54552 7.09086 9.54552ZM7.09086 16.0909H3.8181C3 16.0909 3 16.0909 3 16.9091V20.1819C3 21 3 21 3.8181 21H7.09086C7.90896 21 7.90896 21 7.90896 20.1819V16.9091C7.90914 16.0909 7.90914 16.0909 7.09086 16.0909ZM20.1819 3H16.9091C16.091 3 16.091 3 16.091 3.8181V7.09086C16.091 7.90896 16.091 7.90896 16.9091 7.90896H20.1819C21 7.90896 21 7.90896 21 7.09086V3.8181C21 3 21 3 20.1819 3Z" fill="currentColor"/>
							</svg>
						</button>
						<button class="cw-layout-two transform-scale" aria-label="<?php esc_attr_e( 'Change pattern\'s layout into 2 columns', 'wptravelengine-elementor-widgets' ); ?>" >
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M11 4.44133C11 3.64533 10.3547 3 9.55867 3H4.44133C3.64533 3 3 3.64533 3 4.44133V9.55867C3 10.3547 3.64533 11 4.44133 11H9.55867C10.3547 11 11 10.3547 11 9.55867V4.44133Z" fill="currentColor"/>
								<path d="M21 4.44133C21 3.64533 20.3547 3 19.5587 3H14.4413C13.6453 3 13 3.64533 13 4.44133V9.55867C13 10.3547 13.6453 11 14.4413 11H19.5587C20.3547 11 21 10.3547 21 9.55867V4.44133Z" fill="currentColor"/>
								<path d="M11 14.4413C11 13.6453 10.3547 13 9.55867 13H4.44133C3.64533 13 3 13.6453 3 14.4413V19.5587C3 20.3547 3.64533 21 4.44133 21H9.55867C10.3547 21 11 20.3547 11 19.5587V14.4413Z" fill="currentColor"/>
								<path d="M21 14.4413C21 13.6453 20.3547 13 19.5587 13H14.4413C13.6453 13 13 13.6453 13 14.4413V19.5587C13 20.3547 13.6453 21 14.4413 21H19.5587C20.3547 21 21 20.3547 21 19.5587V14.4413Z" fill="currentColor"/>
							</svg>
						</button>
					</div>
				</div>
				<div class="cw-pattern-library__design">
					<div class="cw-pattern-library__design-wrap">
					<ul class="cw-pattern-library__design-list">
						<?php
						foreach ( $data as $item ) {
							$categories_list = $this->filter_cat_slug( $item );
							?>
							<li class='cw-pattern-library__design-item' data-filter="<?php echo esc_attr( $categories_list ); ?>" data-filter-name="<?php echo esc_attr( $item->title ); ?>">
								<?php if ( isset( $item->featured_media ) ) { ?>
									<div class="cw-pattern-library__design-item-img">
										<button class="cw-pattern-library__design-preview-btn" data-preview-url="<?php echo esc_attr( $item->permalink ); ?>" data-slug-id="<?php echo esc_attr( $item->id ); ?>">
											<span>
											<svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M17.8913 7.54375C16.1966 4.23719 12.8416 2 9 2C5.15844 2 1.8025 4.23875 0.10875 7.54406C0.0372632 7.68547 1.71661e-05 7.8417 1.71661e-05 8.00016C1.71661e-05 8.15861 0.0372632 8.31484 0.10875 8.45625C1.80344 11.7628 5.15844 14 9 14C12.8416 14 16.1975 11.7612 17.8913 8.45594C17.9627 8.31453 18 8.1583 18 7.99984C18 7.84139 17.9627 7.68516 17.8913 7.54375ZM9 12.5C8.10998 12.5 7.23995 12.2361 6.49993 11.7416C5.75991 11.2471 5.18314 10.5443 4.84254 9.72208C4.50195 8.89981 4.41283 7.99501 4.58647 7.12209C4.7601 6.24918 5.18868 5.44736 5.81802 4.81802C6.44736 4.18868 7.24918 3.7601 8.12209 3.58647C8.99501 3.41283 9.89981 3.50195 10.7221 3.84254C11.5443 4.18314 12.2471 4.75991 12.7416 5.49993C13.2361 6.23995 13.5 7.10998 13.5 8C13.5003 8.59103 13.3841 9.17632 13.158 9.72242C12.932 10.2685 12.6005 10.7647 12.1826 11.1826C11.7647 11.6005 11.2685 11.932 10.7224 12.158C10.1763 12.3841 9.59103 12.5003 9 12.5ZM9 5C8.73223 5.00374 8.46618 5.04358 8.20906 5.11844C8.42101 5.40646 8.52271 5.7609 8.49574 6.11748C8.46876 6.47406 8.31489 6.80917 8.06203 7.06203C7.80917 7.31489 7.47406 7.46876 7.11748 7.49574C6.7609 7.52271 6.40646 7.42101 6.11844 7.20906C5.95443 7.81331 5.98403 8.45377 6.20309 9.04031C6.42214 9.62685 6.81962 10.1299 7.33957 10.4787C7.85951 10.8275 8.47575 11.0045 9.10155 10.9847C9.72735 10.965 10.3312 10.7495 10.8281 10.3685C11.325 9.9876 11.6899 9.46044 11.8715 8.86125C12.0531 8.26205 12.0422 7.62099 11.8404 7.0283C11.6386 6.43561 11.256 5.92114 10.7464 5.55728C10.2369 5.19343 9.62611 4.99853 9 5Z" fill="white"/>
											</svg>
											</span>
											<?php esc_html_e( 'Click to Preview', 'wptravelengine-elementor-widgets' ); ?>
										</button>
										<img src="<?php echo esc_url( $item->featured_media ); ?>"/>
									</div>
								<?php } else { ?>
									<div class="cw-pattern-library__design-item-img no-featured-img">
										<button class="cw-pattern-library__design-preview-btn" data-preview-url="<?php echo esc_attr( $item->permalink ); ?>" data-slug-id="<?php echo esc_attr( $item->id ); ?>">
											<span>
											<svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M17.8913 7.54375C16.1966 4.23719 12.8416 2 9 2C5.15844 2 1.8025 4.23875 0.10875 7.54406C0.0372632 7.68547 1.71661e-05 7.8417 1.71661e-05 8.00016C1.71661e-05 8.15861 0.0372632 8.31484 0.10875 8.45625C1.80344 11.7628 5.15844 14 9 14C12.8416 14 16.1975 11.7612 17.8913 8.45594C17.9627 8.31453 18 8.1583 18 7.99984C18 7.84139 17.9627 7.68516 17.8913 7.54375ZM9 12.5C8.10998 12.5 7.23995 12.2361 6.49993 11.7416C5.75991 11.2471 5.18314 10.5443 4.84254 9.72208C4.50195 8.89981 4.41283 7.99501 4.58647 7.12209C4.7601 6.24918 5.18868 5.44736 5.81802 4.81802C6.44736 4.18868 7.24918 3.7601 8.12209 3.58647C8.99501 3.41283 9.89981 3.50195 10.7221 3.84254C11.5443 4.18314 12.2471 4.75991 12.7416 5.49993C13.2361 6.23995 13.5 7.10998 13.5 8C13.5003 8.59103 13.3841 9.17632 13.158 9.72242C12.932 10.2685 12.6005 10.7647 12.1826 11.1826C11.7647 11.6005 11.2685 11.932 10.7224 12.158C10.1763 12.3841 9.59103 12.5003 9 12.5ZM9 5C8.73223 5.00374 8.46618 5.04358 8.20906 5.11844C8.42101 5.40646 8.52271 5.7609 8.49574 6.11748C8.46876 6.47406 8.31489 6.80917 8.06203 7.06203C7.80917 7.31489 7.47406 7.46876 7.11748 7.49574C6.7609 7.52271 6.40646 7.42101 6.11844 7.20906C5.95443 7.81331 5.98403 8.45377 6.20309 9.04031C6.42214 9.62685 6.81962 10.1299 7.33957 10.4787C7.85951 10.8275 8.47575 11.0045 9.10155 10.9847C9.72735 10.965 10.3312 10.7495 10.8281 10.3685C11.325 9.9876 11.6899 9.46044 11.8715 8.86125C12.0531 8.26205 12.0422 7.62099 11.8404 7.0283C11.6386 6.43561 11.256 5.92114 10.7464 5.55728C10.2369 5.19343 9.62611 4.99853 9 5Z" fill="white"/>
											</svg>
											</span>
											<?php esc_html_e( 'Click to Preview', 'wptravelengine-elementor-widgets' ); ?>
										</button>
										<span>
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" clip-rule="evenodd" d="M3.172 3.172C2 4.343 2 6.229 2 10V14C2 17.771 2 19.657 3.172 20.828C4.344 21.999 6.229 22 10 22H14C17.771 22 19.657 22 20.828 20.828C21.999 19.656 22 17.771 22 14V10C22 7.16 22 5.39 21.5 4.189V17C21.0246 17.0001 20.5538 16.9065 20.1146 16.7246C19.6753 16.5427 19.2762 16.2761 18.94 15.94L18.188 15.188C17.466 14.466 17.106 14.106 16.697 13.954C16.2474 13.7868 15.7526 13.7868 15.303 13.954C14.894 14.106 14.533 14.466 13.813 15.188L13.699 15.301C13.114 15.886 12.821 16.179 12.51 16.233C12.2685 16.2756 12.0197 16.2279 11.811 16.099C11.543 15.933 11.38 15.552 11.053 14.791L11 14.667C10.25 12.917 9.876 12.043 9.222 11.715C8.89266 11.5499 8.52411 11.4789 8.157 11.51C7.428 11.572 6.756 12.245 5.41 13.59L3.5 15.5V2.887C3.384 2.973 3.27467 3.068 3.172 3.172Z" fill="#0EB3A0"/>
												<path d="M3 10C3 8.086 3.002 6.751 3.138 5.744C3.269 4.766 3.51 4.248 3.878 3.879C4.248 3.509 4.766 3.269 5.744 3.138C6.751 3.002 8.086 3 10 3H14C15.914 3 17.249 3.002 18.256 3.138C19.234 3.269 19.752 3.51 20.121 3.878C20.491 4.248 20.731 4.766 20.863 5.744C20.998 6.751 21 8.086 21 10V14C21 15.914 20.998 17.249 20.863 18.256C20.731 19.234 20.49 19.752 20.121 20.121C19.752 20.491 19.234 20.731 18.256 20.863C17.249 20.998 15.914 21 14 21H10C8.086 21 6.751 20.998 5.744 20.863C4.766 20.731 4.248 20.49 3.879 20.121C3.509 19.752 3.269 19.234 3.138 18.256C3.002 17.249 3 15.914 3 14V10Z" stroke="#0EB3A0" stroke-width="2"/>
												<path d="M15 11C16.1046 11 17 10.1046 17 9C17 7.89543 16.1046 7 15 7C13.8954 7 13 7.89543 13 9C13 10.1046 13.8954 11 15 11Z" fill="#0EB3A0"/>
											</svg>
										</span>
									</div>
								<?php } ?>
								<div class="cw-pattern-library__design-item-info">
									<span><?php echo esc_html( $item->title ); ?></span>
									<button class="cw-insert-temp transform-scale" data-slug-id="<?php echo esc_attr( $item->id ); ?>"><?php esc_html_e( 'Import', 'wptravelengine-elementor-widgets' ); ?></button>
								</div>
							</li>
						<?php } ?>
					</ul>
					</div>
				</div>
			</div>
		</div>

		<?php
		wp_die();
	}

	/**
	 * Process the data for import
	 *
	 * @return void
	 */
	public function process_data_for_import() {

		// Verify Nonce.
		\check_ajax_referer( 'elementor_import_site', 'nonce' );

		if ( ! \current_user_can( 'edit_posts' ) ) {
			\wp_send_json_error(
				array(
					'message' => __( 'You are not allowed to perform this action', 'wptravelengine-elementor-widgets' ),
				)
			);
		}

		$apiURL = isset( $_POST['apiURL'] ) ? \esc_url_raw( $_POST['apiURL'] ) : '';

		if ( empty( $apiURL ) ) {
			\wp_send_json_error(
				array(
					'message' => __( 'Invalid API URL provided', 'wptravelengine-elementor-widgets' ),
				)
			);
		}

		// SECURITY: Validate URL to prevent SSRF attacks
		$validation = $this->validate_api_url( $apiURL );
		if ( is_wp_error( $validation ) ) {
			\wp_send_json_error(
				array(
					'message' => $validation->get_error_message(),
					'code'    => $validation->get_error_code(),
				)
			);
		}

		// Check if external requests are blocked
		if ( $this->is_http_blocked( $apiURL ) ) {
			\wp_send_json_error(
				array(
					'message' => __( 'External requests are blocked. Please add "wptravelenginedemo.com" to WP_ACCESSIBLE_HOSTS in your wp-config.php file.', 'wptravelengine-elementor-widgets' ),
					'code'    => 'http_blocked',
				)
			);
		}

		$response = $this->make_remote_request( $apiURL );

		if ( \is_wp_error( $response ) ) {
			\wp_send_json_error(
				array(
					'message' => sprintf(
						__( 'Failed to fetch template data: %s', 'wptravelengine-elementor-widgets' ),
						$response->get_error_message()
					),
					'code'    => $response->get_error_code(),
				)
			);
		}

		$response_code = \wp_remote_retrieve_response_code( $response );
		if ( $response_code !== 200 ) {
			\wp_send_json_error(
				array(
					'message' => sprintf(
						__( 'Server returned error code: %d', 'wptravelengine-elementor-widgets' ),
						$response_code
					),
					'code'    => 'http_' . $response_code,
				)
			);
		}

		$body = \wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data ) || ! isset( $data['content'] ) ) {
			\wp_send_json_error(
				array(
					'message' => __( 'Invalid response format from API', 'wptravelengine-elementor-widgets' ),
					'code'    => 'invalid_response',
				)
			);
		}

		$meta = json_decode( $data['content'], true );

		if ( empty( $meta ) || ! isset( $meta['content'] ) ) {
			\wp_send_json_error(
				array(
					'message' => __( 'No Content Found in template', 'wptravelengine-elementor-widgets' ),
					'code'    => 'no_content',
				)
			);
		}

		try {
			$import      = new \WPTRAVELENGINEEB\Import_Content();
			$import_data = $import->import( $meta['content'] );
			\wp_send_json_success( $import_data );
		} catch ( \Exception $e ) {
			\wp_send_json_error(
				array(
					'message' => sprintf(
						__( 'Import failed: %s', 'wptravelengine-elementor-widgets' ),
						$e->getMessage()
					),
					'code'    => 'import_error',
				)
			);
		}
	}

	/**
	 * Filter category slug
	 *
	 * @param object $item
	 * @return string
	 */
	public function filter_cat_slug( $item ) {
		$cat_slug = array();

		if ( isset( $item->cat ) ) {
			foreach ( $item->cat as $key => $cat ) {
				$cat_slug[] = $cat->slug;
			}
		}

		return implode( ' ', $cat_slug );
	}

	/**
	 * Get the active plugins on current WordPress installation
	 *
	 * @return array
	 */
	public function get_active_plugins() {
		$active_plugins = get_plugins();
		$plugins        = array();

		foreach ( $active_plugins as $key => $plugin ) {
			if ( is_plugin_active( $key ) ) {
				$extract     = explode( '/', $key );
				$path        = ABSPATH . 'wp-content/plugins/' . $key;
				$plugin_data = get_plugin_data( $path );
				$plugins[]   = array(
					'name'    => $plugin_data['Name'],
					'slug'    => $extract[0],
					'version' => $plugin_data['Version'],
				);
			}
		}

		return $plugins;
	}

	/**
	 * AJAX handler to fetch required plugins for templates
	 * This routes the JavaScript fetch() through WordPress to avoid CORS issues
	 *
	 * @return void
	 */
	public function fetch_required_plugins() {
		// Verify nonce for security
		\check_ajax_referer( 'elementor_import_site', 'nonce' );

		if ( ! \current_user_can( 'edit_posts' ) ) {
			\wp_send_json_error(
				array(
					'message' => __( 'You are not allowed to perform this action', 'wptravelengine-elementor-widgets' ),
				)
			);
		}

		$server = isset( $_POST['server'] ) ? \sanitize_text_field( $_POST['server'] ) : 'travel-monster';
		$url    = "https://wptravelenginedemo.com/{$server}/wp-json/wpte-elementor-templates/v1/patterns/";

		// SECURITY: Validate URL to prevent SSRF attacks
		$validation = $this->validate_api_url( $url );
		if ( is_wp_error( $validation ) ) {
			\wp_send_json_error(
				array(
					'message' => $validation->get_error_message(),
					'code'    => $validation->get_error_code(),
				)
			);
		}

		// Check if external requests are blocked
		if ( $this->is_http_blocked( $url ) ) {
			\wp_send_json_error(
				array(
					'message' => __( 'External requests are blocked. Please add "wptravelenginedemo.com" to WP_ACCESSIBLE_HOSTS in your wp-config.php file.', 'wptravelengine-elementor-widgets' ),
					'code'    => 'http_blocked',
				)
			);
		}

		$response = $this->make_remote_request( $url );

		if ( \is_wp_error( $response ) ) {
			\wp_send_json_error(
				array(
					'message' => sprintf(
						__( 'Failed to fetch templates: %s', 'wptravelengine-elementor-widgets' ),
						$response->get_error_message()
					),
					'code'    => $response->get_error_code(),
				)
			);
		}

		$body = \wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data ) || ! is_array( $data ) ) {
			\wp_send_json_error(
				array(
					'message' => __( 'No templates found or invalid response format', 'wptravelengine-elementor-widgets' ),
					'code'    => 'invalid_response',
				)
			);
		}

		// Extract required plugins for each template
		$required_plugins = array();
		foreach ( $data as $template ) {
			if ( isset( $template['id'] ) && isset( $template['meta']['required_plugins'] ) ) {
				$required_plugins[ $template['id'] ] = $template['meta']['required_plugins'];
			}
		}

		\wp_send_json_success( $required_plugins );
	}
}
