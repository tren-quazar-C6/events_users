<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Venta;
use App\Services\PurchaseFlowService;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TicketController extends Controller
{
    public function qr(string $code): Response
    {
        $ticket = null;

        if ($this->hasSalesTables()) {
            $usuarioId = DB::table('USUARIO')->where('email', Auth::user()->email)->value('id_usuario');

            if ($usuarioId) {
                $ventaIds = Venta::where('id_usuario', $usuarioId)->pluck('id_venta');

                $ticket = Ticket::whereIn('id_venta', $ventaIds)
                    ->where('codigo_unico', $code)
                    ->first();
            }
        }

        if (! $ticket) {
            $ticket = app(PurchaseFlowService::class)->findTicketForUser($code, Auth::id());
            abort_if(! $ticket, 404);
        }

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $svg = (new Writer($renderer))->writeString($ticket->qr_token);

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml');
    }

    private function hasSalesTables(): bool
    {
        return Schema::hasTable('VENTAS') && Schema::hasTable('TICKETS');
    }
}
