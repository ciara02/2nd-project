<?php

namespace App\Http\Controllers;

use App\Models\Product_Line;
use App\Models\Ticket_Request;
use Illuminate\Http\Request;

class ReportController extends Controller
{
         public function index(Request $request)
{
    $page = max(1, (int) $request->page);
    $perPage = 10;
    $skip = ($page - 1) * $perPage;

    $query = Ticket_Request::with('engineerAssigned');

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

        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }

        // PRODUCT LINE filter
        if ($request->filled('productLine')) {
            $productLines = is_array($request->productLine) ? $request->productLine : [$request->productLine];
            $query->whereIn('prod_id', $productLines);
        }

        // COMPANY filter
        if ($request->filled('company')) {
            $companies = is_array($request->company) ? $request->company : [$request->company];
            $query->whereIn('company_name', $companies);
        }


           $searchableColumns = [
            'ticket_number',
            'company_name',
            'sapdatabase_name',
            'sap_version',
            'contact_number',
            'date_created',
            'Reseller_name',
            'License',
            'serial_number',
            'resolved_by',
    ];

   if ($request->filled('search')) {
    $search = $request->search;

    $query->where(function ($q) use ($search, $searchableColumns) {

        // Use WHERE on the first column
        $first = array_shift($searchableColumns);
        $q->where($first, 'like', "%{$search}%");

        // Use OR for the rest
        foreach ($searchableColumns as $column) {
            $q->orWhere($column, 'like', "%{$search}%");
        }

        // Relations
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

   foreach ($tickets as $ticket) {
    if (!empty($ticket->date_created) && !empty($ticket->date_resolved)) {
        $dateCreated = new \DateTime($ticket->date_created);
        $dateResolved = new \DateTime($ticket->date_resolved);

        $diff = $dateResolved->diff($dateCreated);

        $totalHours = ($diff->days * 24) + $diff->h;
        $totalMinutes = $diff->i;
        $totalSeconds = $diff->s;

        $formatted = "{$totalHours} hrs {$totalMinutes} mins";
        if ($totalSeconds > 0) {
            $formatted .= " {$totalSeconds} secs";
        }

        $ticket->ticket_completion = $formatted;
    } else {
        $ticket->ticket_completion = 'N/A';
    }
}


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

    $status = ['PENDING', 'REQUEST', 'RESOLVED', 'REJECTED'];

    return response()->json([
        'productLines' => $productLines,
        'companies' => $companies,
        'status' => $status
    ]);
}
}
