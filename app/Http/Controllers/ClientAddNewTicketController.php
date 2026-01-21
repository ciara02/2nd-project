<?php

namespace App\Http\Controllers;

use App\Jobs\SendTicketCreatedEmailJob;
use App\Models\Assign_Ticket;
use App\Models\Product_Line;
use App\Models\Ticket_Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClientAddNewTicketController extends Controller
{

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_number' => 'nullable|string|max:20',
            'productName' => 'nullable|string',
            'concern' => 'nullable|string',
        ]);

        $ticketNumber = now()->format('Ymd') . str_pad(
            Ticket_Request::whereDate('date_created', today())->count() + 1,
            4, '0', STR_PAD_LEFT
        );

        $ticket = new Ticket_Request();
        $ticket->ticket_number = $ticketNumber;
        $ticket->date_created = now();
        $ticket->created_by = $validated['name'] ?? null;
        $ticket->company_name = $validated['company_name'] ?? null;
        $ticket->address = $validated['address'] ?? null;
        $ticket->contact_name = $validated['contact_name'] ?? null;
        $ticket->contact_email = $validated['contact_email'] ?? null;
        $ticket->contact_number = $validated['contact_number'] ?? null;
        $ticket->prod_id = $validated['productName'] ?? null;
        $ticket->concern = $validated['concern'] ?? null;
        $ticket->reseller_name = $request->input('Reseller_name');
        $ticket->serial_number = $request->input('serial_number');
        $ticket->license = $request->input('License');
        $ticket->partner_name = $request->input('partner_name');
        $ticket->sapdatabase_name = $request->input('sapdatabase_name');
        $ticket->infrastructure = $request->input('infrastructure');
        $ticket->sap_version = $request->input('sap_version');
        $ticket->status = 'REQUEST';
        $ticket->severity = $request->input('severity');
        $ticket->save(); 

        if ($request->filled('assignEngineerEmail')) {
            Assign_Ticket::create([
                'assign_email' => $request->input('assignEngineerEmail'),
                'assign_name'  => $request->input('assignEngineerName'),
                'assign_date'  => now()->format('Y-m-d H:i:s'),
                'ticket_id'    => $ticket->ticket_id,
            ]);
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('attachments'), $filename);
            $ticket->attachment = $filename;
            $ticket->save();
        }

        $departments = [];

        if (!empty($ticket->prod_id)) {
                $departments = Product_Line::where('prod_id', $ticket->prod_id)
                    ->select('prod_id', 'prod_name')
                    ->get();
            }

            $recipients = [];

            if (!empty($ticket->contact_email)) {
                $recipients[] = [
                    'email' => $ticket->contact_email,
                    'type'  => 'contact', 
                    // 'cc'    => ['techsupport@msi-ecs.com.ph'],
                    'cc'    => ['cdymosco@msi-ecs.com.ph'],

                ];
            }
        if (!empty($recipients)) {
                SendTicketCreatedEmailJob::dispatch($ticket, $departments, $recipients);
            } else {
                Log::warning('No valid email addresses found for ticket #' . $ticket->ticket_id);
            }

        return response()->json([
            'message' => 'Ticket requested successfully!',
            'ticket_id' => $ticket->id,
        ]);
    }

}
