<input id="bluemediaPaymentToken" name="bluemediaPaymentToken" type="hidden" value="">
<script>
    var paymentsClient = null;
    var bmOnceTimeClicker = false;
    var baseRequest = {
        apiVersion: 2,
        apiVersionMinor: 0
    };
    var tokenizationSpecification = {
        type: 'PAYMENT_GATEWAY',
        parameters: {
            'gateway': '<?php echo $bmGateway; ?>',
            'gatewayMerchantId': '<?php echo $bmGatewayMerchantId; ?>'
        }
    };
    var baseCardPaymentMethod = {
        type: 'CARD',
        parameters: {
            allowedAuthMethods: ["PAN_ONLY", "CRYPTOGRAM_3DS"],
            allowedCardNetworks: ["MASTERCARD", "VISA"],
            billingAddressRequired: false
        }
    };
    var cardPaymentMethod = Object.assign(
        {},
        baseCardPaymentMethod,
        {
            tokenizationSpecification: tokenizationSpecification
        }
    );
    function getGooglePaymentsClient() {
        if (paymentsClient === null) {
            paymentsClient = new google.payments.api.PaymentsClient({environment: '<?php echo $bmEnvironment ?>'});
        }
        return paymentsClient;
    }
    function getGoogleTransactionInfo() {
        return {
            currencyCode: '<?php echo $bmCurrencyCode; ?>',
            totalPriceStatus: 'FINAL',
            totalPrice: '<?php echo $bmTotalPrice; ?>'
        };
    }
    function getGooglePaymentDataRequest() {
        const paymentDataRequest = Object.assign({}, baseRequest);
        paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];
        paymentDataRequest.transactionInfo = getGoogleTransactionInfo();
        paymentDataRequest.merchantInfo = {
            merchantId: '<?php echo $bmMerchantId ?>',
            merchantOrigin: '<?php echo $bmMerchantOrigin ?>',
            merchantName: '<?php echo $bmMerchantName ?>',
            authJwt: '<?php echo $bmAuthJwt ?>'
        };
        return paymentDataRequest;
    }
    function onGooglePaymentButtonClicked() {
        const paymentDataRequest = getGooglePaymentDataRequest();
        paymentDataRequest.transactionInfo = getGoogleTransactionInfo();

        const paymentsClient = getGooglePaymentsClient();
        paymentsClient.loadPaymentData(paymentDataRequest)
            .then(function(paymentData) {
                bluemediaProcessPayment(paymentData);
            })
            .catch(function(err) {
                console.error(err);
            });
    }
    function bluemediaProcessPayment(paymentData) {
        bmOnceTimeClicker = true;
        var bluemediaPaymentToken = paymentData.paymentMethodData.tokenizationData.token;
        document.getElementById('bluemediaPaymentToken').value = JSON.stringify(bluemediaPaymentToken);
        jQuery('#place_order').submit();
    }
</script>

<script src="https://pay.google.com/gp/p/js/pay.js"></script>
