<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailViaN8n;
use App\Mail\PurchaseConfirmation;
use App\Models\Evento;
use App\Models\EventoAsiento;
use App\Models\Ticket;
use App\Models\Venta;
use App\Services\EventService;
use App\Services\PurchaseFlowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function initCheckout(Request $request, string $slug): RedirectResponse
    {
        $ids = json_decode($request->input('seats', '[]'), true);

        if (empty($ids)) {
            return back()->with('error', 'Selecciona al menos un asiento.');
        }

        if (! $this->hasSalesTables()) {
            $event = app(EventService::class)->findBySlug($slug);
            abort_if(! $event, 404);

            $selectedSeats = app(EventService::class)
                ->seatsForEvent((int) $event['id'])
                ->whereIn('id', $ids)
                ->values();

            if ($selectedSeats->count() !== count($ids)) {
                return back()->with('error', 'Algunos asientos ya no están disponibles. Por favor selecciona de nuevo.');
            }

            $token = Str::random(40);
            app(PurchaseFlowService::class)->buildCheckout($token, [
                ...$event,
                'venue' => 'Por confirmar',
                'city'  => '',
            ], $selectedSeats);

            return redirect()->route('checkout', $token);
        }

        $eventData = app(EventService::class)->findBySlug($slug);
        abort_if(! $eventData, 404);

        $evento = Evento::find($eventData['id']) ?? Evento::firstOrFail();

        $result = DB::transaction(function () use ($ids, $evento) {
            $eas = EventoAsiento::where('id_evento', $evento->id_evento)
                ->whereIn('id_evento_asiento', $ids)
                ->lockForUpdate()
                ->get();

            $disponibles = $eas->filter(fn ($ea) => $ea->isDisponible());

            if ($disponibles->count() !== count($ids)) {
                return null;
            }

            foreach ($disponibles as $ea) {
                $ea->update(['estado' => 'RESERVADO']);
            }

            // Precios vía EVENTO_ZONA
            $precios = DB::table('EVENTO_ASIENTO as ea')
                ->join('ASIENTOS as a', 'a.id_asiento', '=', 'ea.id_asiento')
                ->join('EVENTO_ZONA as ez', function ($j) use ($evento) {
                    $j->on('ez.id_evento', '=', 'ea.id_evento')
                      ->on('ez.id_zona', '=', 'a.id_zona');
                })
                ->whereIn('ea.id_evento_asiento', $disponibles->pluck('id_evento_asiento'))
                ->pluck('ez.precio', 'ea.id_evento_asiento');

            return ['asientos' => $disponibles, 'precios' => $precios];
        });

        if (! $result) {
            return back()->with('error', 'Algunos asientos ya no están disponibles. Por favor selecciona de nuevo.');
        }

        $token = Str::random(40);

        session(["checkout:{$token}" => [
            'id_evento'          => $evento->id_evento,
            'slug'               => $slug,
            'evento_asiento_ids' => $result['asientos']->pluck('id_evento_asiento')->all(),
            'precios'            => $result['precios']->toArray(),
            'expira'             => now()->addMinutes(15)->toIso8601String(),
        ]]);

        return redirect()->route('checkout', $token);
    }

    public function checkout(string $token): View
    {
        if (! $this->hasSalesTables()) {
            $data = app(PurchaseFlowService::class)->getCheckout($token);
            abort_if(! $data, 404, 'Sesion de checkout invalida o expirada.');

            $evento         = app(PurchaseFlowService::class)->eventFromApi($data['event']);
            $eventoAsientos = app(PurchaseFlowService::class)->seatCollection(collect($data['seats']));
            $subtotal       = $eventoAsientos->sum('precio');
            $fee            = 3500;
            $total          = $subtotal + $fee;
            $minPrice       = (int) $eventoAsientos->min('precio');

            return view('checkout.index', compact('token', 'data', 'evento', 'eventoAsientos', 'subtotal', 'fee', 'total', 'minPrice'));
        }

        $data = session("checkout:{$token}");
        abort_if(! $data, 404, 'Sesión de checkout inválida o expirada.');

        $evento         = Evento::findOrFail($data['id_evento']);
        // Asegurar que evento tenga slug (de sesión si no lo tiene en BD)
        if (! $evento->slug) {
            $evento->slug = $data['slug'] ?? null;
        }

        $eventoAsientos = EventoAsiento::with(['asiento'])
            ->whereIn('id_evento_asiento', $data['evento_asiento_ids'])
            ->get();

        $precios  = $data['precios'] ?? [];

        // Inyectar precio en cada modelo para la vista
        foreach ($eventoAsientos as $ea) {
            $ea->precio = (float) ($precios[$ea->id_evento_asiento] ?? 0);
        }

        $subtotal = array_sum($precios);
        $fee      = 3500;
        $total    = $subtotal + $fee;
        $minPrice = ! empty($precios) ? (int) min($precios) : 0;

        return view('checkout.index', compact('token', 'data', 'evento', 'eventoAsientos', 'subtotal', 'fee', 'total', 'minPrice'));
    }

    public function confirmCheckout(Request $request, string $token): RedirectResponse
    {
        if (! $this->hasSalesTables()) {
            $data = app(PurchaseFlowService::class)->getCheckout($token);
            abort_if(! $data, 404, 'Sesion de checkout invalida o expirada.');

            $venta = app(PurchaseFlowService::class)->createPurchase(
                userId:    Auth::id(),
                userName:  Auth::user()->name,
                userEmail: Auth::user()->email,
                event:     $data['event'],
                selectedSeats: collect($data['seats']),
            );

            app(PurchaseFlowService::class)->forgetCheckout($token);

            $this->sendConfirmationEmail($venta->referencia_interna, Auth::user()->email);

            return redirect()->route('purchase.confirmation', $venta->referencia_interna);
        }

        $data = session("checkout:{$token}");
        abort_if(! $data, 404, 'Sesión de checkout inválida o expirada.');

        $usuarioId = $this->resolveUsuarioId(Auth::user());
        $precios   = $data['precios'] ?? [];

        $venta = DB::transaction(function () use ($data, $usuarioId, $precios) {
            $eventoAsientos = EventoAsiento::whereIn('id_evento_asiento', $data['evento_asiento_ids'])
                ->where('estado', 'RESERVADO')
                ->lockForUpdate()
                ->get();

            if ($eventoAsientos->count() !== count($data['evento_asiento_ids'])) {
                return null;
            }

            $subtotal = array_sum($precios);
            $fee      = 3500;

            $venta = Venta::create([
                'id_usuario'  => $usuarioId,
                'tipo_venta'  => 'ONLINE',
                'total'       => $subtotal + $fee,
                'estado_pago' => 'APPROVED',
                'metodo_pago' => 'MOCK',
                'fecha_pago'  => now(),
                'fecha_venta' => now(),
            ]);

            foreach ($eventoAsientos as $ea) {
                Ticket::updateOrCreate(
                    ['id_evento_asiento' => $ea->id_evento_asiento],
                    [
                        'id_venta'          => $venta->id_venta,
                        'id_estado_ticket'  => 2,   // PAGADO
                        'precio_pagado'     => $precios[$ea->id_evento_asiento] ?? 0,
                    ]
                );

                $ea->update(['estado' => 'VENDIDO']);
            }

            return $venta;
        });

        if (! $venta) {
            session()->forget("checkout:{$token}");
            return redirect()->route('catalog')
                ->with('error', 'La reserva expiró. Por favor selecciona tus asientos nuevamente.');
        }

        session()->forget("checkout:{$token}");

        $this->sendConfirmationEmail($venta->referencia_interna, Auth::user()->email);

        return redirect()->route('purchase.confirmation', $venta->referencia_interna);
    }

    public function confirmation(string $reference): View
    {
        if (! $this->hasSalesTables()) {
            $venta = app(PurchaseFlowService::class)->findPurchaseForUser($reference, Auth::id());
            abort_if(! $venta, 404);

            return view('checkout.confirmation', compact('venta'));
        }

        $usuarioId = $this->resolveUsuarioId(Auth::user());

        $venta = Venta::with([
            'tickets.eventoAsiento.evento',
            'tickets.estadoTicket',
        ])
            ->where('referencia_interna', $reference)
            ->where('id_usuario', $usuarioId)
            ->firstOrFail();

        return view('checkout.confirmation', compact('venta'));
    }

    private function hasSalesTables(): bool
    {
        return Schema::hasTable('EVENTOS')
            && Schema::hasTable('EVENTO_ASIENTO')
            && Schema::hasTable('VENTAS')
            && Schema::hasTable('TICKETS');
    }

    private function resolveUsuarioId(\App\Models\User $user): int
    {
        $id = DB::table('USUARIO')
            ->where('email', $user->email)
            ->value('id_usuario');

        if ($id) return $id;

        return DB::table('USUARIO')->insertGetId([
            'nombre'         => $user->name,
            'email'          => $user->email,
            'telefono'       => $user->telefono ?? null,
            'activo'         => 1,
            'fecha_registro' => now(),
        ]);
    }

    private function sendConfirmationEmail(string $referencia, string $email): void
    {
        if (filled(config('services.n8n.email_webhook'))) {
            SendEmailViaN8n::dispatch(
                type:    'purchase_confirmation',
                to:      $email,
                subject: "Confirmación de compra · {$referencia}",
                html:    '',
                meta:    ['reference' => $referencia],
            );
        }
    }
}
