<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailViaN8n;
use App\Mail\PurchaseConfirmation;
use App\Models\EstadoTicket;
use App\Models\Evento;
use App\Models\EventoAsiento;
use App\Models\Ticket;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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

        $evento = Evento::where('slug', $slug)->firstOrFail();

        $eventoAsientos = DB::transaction(function () use ($ids, $evento) {
            $eas = EventoAsiento::where('evento_id', $evento->id)
                ->whereIn('id', $ids)
                ->lockForUpdate()
                ->get();

            $disponibles = $eas->filter(fn ($ea) => $ea->isDisponible());

            if ($disponibles->count() !== count($ids)) {
                return null;
            }

            $expira = now()->addMinutes(15);
            foreach ($disponibles as $ea) {
                $ea->update([
                    'estado'          => 'RESERVADO',
                    'fecha_reserva'   => now(),
                    'reserva_expira'  => $expira,
                ]);
            }

            return $disponibles;
        });

        if (!$eventoAsientos) {
            return back()->with('error', 'Algunos asientos ya no están disponibles. Por favor selecciona de nuevo.');
        }

        $token = Str::random(40);

        session(["checkout:{$token}" => [
            'evento_id'          => $evento->id,
            'evento_asiento_ids' => $eventoAsientos->pluck('id')->all(),
            'expira'             => now()->addMinutes(15)->toIso8601String(),
        ]]);

        return redirect()->route('checkout', $token);
    }

    public function checkout(string $token): View
    {
        $data = session("checkout:{$token}");
        abort_if(!$data, 404, 'Sesión de checkout inválida o expirada.');

        $evento         = Evento::findOrFail($data['evento_id']);
        $eventoAsientos = EventoAsiento::with(['asiento.zona'])
            ->whereIn('id', $data['evento_asiento_ids'])
            ->get();

        $subtotal = $eventoAsientos->sum('precio');
        $fee      = 3500;
        $total    = $subtotal + $fee;

        return view('checkout.index', compact('token', 'data', 'evento', 'eventoAsientos', 'subtotal', 'fee', 'total'));
    }

    public function confirmCheckout(Request $request, string $token): RedirectResponse
    {
        $data = session("checkout:{$token}");
        abort_if(!$data, 404, 'Sesión de checkout inválida o expirada.');

        $venta = DB::transaction(function () use ($data) {
            $eventoAsientos = EventoAsiento::with(['asiento.zona', 'evento'])
                ->whereIn('id', $data['evento_asiento_ids'])
                ->where('estado', 'RESERVADO')
                ->lockForUpdate()
                ->get();

            if ($eventoAsientos->count() !== count($data['evento_asiento_ids'])) {
                return null;
            }

            $subtotal = $eventoAsientos->sum('precio');
            $fee      = 3500;

            $venta = Venta::create([
                'user_id'        => Auth::id(),
                'tipo_venta'     => 'ONLINE',
                'subtotal'       => $subtotal,
                'cargo_servicio' => $fee,
                'total'          => $subtotal + $fee,
                'moneda'         => 'COP',
                'estado_pago'    => 'APPROVED',
                'metodo_pago'    => 'MOCK',
                'fecha_pago'     => now(),
            ]);

            $estadoId = EstadoTicket::where('nombre_estado', 'CONFIRMADO')->value('id');

            foreach ($eventoAsientos as $ea) {
                Ticket::create([
                    'venta_id'          => $venta->id,
                    'estado_ticket_id'  => $estadoId,
                    'evento_asiento_id' => $ea->id,
                ]);

                $ea->update(['estado' => 'VENDIDO']);
            }

            return $venta;
        });

        if (!$venta) {
            session()->forget("checkout:{$token}");
            return redirect()->route('catalog')
                ->with('error', 'La reserva expiró. Por favor selecciona tus asientos nuevamente.');
        }

        session()->forget("checkout:{$token}");

        $venta->load('tickets.eventoAsiento.asiento.zona', 'tickets.eventoAsiento.evento', 'user');

        $html = view('emails.purchase-confirmation', ['venta' => $venta])->render();

        if (filled(config('services.n8n.email_webhook'))) {
            SendEmailViaN8n::dispatch(
                type: 'purchase_confirmation',
                to: $venta->user->email,
                subject: "Confirmación de compra · {$venta->referencia_interna}",
                html: $html,
                meta: ['venta_id' => $venta->id, 'user_id' => $venta->user_id],
            );
        } else {
            Mail::to($venta->user)->send(new PurchaseConfirmation($venta));
        }

        return redirect()->route('purchase.confirmation', $venta->referencia_interna);
    }

    public function confirmation(string $reference): View
    {
        $venta = Venta::with('tickets.eventoAsiento.asiento.zona', 'tickets.eventoAsiento.evento')
            ->where('referencia_interna', $reference)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('checkout.confirmation', compact('venta'));
    }
}
