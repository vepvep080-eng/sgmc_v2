<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourrierAttachmentController;
use App\Http\Controllers\CourrierController;
use App\Http\Controllers\CourrierExcelExportController;
use App\Http\Controllers\CourrierDocumentPdfExportController;
use App\Http\Controllers\CourrierPdfExportController;
use App\Http\Controllers\CourrierPrintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OutgoingCourrierController;
use App\Http\Controllers\OutgoingCourrierAttachmentController;
use App\Http\Controllers\OutgoingCourrierExportController;
use App\Http\Controllers\OutgoingCourrierReportController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\ScanJobController;
use App\Http\Controllers\ScannerAgentController;
use App\Http\Controllers\QrPayloadController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'splash');
Route::get('/language/{locale}', [LocalizationController::class, 'update'])
    ->whereIn('locale', ['fr', 'ar'])
    ->name('language.update');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'store']);
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/organization', [OrganizationController::class, 'index'])->name('organization.index');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/courriers/export/pdf', [CourrierPdfExportController::class, 'index'])
        ->name('courriers.export.pdf');

    Route::get('/courriers/export/excel', [CourrierExcelExportController::class, 'index'])
        ->name('courriers.export.excel');

    Route::get('/courriers/print', [CourrierPrintController::class, 'index'])->name('courriers.print');
    Route::get('/courriers/{courrier}/print', [CourrierPrintController::class, 'show'])->name('courriers.print.show');

    Route::get('/courriers/{courrier}/export/pdf', [CourrierDocumentPdfExportController::class, 'show'])
        ->name('courriers.export.pdf.show');

    Route::get('/courriers/{courrier}/attachments/{attachment}', [CourrierAttachmentController::class, 'show'])
        ->whereIn('attachment', ['piece_jointe', 'traitement_piece_jointe'])
        ->name('courriers.attachments.show');
    Route::get('/courriers/quick-create', [CourrierController::class, 'quickCreate'])->name('courriers.quick-create');
    Route::resource('courriers', CourrierController::class);
    Route::post('/courriers/{courrier}/scan-jobs', [ScanJobController::class, 'store'])->name('courriers.scan-jobs.store');
    Route::get('/courriers/{courrier}/scan-jobs/{scanJob}', [ScanJobController::class, 'show'])->name('courriers.scan-jobs.show');
    Route::post('/scan-jobs/{scanJob}/upload', [ScanJobController::class, 'upload'])->name('scan-jobs.upload');
    Route::post('/scan-jobs/{scanJob}/cancel', [ScanJobController::class, 'cancel'])->name('scan-jobs.cancel');
    Route::post('/courriers/{courrier}/qr-payload', [QrPayloadController::class, 'store'])->name('courriers.qr-payload.store');

    // Scanner Agent communication contract
    Route::post('/courriers/{courrier}/scan-jobs/{scanJob}/agent-status', [ScannerAgentController::class, 'status'])->name('courriers.scan-jobs.agent-status');
    Route::get('/courriers/{courrier}/scan-jobs/{scanJob}/agent-payload', [ScannerAgentController::class, 'payload'])->name('courriers.scan-jobs.agent-payload');

    Route::get('/courriers-depart/export/pdf', [OutgoingCourrierExportController::class, 'pdf'])->name('courriers-depart.export.pdf');
    Route::get('/courriers-depart/export/excel', [OutgoingCourrierExportController::class, 'excel'])->name('courriers-depart.export.excel');
    Route::get('/courriers-depart/print', [OutgoingCourrierExportController::class, 'print'])->name('courriers-depart.print');
    Route::get('/courriers-depart/report', [OutgoingCourrierReportController::class, 'index'])->name('courriers-depart.report');

    Route::resource('courriers-depart', OutgoingCourrierController::class)
        ->parameters(['courriers-depart' => 'outgoing_courrier']);

    Route::get('/courriers-depart/{outgoing_courrier}/attachment', [OutgoingCourrierAttachmentController::class, 'show'])
        ->name('courriers-depart.attachments.show');

    Route::post('/courriers-depart/{outgoing_courrier}/prepare', [OutgoingCourrierController::class, 'prepare'])
        ->name('courriers-depart.prepare');
    Route::post('/courriers-depart/{outgoing_courrier}/send', [OutgoingCourrierController::class, 'send'])
        ->name('courriers-depart.send');
    Route::post('/courriers-depart/{outgoing_courrier}/archive', [OutgoingCourrierController::class, 'archive'])
        ->name('courriers-depart.archive');

    Route::post('/courriers/{courrier}/archive', [CourrierController::class, 'archive'])
        ->name('courriers.archive');

    Route::post('/courriers/{courrier}/routings', [RoutingController::class, 'store'])
        ->name('courriers.routings.store');

    Route::post('/routings/{routing}/accept', [RoutingController::class, 'accept'])
        ->name('routings.accept');

    Route::post('/routings/{routing}/reject', [RoutingController::class, 'reject'])
        ->name('routings.reject');

    Route::post('/routings/{routing}/process', [RoutingController::class, 'process'])
        ->name('routings.process');

    Route::resource('services', ServiceController::class)->except('show');
    Route::resource('sections', SectionController::class)->except('show');
    Route::resource('departments', DepartmentController::class)->except('show');

    Route::middleware('permission:manage_users')->group(function () {
        Route::resource('users', UserController::class);
    });
});
