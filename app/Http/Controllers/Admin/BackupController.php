<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LogAktivitasService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function __construct(
        private readonly LogAktivitasService $logAktivitasService,
    ) {}

    public function exportSqlite(): StreamedResponse
    {
        abort_unless(config('database.default') === 'sqlite', 422, 'Backup otomatis file hanya tersedia untuk SQLite. Untuk MySQL gunakan mysqldump di server.');

        $databasePath = database_path('database.sqlite');
        abort_unless(file_exists($databasePath), 404, 'File database tidak ditemukan.');

        $this->logAktivitasService->catat('backup_database', 'Backup database SQLite diunduh.');

        return response()->streamDownload(function () use ($databasePath): void {
            echo file_get_contents($databasePath);
        }, 'backup-sippak-'.now()->format('Ymd-His').'.sqlite');
    }
}
