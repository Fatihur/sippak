<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'operator@sippak.test'],
            ['name' => 'Operator PPA', 'password' => 'password', 'role' => 'operator', 'aktif' => true]
        );

        User::updateOrCreate(
            ['email' => 'kabid@sippak.test'],
            ['name' => 'Kepala Bidang PPA', 'password' => 'password', 'role' => 'kepala_bidang', 'aktif' => true]
        );

        User::updateOrCreate(
            ['email' => 'kadis@sippak.test'],
            ['name' => 'Kepala Dinas', 'password' => 'password', 'role' => 'kepala_dinas', 'aktif' => true]
        );
    }
}
