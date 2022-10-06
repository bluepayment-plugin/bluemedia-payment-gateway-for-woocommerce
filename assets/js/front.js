// function initBmAddClassToRadio() {
//     const elements = document.querySelectorAll(".bm-payment-channel-item > input[type='radio']");
//     if(elements) {
//         console.log("elements exist");
//         elements.forEach((element) => {
//             console.log("element", element);
//             element.addEventListener("click", function() {
//                 console.log("clicked");
//                 if(element.checked) {
//                     element.closest(".bm-payment-channel-item").classList.toggle("selected");
//                 }
//                 document.querySelectorAll(".bm-payment-channel-item > input[type='radio']").forEach((element) => {
//                     if(element.checked === false) {
//                         element.closest(".bm-payment-channel-item").classList.remove("selected");
//                     }
//                 })
//             });
//         });
//     }
// }


function addCurrentClass() {
    const elements = document.querySelectorAll(".bm-payment-channel-item > input[type='radio']");
    if (elements) {
        // console.log("elements exist");
        elements.forEach((element) => {
            // console.log("element", element);
            if (element.checked) {
                element.closest(".bm-payment-channel-item").classList.toggle("selected");
            }
            document.querySelectorAll(".bm-payment-channel-item > input[type='radio']").forEach((element) => {
                if (element.checked === false) {
                    element.closest(".bm-payment-channel-item").classList.remove("selected");
                }
            })
        });
    }
}


jQuery(document).ready(function () {
    if (typeof blue_media_ga4_tasks !== 'undefined' && typeof blueMedia.ga4TrackingId !== 'undefined') {
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', blueMedia.ga4TrackingId);


        blue_media_ga4_tasks = JSON.parse(blue_media_ga4_tasks)
        blue_media_ga4_tasks.forEach((event) => {
            event.items.forEach((item) => {
                /*const desc = Object.getOwnPropertyDescriptor(obj, name);
                Object.defineProperty(copy, name, desc);*/

                //console.log(item)

                gtag('event', event.event_name,
                    {
                        'items': [
                            item
                        ]
                    }
                )
            })
        })
    }
})
