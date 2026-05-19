<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Devuelve el QR code en SVG para un ticket del usuario autenticado.
     */
    public function qr(string $code): Response
    {
        $ticket = Ticket::where('unique_code', $code)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $svg = (new Writer($renderer))->writeString($ticket->unique_code);

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml');
    }
}
