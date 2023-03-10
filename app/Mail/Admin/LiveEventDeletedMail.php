<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LiveEventDeletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    private string $liveEventName;

    /**
     * @param string $liveEventName
     * @return void
     */
    public function __construct(string $liveEventName)
    {
        $this->liveEventName = $liveEventName;
    }

    /**
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Cancelamento de Evento o vivo")
            ->markdown('admin.mail.live-event.deleted', [
                'liveEventName' => $this->liveEventName
            ]);
    }
}
