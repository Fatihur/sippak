<?php

namespace App\Providers;

use App\Models\Pengaduan;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.tailadmin-header', function ($view): void {
            $view->with('notifikasiAdmin', [
                'baru' => Pengaduan::whereNotNull('nomor_tiket')->where('status', 'menunggu_verifikasi')->latest()->limit(5)->get(),
                'jumlah' => Pengaduan::whereNotNull('nomor_tiket')->where('status', 'menunggu_verifikasi')->count(),
            ]);
        });
    }
}
