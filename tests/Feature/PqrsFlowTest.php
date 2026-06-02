<?php

namespace Tests\Feature;

use App\Livewire\PqrsForm;
use App\Mail\PqrsSubmitted;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class PqrsFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_pqrs_against_events_api_and_receive_confirmation_email(): void
    {
        Mail::fake();

        config(['services.events_api.url' => 'http://events-api.test']);

        Http::fake([
            'http://events-api.test/api/pqrs' => Http::response([
                'success' => true,
                'data' => [
                    'id_pqrs' => 91,
                    'id_usuario' => 7,
                    'tipo' => 'PREGUNTA',
                    'asunto' => 'No pude entrar',
                    'estado' => 'ABIERTO',
                ],
            ], 201),
        ]);

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PqrsForm::class)
            ->set('tipo', 'PREGUNTA')
            ->set('asunto', 'No pude entrar')
            ->set('mensaje', 'El código QR no se muestra correctamente en mi sesión.')
            ->call('save')
            ->assertHasNoErrors();

        Http::assertSent(function ($request) use ($user) {
            return $request->url() === 'http://events-api.test/api/pqrs'
                && $request['id_usuario'] === $user->id
                && $request['tipo'] === 'PREGUNTA'
                && $request['asunto'] === 'No pude entrar'
                && $request['mensaje'] === 'El código QR no se muestra correctamente en mi sesión.';
        });

        Mail::assertSent(PqrsSubmitted::class, function (PqrsSubmitted $mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
