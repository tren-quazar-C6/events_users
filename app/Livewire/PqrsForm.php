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

        app(PqrsService::class)->submit(Auth::user(), $data);

        $this->reset(['tipo', 'asunto', 'mensaje']);
        $this->tipo = 'PREGUNTA';

        session()->flash('status', 'Tu PQRS fue enviada correctamente.');
    }

    public function render()
    {
        return view('livewire.pqrs-form');
    }
}
