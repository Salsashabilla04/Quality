<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Staff QA (Operasional) & Supervisor QC (Approval / Monitoring)
        User::updateOrCreate(
            ['email' => 'qa@wbn.com'],
            [
                'name'     => 'Staff QA',
                'password' => Hash::make('password'),
                'role'     => 'qa',
            ]
        );

        User::updateOrCreate(
            ['email' => 'spv@wbn.com'],
            [
                'name'     => 'Supervisor QC',
                'password' => Hash::make('password'),
                'role'     => 'supervisor',
            ]
        );

        $this->command->info('Berhasil membuat akun Staff QA (qa@wbn.com) dan Supervisor QC (spv@wbn.com) - Password: password');
    }
}
