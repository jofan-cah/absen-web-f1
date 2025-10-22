<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FCMNotification;
use Illuminate\Support\Facades\Log;

class FCMService
{
    protected $messaging;

    public function __construct()
    {
        try {
            // Load Firebase credentials dari storage
            $credentialsPath = storage_path(env('FIREBASE_CREDENTIALS'));

            if (!file_exists($credentialsPath)) {
                Log::warning("Firebase credentials not found: {$credentialsPath}");
                $this->messaging = null; // Set null explicitly
                return; // PENTING! Exit constructor
            }

            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->messaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('FCM initialization failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Kirim notifikasi ke 1 device
     *
     * @param string $deviceToken FCM token dari Flutter
     * @param string $title Judul notifikasi
     * @param string $body Isi notifikasi
     * @param array $data Data tambahan (optional)
     * @return bool
     */
    public function sendToDevice($deviceToken, $title, $body, $data = [])
    {
        try {
            // Validasi token
            if (empty($deviceToken)) {
                Log::warning('FCM: Empty device token');
                return false;
            }

            // Buat notification
            $notification = FCMNotification::create($title, $body);

            // Buat message
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification)
                ->withData($data);

            // Kirim!
            if (!$this->messaging) {
                Log::warning('FCM not initialized, skipping send');
                return false;
            }

            $result = $this->messaging->send($message);

            Log::info('FCM sent successfully', [
                'device_token' => substr($deviceToken, 0, 20) . '...',
                'title' => $title,
                'body' => substr($body, 0, 50)
            ]);

            return true;
        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            // Token tidak valid atau sudah dihapus
            Log::warning('FCM: Invalid token', [
                'device_token' => substr($deviceToken, 0, 20) . '...',
                'error' => 'Token not found or expired'
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('FCM send failed', [
                'error' => $e->getMessage(),
                'device_token' => substr($deviceToken, 0, 20) . '...',
                'title' => $title
            ]);
            return false;
        }
    }

    /**
     * Kirim notifikasi ke multiple devices
     *
     * @param array $deviceTokens Array of FCM tokens
     * @param string $title Judul notifikasi
     * @param string $body Isi notifikasi
     * @param array $data Data tambahan (optional)
     * @return array Result summary
     */
    public function sendToMultipleDevices($deviceTokens, $title, $body, $data = [])
    {
        $successCount = 0;
        $failCount = 0;
        $invalidTokens = [];

        foreach ($deviceTokens as $token) {
            $result = $this->sendToDevice($token, $title, $body, $data);

            if ($result) {
                $successCount++;
            } else {
                $failCount++;
                $invalidTokens[] = substr($token, 0, 20) . '...';
            }
        }

        $summary = [
            'success' => $successCount,
            'failed' => $failCount,
            'total' => count($deviceTokens),
            'invalid_tokens' => $invalidTokens
        ];

        Log::info('FCM batch send completed', $summary);

        return $summary;
    }

    /**
     * Kirim ke topic (broadcast ke semua subscriber)
     * Topic berguna untuk broadcast ke grup tertentu
     *
     * @param string $topic Nama topic (misal: 'all_karyawan', 'department_it')
     * @param string $title Judul notifikasi
     * @param string $body Isi notifikasi
     * @param array $data Data tambahan (optional)
     * @return bool
     */
    public function sendToTopic($topic, $title, $body, $data = [])
    {
        try {
            $notification = FCMNotification::create($title, $body);

            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification($notification)
                ->withData($data);

            $this->messaging->send($message);

            Log::info('FCM sent to topic successfully', [
                'topic' => $topic,
                'title' => $title
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('FCM send to topic failed', [
                'error' => $e->getMessage(),
                'topic' => $topic
            ]);
            return false;
        }
    }

    /**
     * Subscribe device ke topic
     *
     * @param string|array $deviceTokens Single token or array of tokens
     * @param string $topic Topic name
     * @return bool
     */
    public function subscribeToTopic($deviceTokens, $topic)
    {
        try {
            if (!is_array($deviceTokens)) {
                $deviceTokens = [$deviceTokens];
            }

            $this->messaging->subscribeToTopic($topic, $deviceTokens);

            Log::info('FCM: Subscribed to topic', [
                'topic' => $topic,
                'count' => count($deviceTokens)
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('FCM: Subscribe to topic failed', [
                'error' => $e->getMessage(),
                'topic' => $topic
            ]);
            return false;
        }
    }

    /**
     * Unsubscribe device dari topic
     *
     * @param string|array $deviceTokens Single token or array of tokens
     * @param string $topic Topic name
     * @return bool
     */
    public function unsubscribeFromTopic($deviceTokens, $topic)
    {
        try {
            if (!is_array($deviceTokens)) {
                $deviceTokens = [$deviceTokens];
            }

            $this->messaging->unsubscribeFromTopic($topic, $deviceTokens);

            Log::info('FCM: Unsubscribed from topic', [
                'topic' => $topic,
                'count' => count($deviceTokens)
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('FCM: Unsubscribe from topic failed', [
                'error' => $e->getMessage(),
                'topic' => $topic
            ]);
            return false;
        }
    }

    /**
     * Validate FCM token
     *
     * @param string $deviceToken
     * @return bool
     */
    public function validateToken($deviceToken)
    {
        try {
            // Try to send a test message (dry run)
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withData(['test' => 'validation']);

            $this->messaging->validate($message);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
