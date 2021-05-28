/** Again import google libraries */
importScripts("https://www.gstatic.com/firebasejs/7.14.6/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/7.14.6/firebase-messaging.js");

/** Your web app's Firebase configuration 
 * Copy from Login 
 *      Firebase Console -> Select Projects From Top Naviagation 
 *      -> Left Side bar -> Project Overview -> Project Settings
 *      -> General -> Scroll Down and Choose CDN for all the details
*/
var config = {
    apiKey: "AIzaSyD6F0xGecqIOhh_ntujcOj8iSZs7gAtOM4",
    authDomain: "file-management-system-7f0d0.firebaseapp.com",
    projectId: "file-management-system-7f0d0",
    storageBucket: "file-management-system-7f0d0.appspot.com",
    messagingSenderId: "768668868323",
    appId: "1:768668868323:web:dc8f1258f9ccc5a2b4afd6",
    measurementId: "G-3G0B7FZH5B"
};
firebase.initializeApp(config);

// Retrieve an instance of Firebase Data Messaging so that it can handle background messages.
const messaging = firebase.messaging();

/** THIS IS THE MAIN WHICH LISTENS IN BACKGROUND */
messaging.setBackgroundMessageHandler(function(payload) {
    const notificationTitle = 'BACKGROUND MESSAGE TITLE';
    const notificationOptions = {
        body: 'Data Message body',
        icon: 'https://c.disquscdn.com/uploads/users/34896/2802/avatar92.jpg',
        image: 'https://c.disquscdn.com/uploads/users/34896/2802/avatar92.jpg'
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});
// messaging.onMessage((payload) => {
//   console.log('Message received. ', payload);
//   // ...
// });