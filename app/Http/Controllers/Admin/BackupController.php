<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LogAktivitasService;
use Illuminate\Support\Facades\Process;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function __construct(
        private readonly LogAktivitasService $logAktivitasService,
    ) {}

    public function exportSqlite(): StreamedResponse
    {
        $connection = config('database.default');

        if ($connection === 'sqlite') {
            $databasePath = database_path('database.sqlite');
            abort_unless(file_exists($databasePath), 404, 'File database tidak ditemukan.');

            $this->logAktivitasService->catat('backup_database', 'Backup database SQLite diunduh.');

            return response()->streamDownload(function () use ($databasePath): void {
                echo file_get_contents($databasePath);
            }, 'backup-sippak-'.now()->format('Ymd-His').'.sqlite');
        }

        if ($connection === 'mysql') {
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s --routines --triggers --single-transaction %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
            );

            $result = Process::run($command);
            abort_unless($result->successful(), 500, 'Gagal membackup database: '.$result->errorOutput());

            $this->logAktivitasService->catat('backup_database', 'Backup database MySQL diunduh.');

            return response()->streamDownload(function () use ($result): void {
                echo $result->output();
            }, 'backup-sippak-'.now()->format('Ymd-His').'.sql');
        }

        abort(422, 'Driver database tidak didukung untuk backup.');
    }
}
