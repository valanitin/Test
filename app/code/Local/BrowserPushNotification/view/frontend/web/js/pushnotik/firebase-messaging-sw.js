importScripts("https://www.gstatic.com/firebasejs/4.12.1/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/4.12.1/firebase-messaging.js");
// importScripts("https://www.jiofab.com/kptoken/js/firebase-app.js");
// importScripts("https://www.jiofab.com/kptoken/js/firebase-messaging.js");

var config = {
  apiKey: "AIzaSyABCFa9JeTyXIldu82-mF5Mubxuu_zHZPk",
  authDomain: "sololuxury-b7ddd.firebaseapp.com",
  projectId: "sololuxury-b7ddd",
  storageBucket: "sololuxury-b7ddd.appspot.com",
  messagingSenderId: "101481179434",
  appId: "1:101481179434:web:9cbdca8a644cbdf9935653",
  measurementId: "G-G2Q0L41B9M"
  };

firebase.initializeApp(config);

const messaging = firebase.messaging();


messaging.setBackgroundMessageHandler(function(payload) {

  const notificationTitle = payload.data.title;
  const notificationOptions = {
    body: payload.data.body,
    icon: payload.data.icon,
    data: {url: payload.data.url}
  };

  return self.registration.showNotification(notificationTitle,
      notificationOptions);
});

self.addEventListener('notificationclick', function(event) {
  const clickedNotification = event.notification;
  clients.openWindow(clickedNotification.data.url);
  clickedNotification.close();
});
