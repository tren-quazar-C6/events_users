<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseConfirmation;
use App\Models\Purchase;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Recibe los asientos seleccionados desde el mapa, guarda en sesión y
     * redirige al resumen de checkout.
     */
    public function initCheckout(Request $request, int $id): RedirectResponse
    {
        $seats = json_decode($request->input('seats', '[]'), true);

        if (empty($seats)) {
            return back()->with('error', 'Selecciona al menos un asiento.');
        }

        $token = Str::random(40);

        session(["checkout:{$token}" => [
            'event_id'    => $id,
            'event_title' => $request->input('event_title'),
            'event_date'  => $request->input('event_date'),
            'event_time'  => $request->input('event_time'),
            'venue'       => $request->input('venue'),
            'city'        => $request->input('city'),
            'price'       => (int) $request->input('price'),
            'seats'       => $seats,
        ]]);

        return redirect()->route('checkout', $token);
    }

    /**
     * Muestra el resumen de la compra con form de pago mock.
     */
    public function checkout(string $token): View
    {
        $data = session("checkout:{$token}");

        abort_if(!$data, 404, 'Sesión de checkout inválida o expirada.');

        $subtotal = count($data['seats']) * $data['price'];
        $fee      = 3500;
        $total    = $subtotal + $fee;

        return view('checkout.index', compact('token', 'data', 'subtotal', 'fee', 'total'));
    }

    /**
     * Confirma la compra: crea Purchase + Tickets en BD y envía el email.
     */
    public function confirmCheckout(Request $request, string $token): RedirectResponse
    {
        $data = session("checkout:{$token}");

        abort_if(!$data, 404, 'Sesión de checkout inválida o expirada.');

        $subtotal = count($data['seats']) * $data['price'];
        $fee      = 3500;
        $total    = $subtotal + $fee;

        $purchase = Purchase::create([
            'user_id'     => Auth::id(),
            'event_id'    => $data['event_id'],
            'event_title' => $data['event_title'],
            'event_date'  => $data['event_date'],
            'event_time'  => $data['event_time'],
            'venue'       => $data['venue'],
            'city'        => $data['city'],
            'subtotal'    => $subtotal,
            'service_fee' => $fee,
            'total'       => $total,
            'status'      => 'confirmed',
        ]);

        foreach ($data['seats'] as $seat) {
            Ticket::create([
                'purchase_id'  => $purchase->id,
                'user_id'      => Auth::id(),
                'event_id'     => $data['event_id'],
                'event_title'  => $data['event_title'],
                'event_date'   => $data['event_date'],
                'event_time'   => $data['event_time'],
                'venue'        => $data['venue'],
                'city'         => $data['city'],
                'seat_row'     => $seat['row'],
                'seat_number'  => $seat['num'],
                'seat_section' => $seat['section'],
                'price'        => $data['price'],
                'status'       => 'confirmed',
            ]);
        }

        session()->forget("checkout:{$token}");

        $purchase->load('tickets');
        Mail::to(Auth::user())->send(new PurchaseConfirmation($purchase));

        return redirect()->route('purchase.confirmation', $purchase->reference);
    }

    /**
     * Vista final con tickets y QR codes.
     */
    public function confirmation(string $reference): View
    {
        $purchase = Purchase::with('tickets')
            ->where('reference', $reference)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('checkout.confirmation', compact('purchase'));
    }
}
