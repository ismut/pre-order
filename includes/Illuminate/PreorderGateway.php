<?php

/**
 * Class PaymentGatewayRegister || Add custom Payment Gateway
 */

if (!class_exists('WC_Payment_Gateway')) return;

class PreorderGateway extends WC_Payment_Gateway
{
    /**
     * Constructor for the gateway.
     */
    public function __construct()
    {
        $this->id                = 'sdevs-preorder-gateway';
        $this->icon              = '';
        $this->has_fields        = false;
        $this->method_title      = __('Pay Later', 'sdevs_preorder');
        $this->title             = $this->method_title;
        $this->order_button_text = __('Pay Later', 'sdevs_preorder');

        add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
    }

    /**
     * Admin page.
     */
    public function admin_options()
    {
        $title = (!empty($this->method_title)) ? $this->method_title : __('Settings', 'sdevs_preorder');

        echo '<h3>' . esc_html($title) . '</h3>';

        echo '<p>' . esc_html__('This gateway requires no configuration.', 'sdevs_preorder') . '</p>';

        // Hides the save button
        echo '<style>p.submit input[type="submit"] { display: none }</style>';
    }

    /**
     * Process the payment and return the result
     *
     * @param  int $order_id
     *
     * @return array
     */
    public function process_payment($order_id)
    {
        $order = new WC_Order($order_id);

        // Add custom order note.
        $order->update_status('processing', __('This order is awaiting confirmation.', 'sdevs_preorder'));

        // Remove cart
        WC()->cart->empty_cart();

        // Return thankyou redirect.
        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url($order),
        );
    }

    /**
     * Output for the order received page.
     */
    public function thankyou_page($order_id)
    {
        $order = new WC_Order($order_id);

        if ('completed' == $order->get_status()) {
            echo '<p>' . esc_html__('Your preorder has been confirmed. Thank you.', 'sdevs_preorder') . '</p>';
        } else {
            echo '<p>' . esc_html__('Your preorder is awaiting confirmation. You will be notified by email as soon as we\'ve confirmed availability.', 'sdevs_preorder') . '</p>';
        }
    }
}
