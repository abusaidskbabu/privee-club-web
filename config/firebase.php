<?php
return [
    'credentials' => [
        'file' => storage_path(env('FIREBASE_CREDENTIALS')),
    ],

    'database' => [
        'url' => env('FIREBASE_DATABASE_URL'),
    ],
];
