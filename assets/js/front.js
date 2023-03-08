function addCurrentClass(el) {
    const current_payment_block = el.closest('#payment');

    const elements = current_payment_block.querySelectorAll(".bm-payment-channel-item > input[type='radio']");

    const bank_group_wrap = current_payment_block.querySelector(".bm-group-expandable-wrapper");
    const bank_group_radio = current_payment_block.querySelector("#bm-gateway-bank-group");
    if (elements) {
        elements.forEach((element) => {
            if (element.checked) {
                element.closest(".bm-payment-channel-item").classList.toggle("selected");
                // hide list of "PRZELEW INTERNETOWY"
                if (!isChild(element, current_payment_block.querySelector("div.bm-group-expandable-wrapper"))) {
                    bank_group_wrap.classList.remove('active');
                    if (bank_group_radio.checked) {
                        bank_group_radio.checked = !bank_group_radio.checked;
                    }
                }
            }
            current_payment_block.querySelectorAll(".bm-payment-channel-item > input[type='radio']").forEach((element) => {
                if (element.checked === false) {
                    element.closest(".bm-payment-channel-item").classList.remove("selected");
                }
            })
        });
    }
}


jQuery(document).ready(function () {
    if (typeof window.blueMedia !== 'undefined') {
        if (typeof blue_media_ga4_tasks !== 'undefined' && typeof blueMedia.ga4TrackingId !== 'undefined') {
            window.dataLayer = window.dataLayer || [];


            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', blueMedia.ga4TrackingId);
            let events = JSON.parse(blue_media_ga4_tasks)[0].events;
            console.log(events);

            events.forEach((event) => {
                gtag('event', event.name,
                    {
                        'items': event.params.items
                    }
                )
            });
        }
    }
})

document.addEventListener('click', function (e) {
    e = e || window.event;
    var target = e.target || e.srcElement;

    const bank_group_wrap = document.querySelector(".bm-group-expandable-wrapper");
    // click on PRZELEW INTERNETOWY
    if (target.hasAttribute('id') && target.getAttribute('id') == 'bm-gateway-bank-group') {
        if (target.checked) {

            document.querySelectorAll(".bm-group-expandable-wrapper").forEach((element) => {
                element.classList.add('active');
            });

            document.querySelectorAll(".bm-payment-channel-item > input[type='radio']").forEach((element) => {
                if (element.checked) {
                    element.closest(".bm-payment-channel-item").classList.remove("selected");
                    element.checked = !element.checked;
                }
            })
        }
    }

});


function isChild(obj, parentObj) {
    while (obj != undefined && obj != null && obj.tagName.toUpperCase() != 'BODY') {
        if (obj == parentObj) {
            return true;
        }
        obj = obj.parentNode;
    }
    return false;
}

