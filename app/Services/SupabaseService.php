<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SupabaseService
 *
 * Layanan untuk berinteraksi dengan Supabase REST API.
 * Digunakan sebagai abstraksi untuk query database Supabase
 * tanpa bergantung pada package pihak ketiga yang tidak resmi.
 *
 * Catatan: Untuk query database utama, Laravel menggunakan
 * koneksi pgsql langsung ke Supabase PostgreSQL.
 * Service ini digunakan untuk fitur Supabase-spesifik seperti
 * Storage, Auth API, dan Realtime.
 */
class SupabaseService
{
    protected string $url;

    protected string $anonKey;

    protected string $serviceKey;

    public function __construct()
    {
        $this->url = config('supabase.url', '');
        $this->anonKey = config('supabase.anon_key', '');
        $this->serviceKey = config('supabase.service_key', '');
    }

    // =========================================================
    // HTTP Client Helpers
    // =========================================================

    /**
     * Get HTTP client with anon key (public operations).
     */
    protected function publicClient(): PendingRequest
    {
        return Http::withHeaders([
            'apikey' => $this->anonKey,
            'Authorization' => 'Bearer '.$this->anonKey,
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Get HTTP client with service key (admin operations).
     */
    protected function adminClient(): PendingRequest
    {
        return Http::withHeaders([
            'apikey' => $this->serviceKey,
            'Authorization' => 'Bearer '.$this->serviceKey,
            'Content-Type' => 'application/json',
        ]);
    }

    // =========================================================
    // Connection Test
    // =========================================================

    /**
     * Test Supabase connection by hitting the health endpoint.
     */
    public function testConnection(): bool
    {
        if (empty($this->url) || empty($this->anonKey)) {
            Log::warning('Supabase: URL or Anon Key not configured.');

            return false;
        }

        try {
            $response = $this->publicClient()->get($this->url.'/rest/v1/');

            return $response->successful() || $response->status() === 200;
        } catch (\Exception $e) {
            Log::error('Supabase connection error: '.$e->getMessage());

            return false;
        }
    }

    // =========================================================
    // Storage
    // =========================================================

    /**
     * Upload a file to Supabase Storage.
     *
     * @param  string  $bucket  Storage bucket name
     * @param  string  $path  Path within the bucket
     * @param  string  $contents  File contents
     * @param  string  $mimeType  MIME type of the file
     */
    public function uploadFile(string $bucket, string $path, string $contents, string $mimeType = 'application/octet-stream'): ?string
    {
        $endpoint = $this->url."/storage/v1/object/{$bucket}/{$path}";

        $response = $this->adminClient()
            ->withHeaders(['Content-Type' => $mimeType])
            ->withBody($contents, $mimeType)
            ->post($endpoint);

        if ($response->successful()) {
            return $this->url."/storage/v1/object/public/{$bucket}/{$path}";
        }

        Log::error('Supabase Storage upload failed: '.$response->body());

        return null;
    }

    /**
     * Get the public URL for a stored file.
     */
    public function getPublicUrl(string $bucket, string $path): string
    {
        return $this->url."/storage/v1/object/public/{$bucket}/{$path}";
    }

    /**
     * Delete a file from Supabase Storage.
     */
    public function deleteFile(string $bucket, string $path): bool
    {
        $endpoint = $this->url."/storage/v1/object/{$bucket}";

        $response = $this->adminClient()
            ->delete($endpoint, ['prefixes' => [$path]]);

        return $response->successful();
    }

    // =========================================================
    // Config Check
    // =========================================================

    /**
     * Check if Supabase is properly configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->url)
            && ! empty($this->anonKey)
            && ! empty($this->serviceKey);
    }

    /**
     * Get Supabase configuration summary (safe for logging).
     */
    public function getConfigSummary(): array
    {
        return [
            'url' => $this->url ?: '(not set)',
            'anon_key' => $this->anonKey ? substr($this->anonKey, 0, 10).'...' : '(not set)',
            'service_key' => $this->serviceKey ? substr($this->serviceKey, 0, 10).'...' : '(not set)',
            'configured' => $this->isConfigured(),
        ];
    }
}
