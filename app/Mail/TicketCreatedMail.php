<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TicketCreatedMail extends Mailable 
{
    use Queueable, SerializesModels;

    public $ticket;
    public $departments;
    public $type; 
    public $assignRecord;
    public $newStatusRecord;

    public function __construct($ticket, $departments, $type = 'contact',  $assignRecord = null, $newStatusRecord = null)
    {
        $this->ticket = $ticket;
        $this->departments = $departments;
        $this->type = $type;
        $this->assignRecord = $assignRecord;
        $this->newStatusRecord = $newStatusRecord;
    }

   public function build()
    {
        switch ($this->type) {

            case 'assign':
                $view = 'EmailTemplate.TicketAssigned';
                $subject = '[TEST] iSupport Ticketing System - New Ticket Assigned - ' . ($this->ticket->ticket_number ?? '');
                break;

            case 'approve':
                $view = 'EmailTemplate.TicketApproved';
                $subject = '[TEST] iSupport Ticketing System - Ticket Approved ' . ($this->ticket->ticket_number ?? '');
                break;

            case 'reject':
                $view = 'EmailTemplate.TicketDeclined';
                $subject = '[TEST] iSupport Ticketing System - Ticket Declined ' . ($this->ticket->ticket_number ?? '');
                break;

            case 'resolved':
                $view = 'EmailTemplate.TicketResolved';
                $subject = '[TEST] iSupport Ticketing System - Ticket Resolved ' . ($this->ticket->ticket_number ?? '');
                break;

                      
            case 'update':
                $view = 'EmailTemplate.TicketUpdated';
                $subject  = '[TEST] iSupport Ticketing System - Ticket Updated ' . $this->ticket->ticket_number;
                break;

            case 'reassign':
                $view = 'EmailTemplate.TicketReAssigned';
                $subject  = '[TEST] iSupport Ticketing System - Ticket Reassigned ' . $this->ticket->ticket_number;
                break;


            default:
                $view = 'EmailTemplate.TicketCreated';
                $subject = '[TEST] iSupport Ticketing System - New Ticket Created ' . ($this->ticket->ticket_number ?? '');
        }

        $m = $this->subject($subject)->view($view)->with([
            'ticket'       => $this->ticket,
            'departments'  => $this->departments,
            'assignRecord' => $this->assignRecord,
            'statusUpdate' => $this->newStatusRecord,
        ]);

        if (!empty($this->ticket->attachment)) {
            $path = public_path('attachments/' . $this->ticket->attachment);
            if (file_exists($path)) {
                $m->attach($path);
                Log::info('TicketCreatedMail::attachment attached', ['path' => $path]);
            } else {
                Log::warning('TicketCreatedMail::attachment not found', ['path' => $path]);
            }
        }

        return $m;
    }

}
