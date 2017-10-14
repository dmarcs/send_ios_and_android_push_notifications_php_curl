# Send iOS And Android Push Notifications

Use this to send push notifications to iOS and Android devices. This code is written in PHP and used cURL to send requests to https://api.development.push.apple.com/3/device/ or https://fcm.googleapis.com/fcm/send.

<img src="https://user-images.githubusercontent.com/26162804/31578135-fe7a14fe-b0e8-11e7-9da1-61a797e4f607.png" width="200" /> <img src="https://user-images.githubusercontent.com/26162804/31578138-032655f8-b0e9-11e7-8265-833c6e4eb507.png" width="200" />

## Getting Started

Add the code from this repo to your project. Get a device ID for your iOS or Android device by either

1. using [Phonegap Push Plugin](https://github.com/phonegap/phonegap-plugin-push) (assuming you're using Cordova)
2. write Swift/Objective C/Java/Kotlin for iOS and Android that registers your device

Make sure that you have an APNS certificate (you need an Apple Developer account to get a certificate). You also need to register for an FCM account. Once you do, you need to add `google-services.json` to your Android project and get the server key from FCM.

Simply call `sendPushNotification('android',$device_id,$payload)` or `sendPushNotification('ios',$device_id,$payload)` and you'll receive a push notification.

You need to change the constants below in order for this to work.

```
//change these constants
define("IOS_CERTIFICATE_FILE", "ios_certificate_file.pem");
define("IOS_CERTIFICATE_PASSWORD", "ios_certificate_password");
define("FCM_SERVER_KEY","AATA7pqcNik:APA96bFf5A4MgA8HMOjeyjFAtLrWCEKciIgm58qmqwkhDHjEQ8hn9gnBHL9VPl0uQ97vxfQXHhob1Oo0eT3a_SIL5yiFcALUSHMHYMbyf9vC-swOnROszrTNY_Cta16bRX02d0TtLobDC1");

//returns 1 when push notification sent successfully and returns 0 when push notification fails to send
function sendPushNotification($os, $device_token, $payload) {
  if($os == 'ios') {
    $ch = curl_init("https://api.development.push.apple.com/3/device/$device_token");
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
    curl_setopt($ch, CURLOPT_SSLCERT, realpath(IOS_CERTIFICATE_FILE));
    curl_setopt($ch, CURLOPT_SSLCERTPASSWD, IOS_CERTIFICATE_PASSWORD);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);
    return $http_code==200?1:0;
  } else if($os == 'android') {
    $payload['to'] = $device_token;
    $ch = curl_init('https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: key=' . FCM_SERVER_KEY, 'Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode ($payload));
    curl_exec ($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);
    return $http_code==200?1:0;
  }
}

//Send a push notification to an iOS device (see https://developer.apple.com/library/content/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/PayloadKeyReference.html#//apple_ref/doc/uid/TP40008194-CH17-SW1 for documentation on this)
$alert = array('title' => 'Hello World',
               'body' => 'test message');
$aps = array('alert' => $alert);
$payload = array('aps' => $aps);
$device_id = '0b69b26d3a3c91f589d693c5cb5ae4c5042cefbe2703835f3cdf377f35071cb6';
sendPushNotification('ios',$device_id,$payload);

//Send a push notification to an Android device (see https://firebase.google.com/docs/cloud-messaging/http-server-ref for documentation on this)
$notification = array('title' => 'Hello World',
                      'body' => 'test message');
$payload = array('notification' => $notification);
$device_id = 'fNP-8vxHYic:APA91bFT6mQ_HCtZAeQaEDvP_jeW19iUtxGM3PAPwT1JBokY1NPQXA5a_rlpY9Xiu2hz1CJCnMq3KcFmlkkUBVlhCOwxqFwgO7sRkjl0mQwsC3aOTEMd4vhMYh_tapcyfp-7cLKE6i9F';
sendPushNotification('android',$device_id,$payload);
```
