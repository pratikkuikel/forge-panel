<?php

return [
    'token' => env('FORGE_TOKEN'),
    'organization' => env('FORGE_ORGANIZATION'),
    'cache_ttl' => env('FORGE_CACHE_TTL', 86400),
];
