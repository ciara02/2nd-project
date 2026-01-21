<?php

namespace App\Http\Controllers;

use App\Models\Ticket_Request;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PendingTicketController extends Controller
{
    public function index(Request $request)
{
    $page = max(1, (int) $request->page);
    $perPage = 10;
    $skip = ($page - 1) * $perPage;

    $query = Ticket_Request::with('engineerAssigned')
             ->where('status', 'PENDING');

    if ($request->filled('dateFrom')) {
        $query->where('date_created', '>=', $request->dateFrom);
    }

    if ($request->filled('dateTo')) {
        $query->where('date_created', '<=', $request->dateTo);
    }

    $engineers = [];

        if ($request->filled('engineer')) {
            if (is_array($request->engineer)) {
                $engineers = array_map(fn($e) => is_object($e) ? $e->engineer : $e, $request->engineer);
            } elseif (is_object($request->engineer)) {
                $engineers = [$request->engineer->engineer];
            } else {
                $engineers = [$request->engineer];
            }
        }

        if (!empty($engineers)) {
            $query->whereHas('engineerAssigned', fn($q) => $q->whereIn('assign_name', $engineers));
        }

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

public function GetEditData($id)
{
    $ticket = Ticket_Request::with(['engineerAssigned', 'productLine', 'remarksLogs', 'updateStatus', 'assignments'])
                ->findOrFail($id);

     $user = auth()->user();

     if ($user->role !== '1') { 
        $assignments = $ticket->assignments; 
    
        if ($assignments && ! $assignments instanceof \Illuminate\Support\Collection) {
            $assignments = collect([$assignments]);
        }
    
        $isAssigned = $assignments
            ? $assignments->contains(function($engineer) use ($user) {
                return $engineer->assign_email === $user->email;
            })
            : false;
    
        if (!$isAssigned) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            } else {
                return response()->view('errors.403', [], 403);
            }
        }
    }

    $ticketReferences = Ticket_Request::orderBy('date_created', 'desc')
                        ->pluck('ticket_number');

    return response()->json([
        'ticket' => $ticket,
        'references' => $ticketReferences,
    ]);
}

public function getResolvedClientData($id){
    $ticket = Ticket_Request::with(['assignments'])->findOrFail($id);


    return response()->json([
        'ticket' => $ticket,
    ]);
}
}