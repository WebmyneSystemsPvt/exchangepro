<?php

return [
    'BORROWER' => 'borrower',
    'SELLER' => 'seller',
    'UNIVERSAL_DATE_FORMAT' => env('UNIVERSAL_DATE_FORMAT', 'd-m-Y'),
    'APP_NAME' => env('APP_NAME', 'Borrowers'),
    'CURRENCY' => '$',
    'GOOGLE_MAP_API_KEY' => env('GOOGLE_MAP_API_KEY', ''),
    'MAP_DEFAULT_ZOOM_SIZE' => env('MAP_DEFAULT_ZOOM_SIZE', 5),
    'MAP_DEFAULT_LAT' => env('MAP_DEFAULT_LAT', 20.5937),
    'MAP_DEFAULT_LNG' => env('MAP_DEFAULT_LNG', 78.9629),
    'APPLICATION_FEE' => 10,
    'OTHER_FEE' => 5,
    'TAX' => 12,
];
