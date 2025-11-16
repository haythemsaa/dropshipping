<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Gateway par défaut utilisé pour les paiements par carte
    |
    */

    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'clictopay'),

    /*
    |--------------------------------------------------------------------------
    | e-Dinar Configuration (D17 - Poste Tunisienne)
    |--------------------------------------------------------------------------
    */

    'edinar' => [
        'test_mode' => env('EDINAR_TEST_MODE', true),
        'test_url' => env('EDINAR_TEST_URL', 'https://test.edinar.poste.tn/api'),
        'live_url' => env('EDINAR_LIVE_URL', 'https://edinar.poste.tn/api'),
        'merchant_id' => env('EDINAR_MERCHANT_ID'),
        'secret_key' => env('EDINAR_SECRET_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Clictopay Configuration (SMT)
    |--------------------------------------------------------------------------
    */

    'clictopay' => [
        'test_mode' => env('CLICTOPAY_TEST_MODE', true),
        'test_url' => env('CLICTOPAY_TEST_URL', 'https://test.clictopay.com/api/v2'),
        'live_url' => env('CLICTOPAY_LIVE_URL', 'https://secure.clictopay.com/api/v2'),
        'merchant_id' => env('CLICTOPAY_MERCHANT_ID'),
        'api_key' => env('CLICTOPAY_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Konnect Configuration
    |--------------------------------------------------------------------------
    */

    'konnect' => [
        'test_mode' => env('KONNECT_TEST_MODE', true),
        'test_url' => env('KONNECT_TEST_URL', 'https://api.preprod.konnect.network/api/v2'),
        'live_url' => env('KONNECT_LIVE_URL', 'https://api.konnect.network/api/v2'),
        'api_key' => env('KONNECT_API_KEY'),
        'wallet_id' => env('KONNECT_WALLET_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cash on Delivery (COD)
    |--------------------------------------------------------------------------
    */

    'cod' => [
        'enabled' => env('COD_ENABLED', true),
        'max_amount' => env('COD_MAX_AMOUNT', 500), // TND
        'extra_fee' => env('COD_EXTRA_FEE', 7), // Frais supplémentaires COD en TND
    ],

    /*
    |--------------------------------------------------------------------------
    | General Settings
    |--------------------------------------------------------------------------
    */

    'currency' => 'TND',
    'currency_symbol' => 'DT',

    // Timeout pour les paiements en attente (minutes)
    'payment_timeout' => 15,

    // Activer les logs de paiement
    'log_payments' => env('PAYMENT_LOGGING', true),

];
