<?php

use App\Http\Controllers\Auth\SuperAdminRegistrationController;
use App\Http\Controllers\HomeRedirectController;
use App\Http\Controllers\PaymentController;
use App\Livewire\Admin\Addresses\Index as AddressesIndex;
use App\Livewire\Admin\AddressIndexing\Index as AddressIndexingIndex;
use App\Livewire\Admin\Approvals\Index as ApprovalsIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Fees\Index as FeesIndex;
use App\Livewire\Admin\FieldReports\Index as FieldReportsIndex;
use App\Livewire\Admin\Payments\Index as PaymentsIndex;
use App\Livewire\Admin\Permissions\Index as PermissionsIndex;
use App\Livewire\Admin\Reports\Index as ReportsIndex;
use App\Livewire\Admin\Roles\Index as RolesIndex;
use App\Livewire\Admin\Settings\Index as SettingsIndex;
use App\Livewire\Admin\Streets\Index as StreetsIndex;
use App\Livewire\Admin\StreetApplications\Index as StreetApplicationsIndex;
use App\Livewire\Admin\StreetNumberingPlates\Index as StreetNumberingPlatesIndex;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Livewire\Admin\TownMap\Index as TownMapIndex;
use App\Livewire\Portal\Dashboard as PortalDashboard;
use App\Livewire\Portal\RegisterStreet;
use App\Livewire\Portal\RegisterAddressIndexing;
use App\Livewire\Portal\StreetRevalidationForm;
use App\Livewire\Portal\RequestNumberingPlates;
use App\Livewire\Portal\Verification;
use App\Livewire\Portal\QrScanner;
use App\Livewire\Portal\StreetDirectory;
use App\Livewire\Portal\FeeSchedule;
use App\Livewire\Portal\InteractiveMap;
use App\Livewire\Portal\AiLookup;
use App\Livewire\Portal\RegisterAddress;
use App\Livewire\Portal\Complaints;
use App\Http\Controllers\Portal\CertificateController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::view('/', 'welcome')->name('home');

// Super Admin Registration Routes
Route::get('/auth/register-super-admin', [SuperAdminRegistrationController::class, 'show'])->name('auth.register-super-admin');
Route::post('/auth/register-super-admin', [SuperAdminRegistrationController::class, 'store'])->name('auth.register-super-admin.store');

// Storage Link Route
Route::get('/storage/{path}', function ($path) {
    return Storage::disk('public')->download($path);
})->where('path', '.*')->name('storage');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', HomeRedirectController::class)->name('dashboard');
});

// Citizen / Field Officer Portal Routes
Route::middleware(['auth', 'verified'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', PortalDashboard::class)->name('dashboard');
    Route::get('/register-street', RegisterStreet::class)->name('register-street');
    Route::get('/register-address', RegisterAddress::class)->name('register-address');
    Route::get('/register-address-indexing', RegisterAddressIndexing::class)->name('register-address-indexing');
    Route::get('/street-revalidation', StreetRevalidationForm::class)->name('street-revalidation');
    Route::get('/request-numbering-plates', RequestNumberingPlates::class)->name('request-numbering-plates');
    Route::get('/verification', Verification::class)->name('verification');
    Route::get('/qr-scanner', QrScanner::class)->name('qr-scanner');
    Route::get('/street-directory', StreetDirectory::class)->name('street-directory');
    Route::get('/fee-schedule', FeeSchedule::class)->name('fee-schedule');
    Route::get('/map', InteractiveMap::class)->name('map');
    Route::get('/ai-lookup', AiLookup::class)->name('ai-lookup');
    Route::get('/complaints', Complaints::class)->name('complaints');
    Route::get('/notifications', \App\Livewire\Portal\Notifications\Index::class)->name('notifications');

    // Certificates
    Route::get('/certificates/street/{id}', [CertificateController::class, 'streetCertificate'])->name('certificates.street');
    Route::get('/certificates/address/{id}', [CertificateController::class, 'addressCertificate'])->name('certificates.address');

    // Field Officer Data Collection Forms
    Route::middleware(['role:field-officer'])->group(function () {
        Route::get('/field-agent-forms', \App\Livewire\Portal\FieldAgentForms::class)->name('field-agent-forms');
    });
});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard — all authenticated admins
    Route::get('/', Dashboard::class)->name('dashboard');

    // Address & Street management
    Route::middleware('can:view addresses')->group(function () {
        Route::get('/addresses', AddressesIndex::class)->name('addresses.index');
        Route::get('/address-indexing', AddressIndexingIndex::class)->name('address-indexing.index');
        Route::get('/streets', StreetsIndex::class)->name('streets.index');
    });

    // Approvals
    Route::middleware('can:view approvals')->group(function () {
        Route::get('/approvals', ApprovalsIndex::class)->name('approvals.index');
    });

    // Staff Functions (Street Applications & Field Reports)
    Route::middleware('can:view approvals')->group(function () {
        Route::get('/street-applications', StreetApplicationsIndex::class)->name('street-applications.index');
        Route::get('/production-workflow', \App\Livewire\Admin\StreetApplications\ProductionWorkflow::class)->name('street-applications.production-workflow');
        Route::get('/field-reports', FieldReportsIndex::class)->name('field-reports.index');
        Route::get('/street-numbering-plates', StreetNumberingPlatesIndex::class)->name('street-numbering-plates.index');
    });

    // Payments
    Route::middleware('can:view payments')->group(function () {
        Route::get('/payments', PaymentsIndex::class)->name('payments.index');
    });

    // Reports
    Route::middleware('can:view reports')->group(function () {
        Route::get('/reports', ReportsIndex::class)->name('reports.index');
        Route::get('/map', TownMapIndex::class)->name('map.index');
    });

    // Fee Management (View for all with permission, or super-admin)
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/fee-schedules', FeesIndex::class)->name('fee-schedules.index');
    });

    // Super-admin only
    Route::middleware('role:super-admin')->group(function () {
        Route::get('/users', UsersIndex::class)->name('users.index');
        Route::get('/roles', RolesIndex::class)->name('roles.index');
        Route::get('/permissions', PermissionsIndex::class)->name('permissions.index');
        Route::get('/settings', SettingsIndex::class)->name('settings.index');
    });
});

// Payment Routes
Route::middleware('auth')->prefix('payment')->name('payment.')->group(function () {
    // Generic payment initialization (supports all types)
    Route::post('/initialize', [PaymentController::class, 'initializeTransaction'])->name('initialize');
    
    // Backward compatibility endpoints
    Route::post('/address/initialize', [PaymentController::class, 'initializeAddressPayment'])->name('address.initialize');
    
    // Verification & Status
    Route::post('/verify', [PaymentController::class, 'verifyTransaction'])->name('verify');
    Route::get('/callback', [PaymentController::class, 'handleCallback'])->name('callback');
    Route::get('/status/{reference}', [PaymentController::class, 'getPaymentStatus'])->name('status');
});

// Portal Payment Management Routes
Route::middleware(['auth', 'verified'])->prefix('portal/payments')->name('portal.payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'userPayments'])->name('index');
    Route::get('/{payment}', [PaymentController::class, 'showPayment'])->name('show');
    Route::get('/{payment}/retry', [PaymentController::class, 'showRetry'])->name('retry');
    Route::post('/{payment}/retry', [PaymentController::class, 'processRetry'])->name('retry.process');
    Route::post('/{payment}/upload-proof', [PaymentController::class, 'uploadProof'])->name('upload');
});

// Paystack Webhook
Route::post('/webhook/paystack', [PaymentController::class, 'webhook'])->name('webhook.paystack');

require __DIR__ . '/settings.php';
