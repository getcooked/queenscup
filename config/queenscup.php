<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Take-out surcharge
    |--------------------------------------------------------------------------
    |
    | Charged per cup on take-out reservations to cover the cup and lid. Dine-in
    | reservations are never charged this. The total is always recalculated on
    | the server so a client cannot submit its own figure.
    |
    */

    'takeout_fee_per_cup' => env('QC_TAKEOUT_FEE_PER_CUP', 5.00),

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging
    |--------------------------------------------------------------------------
    |
    | Used to push "your order is ready" to the Android app. Create a Firebase
    | project, enable Cloud Messaging, and put the values in your .env file.
    | Leaving the credentials empty disables push without breaking anything --
    | the app still shows status changes when it polls.
    |
    | project_id      Firebase console -> Project settings -> General
    | credentials     Absolute path to the service account JSON key file
    |                 (Project settings -> Service accounts -> Generate key)
    |
    */

    'fcm' => [
        'project_id' => env('FCM_PROJECT_ID'),
        'credentials' => env('FCM_CREDENTIALS_PATH'),
    ],

];
