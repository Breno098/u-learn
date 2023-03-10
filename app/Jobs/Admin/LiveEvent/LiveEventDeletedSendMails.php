<?php

namespace App\Jobs\Admin\LiveEvent;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;
use App\Mail\Admin\LiveEventDeletedMail;

class LiveEventDeletedSendMails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private string $liveEventName;

     /**
     * @var Collection
     */
    private Collection $emails;

    /**
     * @param string $liveEventName
     * @param array $emails
     */
    public function __construct(string $liveEventName, array $emails = [])
    {
        $this->liveEventName = $liveEventName;
        $this->emails = new Collection($emails);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->emails->each(function($email){
            Mail::to($email)->send(
                new LiveEventDeletedMail($this->liveEventName)
            );
        });
    }
}
