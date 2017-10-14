<?php
//change these constants
define("IOS_CERTIFICATE_FILE", "ios_certificate_file.pem");
define("IOS_CERTIFICATE_PASSWORD", "ios_certificate_password");
define("FCM_SERVER_KEY","AAM2A7pqcNik:APA96bFf5A4MgA8HMOjeyjFAtLrWCEKciIgm58qmqwkhDHjEQ8hn9gnBHL9VPl0uQ97vxfQXHhob1Oo0eT3a_SIL5yiFcALUSHMHYMbyf9vC-swOnROszrTNY_Cta16bRX02d0TtLobDC1");

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
  } else {
    return 0;
  }
}

//Send a push notification to an iOS device (see https://developer.apple.com/library/content/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/PayloadKeyReference.html#//apple_ref/doc/uid/TP40008194-CH17-SW1 for documentation on this)
$alert = array('title' => 'Hello World',
               'body' => 'It works perfectly');
$aps = array('alert' => $alert);
$payload = array('aps' => $aps);
$device_id = '0b69b26d3a3c91f589d693c5cb5ae4c5042cefbe2703835f3cdf377f35071cb6';
sendPushNotification('ios',$device_id,$payload);

//Send a push notification to an Android device (see https://firebase.google.com/docs/cloud-messaging/http-server-ref for documentation on this)
$notification = array('title' => 'Hello World',
                      'body' => 'It works perfectly');
$payload = array('notification' => $notification);
$device_id = 'fNP-8vxHYic:APA91bFT6mQ_HCtZAeQaEDvP_jeW19iUtxGM3PAPwT1JBokY1NPQXA5a_rlpY9Xiu2hz1CJCnMq3KcFmlkkUBVlhCOwxqFwgO7sRkjl0mQwsC3aOTEMd4vhMYh_tapcyfp-7cLKE6i9F';
sendPushNotification('android',$device_id,$payload);
?>
