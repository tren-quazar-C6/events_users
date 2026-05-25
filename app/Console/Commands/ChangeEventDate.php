<?php

namespace App\Console\Commands;

use App\Mail\EventDateChanged;
use App\Models\Evento;
use App\Models\Favorito;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ChangeEventDate extends Command
{
    protected $signature   = 'events:change-date {slug} {oldDate} {newDate}';
    protected $description = 'Cambia la fecha de un evento en BD y notifica a los usuarios que lo tienen como favorito';

    public function handle(): int
    {
        $slug    = $this->argument('slug');
        $oldDate = $this->argument('oldDate');
        $newDate = $this->argument('newDate');

        $evento = Evento::where('slug', $slug)->first();

        if (!$evento) {
            $this->error("Evento '{$slug}' no encontrado.");
            return self::FAILURE;
        }

        $hora = $evento->fecha_evento->format('H:i:s');
        $evento->update(['fecha_evento' => $newDate . ' ' . $hora]);

        $this->info("Fecha actualizada: {$oldDate} → {$newDate}");

        $usuarios = Favorito::where('evento_id', $evento->id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        if ($usuarios->isEmpty()) {
            $this->info('Sin favoritos. No se envían correos.');
            return self::SUCCESS;
        }

        foreach ($usuarios as $user) {
            Mail::to($user)->send(new EventDateChanged($user, $evento, $oldDate, $newDate));
        }

        $this->info("Correos encolados para {$usuarios->count()} usuario(s).");

        return self::SUCCESS;
    }
}
