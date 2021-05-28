<?php

/** Google URL with which notifications will be pushed */
$url = "https://fcm.googleapis.com/fcm/send";
/** 
 * Firebase Console -> Select Projects From Top Naviagation 
 *      -> Left Side bar -> Project Overview -> Project Settings
 *      -> General -> Scroll Down and you will be able to see KEYS
 */
$subscription_key  = "key=AAAAsvg8HuM:APA91bG1Emb-L3CuBbG4CAjRcvq0fsGX0qhZ-gZOaHhvIBT7PPUA16X0Eyr4xsGs8bBBQhSAGThSkdi_vhPqz3d5WhY3qR8RMl9atohHMNDkYtvynaIwz6eW41Nkzr3btdPUgZeAjgkJ";

/** We will need to set the following header to make request work */
$request_headers = array(
    "Authorization:" . $subscription_key,
    "Content-Type: application/json"
);

