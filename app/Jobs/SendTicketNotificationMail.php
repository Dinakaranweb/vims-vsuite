<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTicketNotificationMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email_to;
    protected $mail_details;
    protected $subject;
    protected $view;


    /**
     * Create a new job instance.
     */
    public function __construct($email_to, $mail_details, $subject, $view)
    {
        $this->email_to = $email_to;
        $this->mail_details = $mail_details;
        $this->subject = $subject;
        $this->view = $view;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::send($this->view, ['user' => $this->email_to, 'mail_details' => $this->mail_details], function($message) {
            $message->to($this->email_to->email);
            $message->subject($this->subject);
        });
    }
}
