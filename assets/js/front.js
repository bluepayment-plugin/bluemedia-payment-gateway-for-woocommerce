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
    if(elements) {
        // console.log("elements exist");
        elements.forEach((element) => {
            // console.log("element", element);
                if(element.checked) {
                    element.closest(".bm-payment-channel-item").classList.toggle("selected");
                }
                document.querySelectorAll(".bm-payment-channel-item > input[type='radio']").forEach((element) => {
                    if(element.checked === false) {
                        element.closest(".bm-payment-channel-item").classList.remove("selected");
                    }
                })
        });
    }
}
