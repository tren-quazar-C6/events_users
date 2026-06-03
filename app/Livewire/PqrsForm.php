<?php

namespace App\Livewire;

use App\Services\PqrsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PqrsForm extends Component
{
    public string $tipo = 'PREGUNTA';

    public string $asunto = '';

    public string $mensaje = '';

    protected array $rules = [
        'tipo' => ['required', 'in:PREGUNTA,QUEJA,RECLAMO,SUGERENCIA'],
        'asunto' => ['required', 'string', 'min:3', 'max:120'],
        'mensaje' => ['required', 'string', 'min:10', 'max:2000'],
    ];

    public function save(): void
    {
        $data = $this->validate();

        try {
            app(PqrsService::class)->submit(Auth::user(), $data);
            $this->reset(['tipo', 'asunto', 'mensaje']);
            $this->tipo = 'PREGUNTA';
            session()->flash('status', 'Tu PQRS fue enviada correctamente.');
        } catch (\Throwable $e) {
            \Log::error('PQRS save error', ['error' => $e->getMessage()]);
            session()->flash('error', 'Ocurrió un error al enviar tu solicitud. Intenta de nuevo.');
        }
    }

    public function render()
    {
        try {
            $historial = app(PqrsService::class)->listByUser(Auth::user());
        } catch (\Throwable $e) {
            $historial = collect();
        }

        return view('livewire.pqrs-form', [
            'historial' => $historial,
        ]);
    }
}
