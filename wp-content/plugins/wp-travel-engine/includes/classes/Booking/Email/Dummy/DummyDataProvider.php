<?php
/**
 * Dummy Data Provider for Email Template Preview.
 *
 * @since 6.7.9
 */

namespace WPTravelEngine\Booking\Email\Dummy;

/**
 * DummyDataProvider class.
 *
 * @since 6.7.9
 */
class DummyDataProvider {

	/**
	 * @return array{ fname: string, lname: string, email: string, address: string, city: string, country: string, phone: string }
	 */
	public static function get_billing_info(): array {
		return array(
			'fname'   => 'John',
			'lname'   => 'Doe',
			'email'   => 'john.doe@example.com',
			'address' => '123 Adventure Lane',
			'city'    => 'Kathmandu',
			'country' => 'NP',
			'phone'   => '+977-9800000000',
		);
	}

	/**
	 * @return array{ ID: int, title: string, datetime: string, end_datetime: string, package_name: string, has_time: bool, cost: int, pax: array, pax_cost: array, trip_extras: array }
	 */
	public static function get_order_trip(): array {
		return array(
			'ID'           => 0,
			'title'        => 'Everest Base Camp Trek',
			'datetime'     => '2025-06-01',
			'end_datetime' => '2025-06-15',
			'package_name' => 'Standard Package',
			'has_time'     => false,
			'cost'         => 1500,
			'pax'          => array(
				'Adult' => 2,
				'Child' => 1,
			),
			'pax_cost'     => array(
				'Adult' => 1200,
				'Child' => 300,
			),
			'trip_extras'  => array(
				array(
					'extra_service' => 'Airport Transfer',
					'qty'           => 1,
					'price'         => 50,
				),
				array(
					'accommodation' => 'Single Room',
					'qty'           => 1,
					'price'         => 100,
				),
				array(
					'pickupoints' => 'Kathmandu',
					'qty'         => 1,
					'price'       => 50,
				),
				array(
					'travel_insurance' => 'Basic Coverage',
					'qty'              => 3,
					'price'            => 30,
				),
			),
		);
	}

	/**
	 * Cart info with fully verified calculations:
	 *
	 * @return array{ currency: string, subtotal: float, total: float, payment_type: string, tax_percentage: int, discounts: array, items: array, totals: array }
	 */
	public static function get_cart_info(): array {
		return array(
			'currency'       => 'USD',
			'subtotal'       => 1890.00,
			'total'          => 1690.00,
			'payment_type'   => 'partial',
			'tax_percentage' => 15,
			'discounts'      => array(
				'labels'  => 'Big Sale',
				'amounts' => 200.00,
			),
			'items'          => array(
				array(
					'travelers'       => array(
						'Adult' => 2,
						'Child' => 1,
					),
					'travelers_count' => 3,
					'line_items'      => array(
						'accommodations'   => array(
							array(
								'label'    => 'Budget Friendly',
								'quantity' => 2,
								'price'    => 100.00,
								'total'    => 200.00,
							),
						),
						'extra_services'   => array(
							array(
								'label'    => 'Airport Transfer',
								'quantity' => 1,
								'price'    => 50.00,
								'total'    => 50.00,
							),
						),
						'pickup_points'    => array(
							array(
								'label'    => 'KTM (Traveller 1)',
								'quantity' => 1,
								'price'    => 50.00,
								'total'    => 50.00,
							),
						),
						'travel_insurance' => array(
							array(
								'label'    => 'Basic Coverage',
								'quantity' => 3,
								'price'    => 30.00,
								'total'    => 90.00,
							),
						),
					),
				),
			),
			'totals'         => array(
				'partial_total' => 750.00,
				'due_total'     => 1193.50,
				'total_tax'     => 253.50,
			),
		);
	}

	/**
	 * @return \stdClass
	 */
	public static function get_booking(): \stdClass {
		$booking                                   = new \stdClass();
		$booking->ID                               = 12345;
		$booking->order_trips                      = array( self::get_order_trip() );
		$booking->billing_info                     = self::get_billing_info();
		$booking->cart_info                        = self::get_cart_info();
		$booking->wptravelengine_billing_details   = self::get_billing_info();
		$booking->wptravelengine_travelers_details = array( self::get_traveller() );
		$booking->wptravelengine_emergency_details = self::get_emergency_contact();
		$booking->wptravelengine_additional_note   = 'Please arrange vegetarian meals for the group.';
		$booking->paid_amount                      = 750.00;
		$booking->due_amount                       = 1032.50;
		return $booking;
	}

	/**
	 * @return \stdClass
	 */
	public static function get_payment(): \stdClass {
		$payment                  = new \stdClass();
		$payment->ID              = 67890;
		$payment->payment_gateway = 'Stripe Payment';
		$payment->paid_amount     = 750.00;
		$payment->payable         = array(
			'currency' => 'USD',
			'amount'   => 750.00,
		);
		return $payment;
	}

	/**
	 * @return array
	 */
	public static function get_traveller(): array {
		return array(
			'first_name' => 'John',
			'last_name'  => 'Doe',
			'email'      => 'john.doe@example.com',
			'phone'      => '+977-9800000000',
		);
	}

	/**
	 * @return array
	 */
	public static function get_bank_account(): array {
		return array(
			'account_name'   => 'Demo Travel Account',
			'account_number' => '12345678',
			'bank_name'      => 'Global Travel Bank',
			'sort_code'      => '12-34-56',
			'iban'           => 'GB12DEMO12345612345678',
			'swift'          => 'DEMOBANKXXX',
		);
	}

	/**
	 * @return array
	 */
	public static function get_emergency_contact(): array {
		return array(
			'fname'    => 'Jane',
			'lname'    => 'Doe',
			'email'    => 'jane.doe@example.com',
			'phone'    => '+977-9800000001',
			'relation' => 'Spouse',
		);
	}
}
