<?php

use App\Http\Controllers\AddNewTicketController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AzureController;
use App\Http\Controllers\ClientAddNewTicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportTicketController;
use App\Http\Controllers\FetchEngineers;
use App\Http\Controllers\PendingTicketController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResolveTicketController;
use App\Http\Controllers\TicketRequestController;
use App\Http\Controllers\TicketUpdateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/api/user', [UserController::class, 'current']);
Route::post('/api/login', [AuthController::class, 'login']);
// Azure login routes
Route::get('/login/redirect/azure', [AzureController::class, 'handleRedirect']);
Route::get('/login/callback/azure', [AzureController::class, 'handleCallback']);
Route::post('/logout', [AzureController::class, 'logout'])->middleware('auth');

Route::get('/', function () {
    return view('app');
});

Route::get('/', function () { return view('app'); });
Route::get('/login', function () { return view('app'); })->name('login');
Route::get('/dashboard', function () { return view('app'); })->middleware('auth');

Route::get('/{any}', function () { return view('app'); })
    ->where('any', '^(?!api).*')
    ->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/api/attachments/download/{filename}', [AttachmentController::class, 'download']);
});

Route::middleware(['auth'])->prefix('api/tickets')->group(function () {
    Route::post('/status', [TicketUpdateController::class, 'addStatus']);
    Route::post('/remarks', [TicketUpdateController::class, 'addRemarks']);
    Route::put('/severity', [TicketUpdateController::class, 'updateSeverity']);
    Route::post('/update/{ticket_id}', [TicketUpdateController::class, 'updateTicket']);
    Route::post('/assign-engineer', [TicketUpdateController::class, 'assignEngineer']);
    Route::get('/sap-versions', [TicketUpdateController::class, 'fetchVersions']);
    Route::post('/saveSolution', [TicketUpdateController::class, 'saveSolution']);
});

Route::middleware('auth')->group(function () {
    Route::get('/api/dashboard', [DashboardController::class, 'index']);
    Route::get('/api/tickets', [PendingTicketController::class, 'index']);
    Route::get('/api/fetch-engineers', [FetchEngineers::class, 'index']);
    Route::get('/api/tickets/edit/{id}', [PendingTicketController::class, 'GetEditData']);
    Route::get('/api/tickets/resolveTicket/{id}', [PendingTicketController::class, 'getResolvedClientData']);

    Route::get('/api/tickets/export', [ExportTicketController::class, 'exportPendingTickets']);
    Route::get('/api/tickets/exportResolved', [ExportTicketController::class, 'exportResolvedTickets']);
    Route::get('/api/tickets/exportRequest', [ExportTicketController::class, 'exportRequestTickets']);
    Route::get('/api/tickets/exportReports', [ExportTicketController::class, 'exportReportsTickets']);


    Route::get('/api/tickets/resolved', [ResolveTicketController::class, 'index']);

    Route::get('/api/tickets/reports', [ReportController::class, 'index']);
    Route::get('/api/filters/productLine', [ReportController::class, 'getprodline_company']);

    Route::get('/api/tickets/request', [TicketRequestController::class, 'index']);
    Route::get('/api/filters/data', [TicketRequestController::class, 'getprodline_company']);
    Route::post('/api/tickets/{id}/approve', [TicketRequestController::class, 'approve']);
    Route::post('/api/tickets/{id}/disapprove', [TicketRequestController::class, 'disapprove']);

    Route::get('/api/products', [AddNewTicketController::class, 'fetchProductName']);
    Route::post('/api/addtickets', [AddNewTicketController::class, 'store']);
});
    Route::get('/api/products', [AddNewTicketController::class, 'fetchProductName']);
    Route::post('/api/clientAddTickets', [ClientAddNewTicketController::class, 'store']);
    Route::get('/api/fetch-engineers', [FetchEngineers::class, 'index']);
    Route::get('api/tickets/sap-versions', [TicketUpdateController::class, 'fetchVersions']);
