<?php

namespace App\Http\Controllers;

use App\Models\Ticket_Request;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
public function index()
{
    try {
        $monthlyTickets = Ticket_Request::selectRaw('YEAR(date_created) as year, MONTH(date_created) as month, COUNT(*) as total')
            ->whereNotNull('date_created')
            ->groupByRaw('YEAR(date_created), MONTH(date_created)')
            ->get();

        $monthlyTickets = $monthlyTickets->filter(function ($ticket) {
            return !is_null($ticket->year) && !is_null($ticket->month);
        });

        $lastYearTickets = Ticket_Request::selectRaw('MONTH(date_created) as month, COUNT(*) as total')
            ->whereYear('date_created', date('Y', strtotime('-1 year')))
            ->groupByRaw('MONTH(date_created)')
            ->pluck('total', 'month');

        Log::info('Monthly Tickets Query:', [$monthlyTickets]);

        return response()->json([
            'pendingTickets'  => Ticket_Request::where('status', 'PENDING')->count(),
            'resolvedTickets' => Ticket_Request::where('status', 'RESOLVED')->count(),
            'todayTickets'    => Ticket_Request::whereDate('date_created', today())->count(),
            'requestTickets'  => Ticket_Request::where('status', 'REQUEST')->count(),
            'monthlyTickets'  => $monthlyTickets,
            'lastYearTickets' => $lastYearTickets
        ]);
    } catch (\Exception $e) {
        Log::error($e->getMessage().' in '.$e->getFile().' at line '.$e->getLine());
        return response()->json([
            'pendingTickets'  => 0,
            'resolvedTickets' => 0,
            'todayTickets'    => 0,
            'requestTickets'  => 0,
            'monthlyTickets'  => [],
            'lastYearTickets' => [],
            'error'           => $e->getMessage()
        ], 500);
    }
}


}
