<?php

namespace App\Http\Controllers;

use App\Jobs\SendTicketCreatedEmailJob;
use App\Models\Assign_Ticket;
use App\Models\Product_Line;
use App\Models\Ticket_Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketRequestController extends Controller
{
   public function index(Request $request)
{
    $page = max(1, (int) $request->page);
    $perPage = 10;
    $skip = ($page - 1) * $perPage;

    $query = Ticket_Request::with('engineerAssigned')
             ->where('status', 'REQUEST');


        $searchableColumns = [
            'ticket_number',
            'company_name',
            'contact_name',
            'contact_email',
            'contact_number',
            'date_created',
            'Reseller_name',
            'License',
            'serial_number',
            'severity',
    ];

     if ($request->filled('productLine')) {
            $query->where('prod_id', $request->productLine);
        }

        if ($request->filled('company')) {
            $query->where('company_name', $request->company);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $q->orWhere($column, 'like', "%{$search}%");
            }

            $q->orWhereHas('engineerAssigned', function ($sub) use ($search) {
                $sub->where('assign_name', 'like', "%{$search}%");
            });

            $q->orWhereHas('productLine', function ($sub) use ($search) {
                $sub->where('prod_name', 'like', "%{$search}%");
            });
        });
    }


    $ids = $query->orderBy('date_created', 'desc')
                 ->pluck('ticket_id')
                 ->slice($skip, $perPage)
                 ->toArray();

    $tickets = Ticket_Request::with('engineerAssigned', 'productLine', 'assignments')
                             ->whereIn('ticket_id', $ids)
                             ->orderBy('date_created', 'desc')
                             ->get();

    $total = $query->count();

    return response()->json([
        'data' => $tickets,
        'current_page' => $page,
        'last_page' => ceil($total / $perPage),
        'per_page' => $perPage,
        'total' => $total,
    ]);
}

   public function getprodline_company()
{
    $productLines = Product_Line::select('prod_id', 'prod_name')->get();

    $companies = Ticket_Request::select('company_name')
        ->distinct()
        ->orderBy('company_name')
        ->get();

    $severities = ['Low', 'Medium', 'High', 'Critical'];

    return response()->json([
        'productLines' => $productLines,
        'companies' => $companies,
        'severities' => $severities
    ]);
}

 public function approve($id)
    {
        $ticket = Ticket_Request::findOrFail($id);

        if (Auth::user()->role !== '1') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $ticket->status = 'PENDING';
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
                    'type'  => 'approve', 
                    // 'cc'    => ['techsupport@msi-ecs.com.ph'],
                ];
            }

            $assignRecord = Assign_Ticket::where('ticket_id', $ticket->ticket_id)->first();
            if (!empty($assignRecord->assign_email)) {
                $recipients[] = [
                    'email' => $assignRecord->assign_email,
                    'type'  => 'approve',
                    'record'=> $assignRecord,
                    // 'cc'    => ['techsupport@msi-ecs.com.ph'],
                ];
            }

            if (!empty($recipients)) {
                SendTicketCreatedEmailJob::dispatch($ticket, $departments, $recipients);
            } else {
                Log::warning('No valid email addresses found for ticket #' . $ticket->ticket_id);
            }

        return response()->json([
            'message' => 'Ticket approved successfully',
            'ticket' => $ticket
        ], 200);
    }

    public function disapprove(Request $request, $id)
{
    $ticket = Ticket_Request::findOrFail($id);

    if (Auth::user()->role !== '1') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $ticket->status = 'REJECTED';
    $ticket->declined_reason = $request->input('reason'); 
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
                    'type'  => 'reject', 
                    // 'cc'    => ['techsupport@msi-ecs.com.ph'],
                ];
            }

            $assignRecord = Assign_Ticket::where('ticket_id', $ticket->ticket_id)->first();
            if (!empty($assignRecord->assign_email)) {
                $recipients[] = [
                    'email' => $assignRecord->assign_email,
                    'type'  => 'reject',
                    'record'=> $assignRecord,
                    // 'cc'    => ['techsupport@msi-ecs.com.ph'],
                ];
            }

            if (!empty($recipients)) {
                SendTicketCreatedEmailJob::dispatch($ticket, $departments, $recipients);
            } else {
                Log::warning('No valid email addresses found for ticket #' . $ticket->ticket_id);
            }


    return response()->json([
        'message' => 'Ticket declined successfully',
        'ticket' => $ticket
    ], 200);
}

}
