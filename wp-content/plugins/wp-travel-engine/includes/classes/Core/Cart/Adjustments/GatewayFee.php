<?php
/**
 * Gateway Fee Adjustment class.
 *
 * @since 6.7.8
 */

namespace WPTravelEngine\Core\Cart\Adjustments;

use WPTravelEngine\Core\Cart\Cart;
use WPTravelEngine\Core\Cart\Item;

class GatewayFee extends BaseCartAdjustment {

	public function __construct( Cart $cart, array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'name'        => 'gateway_fee',
				'label'       => __( 'Gateway Fee', 'wp-travel-engine' ),
				'description' => __( 'This is a gateway fee. It is included by the payment gateway.', 'wp-travel-engine' ),
				'order'       => 55,
				'apply_tax'   => false,
			)
		);

		parent::__construct( $cart, $args );
	}

	/**
	 * @inheritdoc
	 */
	public function apply( float $total, Item $cart_item ): float {
		return floatval( $this->cart->get_totals()[ "total_{$this->name}" ] ?? 0 );
	}
}
