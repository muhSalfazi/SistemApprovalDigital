<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ApprovalController;
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

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('postlogin');

Route::middleware(['auth'])->group(function () {
    //    untuk logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Route::resource('users', UserController::class);

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{userId}', [UserController::class, 'update'])->name('users.update');
    Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check-email');



    // kategori route
    Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    // submission route
    Route::get('/approv', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('submission', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
    Route::put('submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    Route::delete('submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');
    Route::get('/submissions/{id}/details', [SubmissionController::class, 'getSubmissionDetails'])->name('submissions.details');

    // download file
    Route::get('submissions/download/{id}', [SubmissionController::class, 'download'])->name('submissions.download');
    // generate transaction number
    Route::get('submissions/generate-transaction-number', [SubmissionController::class, 'generateTransactionNumber'])->name('submissions.generateTransactionNumber');
    // approval store
    Route::post('/submissions/approval', [ApprovalController::class, 'store'])->name('submissions.approval');

    // history approval
    Route::get('/approval/history', [ApprovalController::class, 'index'])->name('approval.history');
    // history perid
    Route::get('/approval/history/{id_submission}', [ApprovalController::class, 'history'])->name('approval.history.id');

    Route::get('/get-approval-data/{submissionId}', [ApprovalController::class, 'getApprovalData']);

    Route::get('/modal/{submissionId}/approval-table', [ApprovalController::class, 'getApprovalTable']);


});
