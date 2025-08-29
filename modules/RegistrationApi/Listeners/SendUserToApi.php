<?php

namespace Modules\RegistrationApi\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendUserToApi
{
    public function handle(Registered $event): void
    {
        $user = $event->user;

        $baseUrl  = config('registration_api.base_url');
        $token    = config('registration_api.token');
        $endpoint = config('registration_api.endpoint', '/api/send/template');
        $template = config('registration_api.template');
        $language = config('registration_api.language', 'en');
        $header   = config('registration_api.header_image');
        $payload0 = config('registration_api.button_payload_0');
        $payload2 = config('registration_api.button_payload_2');

        if (!$baseUrl || !$token || !$template) {
            Log::warning('Registration API base URL, token, or template not configured.');
            return;
        }

        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $components = [];

        if ($header) {
            $components[] = [
                'type' => 'header',
                'parameters' => [[
                    'type' => 'image',
                    'image' => ['link' => $header],
                ]],
            ];
        }

        $bodyParams = [
            ['type' => 'text', 'text' => $user->first_name ?? ''],
            ['type' => 'text', 'text' => $user->last_name ?? ''],
            ['type' => 'text', 'text' => $user->email ?? ''],
        ];

        $components[] = [
            'type' => 'body',
            'parameters' => $bodyParams,
        ];

        if ($payload0) {
            $components[] = [
                'type' => 'button',
                'sub_type' => 'quick_reply',
                'index' => '0',
                'parameters' => [[
                    'type' => 'payload',
                    'payload' => $payload0,
                ]],
            ];
        }

        if ($payload2) {
            $components[] = [
                'type' => 'button',
                'sub_type' => 'quick_reply',
                'index' => '2',
                'parameters' => [[
                    'type' => 'payload',
                    'payload' => $payload2,
                ]],
            ];
        }

        $payload = [
            'phone' => $user->phone ?? null,
            'template' => [
                'name' => $template,
                'language' => [
                    'code' => $language,
                ],
                'components' => $components,
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            Log::info('Sent registration data to API', [
                'status' => $response->status(),
                'url' => $url,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send registration data to API: ' . $e->getMessage());
        }
    }
}
