<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EstadoTicket;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    public function markAsUsed(string $code): JsonResponse
    {
        $ticket = Ticket::with('estadoTicket')
            ->where('codigo_unico', $code)
            ->first();

        if (!$ticket) {
            return response()->json(['status' => 'not_found'], 404);
        }

        $estado = $ticket->estadoTicket->nombre_estado;

        if ($estado === 'USADO') {
            return response()->json(['status' => 'already_used'], 409);
        }

        if ($estado === 'CANCELADO') {
            return response()->json(['status' => 'cancelled'], 422);
        }

        $estadoUsado = EstadoTicket::where('nombre_estado', 'USADO')->value('id');
        $ticket->update(['estado_ticket_id' => $estadoUsado]);

        return response()->json(['status' => 'ok', 'code' => $ticket->codigo_unico]);
    }
}
