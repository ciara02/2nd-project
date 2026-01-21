<?php

namespace App\Jobs;

use App\Mail\TicketCreatedMail;
use App\Services\EmailApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTicketCreatedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ticket;
    public $departments;

    public $tries = 3;
    public $timeout = 30;
    public $recipients;
    public $newStatusRecord;

    public function __construct($ticket, $departments, $recipients = [], $newStatusRecord = null)
    {
        $this->ticket = $ticket;
        $this->departments = $departments;
        $this->recipients = $recipients;
        $this->newStatusRecord = $newStatusRecord;
    }

    public function handle(EmailApiService $emailApi)
    {
        foreach ($this->recipients as $recipient) {

           switch ($recipient['type']) {

                case 'assign':
                    $template = 'EmailTemplate.TicketAssigned';
                    $subject  = 'iSupport Ticketing System - New Ticket Assigned ' . $this->ticket->ticket_number;
                    break;

                case 'approve':
                    $template = 'EmailTemplate.TicketApproved';
                    $subject  = 'iSupport Ticketing System - Ticket Approved ' . $this->ticket->ticket_number;
                    break;

                case 'reject':
                    $template = 'EmailTemplate.TicketDeclined';
                    $subject  = 'iSupport Ticketing System - Ticket Declined ' . $this->ticket->ticket_number;
                    break;

                case 'resolved':
                    $template = 'EmailTemplate.TicketResolved';
                    $subject  = 'iSupport Ticketing System - Ticket Resolved ' . $this->ticket->ticket_number;
                    break;  
                    
                case 'update':
                    $template = 'EmailTemplate.TicketUpdated';
                    $subject  = 'iSupport Ticketing System - Ticket Updated ' . $this->ticket->ticket_number;
                    break;

                case 'reassign':
                    $template = 'EmailTemplate.TicketReAssigned';
                    $subject  = 'iSupport Ticketing System - Ticket Reassigned ' . $this->ticket->ticket_number;
                    break;

                default:
                    $template = 'EmailTemplate.TicketCreated';
                    $subject  = 'iSupport Ticketing System - New Ticket Created ' . $this->ticket->ticket_number;
            }


            $html = view($template, [
                'ticket'      => $this->ticket,
                'departments' => $this->departments,
                'assignRecord'=> $recipient['record'] ?? null,
                'newStatusRecord'=> $this->newStatusRecord
            ])->render();

            try {
                $response = $emailApi->send(
                    $recipient['email'], 
                    $subject, 
                    $html, 
                    $recipient['cc'] ?? []
                );

                if (!$response->successful()) {
                    throw new \Exception("Email API failed for {$recipient['email']}");
                }

                Log::info('Email sent via internal API', [
                    'ticket_id' => $this->ticket->ticket_id,
                    'email'     => $recipient['email'],
                    'cc'        => $recipient['cc'] ?? [],
                ]);

            } catch (\Exception $e) {
                Log::error('Email NOT sent via internal API', [
                    'ticket_id' => $this->ticket->ticket_id,
                    'email'     => $recipient['email'],
                    'error'     => $e->getMessage(),
                    'cc'        => $recipient['cc'] ?? [],
                ]);

                try {
                    $mail = new TicketCreatedMail(
                        $this->ticket,
                        $this->departments,
                        $recipient['type'],
                        $recipient['record'] ?? null,
                        $this->newStatusRecord
                    );

                    $mailable = Mail::to($recipient['email']);
                    if (!empty($recipient['cc'])) {
                        $mailable->cc($recipient['cc']);
                    }

                    $mailable->send($mail);

                    Log::info('Email sent via SMTP as fallback', [
                        'ticket_id' => $this->ticket->ticket_id,
                        'email'     => $recipient['email'],
                        'cc'        => $recipient['cc'] ?? [],
                    ]);

                } catch (\Exception $smtpException) {
                    Log::error('SMTP fallback failed', [
                        'ticket_id' => $this->ticket->ticket_id,
                        'email'     => $recipient['email'],
                        'error'     => $smtpException->getMessage(),
                        'cc'        => $recipient['cc'] ?? [],
                    ]);
                }
            }
        }
    }

}

