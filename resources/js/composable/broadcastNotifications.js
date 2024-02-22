// import {formatCurrency} from '@/composable/utilities';

// export function webNotifications() {
//     //#region Web Notifications con Echo
//     const contractNotification = (customerSelect, shipSelect, formData) =>
//         window.Echo.private("contracts").listen(".ContractsEvent", (e) => {
//             // Push.Permission.get();
//             const customerName = customerSelect.value
//                 ? customerSelect.value.name
//                 : "Cliente no seleccionado";
//             const shipName = shipSelect.value
//                 ? shipSelect.value.name
//                 : "Buque no seleccionado";
//             Push.create(e.message, {
//                 body: `Cliente: ${customerName},
//                         \nBuque: ${shipName},
//                         \nCosto: ${formData.cost}`,
//                 icon: "/images/cotecmar-logo-bg-white.png",
//                 requireInteraction: true,
//                 // timeout: 5000,
//                 onClick: function () {
//                     window.open("https://www.cotecmar.com/", "_blank");
//                     this.close();
//                 },
//             });
//         });
//     //endregion
//     return { contractNotification };
// }

export function broadcastNotifications() {
    const eventListener = (channel, events) => {

        window.Echo.private(channel).listen(events, (e) => {
            handleDataEvents(e);
        });
    };

    const handleDataEvents = (data) => {
        showNotification(data.title, data.message);
    };

    const showNotification = (title, message) => {
        Push.create(title, {
            body: message,
            icon: "/images/cotecmar-logo-bg-white.png",
            requireInteraction: true,
            timeout: 10000,
            onClick: () => {
                window.open("https://top.cotecmar.com/", "_blank");
                this.close();
            },
        });
    };
    return { eventListener, handleDataEvents };
}
