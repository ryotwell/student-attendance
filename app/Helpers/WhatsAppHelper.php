<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppHelper
{
    protected string $baseUrl;
    protected ?string $deviceId;
    protected array $auth;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.whatsapp.url'), '/');

        $this->deviceId = config('services.whatsapp.device_id');

        $this->auth = [
            config('services.whatsapp.username'),
            config('services.whatsapp.password'),
        ];
    }

    /**
     * HTTP Client GOWA
     */
    protected function client()
    {
        return Http::timeout(30)
            ->withBasicAuth(
                $this->auth[0],
                $this->auth[1]
            )
            ->when($this->deviceId, function ($http) {
                $http->withHeaders([
                    'X-Device-Id' => $this->deviceId,
                ]);
            });
    }

    /**
     * Kirim pesan text
     */
    public function sendText(string $phone, string $message): array
    {
        $response = $this->client()->post(
            $this->baseUrl . '/send/message',
            [
                'phone' => $this->normalizePhone($phone),
                'message' => $message,
            ]
        );

        return $this->response($response);
    }

    /**
     * Kirim gambar
     */
    public function sendImage(
        string $phone,
        string $imageUrl,
        ?string $caption = null
    ): array {
        $response = $this->client()->post(
            $this->baseUrl . '/send/image',
            [
                'phone' => $this->normalizePhone($phone),
                'image' => [
                    'url' => $imageUrl,
                ],
                'caption' => $caption,
            ]
        );

        return $this->response($response);
    }

    /**
     * Kirim dokumen
     */
    public function sendDocument(
        string $phone,
        string $url,
        string $filename
    ): array {
        $response = $this->client()->post(
            $this->baseUrl . '/send/file',
            [
                'phone' => $this->normalizePhone($phone),
                'file' => [
                    'url' => $url,
                    'filename' => $filename,
                ],
            ]
        );

        return $this->response($response);
    }

    /**
     * Cek status device
     */
    public function status(): array
    {
        $response = $this->client()->get(
            $this->baseUrl . '/app/status'
        );

        return $this->response($response);
    }

    /**
     * Logout device
     */
    public function logout(): array
    {
        $response = $this->client()->get(
            $this->baseUrl . '/app/logout'
        );

        return $this->response($response);
    }

    /**
     * Normalize nomor WhatsApp
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Response handler
     */
    protected function response($response): array
    {
        if ($response->failed()) {
            Log::error('WhatsApp API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'data' => $response->json(),
        ];
    }
}