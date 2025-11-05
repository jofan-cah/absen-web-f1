<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackNotificationService
{
    protected $webhookUrl;
    protected $enabled;

    public function __construct()
    {
        // ✅ FIXED: Ambil dari root level config
        $this->webhookUrl = config('services.slack.webhook_url');
        $this->enabled = config('services.slack.enabled', true);

        // Debug log
        Log::info('SlackNotificationService initialized', [
            'webhook_exists' => !empty($this->webhookUrl),
            'webhook_preview' => substr($this->webhookUrl ?? 'NOT SET', 0, 50),
            'enabled' => $this->enabled,
        ]);
    }

    /**
     * Kirim notifikasi success ke Slack
     */
    public function notifySuccess($title, $details = [])
    {
        if (!$this->enabled || !$this->webhookUrl) {
            Log::warning('Slack notification skipped', [
                'reason' => !$this->enabled ? 'disabled' : 'no webhook url',
                'title' => $title,
            ]);
            return false;
        }

        $message = $this->buildMessage('success', $title, $details);
        return $this->send($message);
    }

    /**
     * Kirim notifikasi error ke Slack
     */
    public function notifyError($title, $error, $details = [])
    {
        if (!$this->enabled || !$this->webhookUrl) {
            Log::warning('Slack notification skipped', [
                'reason' => !$this->enabled ? 'disabled' : 'no webhook url',
                'title' => $title,
            ]);
            return false;
        }

        $message = $this->buildMessage('error', $title, array_merge($details, [
            'error' => $error instanceof \Exception ? $error->getMessage() : $error,
            'trace' => $error instanceof \Exception ? substr($error->getTraceAsString(), 0, 1000) : null,
        ]));

        return $this->send($message);
    }

    /**
     * Kirim notifikasi warning ke Slack
     */
    public function notifyWarning($title, $details = [])
    {
        if (!$this->enabled || !$this->webhookUrl) {
            return false;
        }

        $message = $this->buildMessage('warning', $title, $details);
        return $this->send($message);
    }

    /**
     * Build message format untuk Slack
     */
    protected function buildMessage($type, $title, $details = [])
    {
        // Emoji & Color berdasarkan type
        $config = [
            'success' => ['emoji' => '✅', 'color' => '#36a64f'],
            'error' => ['emoji' => '🔴', 'color' => '#ff0000'],
            'warning' => ['emoji' => '⚠️', 'color' => '#ffcc00'],
        ];

        $emoji = $config[$type]['emoji'] ?? '📢';
        $color = $config[$type]['color'] ?? '#439FE0';

        // Build fields
        $fields = [];
        foreach ($details as $key => $value) {
            if ($key === 'trace') {
                continue; // Skip trace dari fields
            }

            $fields[] = [
                'title' => ucfirst(str_replace('_', ' ', $key)),
                'value' => is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : (string)$value,
                'short' => strlen((string)$value) < 50,
            ];
        }

        // Build attachment
        $attachment = [
            'color' => $color,
            'title' => "{$emoji} {$title}",
            'fields' => $fields,
            'footer' => config('app.name') . ' API',
            'ts' => time(),
        ];

        // Tambah trace kalau ada (untuk error)
        if (isset($details['trace']) && $details['trace']) {
            $attachment['text'] = "```\n" . $details['trace'] . "\n```";
        }

        return [
            'username' => config('app.name') . ' Bot',
            'icon_emoji' => ':robot_face:',
            'attachments' => [$attachment],
        ];
    }

    /**
     * Kirim ke Slack webhook
     */
    protected function send($message)
    {
        if (!$this->webhookUrl) {
            Log::error('Slack webhook URL not configured');
            return false;
        }

        try {
            Log::info('Sending to Slack', [
                'webhook' => substr($this->webhookUrl, 0, 50),
                'title' => $message['attachments'][0]['title'] ?? 'N/A',
            ]);

            $response = Http::timeout(5)->post($this->webhookUrl, $message);

            if ($response->successful()) {
                Log::info('Slack notification sent successfully', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return true;
            }

            Log::warning('Slack notification failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Slack notification exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Quick send - simple text message
     */
    public function sendSimple($text, $type = 'info')
    {
        if (!$this->enabled || !$this->webhookUrl) {
            Log::warning('Slack sendSimple skipped', [
                'reason' => !$this->enabled ? 'disabled' : 'no webhook url',
            ]);
            return false;
        }

        $emoji = [
            'info' => 'ℹ️',
            'success' => '✅',
            'error' => '🔴',
            'warning' => '⚠️',
        ][$type] ?? 'ℹ️';

        $message = [
            'text' => "{$emoji} {$text}",
            'username' => config('app.name') . ' Bot',
        ];

        return $this->send($message);
    }
}
