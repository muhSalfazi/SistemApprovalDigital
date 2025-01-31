<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\DepartementController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/validate-qrcode', [QrCodeController::class, 'showqr'])->name('validate.qrcode');
Route::post('/validate-qrcode', [QrCodeController::class, 'validateQRCode']);
Route::post('/upload-qr', [QRCodeController::class, 'uploadQRCode'])->name('upload.qrcode');


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login-rfid', [AuthController::class, 'showLoginRFID'])->name('login-rfid');
// methodpost
Route::post('/login', [AuthController::class, 'login'])->name('postlogin');
Route::post('/validate-rfid', [AuthController::class, 'loginrfid'])->name('postloginrfid');

Route::middleware(['auth'])->group(function () {
    //    untuk logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{userId}', [UserController::class, 'update'])->name('users.update');
    Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check-email');
    Route::get('users/{userId}/edit', [UserController::class, 'edit']);
    Route::put('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::get('/users/{user}/roles-kategories', [UserController::class, 'getRolesAndCategories']);
    Route::post('/users/{user}/delete-role-kategori', [UserController::class, 'deleteRoleOrKategori']);


    // kategori route
    Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    Route::post('/kategori/toggle/{id}', [KategoriController::class, 'toggleStatus'])->name('kategori.toggle');

    // departement route
    Route::get('departement', [DepartementController::class, 'index'])->name('departement.index');
    Route::get('departement/create', [DepartementController::class, 'create'])->name('departement.create');
    Route::post('departement', [DepartementController::class, 'store'])->name('departement.store');
    Route::get('departement/{departement}/edit', [DepartementController::class, 'edit'])->name('departement.edit');
    Route::put('departement/{departement}', [DepartementController::class, 'update'])->name('departement.update');
    Route::delete('departement/{departement}', [DepartementController::class, 'destroy'])->name('departement.destroy');
    Route::post('/departement/toggle/{id}', [DepartementController::class, 'toggleStatus'])->name('departement.toggle');


    // submission route
    Route::get('/approv', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('submission', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
    Route::put('submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    Route::delete('submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');
    Route::get('/submissions/{id}/details', [SubmissionController::class, 'getSubmissionDetails'])->name('submissions.details');

    // download file
    Route::get('/download-qrcode/{id}', [SubmissionController::class, 'downloadWithQRCode'])->name('submissions.download');
    // generate transaction number
    Route::post('submissions/generate-transaction-number', [SubmissionController::class, 'generateTransactionNumber'])->name('generateTransactionNumber');
    // approval store
    Route::post('/submissions/approval', [ApprovalController::class, 'store'])->name('submissions.approval');

    // history approval
    Route::get('/approval/history', [ApprovalController::class, 'index'])->name('approval.history');
    Route::get('/get-approval-data/{submissionId}', [ApprovalController::class, 'getApprovalData']);
    Route::get('/modal/{submissionId}/approval-table', [ApprovalController::class, 'getApprovalTable']);

});
