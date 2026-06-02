<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\SendEmailViaN8n;
use App\Mail\PqrsSubmitted;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class PqrsService
{
    public function submit(User $user, array $data): array
    {
        $response = Http::baseUrl(config('services.events_api.url'))
            ->timeout(config('services.events_api.timeout', 5))
            ->acceptJson()
            ->asJson()
            ->post('/api/pqrs', [
                'id_usuario' => $user->id,
                'tipo' => $data['tipo'],
                'asunto' => $data['asunto'],
                'mensaje' => $data['mensaje'],
            ]);

        try {
            $response->throw();
        } catch (RequestException $exception) {
            throw new RuntimeException('No fue posible registrar la PQRS en el servicio externo.', 0, $exception);
        }

        $payload = $response->json();

        if (! (bool) data_get($payload, 'success')) {
            $message = data_get($payload, 'errors.0')
                ?? data_get($payload, 'message')
                ?? 'No fue posible registrar la PQRS en el servicio externo.';

            throw new RuntimeException($message);
        }

        $pqrsData = data_get($payload, 'data', []);
        $mailable = new PqrsSubmitted($user, (object) $pqrsData);
        $html = view('emails.pqrs-submitted', [
            'user' => $user,
            'pqrs' => (object) $pqrsData,
        ])->render();

        if (filled(config('services.n8n.email_webhook'))) {
            SendEmailViaN8n::dispatch(
                type: 'pqrs_submitted',
                to: $user->email,
                subject: $mailable->envelope()->subject,
                html: $html,
                meta: [
                    'pqrs_id' => data_get($pqrsData, 'id_pqrs'),
                    'user_id' => $user->id,
                    'tipo' => data_get($pqrsData, 'tipo', $data['tipo']),
                ],
            );
        } else {
            Mail::to($user->email)->send($mailable);
        }

        return $pqrsData;
    }
}
