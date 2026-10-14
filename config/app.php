<?php

declare(strict_types=1);

return [
    'name' => 'Face Recognition Attendance System',
    'env' => 'development', // 'development' or 'production'
    'debug' => true,
    'url' => 'http://localhost/project%20se/public',
    'timezone' => 'Africa/Cairo',
    'locale' => 'en',
    'session' => [
        'name' => 'FACE_ATTENDANCE_SESS',
        'lifetime' => 1800, // 30 minutes idle timeout
        'secure' => false,  // Set true when local HTTPS is enabled
        'httponly' => true,
        'samesite' => 'Lax'
    ]
];
