<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\NotulenController;
use App\Http\Controllers\ProktorController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes (Guest Only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect('/login'));

    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);

    Route::get('/register', [RegisterController::class, 'index']);
    Route::post('/register', [RegisterController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user,admin,proktor'])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
    Route::get('/dashboard/data', [UserController::class, 'getBookings'])->name('calendar.data');

    // Booking Routes
    Route::get('/data-rapat', [BookingController::class, 'data'])->name('user.data');
    Route::get('/buat-rapat', [BookingController::class, 'create']);
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::post('/buat-rapat', [BookingController::class, 'storeMeeting']);
    Route::get('/edit-rapat/{no_transaksi}', [BookingController::class, 'editMeeting']);
    Route::post('/update-rapat/{no_transaksi}', [BookingController::class, 'updateMeeting']);
    Route::post('/cancel-rapat/{no_transaksi}', [BookingController::class, 'cancelMeeting'])->name('user.cancel');
    Route::delete('/hapus-rapat/{no_transaksi}', [BookingController::class, 'destroy'])->name('user.destroy');

    // Notulen Routes
    Route::get('/notulen', [NotulenController::class, 'notulen'])->name('notulen.index');
    Route::get('/notulen/{no_transaksi}', [NotulenController::class, 'showNotulen'])
        ->name('notulen.show');
    Route::get('/notulen/{no_transaksi}/create', [NotulenController::class, 'createNotulen'])
        ->name('notulen.create');
    Route::post('/notulen/{no_transaksi}', [NotulenController::class, 'storeNotulen'])
        ->name('notulen.store');
    Route::get('/notulen/{no_transaksi}/edit', [NotulenController::class, 'editNotulen'])
        ->name('notulen.edit');
    Route::put('/notulen/{no_transaksi}', [NotulenController::class, 'updateNotulen'])
        ->name('notulen.update');
    Route::get('/notulen/{no_transaksi}/download', [NotulenController::class, 'downloadPdf'])
        ->name('notulen.download');
    Route::get('/notulen/{no_transaksi}/pdf', [NotulenController::class, 'previewPdf'])
        ->name('notulen.pdf');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/booking/approve/{no_transaksi}', [AdminController::class, 'approveBooking'])->name('admin.approveBooking');
    Route::post('/admin/booking/reject/{no_transaksi}', [AdminController::class, 'rejectBooking'])->name('admin.rejectBooking');

    Route::get('/admin/arsip-rapat', [AdminController::class, 'arsipNotulen'])->name('admin.arsip');
    Route::post('/admin/notulen/approve/{id_notulen}', [AdminController::class, 'approveNotulen'])->name('admin.approveNotulen');
    Route::post('/admin/notulen/reject/{id_notulen}', [AdminController::class, 'rejectNotulen'])->name('admin.rejectNotulen');
});

 /*
|--------------------------------------------------------------------------
| Approver Routes
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth', 'role:sekretaris'])->group(function () {
//     Route::get('/approver', [ApproverController::class, 'index'])->name('approver.dashboard');
//     Route::post('/approver/{id}/approve', [ApproverController::class, 'approve'])->name('approver.approve');
//     Route::post('/approver/{id}/reject', [ApproverController::class, 'reject'])->name('approver.reject');
// });


/*
|--------------------------------------------------------------------------
| Proktor Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:proktor'])->group(function () {
    Route::get('/proktor', [ProktorController::class, 'index'])->name('proktor.dashboard');
    Route::get('/proktor/filter-room', [ProktorController::class, 'filterRoom'])->name('proktor.filterRoom');
    Route::post('/proktor/{id}/approve', [ProktorController::class, 'approve'])->name('proktor.approve');
    Route::post('/proktor/{id}/reject', [ProktorController::class, 'reject'])->name('proktor.reject');
    Route::post('/proktor/toggle-facility', [ProktorController::class, 'toggleFacility'])->name('proktor.toggleFacility');
    Route::post('/proktor/toggle-room-status', [ProktorController::class, 'toggleRoomStatus'])->name('proktor.toggleRoomStatus');
    Route::post('/proktor/update-facilities', [ProktorController::class, 'updateFacilities']);
    Route::post('/proktor/update-rooms', [ProktorController::class, 'updateRooms']);
    Route::post('/proktor/add-facility', [ProktorController::class, 'addFacility'])->name('proktor.addFacility');
});

