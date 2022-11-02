define([
        'jquery',
        'mage/url',
        "mage/template",
        'mage/cookies',
        'firebase',
        'domReady!',
    ], function ($, url, mageTemplate) {

    'use strict';

    return function (config, element) {

        if ($.cookie('push-notification-status') === 'disabled') {
           return;
        }

        var template = mageTemplate('#notification-template');
        var notification = template({
                            data: {
                                yes: 'Yes',
                                no: 'No'
                            }
                        });

        var $notification = $(notification);
        $notification.appendTo("body");
        

        var navigatorVar = '';

        if ('serviceWorker' in navigator){
            navigator.serviceWorker.register(config.serviceWorkerUrl).then(function(){
                navigatorVar = 'Yes';
            }).catch(function(err) {
                console.log('Unable to register service worker ', err);
            });
        } else {
            console.log('Service workers aren\'t supported in this browser.');
        }

        const firebaseConfig = {
            apiKey: config.apiKey,
            authDomain: config.authDomain,
            projectId: config.projectId,
            storageBucket: config.storageBucket,
            messagingSenderId: config.messagingSenderId,
            appId: config.appId,
            measurementId: config.measurementId
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();


        $notification.on('click', '.clsbtnyes' , function() {

            var start = new Date();
            var end = new Date("04 Sep 2022");
            var diff = new Date(end - start);
            var days = diff/1000/60/60/24;
            days.toFixed(0);
            $.cookie('push-notification-status', 'disabled', { expires: days, path: '/' });

            $notification.remove();

            messaging.requestPermission()
                .then(function(){
                    console.log("Have Permission");
                    getToken();
                })
                .catch(function(err){
                    console.log("Error Occured.", err);
                })
        });

        $notification.on('click', '.clsbtnno' , function() {

            var start = new Date();
            var end = new Date("04 Sep 2022");
            var diff = new Date(end - start);
            var days = diff/1000/60/60/24;
            days.toFixed(0);
            $.cookie('push-notification-status', 'disabled', { expires: days, path: '/' });

            $notification.remove();
        });

        messaging.onMessage(function(payload) {
            console.log('Received in main js', payload);
            // Customize notification here
            const notificationTitle = payload.data.title;
            const notificationOptions = {
                body: payload.data.body,
                icon: payload.data.icon,
                data: {url: payload.data.url}
            };
            navigator.serviceWorker.ready.then(function(registration){
                return registration.showNotification(notificationTitle, notificationOptions);
            })
                .catch(function(err){
                    console.log("Error Occured in show notification.");
                });
        });

        if(navigatorVar === 'Yes') {
            navigator.serviceWorker.ready.then(function(registration){
                registration.addEventListener('notificationclick', function(event) {
                    const clickedNotification = event.notification;
                    window.location.href = clickedNotification.data.url;
                    clickedNotification.close();
                });
                registration.addEventListener('pushsubscriptionchange', deleteToken);
            });
        }

        function deleteToken(){
            messaging.getToken()
                .then(function(currentToken) {
                    messaging.deleteToken(currentToken)
                        .then(function() {
                            console.log('Token deleted.');
                            getToken();
                        })
                        .catch(function(err) {
                            console.log('Unable to delete token. ', err);
                            getToken();
                        });
                    // [END delete_token]
                })
                .catch(function(err) {
                    console.log('Error retrieving Instance ID token. ', err);
                });
        }

        function getToken(){
            var tokenUrlController = window.location.origin+ "/pushnotification/pushnotik/regtoken";
            
            messaging.getToken()
                .then(function(token){
                    console.log('my token : ', token);
                    jQuery.ajax({
                        url: tokenUrlController,
                        type: 'POST',
                        data:{
                            token:token,
                            website:config.tokenWebsite
                        },
                        success: function(response) {
                            if(response['success'] == 1)
                                console.log('Token registred successfully.');
                            else
                                console.log("Failed to register token");
                        }
                    });
                })
                .catch(function(err){
                    console.log("Error Occured.", err);
                });
        }
    }
});