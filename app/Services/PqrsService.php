<?php

namespace App\Services;

use App\Models\User;
use App\Models\Pqrs;
use App\Models\PqrsMensaje;
use App\Jobs\SendEmailViaN8n;
use App\Mail\PqrsSubmitted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class PqrsService
{
    public function submit(User $user, array $data): Pqrs
    {
        $idUsuario = $this->resolveIdUsuario($user);

        $pqrs = Pqrs::create([
            'id_usuario' => $idUsuario,
            'tipo' => $data['tipo'],
            'asunto' => $data['asunto'],
            'estado' => 'ABIERTO',
            'fecha_creacion' => now(),
        ]);

        PqrsMensaje::create([
            'id_pqrs' => $pqrs->id_pqrs,
            'remitente' => 'USUARIO',
            'id_remitente' => $idUsuario,
            'mensaje' => $data['mensaje'],
            'fecha' => now(),
        ]);

        $pqrs->load('mensajes');

        $mailable = new PqrsSubmitted($user, $pqrs);
        $html = view('emails.pqrs-submitted', [
            'user' => $user,
            'pqrs' => $pqrs,
        ])->render();

        if (filled(config('services.n8n.email_webhook'))) {
            SendEmailViaN8n::dispatch(
                type: 'pqrs_submitted',
                to: $user->email,
                subject: $mailable->envelope()->subject,
                html: $html,
                meta: [
                    'pqrs_id' => $pqrs->id_pqrs,
                    'user_id' => $user->id,
                    'tipo' => $pqrs->tipo,
                ],
            );
        } else {
            Mail::to($user->email)->send($mailable);
        }

        return $pqrs;
    }

    public function listByUser(User $user): \Illuminate\Database\Eloquent\Collection
    {
        $idUsuario = $this->resolveIdUsuario($user);

        return Pqrs::where('id_usuario', $idUsuario)
            ->with('mensajes')
            ->latest('fecha_creacion')
            ->get();
    }

    private function resolveIdUsuario(User $user): int
    {
        $idUsuario = DB::table('USUARIO')
            ->where('email', $user->email)
            ->value('id_usuario');

        if (! $idUsuario) {
            throw new RuntimeException("No se encontró usuario en el sistema de ticketing con email: {$user->email}");
        }

        return $idUsuario;
    }
}
