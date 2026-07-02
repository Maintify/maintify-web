<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supabase Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk koneksi ke Supabase. Set nilai-nilai ini
    | di file .env Anda.
    |
    */

    'url' => env('SUPABASE_URL'),

    'anon_key' => env('SUPABASE_ANON_KEY'),

    'service_key' => env('SUPABASE_SERVICE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Supabase Storage
    |--------------------------------------------------------------------------
    |
    | Default bucket untuk penyimpanan file di Supabase Storage.
    |
    */

    'storage_bucket' => env('SUPABASE_STORAGE_BUCKET', 'maintify'),

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    |
    | Endpoint untuk berbagai layanan Supabase.
    |
    */

    'endpoints' => [
        'auth' => env('SUPABASE_URL').'/auth/v1',
        'rest' => env('SUPABASE_URL').'/rest/v1',
        'storage' => env('SUPABASE_URL').'/storage/v1',
        'realtime' => env('SUPABASE_URL').'/realtime/v1',
    ],

];
