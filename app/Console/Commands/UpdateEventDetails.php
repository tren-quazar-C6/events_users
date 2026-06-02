<?php

namespace App\Console\Commands;

use App\Models\Evento;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('app:update-event-details')]
#[Description('Actualiza detalles de eventos activos con información coherente')]
class UpdateEventDetails extends Command
{
    public function handle()
    {
        $eventos = Evento::where('activo', true)
            ->where('publicado', true)
            ->get();

        if ($eventos->isEmpty()) {
            $this->info('No hay eventos activos para actualizar.');
            return 0;
        }

        $eventDetails = $this->generateEventDetails();
        $updated = 0;

        foreach ($eventos as $index => $evento) {
            $detail = $eventDetails[$index % count($eventDetails)] ?? $eventDetails[0];

            $evento->update([
                'nombre_evento' => $detail['nombre'],
                'descripcion' => $detail['descripcion'],
                'fecha_evento' => $detail['fecha'],
                'ruta_url' => $detail['imagen'],
            ]);

            $updated++;
            $this->line("✓ Actualizado: {$evento->nombre_evento}");
        }

        $this->info("\n$updated eventos actualizados correctamente.");
        return 0;
    }

    private function generateEventDetails(): array
    {
        return [
            [
                'nombre' => 'El Rey León - Musical Épico',
                'descripcion' => 'El icónico musical de Disney llega al Teatro Quasar con todo el esplendor de su puesta en escena. Una experiencia sensorial completa con máscaras de Julie Taymor, coreografías espectaculares y la banda sonora que ha conquistado millones de corazones. Viaja a las praderas africanas sin salir de tu butaca. Artista: Adaptación Disney. Duración: 2h 45min. Precio desde: $180.000 COP.',
                'fecha' => now()->addDays(15)->setHour(20)->setMinute(0),
                'imagen' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?fit=crop&w=1200&h=800&q=80',
            ],
            [
                'nombre' => 'Hamlet - Tragedia Inmortal',
                'descripcion' => 'La obra maestra de Shakespeare en una versión contemporánea que resuena con los dilemas modernos. Un príncipe atrapado entre el deber y la parálisis, en una corte donde la traición acecha. Teatro Quasar presenta una interpretación íntima y poderosa del texto clásico. Dramaturgo: William Shakespeare. Duración: 2h 30min. Precio desde: $95.000 COP.',
                'fecha' => now()->addDays(18)->setHour(19)->setMinute(30),
                'imagen' => 'https://images.unsplash.com/photo-1507676184212-d0cf64c6b3e5?fit=crop&w=1200&h=800&q=80',
            ],
            [
                'nombre' => 'Noche de Danza Contemporánea',
                'descripcion' => 'Coreógrafos internacionales se reúnen en Teatro Quasar para presentar lo más vanguardista de la danza contemporánea. Movimiento, emoción y experimentación en cada número. Una exploración del cuerpo humano como expresión artística pura. Directores: Colectivo Internacional de Danza. Duración: 1h 45min. Precio desde: $120.000 COP.',
                'fecha' => now()->addDays(21)->setHour(20)->setMinute(0),
                'imagen' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?fit=crop&w=1200&h=800&q=80',
            ],
            [
                'nombre' => 'Concierto Sinfónico - Clásicos Eternos',
                'descripcion' => 'La Orquesta Filarmónica de Bogotá presenta los grandes maestros en Teatro Quasar. Desde Bach hasta Contemporáneos, recorre los mundos sonoros que han trascendido siglos. Una noche de pura música clásica para los sentidos. Director: Maestro Renombrado Internacionalmente. Duración: 2h. Precio desde: $150.000 COP.',
                'fecha' => now()->addDays(24)->setHour(19)->setMinute(0),
                'imagen' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?fit=crop&w=1200&h=800&q=80',
            ],
            [
                'nombre' => 'Noche de Comedia Stand-up',
                'descripcion' => 'Los mejores humoristas colombianos se suben al escenario del Teatro Quasar para una noche de risas sin control. Humor ácido, observacional y absurdo. Cada función es una experiencia única. Curador: Colectivo Comedy Medellín. Duración: 1h 30min. Precio desde: $75.000 COP.',
                'fecha' => now()->addDays(12)->setHour(21)->setMinute(0),
                'imagen' => 'https://images.unsplash.com/photo-1527224538127-21ea696e2e12?fit=crop&w=1200&h=800&q=80',
            ],
            [
                'nombre' => 'Jazz Fusión - Improvisación Pura',
                'descripcion' => 'Músicos de jazz de talla internacional se reúnen en Teatro Quasar para una noche de improvisación en vivo. Saxofón, trompeta y ritmo se funden en composiciones que nacen en el momento. Una experiencia irrepetible cada noche. Artistas Invitados Internacionales. Duración: 2h. Precio desde: $140.000 COP.',
                'fecha' => now()->addDays(27)->setHour(20)->setMinute(30),
                'imagen' => 'https://images.unsplash.com/photo-1511192336575-5a41e0c9ef14?fit=crop&w=1200&h=800&q=80',
            ],
            [
                'nombre' => 'Romeo y Julieta - Amor Eterno',
                'descripcion' => 'La historia de amor más célebre del teatro mundial cobra vida en el Teatro Quasar. Pasión, tragedia y destino se entrelazan en la Verona de Shakespeare. Dirección innovadora que mantiene la esencia del clásico. Dramaturgo: William Shakespeare. Duración: 2h 15min. Precio desde: $110.000 COP.',
                'fecha' => now()->addDays(30)->setHour(18)->setMinute(0),
                'imagen' => 'https://images.unsplash.com/photo-1518998053901-53d6fbf04261?fit=crop&w=1200&h=800&q=80',
            ],
            [
                'nombre' => 'Chicago - Jazz, Crimen y Escándalo',
                'descripcion' => 'El musical de Broadway que cautivó al mundo llega al Teatro Quasar. Velma Kelly y Roxie Hart compiten por los titulares en los años veinte. Jazz, danza y escándalo en la ciudad que nunca duerme. Compositores: John Kander & Fred Ebb. Duración: 2h 15min. Precio desde: $165.000 COP.',
                'fecha' => now()->addDays(33)->setHour(20)->setMinute(0),
                'imagen' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?fit=crop&w=1200&h=800&q=80',
            ],
        ];
    }
}
