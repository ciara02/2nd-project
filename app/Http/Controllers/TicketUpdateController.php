<?php

namespace App\Http\Controllers;

use App\Jobs\SendTicketCreatedEmailJob;
use App\Models\Assign_Ticket;
use App\Models\Product_Line;
use App\Models\Ticket_Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketUpdateController extends Controller
{
    public function addStatus(Request $request)
    {
        $validated = $request->validate([
            'ticket_id'     => 'required|integer',
            'status'        => 'required|string',
            'custom_status' => 'nullable|string',
        ]);
    
        $updatedBy = Auth::check() ? Auth::user()->name : 'system';
    
        try {
            $ticket = Ticket_Request::findOrFail($validated['ticket_id']);
    
            $insertedId = DB::table('update_status')->insertGetId([
                'ticket_id' => $validated['ticket_id'],
                'status' => $validated['status'],
                'Custom_Status' => $validated['custom_status'],
                'update_date' => now(),
                'update_by' => $updatedBy,
            ]);
    
            $newStatusRecord = DB::table('update_status')
                ->where('status_id', $insertedId)
                ->first();
    
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
                    'type'  => 'update',
                    'cc'    => ['cdymosco@msi-ecs.com.ph'],
                ];
            }
    
            if (!empty($recipients)) {
                SendTicketCreatedEmailJob::dispatch(
                    $ticket,
                    $departments,
                    $recipients,
                    $newStatusRecord
                );
            }
    
            Log::info("Status added successfully for ticket_id={$ticket->id}, by={$updatedBy}");
    
            return response()->json([
                'message'          => 'Status added successfully',
                'newStatusRecord'  => $newStatusRecord,
            ]);
    
        } catch (\Exception $e) {
            Log::error("Status update failed for ticket_id={$validated['ticket_id']}, by={$updatedBy}: {$e->getMessage()}");
    
            return response()->json([
                'message' => 'Failed to update status'
            ], 500);
        }
    }
    


    public function addRemarks(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|integer',
            'logs' => 'required|string',
        ]);

        $logBy = Auth::check() ? Auth::user()->name : 'system';

        try {
            $insertedId = DB::table('logs')->insertGetId([
                'ticket_id' => $validated['ticket_id'],
                'logs' => $validated['logs'],
                'log_date' => now(),
                'log_by' => $logBy,
            ]);

            $newRemarkRecord = DB::table('logs')->where('log_id', $insertedId)->first();

            return response()->json([
                'message' => 'Log added successfully',
                'newRemarkRecord' => $newRemarkRecord,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to add remark for ticket_id={$validated['ticket_id']}: " . $e->getMessage());
            return response()->json(['message' => 'Failed to add log'], 500);
        }
    }


    public function updateSeverity(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|integer',
            'severity' => 'required|string',
            'severity_reason' => 'nullable|string',
        ]);

        DB::table('ticket_request')
            ->where('ticket_id', $validated['ticket_id'])
            ->update([
                'severity' => $validated['severity'],
                'severity_reason' => $validated['severity_reason'],
            ]);

        return response()->json(['message' => 'Severity updated successfully']);
    }
    public function updateTicket(Request $request, $ticket_id)
    {
        Log::info('Incoming ticket update request:', $request->all());
        $ticket = Ticket_Request::findOrFail($ticket_id);
    
        $ticketFields = $request->only([
            'company_name', 'contact_name', 'contact_email', 'contact_number',
            'ticketRef', 'concern', 'productName', 'Reseller_name',
            'serial_number', 'License', 'partner_name', 'sap_version'
        ]);
    
        $ticketFields = array_merge($ticketFields, [
            'sapdatabase_name' => $request->sapdatabase_name,
            'infrastructure'  => $request->infrastructure ?? 'Select Infrastructure',
        ]);
    
        if (isset($ticketFields['ticketRef'])) {
            $ticketFields['refence_ticket'] = $ticketFields['ticketRef'];
            unset($ticketFields['ticketRef']);
        }
    
        $ticket->update($ticketFields);
    
        Log::info('Received engineer_name:', ['engineer_name' => $request->engineer_name]);
    
        $assignment = null;
        $recipients = [];
    
        if ($request->filled('engineer_name') && $request->filled('engineer_email')) {
            $newEngineerEmail = $request->engineer_email;
    
            $previousAssignment = Assign_Ticket::where('ticket_id', $ticket_id)
                ->orderByDesc('assign_id')
                ->first();
    
            if (!$previousAssignment || $previousAssignment->assign_email !== $newEngineerEmail) {
                $assignment = Assign_Ticket::create([
                    'ticket_id'    => $ticket_id,
                    'assign_name'  => $request->engineer_name,
                    'assign_email' => $newEngineerEmail,
                    'assign_date'  => now()->format('Y-m-d H:i:s'),
                ]);
            }
    
            $recipients[] = [
                'email'  => $newEngineerEmail,
                'type'   => 'assign',
                'record' => $assignment,
                'cc'     => ['cdymosco@msi-ecs.com.ph'],
            ];
    
            if ($previousAssignment && $previousAssignment->assign_email !== $newEngineerEmail) {
                $recipients[] = [
                    'email'  => $previousAssignment->assign_email,
                    'type'   => 'reassign',
                    'record' => $previousAssignment,
                    'cc'     => ['cdymosco@msi-ecs.com.ph'],
                ];
            }
        }
    
        if (!empty($recipients)) {
            SendTicketCreatedEmailJob::dispatch($ticket, [], $recipients);
            Log::info('Email job dispatched for ticket #' . $ticket->ticket_id);
        } else {
            Log::warning('No valid engineer email found for ticket #' . $ticket->ticket_id);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Ticket updated successfully',
            'ticket'  => $ticket,
            'assignedEngineer' => $assignment,
        ]);
    }

    public function fetchVersions()
    {
        $versions = DB::table('sap_versions')->pluck('sap_b1_version');
        return response()->json($versions);
    }

    public function saveSolution(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|integer',
            'assignEngineer' => 'required|string',
            'solution' => 'required|string',
        ]);
    
        $ticket = Ticket_Request::findOrFail($validated['ticket_id']);
    
        $ticket->resolved_by = $validated['assignEngineer'];
        $ticket->solution = $validated['solution'];
        $ticket->date_resolved = Carbon::now()->format('Y-m-d G:i:s');
        $ticket->status = 'RESOLVED'; 
    
        $ticket->save();

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
                    'type'  => 'resolved', 
                    // 'cc'    => ['techsupport@msi-ecs.com.ph'],
                ];
            }

            $assignRecord = Assign_Ticket::where('ticket_id', $ticket->ticket_id)->first();
            if (!empty($assignRecord->assign_email)) {
                $recipients[] = [
                    'email' => $assignRecord->assign_email,
                    'type'  => 'resolved',
                    'record'=> $assignRecord,
                    // 'cc'    => ['techsupport@msi-ecs.com.ph'],
                ];
            }

            if (!empty($recipients)) {
                SendTicketCreatedEmailJob::dispatch($ticket, $departments, $recipients);
            } else {
                Log::warning('No valid email addresses found for ticket #' . $ticket->ticket_id);
            }
    
        return response()->json(['message' => 'Ticket marked as resolved successfully.']);
    }
    

}
