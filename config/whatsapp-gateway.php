<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway Base URL
    |--------------------------------------------------------------------------
    |
    | Base URL dari WhatsApp Gateway API. Ganti sesuai domain deployment Anda.
    |
    */
    'base_url' => env('WA_GATEWAY_BASE_URL', 'https://wa-gateway.asp.web.id/api/v1'),

    /*
    |--------------------------------------------------------------------------
    | API Client Credentials
    |--------------------------------------------------------------------------
    |
    | Kredensial API Client yang dapat diperoleh dari dashboard web
    | di menu API Clients. Kedua nilai ini wajib diisi.
    |
    */
    'client_id' => env('WA_GATEWAY_CLIENT_ID', ''),
    'api_key'   => env('WA_GATEWAY_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Device ID
    |--------------------------------------------------------------------------
    |
    | Device ID default yang digunakan jika tidak ada device yang ditentukan
    | secara eksplisit. Kosongkan jika ingin menggunakan auto-resolve.
    |
    */
    'default_device_id' => env('WA_GATEWAY_DEFAULT_DEVICE_ID', null),

    /*
    |--------------------------------------------------------------------------
    | HTTP Timeout
    |--------------------------------------------------------------------------
    |
    | Batas waktu tunggu (dalam detik) untuk setiap request HTTP ke API.
    |
    */
    'timeout' => (int) env('WA_GATEWAY_TIMEOUT', 15),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi retry otomatis jika terjadi kegagalan jaringan sementara.
    | `times` adalah jumlah percobaan ulang, `sleep` adalah jeda dalam ms.
    |
    */
    'retry' => [
        'times' => (int) env('WA_GATEWAY_RETRY_TIMES', 2),
        'sleep' => (int) env('WA_GATEWAY_RETRY_SLEEP', 500),
    ],
];
