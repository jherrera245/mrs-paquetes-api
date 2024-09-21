<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendDocuments extends Notification
{
    use Queueable;
    public $message;
    public $subject;
    public $fromEmail;
    public $mailer;
    private $pdfBase64;

    /**
     * Create a new notification instance.
     *
     * @param array $details Array que contiene la información del cliente y el PDF en base64
     * @return void
     */
    public function __construct($details)
    {
        $this->subject = 'Mrs. Paquetes';
        $this->fromEmail = "soportemrspaquetes@gmail.com";
        $this->mailer = 'smtp';
        $this->cliente = $details['cliente'];
        $this->numero_control = $details['numero_control'];
        $this->fecha = $details['fecha'];
        $this->tipo_documento = $details['tipo_documento'];
        $this->total_pagar = $details['total_pagar'];
        $this->numero_tracking = $details['numero_tracking'];
        $this->pdfBase64 = $details['pdfBase64'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Decodifica el Base64
        $pdfContent = base64_decode($this->pdfBase64);

        // Construir el correo electrónico
        $mailMessage = (new MailMessage)
            ->mailer($this->mailer)
            ->subject($this->subject)
            ->markdown('emails.send_documents', [
                'cliente' => $this->cliente,
                'numero_control' => $this->numero_control,
                'numero_tracking' => $this->numero_tracking,
                'fecha' => $this->fecha,
                'tipo_documento' => $this->tipo_documento,
                'total_pagar' => $this->total_pagar,
            ])
            ->attachData($pdfContent, 'DocumentoTributario.pdf', [
                'mime' => 'application/pdf',
            ]);

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
