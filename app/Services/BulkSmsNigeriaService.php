<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BulkSmsNigeriaService
{
    protected string $apiToken;
    protected string $senderId;
    protected string $recipient;

    public function __construct()
    {
        $this->apiToken = config('services.bulksms_nigeria.api_token') ?? '';
        $this->senderId = config('services.bulksms_nigeria.sender_id', 'ATLAS');
        
        $phone = config('services.bulksms_nigeria.recipient', '08103996947');
        $this->recipient = $this->formatPhoneNumber($phone);
    }

    /**
     * Send SMS notification via BulkSMS Nigeria API
     */
    public function sendSms(string $message, ?string $toPhone = null): bool
    {
        $recipient = $toPhone ? $this->formatPhoneNumber($toPhone) : $this->recipient;

        if (empty($this->apiToken)) {
            Log::warning("BulkSMS Nigeria: API token is empty. Logging SMS instead.", [
                'to' => $recipient,
                'from' => $this->senderId,
                'message' => $message,
            ]);
            return false;
        }

        try {
            $url = 'https://www.bulksmsnigeria.com/api/v2/sms/send';

            $response = Http::timeout(10)->post($url, [
                'api_token' => $this->apiToken,
                'from'      => $this->senderId,
                'to'        => $recipient,
                'body'      => $message,
            ]);

            if ($response->successful()) {
                Log::info("BulkSMS Nigeria SMS sent successfully to {$recipient}", [
                    'response' => $response->json(),
                ]);
                return true;
            } else {
                Log::error("BulkSMS Nigeria API error [{$response->status()}]", [
                    'body' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("BulkSMS Nigeria SMS sending exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format Nigerian phone numbers to standard 234 format
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($clean) === 11 && str_starts_with($clean, '0')) {
            return '234' . substr($clean, 1);
        }
        return $clean;
    }
}
