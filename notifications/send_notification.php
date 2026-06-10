<?php

require '../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

$factory = (new Factory)
    ->withServiceAccount('../firebase/service-account.json');

$messaging = $factory->createMessaging();

$deviceToken = "TOKEN_DEVICE";

$message = CloudMessage::withTarget('token', $deviceToken)
    ->withNotification(Notification::create(
        'Booking Baru',
        'Ada booking masuk'
    ));

$messaging->send($message);

echo "Notif berhasil";