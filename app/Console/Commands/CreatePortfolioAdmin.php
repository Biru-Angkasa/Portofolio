<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('portfolio:admin')]
#[Description('Buat akun admin portofolio tanpa menyimpan kata sandi di repo')]
class CreatePortfolioAdmin extends Command
{
    public function handle(): int
    {
        $name = trim((string) $this->ask('Nama'));
        $email = trim((string) $this->ask('Email'));
        $password = (string) $this->secret('Kata sandi');
        $confirmation = (string) $this->secret('Ulangi kata sandi');

        if ($name === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error('Nama dan email yang valid wajib diisi.');

            return self::FAILURE;
        }

        if (strlen($password) < 12 || $password !== $confirmation) {
            $this->error('Kata sandi minimal 12 karakter dan harus sama saat diulang.');

            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->error('Email itu sudah dipakai.');

            return self::FAILURE;
        }

        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->info('Akun admin dibuat. Masuk lewat /login.');

        return self::SUCCESS;
    }
}
