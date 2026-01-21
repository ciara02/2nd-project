<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmailApiService
{
    /**
     * Send an email via the internal Email API.
     *
     * @param string $to 
     * @param string $subject 
     * @param string $html 
     * @param array $extraFields
     * @return \Illuminate\Http\Client\Response
     */
    public function send(string $to, string $subject, string $html, array $cc = [])
    {
        $payload = [
            'SystemName' => 'iSupport_Ticketing_System',
            'To'         => $to,
            'Subject'    => $subject,
            'HtmlBody'   => $html,
        ];

        if (!empty($cc)) {
            $payload['cc'] = $cc; 
        }

        Log::debug('Email API Request Debug', [
            'url'     => env('EMAIL_API'),
            'payload' => $payload,
        ]);

        $response = Http::asJson()->post(env('EMAIL_API'), $payload);

        if (! $response->successful()) {
            Log::error('Email API failed', [
                'status'  => $response->status(),
                'body'    => $response->body(),
                'payload' => $payload,
            ]);
        }

        return $response;
    }
    
}
