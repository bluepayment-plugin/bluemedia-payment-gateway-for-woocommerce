<?php

final class WC_Payment_Gateway_BlueMedia_Blik_Zero extends WC_Payment_Gateway
{
    protected $bluemedia_payment;

    public function __construct()
    {
        $this->id = 'bluemedia_payment_gateway_blik';
        $this->bluemedia_payment = new WC_Payment_Gateway_BlueMedia();

        $this->title = __("BLIK", 'bluepayment-gateway-for-woocommerce');
        $this->description = $this->get_blik_form_content();
        $this->method_title = __("Pay via Blue Media online payment system", 'bluepayment-gateway-for-woocommerce');
        $this->method_description = __("Let your customers pay with BLIK on your store's website", 'bluepayment-gateway-for-woocommerce');
    }

    public function is_available()
    {
        return $this->bluemedia_payment->is_available();
    }

    public function admin_options()
    {
        wp_redirect(admin_url('admin.php?page=wc-settings&tab=checkout&section=' . $this->bluemedia_payment->id));
    }

    private function get_blik_form_content()
    {
        wp_enqueue_style('bluemedia-blik', plugins_url() . '/' . plugin_basename(dirname(dirname(__FILE__))) . '/assets/css/bluemedia-blik.css');
        remove_filter('the_excerpt', 'wpautop');
        ob_start();
        require_once dirname(__FILE__) . '/../template/_partials/order/bluemedia-blik-form-tpl.php';
        return ob_get_clean();
    }

    public function get_icon()
    {
        $image_path = plugin_basename(dirname(dirname(__FILE__))) . '/assets/images/blik.png';
        $icon = sprintf(
            '<img src="%s" alt="%s" />',
            WP_PLUGIN_URL . '/' . $image_path,
            __("Pay via Blue Media online payment system", 'bluepayment-gateway-for-woocommerce')
        );

        return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
    }

    public function validate_blik_transaction(array $blik_data = [])
    {
        global $wpdb;

        // Sprawdzam czy wprowadzono adres e-mail
        if (empty($blik_data['order_email'])) {
            echo json_encode([
                'type' => 'error',
                'msg' => __("Please enter your e-mail address.", 'bluepayment-gateway-for-woocommerce')
            ]);
            exit();
        }

        if (strlen($blik_data['bluemedia_blik_code']) !== BlikEnum::BLIK_CODE_LENGTH) {
            echo json_encode([
                'type' => 'error',
                'msg' => __("The provided code is invalid.", 'bluepayment-gateway-for-woocommerce')
            ]);
            exit();
        }

        // Ustawiam zmienne
        $hash_cart = $wpdb->_escape(WC()->session->get('next_bm_blik_order'));
        $blik_data['bluemedia_blik_order'] = $hash_cart;
        $blik_code = $wpdb->_escape($blik_data['bluemedia_blik_code']);

        // Sprawdzam czy taka transakcja została już zarejestrowana
        $find_blik_transaction = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT status, date_start FROM {$wpdb->prefix}bluemedia_blik WHERE hash_cart = %s AND blik_code = %d LIMIT 1",
                $hash_cart,
                $blik_code
            )
        );
        // Rozpoczynam transakcje
        if (empty($find_blik_transaction)) {
            $order = new WC_Order($hash_cart);

            if (!empty($this->bluemedia_payment->settings['status_pending']) && $this->bluemedia_payment->settings['status_pending'] == 'on-hold') {
                $order->update_status('on-hold', __("Awaiting payment", 'bluepayment-gateway-for-woocommerce'));
            } else {
                $order->update_status('pending', __("Order received", 'bluepayment-gateway-for-woocommerce'));
            }

            $handler = new BlueMediaSdkHandler(
                $this->settings['payment_domain'],
                $this->bluemedia_payment->get_currency_service_id(Utils::get_current_currency()),
                $this->bluemedia_payment->settings['hash_key_' . get_woocommerce_currency()]
            );

            $bm_transaction_data = $handler->call(
                InitTransactionHandler::class,
                (new BlikTransactionBuilder())->build($blik_data, $this->bluemedia_payment->service_id)
            );

            if (isset($bm_transaction_data['reason']) === false) {
                if ($bm_transaction_data['confirmation'] == 'CONFIRMED') {
                    // Dodaję wpis do bazy danych, ustawiam kartę jako opłaconą

                    $wpdb->insert($wpdb->prefix . 'bluemedia_blik', [
                        'hash_cart' => $hash_cart,
                        'blik_code' => $blik_code,
                        'status' => BlikEnum::BLIK_STATUS_PENDING,
                        'date_start' => date('Y-m-d H:i:s')
                    ], ['%s', '%d']);

                    // Płatność oczekująca na odpowiedź z banku
                    if ($bm_transaction_data['paymentStatus'] == 'PENDING') {
                        echo json_encode([
                            'type' => 'pending',
                            'msg' => __("Confirm the operation in your banking application.", 'bluepayment-gateway-for-woocommerce')
                        ]);
                    } elseif ($bm_transaction_data['paymentStatus'] == 'SUCCESS') {
                        //robię update statusu
                        $wpdb->update($wpdb->prefix . 'bluemedia_blik',
                            [
                                'status' => BlikEnum::BLIK_STATUS_SUCCESS,
                            ],
                            [
                                'hash_cart' => $hash_cart,
                                'blik_code' => $blik_code,
                            ]
                        );

                        //dodaję blik code do sesji (potrzebne do wydobycia id order)
                        WC()->session->set('bluemedia_blik_order', $hash_cart);
                        $this->bm_process_payment($hash_cart);

                        //wyświetlam komunikat
                        echo json_encode([
                            'type' => 'success',
                            'msg' => __('Payment has been successfully completed.', 'bluepayment-gateway-for-woocommerce'),
                            'redirect' => $this->bluemedia_payment->getThankYouPage($hash_cart),
                        ]);
                    } else {
                        echo json_encode([
                            'type' => 'error',
                            'msg' => __("The provided code is invalid.", 'bluepayment-gateway-for-woocommerce')
                        ]);
                    }

                    exit();
                }
            } elseif (isset($bm_transaction_data['reason'])) {
                if ($bm_transaction_data['reason'] === 'WRONG_TICKET') {
                    echo json_encode([
                        'type' => 'error',
                        'msg' => __("The provided code is invalid.", 'bluepayment-gateway-for-woocommerce')
                    ]);
                } elseif ($bm_transaction_data['reason'] === 'MULTIPLY_PAID_TRANSACTION') {
                    echo json_encode([
                        'type' => 'error',
                        'msg' => __("Your transaction has already been paid.", 'bluepayment-gateway-for-woocommerce')
                    ]);
                } elseif ($bm_transaction_data['reason'] === 'TICKET_EXPIRED') {
                    echo json_encode([
                        'type' => 'error',
                        'msg' => __("The BLIK code has timed out.", 'bluepayment-gateway-for-woocommerce')
                    ]);
                } else { // Obsługa pozostałych rodzajów błędów
                    echo json_encode([
                        'type' => 'error',
                        'msg' => __("The provided code is invalid.", 'bluepayment-gateway-for-woocommerce')
                    ]);
                }

                exit();
            } else //w pozostałych przypadkach pokazuję błąd
            {
                echo json_encode([
                    'type' => 'error',
                    'msg' => sprintf(__("An error occurred while connecting to the server. Please enter the BLIK code again. If the situation persists, please contact bluemedia and provide the transaction number: %d", 'bluepayment-gateway-for-woocommerce'), $blik_data['bluemedia_blik_order'])
                ]);
                exit();
            }
        } else //transakcja rozpoczęta, sprawdzam jej status
        {
            if ((int) $find_blik_transaction[0]->status === BlikEnum::BLIK_STATUS_SUCCESS)
            {
                WC()->session->set('bluemedia_blik_order', $hash_cart);
                $this->bm_process_payment($hash_cart);
                //wyświetlam komunikat
                echo json_encode([
                    'type' => 'success',
                    'msg' => __("Payment has been successfully completed.", 'bluepayment-gateway-for-woocommerce'),
                    'redirect' => $this->bluemedia_payment->getThankYouPage($hash_cart),
                ]);
            } else {
                // Kod może być ważny tylko 2 minuty, po upłynięciu 2,5 minuty
                // Daję komunikat o przekroczonym czasie
                if (time() >= strtotime('+2 minutes', strtotime($find_blik_transaction[0]->date_start))) {
                    echo json_encode([
                        'type' => 'error',
                        'msg' => __("The BLIK code has timed out.", 'bluepayment-gateway-for-woocommerce')
                    ]);
                } else {
                    echo json_encode([
                        'type' => 'pending',
                        'msg' => __("Confirm the operation in your banking application.", 'bluepayment-gateway-for-woocommerce')
                    ]);
                }
            }

            exit();
        }
    }

    public function bm_process_payment($order_id)
    {
        global $woocommerce, $wpdb;

        $blik_order = WC()->session->get('bluemedia_blik_order');

        WC()->session->set('bluemedia_blik_order', 0);
        WC()->session->set('next_bm_blik_order', 0);
        WC()->session->set('order_id_validation', 0);

        if (!empty($blik_order)) {
            // Sprawdzam czy taka transakcja została już zarejestrowana
            $find_blik_transaction = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT status FROM {$wpdb->prefix}bluemedia_blik WHERE hash_cart = %s AND status = 1 LIMIT 1",
                    $blik_order
                )
            );

            if (!empty($find_blik_transaction)) {
                $order = new WC_Order($order_id);
                $order->payment_complete();
            }
        }

        wc_clear_notices();
        $woocommerce->cart->empty_cart();
    }
}
