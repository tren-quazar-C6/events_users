<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SendEmailViaN8n implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public string $type,
        public string $to,
        public string $subject,
        public string $html,
        public array  $meta = [],
        public string $from = '',
    ) {
        if (empty($this->from)) {
            $this->from = config('services.n8n.email_from', 'onboarding@resend.dev');
        }
    }

    public function handle(): void
    {
        $url = config('services.n8n.email_webhook');

        if (empty($url)) {
            throw new RuntimeException('N8N_EMAIL_WEBHOOK_URL no está configurado.');
        }

        $recipient = $this->to;
        $devOverride = config('services.n8n.dev_recipient');
        if (app()->environment() !== 'production' && !empty($devOverride)) {
            $recipient = $devOverride;
        }

        $response = Http::timeout(10)
            ->acceptJson()
            ->asJson()
            ->post($url, [
                'type'    => $this->type,
                'from'    => $this->from,
                'to'      => $recipient,
                'subject' => $this->subject,
                'html'    => $this->html,
                'meta'    => $this->meta,
            ]);

        if (!$response->successful()) {
            Log::warning('n8n email webhook respondió con error', [
                'type'   => $this->type,
                'to'     => $this->to,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new RuntimeException(
                "n8n webhook falló ({$response->status()}): " . $response->body()
            );
        }
    }
}
