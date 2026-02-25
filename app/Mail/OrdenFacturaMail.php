<?php

namespace App\Mail;

use App\Models\Orden;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdenFacturaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $orden;

    public function __construct(Orden $orden)
    {
        $this->orden = $orden;
    }

    public function build()
    {
        // Generamos el PDF en memoria para el adjunto
        $pdf = Pdf::loadView('ordenes.pdf', ['orden' => $this->orden]);

        return $this->view('emails.orden_factura') // Crearemos esta vista ahora
                    ->subject('Factura de Servicio - ' . $this->orden->vehiculo->placa)
                    ->attachData($pdf->output(), "factura_{$this->orden->id}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
    }
}       