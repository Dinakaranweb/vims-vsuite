<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\Messaging\Unregistered;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Throwable;

/**
 * Thin wrapper around the Firebase Admin SDK so the rest of the app can call
 * PushNotificationService::sendToUser(...) without caring whether a Firebase
 * project has actually been configured yet — until FIREBASE_CREDENTIALS
 * points at a real service-account file, this silently no-ops instead of
 * throwing and breaking the request it's called from.
 */
class PushNotificationService
{
    public static function configured(): bool
    {
        $path = config('firebase.projects.app.credentials');
        return is_string($path) && $path !== '' && file_exists($path);
    }

    /**
     * @param array<string, string|int|float> $data Extra payload for the
     *        mobile app to act on (e.g. ['type' => 'document', 'id' => 42]).
     */
    public static function sendToUser(int $userId, string $title, string $body, array $data = []): void
    {
        if (!self::configured()) {
            return;
        }

        $tokens = DeviceToken::where('user_id', $userId)->pluck('fcm_token', 'id');
        if ($tokens->isEmpty()) {
            return;
        }

        try {
            $messaging = app(Messaging::class);
        } catch (Throwable $e) {
            Log::warning('Push notification skipped — Firebase misconfigured: ' . $e->getMessage());
            return;
        }

        foreach ($tokens as $tokenId => $fcmToken) {
            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification(FirebaseNotification::create($title, $body))
                ->withData(array_map('strval', $data));

            try {
                $messaging->send($message);
            } catch (NotFound|Unregistered $e) {
                // Token no longer valid on this device — stop trying it.
                DeviceToken::where('id', $tokenId)->delete();
            } catch (Throwable $e) {
                Log::warning("Push notification failed for device token id {$tokenId}: " . $e->getMessage());
            }
        }
    }
}
