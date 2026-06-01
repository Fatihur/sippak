<?php

use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\LogWhatsAppController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\WhatsAppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\PublicAiChatController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'beranda'])->name('beranda');
Route::get('/tentang', [PublicPageController::class, 'tentang'])->name('tentang');
Route::get('/faq', [PublicPageController::class, 'faq'])->name('faq');
Route::get('/statistik', [PublicPageController::class, 'statistik'])->name('statistik');
Route::get('/edukasi', [PublicPageController::class, 'edukasi'])->name('edukasi');
Route::post('/ai/chat', PublicAiChatController::class)->name('ai.chat');
Route::get('/pengaduan', [PengaduanController::class, 'buat'])->name('pengaduan.buat');
Route::post('/pengaduan', [PengaduanController::class, 'simpan'])->name('pengaduan.simpan');
Route::get('/pengaduan/{pengaduan}/otp', [PengaduanController::class, 'tampilOtp'])->name('pengaduan.otp');
Route::post('/pengaduan/{pengaduan}/otp', [PengaduanController::class, 'verifikasiOtp'])->name('pengaduan.verifikasi-otp');
Route::get('/pengaduan/{pengaduan}/sukses', [PengaduanController::class, 'sukses'])->name('pengaduan.sukses');
Route::get('/tracking', [PengaduanController::class, 'formTracking'])->name('tracking.form');
Route::post('/tracking', [PengaduanController::class, 'tracking'])->name('tracking.hasil');
Route::get('/tracking/{nomor_tiket}', [PengaduanController::class, 'trackingPublik'])->name('tracking.publik');

Route::middleware('auth')->get('/admin', [AuthController::class, 'redirectSetelahLogin'])->name('admin.home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'tampilLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
    Route::get('/lupa-password', [AuthController::class, 'tampilLupaPassword'])->name('password.request');
    Route::post('/lupa-password', [AuthController::class, 'kirimResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'tampilResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:operator,kepala_bidang,kepala_dinas'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/{laporan}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::get('/laporan/{laporan}/edit', [LaporanController::class, 'edit'])->middleware('role:operator')->name('laporan.edit');
    Route::put('/laporan/{laporan}', [LaporanController::class, 'update'])->middleware('role:operator')->name('laporan.update');
    Route::delete('/laporan/{laporan}', [LaporanController::class, 'destroy'])->middleware('role:operator')->name('laporan.destroy');
    Route::patch('/laporan/{laporan}/status', [LaporanController::class, 'updateStatus'])->middleware('role:operator')->name('laporan.status');
    Route::post('/laporan/{laporan}/asesmen', [LaporanController::class, 'simpanAsesmen'])->middleware('role:operator')->name('laporan.asesmen');
    Route::post('/laporan/{laporan}/panggil-kantor', [LaporanController::class, 'panggilKeKantor'])->middleware('role:operator')->name('laporan.panggil-kantor');
    Route::get('/bukti/{id}', [LaporanController::class, 'unduhBukti'])->name('bukti.unduh');
    Route::get('/bukti/{id}/preview', [LaporanController::class, 'previewBukti'])->name('bukti.preview');
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/export-csv', [RekapController::class, 'exportCsv'])->middleware('role:operator')->name('rekap.export-csv');
    Route::get('/rekap/export-pdf', [RekapController::class, 'exportPdf'])->name('rekap.export-pdf');
    Route::get('/backup/sqlite', [BackupController::class, 'exportSqlite'])->middleware('role:operator')->name('backup.sqlite');
    Route::get('/whatsapp', [WhatsAppController::class, 'index'])->middleware('role:operator')->name('whatsapp.index');
    Route::post('/whatsapp', [WhatsAppController::class, 'simpan'])->middleware('role:operator')->name('whatsapp.simpan');
    Route::post('/whatsapp/test', [WhatsAppController::class, 'testKirim'])->middleware('role:operator')->name('whatsapp.test');
    Route::post('/whatsapp/restart', [WhatsAppController::class, 'restart'])->middleware('role:operator')->name('whatsapp.restart');
    Route::post('/whatsapp/logout', [WhatsAppController::class, 'logout'])->middleware('role:operator')->name('whatsapp.logout');
    Route::post('/whatsapp/log/{log}/kirim-ulang', [LogWhatsAppController::class, 'kirimUlang'])->middleware('role:operator')->name('whatsapp.log.kirim-ulang');
    Route::resource('pengguna', PenggunaController::class)->except('show')->middleware('role:operator');
});
