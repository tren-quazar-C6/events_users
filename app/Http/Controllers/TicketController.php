<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Venta;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function qr(string $code): Response
    {
        $ventaIds = Venta::where('user_id', Auth::id())->pluck('id');

        $ticket = Ticket::whereIn('venta_id', $ventaIds)
            ->where('codigo_unico', $code)
            ->firstOrFail();

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $svg = (new Writer($renderer))->writeString($ticket->qr_token);

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml');
    }
}
