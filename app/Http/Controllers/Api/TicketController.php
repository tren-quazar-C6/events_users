<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    public function markAsUsed(string $code): JsonResponse
    {
        $ticket = Ticket::where('unique_code', $code)->first();

        if (!$ticket) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ($ticket->status === 'used') {
            return response()->json(['status' => 'already_used'], 409);
        }

        if ($ticket->status === 'cancelled') {
            return response()->json(['status' => 'cancelled'], 422);
        }

        $ticket->update(['status' => 'used']);

        return response()->json(['status' => 'ok', 'code' => $ticket->unique_code]);
    }
}
