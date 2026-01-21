<?php

namespace App\Http\Controllers;

use App\Models\Ticket_Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExportTicketController extends Controller
{
   public function exportPendingTickets(Request $request)
{
    $query = Ticket_Request::with('engineerAssigned', 'productLine')
             ->where('status', 'PENDING');

    if ($request->filled('dateFrom')) {
        $query->where('date_created', '>=', $request->dateFrom);
    }

    if ($request->filled('dateTo')) {
        $query->where('date_created', '<=', $request->dateTo);
    }
    
  $engineers = collect($request->engineer)
    ->map(function ($item) {
        if (is_string($item) && str_starts_with($item, '{')) {
            $decoded = json_decode($item, true);
            return $decoded['engineer'] ?? null;
        }

        if (is_array($item)) {
            return $item['engineer'] ?? null;
        }
        if (is_object($item)) {
            return $item->engineer ?? null;
        }
        return $item;
    })
    ->filter()
    ->values()
    ->toArray();


    if (!empty($engineers)) {
        $query->whereHas('engineerAssigned', fn($q) => 
            $q->whereIn('assign_name', $engineers)
        );
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
        'resolved_by',
    ];

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $q->orWhere($column, 'like', "%{$search}%");
            }

            $q->orWhereHas('engineerAssigned', fn($sub) =>
                $sub->where('assign_name', 'like', "%{$search}%")
            );

            $q->orWhereHas('productLine', fn($sub) =>
                $sub->where('prod_name', 'like', "%{$search}%")
            );
        });
    }

    $tickets = $query->orderBy('date_created', 'desc')->get();

    return response()->json([
        'data' => $tickets
    ]);
}

   public function exportResolvedTickets(Request $request)
{
    $query = Ticket_Request::with('engineerAssigned', 'productLine')
             ->where('status', 'RESOLVED');

    if ($request->filled('dateFrom')) {
        $query->where('date_created', '>=', $request->dateFrom);
    }

    if ($request->filled('dateTo')) {
        $query->where('date_created', '<=', $request->dateTo);
    }
    
  $engineers = collect($request->engineer)
    ->map(function ($item) {
        if (is_string($item) && str_starts_with($item, '{')) {
            $decoded = json_decode($item, true);
            return $decoded['engineer'] ?? null;
        }

        if (is_array($item)) {
            return $item['engineer'] ?? null;
        }
        if (is_object($item)) {
            return $item->engineer ?? null;
        }
        return $item;
    })
    ->filter()
    ->values()
    ->toArray();


    if (!empty($engineers)) {
        $query->whereHas('engineerAssigned', fn($q) => 
            $q->whereIn('assign_name', $engineers)
        );
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
        'resolved_by',
    ];

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $q->orWhere($column, 'like', "%{$search}%");
            }

            $q->orWhereHas('engineerAssigned', fn($sub) =>
                $sub->where('assign_name', 'like', "%{$search}%")
            );

            $q->orWhereHas('productLine', fn($sub) =>
                $sub->where('prod_name', 'like', "%{$search}%")
            );
        });
    }

    $tickets = $query->orderBy('date_created', 'desc')->get();

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

    return response()->json([
        'data' => $tickets
    ]);
}
public function exportRequestTickets(Request $request)
{
    $query = Ticket_Request::with('engineerAssigned', 'productLine')
             ->where('status', 'REQUEST');

    if ($request->filled('productLine')) {
        $productLine = json_decode($request->productLine, true);
        
        if (isset($productLine['value'])) {
            $query->where('prod_id', $productLine['value']);
        } else {
            return response()->json(['error' => 'Invalid product line value'], 400);
        }
    }

    if ($request->filled('company')) {
        $company = json_decode($request->company, true);
        
        if (isset($company['value'])) {
            $query->where('company_name', $company['value']);
        } else {
            return response()->json(['error' => 'Invalid company value'], 400);
        }
    }

    if ($request->filled('severity')) {
        $severity = json_decode($request->severity, true);
        
        if (isset($severity['value'])) {
            $query->where('severity', $severity['value']);
        } else {
            return response()->json(['error' => 'Invalid severity value'], 400);
        }
    }
    $engineers = collect($request->engineer)
        ->map(function ($item) {
            if (is_string($item) && str_starts_with($item, '{')) {
                $decoded = json_decode($item, true);
                return $decoded['engineer'] ?? null;
            }

            if (is_array($item)) {
                return $item['engineer'] ?? null;
            }
            if (is_object($item)) {
                return $item->engineer ?? null;
            }
            return $item;
        })
        ->filter()
        ->values()
        ->toArray();

    if (!empty($engineers)) {
        $query->whereHas('engineerAssigned', fn($q) => 
            $q->whereIn('assign_name', $engineers)
        );
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
        'resolved_by',
    ];

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $q->orWhere($column, 'like', "%{$search}%");
            }

            $q->orWhereHas('engineerAssigned', fn($sub) =>
                $sub->where('assign_name', 'like', "%{$search}%")
            );

            $q->orWhereHas('productLine', fn($sub) =>
                $sub->where('prod_name', 'like', "%{$search}%")
            );
        });
    }

    $tickets = $query->orderBy('date_created', 'desc')->get();

    return response()->json([
        'data' => $tickets
    ]);
}

public function exportReportsTickets(Request $request)
{
    $query = Ticket_Request::with('engineerAssigned', 'productLine');

    if ($request->filled('dateFrom')) {
        $query->where('date_created', '>=', $request->dateFrom);
    }

    if ($request->filled('dateTo')) {
        $query->where('date_created', '<=', $request->dateTo);
    }

    $engineers = collect($request->engineer)
        ->map(function ($item) {
            if (is_string($item) && str_starts_with($item, '{')) {
                $decoded = json_decode($item, true);
                return $decoded['engineer'] ?? null;
            }

            if (is_array($item)) {
                return $item['engineer'] ?? null;
            }
            if (is_object($item)) {
                return $item->engineer ?? null;
            }
            return $item;
        })
        ->filter()
        ->values()
        ->toArray();

    if (!empty($engineers)) {
        $query->whereHas('engineerAssigned', fn($q) => 
            $q->whereIn('assign_name', $engineers)
        );
    }

    if ($request->filled('productLine')) {
        $productLine = json_decode($request->productLine, true);
        
        if (isset($productLine['value'])) {
            $query->where('prod_id', $productLine['value']);
        } else {
            return response()->json(['error' => 'Invalid product line value'], 400);
        }
    }

    if ($request->filled('company')) {
        $company = json_decode($request->company, true);
        
        if (isset($company['value'])) {
            $query->where('company_name', $company['value']);
        } else {
            return response()->json(['error' => 'Invalid company value'], 400);
        }
    }

    if ($request->filled('severity')) {
        $severity = json_decode($request->severity, true);
        
        if (isset($severity['value'])) {
            $query->where('severity', $severity['value']);
        } else {
            return response()->json(['error' => 'Invalid severity value'], 400);
        }
    }

    if ($request->filled('status')) {
        $status = json_decode($request->status, true);
        
        if (isset($status['value'])) {
            $query->where('status', $status['value']);
        } else {
            return response()->json(['error' => 'Invalid status value'], 400);
        }
    }

    Log::info("Filters received: ", $request->all());

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
        'resolved_by',
    ];

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $q->orWhere($column, 'like', "%{$search}%");
            }

            $q->orWhereHas('engineerAssigned', fn($sub) =>
                $sub->where('assign_name', 'like', "%{$search}%")
            );

            $q->orWhereHas('productLine', fn($sub) =>
                $sub->where('prod_name', 'like', "%{$search}%")
            );
        });
    }

    $tickets = $query->orderBy('date_created', 'desc')->get();

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

    return response()->json([
        'data' => $tickets
    ]);
}


}
