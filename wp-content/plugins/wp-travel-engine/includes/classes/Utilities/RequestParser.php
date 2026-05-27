<?php
/**
 * Request Parser.
 *
 * @package WPTravelEngine
 * @since 6.0.0
 */

namespace WPTravelEngine\Utilities;

use WP_REST_Request;

/**
 * Request Parser class.
 */
class RequestParser extends WP_REST_Request {

	/**
	 * Overridden to return by reference.
	 *
	 * @since 6.8.0
	 */
	public function &offsetGet( $offset ) {
		// 1. Check JSON body parameters
		if ( isset( $this->params['JSON'][ $offset ] ) ) {
			return $this->params['JSON'][ $offset ];
		}

		// 2. Check FILES (uploaded files)
		if ( isset( $this->params['FILES'][ $offset ] ) ) {
			return $this->params['FILES'][ $offset ];
		}

		// 3. Check POST (form data)
		if ( isset( $this->params['POST'][ $offset ] ) ) {
			return $this->params['POST'][ $offset ];
		}

		// 4. Check GET (query string)
		if ( isset( $this->params['GET'][ $offset ] ) ) {
			return $this->params['GET'][ $offset ];
		}

		// 5. Check URL placeholders
		if ( isset( $this->params['URL'][ $offset ] ) ) {
			return $this->params['URL'][ $offset ];
		}

		// Return a reference to a dummy variable for non-existent keys
		$null = null;
		return $null;
	}
}
