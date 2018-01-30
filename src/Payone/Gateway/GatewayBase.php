<?php

namespace Payone\Gateway;

abstract class GatewayBase extends \WC_Payment_Gateway {
	/**
	 * @var string
	 */
	protected $requestType;

	protected $min_amount;

	public function __construct( $id ) {
		$this->id         = $id;
		$this->has_fields = true;

		$this->init_form_fields();
		$this->init_settings();

		$this->title       = $this->get_option( 'title' );
		$this->requestType = $this->settings['request_type'];
		$this->min_amount  = $this->settings['min_amount'];
		$this->max_amount  = $this->settings['max_amount'];
		$this->countries   = $this->settings['countries'];

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
	}

	public function add( $methods ) {
		$methods[] = get_class( $this );

		return $methods;
	}

	/**
	 * @todo Es ist nicht klar, warum das nicht ohne eigenen Code funktioniert. Die Doku zu $this->countries sieht
	 *       eigentlich so aus, als ob es funktionieren müsste.
	 * @todo Soll die billing_country oder shipping_country genommen werden?
	 *
	 * @return bool
	 */
	public function is_available() {
		$is_available = parent::is_available();

		if ( $is_available && $this->min_amount > $this->get_order_total() ) {
			$is_available = false;
		}

		if ( $is_available ) {
			$order_id = absint( get_query_var( 'order-pay' ) );

			if ( $order_id ) {
				$order = wc_get_order( $order_id );
				$country = (string) $order->get_billing_country();
			} elseif ( WC()->customer->get_billing_country() ) {
				$country = (string) WC()->customer->get_billing_country();
			}

			$is_available = in_array($country, $this->countries);
		}

		return $is_available;
	}

	public function init_common_form_fields( $label ) {
		$this->form_fields = [
			'enabled'      => [
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable payment method', 'payone' ),
				'default' => 'yes',
			],
			'request_type' => [
				'title'   => __( 'Request type', 'payone' ),
				'type'    => 'select',
				'options' => [
					'authorization'    => 'Authorization',
					'preauthorization' => 'Preauthorization',
				],
				'default' => 'authorization',
			],
			'title'        => [
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => $label,
				'desc_tip'    => true,
			],
			'min_amount'   => [
				'title'   => __( 'Minimum order value', 'payone' ),
				'type'    => 'text',
				'default' => '0',
			],
			'max_amount'   => [
				'title'   => __( 'Maximum order value', 'payone' ),
				'type'    => 'text',
				'default' => '0',
			],
			'countries'    => [
				'title'   => __( 'Active Countries', 'payone' ),
				'type'    => 'multiselect',
				'options' => [
					'DE' => __( 'Germany', 'payone' ),
					'AT' => __( 'Austria', 'payone' ),
					'CH' => __( 'Switzerland', 'payone' ),
				],
				'default' => [ 'DE', 'AT', 'CH' ],
			],
			'description'  => [
				'title'   => __( 'Customer Message', 'woocommerce' ),
				'type'    => 'textarea',
				'default' => '',
			],
		];
	}
}