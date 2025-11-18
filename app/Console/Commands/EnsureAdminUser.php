<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class EnsureAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ensure-admin {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer ou mettre à jour l’administrateur principal depuis les variables d’environnement.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $config = config('admin.default_admin');
        $email = $config['email'] ?? null;
        $password = $this->option('password') ?? ($config['password'] ?? null);

        if (blank($email) || blank($password)) {
            $this->warn('ADMIN_EMAIL et ADMIN_PASSWORD doivent être définis pour créer un administrateur par défaut.');

            return self::SUCCESS;
        }

        $attributes = [
            'name' => $config['name'] ?? 'Administrateur ProConnect',
            'address' => $config['address'] ?? 'Adresse administrateur',
            'account_type' => $config['account_type'] ?? 'pro',
            'role' => 'admin',
            'password' => Hash::make($password),
        ];

        $user = User::updateOrCreate(
            ['email' => $email],
            $attributes
        );

        $this->info("Administrateur synchronisé ({$user->email}).");

        return self::SUCCESS;
    }
}
