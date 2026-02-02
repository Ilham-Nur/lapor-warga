<?php

return [
    'proxies' => '*',

    /*
     |------------------------------------------------------------------
     | Trusted Proxy Headers
     |------------------------------------------------------------------
     | Laravel 12 menggunakan bitmask integer, bukan constant lama.
     */
    'headers' =>
        1   | // HEADER_FORWARDED
        2   | // HEADER_X_FORWARDED_FOR
        4   | // HEADER_X_FORWARDED_HOST
        8   | // HEADER_X_FORWARDED_PROTO
        16,   // HEADER_X_FORWARDED_PORT
];
