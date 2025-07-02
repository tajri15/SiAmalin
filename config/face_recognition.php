<?php

return [
    'threshold' => env('FACE_RECOGNITION_THRESHOLD', 0.70),
    'embedding_version' => env('FACE_EMBEDDING_VERSION', 'v2'),
    'max_distance' => env('FACE_MAX_DISTANCE', 55),
    'timeout' => env('FACE_DETECTION_TIMEOUT', 30),
    'max_attempts' => env('FACE_MAX_ATTEMPTS', 3),
    'min_face_size' => env('FACE_MIN_SIZE', 150),
    'enable_anti_spoofing' => env('FACE_ANTI_SPOOFING', true),
];