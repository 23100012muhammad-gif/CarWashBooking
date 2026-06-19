<?php

return [
    'gas' => [
        'url' => env('GAS_SCRIPT_URL'),
        'secret' => env('APP_SECRET'),
    ],
    
    'booking' => [
        'max_days_ahead' => env('BOOKING_MAX_DAYS_AHEAD', 14),
        'default_range_days' => env('BOOKING_DEFAULT_RANGE_DAYS', 7),
    ],
];