<?php use BlueMedia\OnlinePayments\Model\Gateway;

if (!empty($paymentChannels)): ?>
    <ul>
        <?php
        /** @var WC_Payment_Gateway_BlueMedia $paymentGatewayBlueMedia */
        /** @var string $currency wp-content/plugins/PayBM_eCommercePlugins.WooCommerce_new/bluemedia-payment-gateway-for-woocommerce.php:333 */
        $excluded_gateway = [];
        $excluded_gateway[] = (string)Gateway::GATEWAY_ID_GOOGLE_PAY;
        $excluded_gateway[] = (string)Gateway::GATEWAY_ID_SMARTNEY;
        if ((isset($postData['blik_pbl']) && $postData['blik_pbl'] == 'yes')
            || (isset($postData['blik_zero']) && $postData['blik_zero'] == 'yes')
        ) {
            $excluded_gateway[] = (string)Gateway::GATEWAY_ID_BLIK;
        }
        if (isset($postData['installment']) && $postData['installment'] == 'yes') {
            $excluded_gateway[] = (string)Gateway::GATEWAY_ID_IFRAME;
        }
        if (isset($postData['card']) && $postData['card'] == 'yes') {
            $excluded_gateway[] = (string)Gateway::GATEWAY_ID_CARD;
        }
        ?>
        <?php foreach ($paymentChannels as $channel): ?>
            <?php if (!in_array((string)$channel['gatewayID'], $excluded_gateway)): ?>
                <li class="bm-channel">
                    <?php foreach ($channel as $k => $v):
                        if (strpos($k, 'regulation') !== false) {
                            continue;
                        }
                        ?>
                        <?php if ($k != 'gatewayName'): ?>
                            <input type="hidden" name="backgorund_channels[<?php echo $currency; ?>][<?php echo $channel['gatewayID']; ?>][<?php echo $k ?>]" value="<?php echo $v; ?>"/>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <img src="<?php echo $channel['iconURL']; ?>" alt="<?php echo $channel['gatewayName']; ?>"/>
                    <br/>
                    <?php echo $channel['gatewayName']; ?>
                    <input type="hidden" name="backgorund_channels[<?php echo $currency; ?>][<?php echo $channel['gatewayID']; ?>][gatewayName]" value="<?php echo $channel['gatewayName']; ?>">
                    <br/>
                    <a href="#" class="bm-channel-move" <?php echo (!empty($blue_media_settings["background_channels_sort_$currency"])) ? 'style="display: block;"' : 'style="display: none;"' ?>>
                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDUxMS42MjYgNTExLjYyNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTExLjYyNiA1MTEuNjI2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPHBhdGggZD0iTTUwNi4xOTksMjQyLjk2OGwtNzMuMDktNzMuMDg5Yy0zLjYxNC0zLjYxNy03Ljg5OC01LjQyNC0xMi44NDgtNS40MjRjLTQuOTQ4LDAtOS4yMjksMS44MDctMTIuODQ3LDUuNDI0ICAgYy0zLjYxMywzLjYxOS01LjQyNCw3LjkwMi01LjQyNCwxMi44NXYzNi41NDdIMjkyLjM1NVYxMDkuNjQxaDM2LjU0OWM0Ljk0OCwwLDkuMjMyLTEuODA5LDEyLjg0Ny01LjQyNCAgIGMzLjYxNC0zLjYxNyw1LjQyMS03Ljg5Niw1LjQyMS0xMi44NDdjMC00Ljk1Mi0xLjgwNy05LjIzNS01LjQyMS0xMi44NTFMMjY4LjY2LDUuNDI5Yy0zLjYxMy0zLjYxNi03Ljg5NS01LjQyNC0xMi44NDctNS40MjQgICBjLTQuOTUyLDAtOS4yMzIsMS44MDktMTIuODUsNS40MjRsLTczLjA4OCw3My4wOWMtMy42MTgsMy42MTktNS40MjQsNy45MDItNS40MjQsMTIuODUxYzAsNC45NDYsMS44MDcsOS4yMjksNS40MjQsMTIuODQ3ICAgYzMuNjE5LDMuNjE1LDcuODk4LDUuNDI0LDEyLjg1LDUuNDI0aDM2LjU0NXYxMDkuNjM2SDEwOS42MzZ2LTM2LjU0N2MwLTQuOTUyLTEuODA5LTkuMjM0LTUuNDI2LTEyLjg1ICAgYy0zLjYxOS0zLjYxNy03LjkwMi01LjQyNC0xMi44NS01LjQyNGMtNC45NDcsMC05LjIzLDEuODA3LTEyLjg0Nyw1LjQyNEw1LjQyNCwyNDIuOTY4QzEuODA5LDI0Ni41ODUsMCwyNTAuODY2LDAsMjU1LjgxNSAgIHMxLjgwOSw5LjIzMyw1LjQyNCwxMi44NDdsNzMuMDg5LDczLjA4N2MzLjYxNywzLjYxMyw3Ljg5Nyw1LjQzMSwxMi44NDcsNS40MzFjNC45NTIsMCw5LjIzNC0xLjgxNywxMi44NS01LjQzMSAgIGMzLjYxNy0zLjYxLDUuNDI2LTcuODk4LDUuNDI2LTEyLjg0N3YtMzYuNTQ5SDIxOS4yN3YxMDkuNjM2aC0zNi41NDJjLTQuOTUyLDAtOS4yMzUsMS44MTEtMTIuODUxLDUuNDI0ICAgYy0zLjYxNywzLjYxNy01LjQyNCw3Ljg5OC01LjQyNCwxMi44NDdzMS44MDcsOS4yMzMsNS40MjQsMTIuODU0bDczLjA4OSw3My4wODRjMy42MjEsMy42MTQsNy45MDIsNS40MjQsMTIuODUxLDUuNDI0ICAgYzQuOTQ4LDAsOS4yMzYtMS44MSwxMi44NDctNS40MjRsNzMuMDg3LTczLjA4NGMzLjYyMS0zLjYyLDUuNDI4LTcuOTA1LDUuNDI4LTEyLjg1NHMtMS44MDctOS4yMjktNS40MjgtMTIuODQ3ICAgYy0zLjYxNC0zLjYxMy03Ljg5OC01LjQyNC0xMi44NDctNS40MjRoLTM2LjU0MlYyOTIuMzU2aDEwOS42MzN2MzYuNTUzYzAsNC45NDgsMS44MDcsOS4yMzIsNS40MiwxMi44NDcgICBjMy42MjEsMy42MTMsNy45MDUsNS40MjgsMTIuODU0LDUuNDI4YzQuOTQ0LDAsOS4yMjYtMS44MTQsMTIuODQ3LTUuNDI4bDczLjA4Ny03My4wOTFjMy42MTctMy42MTcsNS40MjQtNy45MDEsNS40MjQtMTIuODUgICBTNTA5LjgyLDI0Ni41ODUsNTA2LjE5OSwyNDIuOTY4eiIgZmlsbD0iIzAwMDAwMCIvPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo="/>
                    </a>
                </li>
            <?php else: ?>
                <?php foreach ($channel as $k => $v): ?>
                    <input type="hidden" name="backgorund_channels[<?php echo $currency; ?>][<?php echo $channel['gatewayID']; ?>][<?php echo $k; ?>]" value="<?php echo $v; ?>"/>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
