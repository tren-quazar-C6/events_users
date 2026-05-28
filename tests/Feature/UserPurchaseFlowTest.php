<?php

namespace Tests\Feature;

use App\Livewire\FavoriteButton;
use App\Mail\PurchaseConfirmation;
use App\Models\EstadoTicket;
use App\Models\Evento;
use App\Models\EventoAsiento;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class UserPurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_favorite_purchase_receive_email_and_view_ticket_qr(): void
    {
        Mail::fake();

        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create();
        $evento = Evento::query()->where('publicado', true)->where('activo', true)->firstOrFail();
        $seatIds = EventoAsiento::query()
            ->where('evento_id', $evento->id)
            ->where('estado', 'DISPONIBLE')
            ->limit(2)
            ->pluck('id')
            ->all();

        $this->assertCount(2, $seatIds);

        $this->get(route('catalog'))->assertOk()->assertSee($evento->nombre_evento);

        Livewire::actingAs($user)
            ->test(FavoriteButton::class, ['eventoId' => $evento->id])
            ->call('toggle')
            ->assertDispatched('favorites-changed');

        $this->assertDatabaseHas('favoritos', [
            'user_id' => $user->id,
            'evento_id' => $evento->id,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.favorites'))
            ->assertOk()
            ->assertSee($evento->nombre_evento);

        $checkoutResponse = $this->actingAs($user)->post(route('checkout.init', $evento->slug), [
            'seats' => json_encode($seatIds),
        ]);

        $checkoutResponse->assertRedirect();
        $checkoutUrl = $checkoutResponse->headers->get('Location');
        $token = basename(parse_url($checkoutUrl, PHP_URL_PATH));

        $this->actingAs($user)
            ->get(route('checkout', $token))
            ->assertOk()
            ->assertSee($evento->nombre_evento);

        $this->actingAs($user)
            ->post(route('checkout.confirm', $token))
            ->assertRedirect();

        Mail::assertSent(PurchaseConfirmation::class);

        $estadoConfirmado = EstadoTicket::where('nombre_estado', 'CONFIRMADO')->value('id');
        $ticket = $user->tickets()
            ->where('estado_ticket_id', $estadoConfirmado)
            ->firstOrFail();

        $this->actingAs($user)
            ->get(route('dashboard.tickets'))
            ->assertOk()
            ->assertSee($ticket->codigo_unico);

        $this->actingAs($user)
            ->get(route('tickets.qr', $ticket->codigo_unico))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml');
    }
}
