<?php

return [
    'base_url' => env('REG_API_BASE_URL', 'https://whapier.com'),
    'token' => env('REG_API_TOKEN', 'oJwxFvPZ4zXgYIRdt5rytpQeAHh4bRNXMJWfFcgK'),
    'endpoint' => env('REG_API_ENDPOINT', '/api/send/template'),
    'template' => env('REG_API_TEMPLATE', 'onboarding_1'),
    'language' => env('REG_API_LANGUAGE', 'en'),
    'header_image' => env('REG_API_HEADER_IMAGE'),
    'button_payload_0' => env('REG_API_BUTTON_PAYLOAD_0'),
    'button_payload_2' => env('REG_API_BUTTON_PAYLOAD_2'),
];
