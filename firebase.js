/** Your web app's Firebase configuration 
 * Copy from Login 
 *      Firebase Console -> Select Projects From Top Naviagation 
 *      -> Left Side bar -> Project Overview -> Project Settings
 *      -> General -> Scroll Down and Choose CDN for all the details
*/


var firebaseConfig = {
    apiKey: "AIzaSyD6F0xGecqIOhh_ntujcOj8iSZs7gAtOM4",
    authDomain: "file-management-system-7f0d0.firebaseapp.com",
    projectId: "file-management-system-7f0d0",
    storageBucket: "file-management-system-7f0d0.appspot.com",
    messagingSenderId: "768668868323",
    appId: "1:768668868323:web:dc8f1258f9ccc5a2b4afd6",
    measurementId: "G-3G0B7FZH5B"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);

/**
 * We can start messaging using messaging() service with firebase object
 */
var messaging = firebase.messaging();

/** Register your service worker here
 *  It starts listening to incoming push notifications from here
 */
navigator.serviceWorker.register('firebase-messaging-sw.js')
.then(function (registration) {
    /** Since we are using our own service worker ie firebase-messaging-sw.js file */
    messaging.useServiceWorker(registration);

    /** Lets request user whether we need to send the notifications or not */
    messaging.requestPermission()
        .then(function () {
            /** Standard function to get the token */
            messaging.getToken()
            .then(function(token) {
                /** Here I am logging to my console. This token I will use for testing with PHP Notification */
                console.log(token);
                $.ajax({
                    type: "POST",
                    url: 'ajax.php?action=fcm',
                    data: {token: token},
                    success: function(data){
                        console.log(data);
                    },
                    error: function(xhr, status, error){
                        console.error(xhr);
                    }
                });
                /** SAVE TOKEN::From here you need to store the TOKEN by AJAX request to your server */
            })
            .catch(function(error) {
                /** If some error happens while fetching the token then handle here */
                updateUIForPushPermissionRequired();
                console.log('Error while fetching the token ' + error);
            });
        })
        .catch(function (error) {
            /** If user denies then handle something here */
            console.log('Permission denied ' + error);
        })
})
.catch(function () {
    console.log('Error in registering service worker');
});

/** What we need to do when the existing token refreshes for a user */
messaging.onTokenRefresh(function() {
    messaging.getToken()
    .then(function(renewedToken) {
        console.log(renewedToken);
        $.ajax({
            type: "POST",
            url: 'ajax.php?action=fcm',
            data: {token: token},
                success: function(data){
                console.log(data);
            },
            error: function(xhr, status, error){
                console.error(xhr);
            }         
        });
        /** UPDATE TOKEN::From here you need to store the TOKEN by AJAX request to your server */
    })
    .catch(function(error) {
        /** If some error happens while fetching the token then handle here */
        console.log('Error in fetching refreshed token ' + error);
    });
});

// Handle incoming messages
// messaging.onMessage(function(payload) {
//     const notificationTitle = 'Data Message Title';
//     const notificationOptions = {
//         body: "text",
//         tag: "notification-1"
//     };
   
//     return self.registration.showNotification(notificationTitle, notificationOptions);
// });

messaging.onMessage((payload) => {
  console.log('Message received. ', payload);
  // ...
  alert_notif(payload.notification.title, payload.notification.body, 'peri purnama')
});

// messaging.onBackgroundMessage((payload) => {
//   console.log('[firebase-messaging-sw.js] Received background message ', payload);
//   // Customize notification here
//   const notificationTitle = 'Background Message Title';
//   const notificationOptions = {
//     body: 'Background Message body.',
//     icon: '/firebase-logo.png'
//   };

//   self.registration.showNotification(notificationTitle,
//     notificationOptions);
// });