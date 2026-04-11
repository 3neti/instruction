<?php

return [

    'debug' => env('INSTRUCTION_DEBUG', false),

    'system_user_email' => env('SYSTEM_USER_ID'),

    'route' => [
        'prefix' => 'api/instruction/v1',
        'middleware' => ['api'],
    ],

];
