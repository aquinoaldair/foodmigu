<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateAdminUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foodmigu:create-admin
                            {--name= : Nombre del administrador}
                            {--email= : Correo electrónico}
                            {--password= : Contraseña (omitir para ingresar de forma interactiva)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un nuevo usuario administrador';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Crear usuario administrador');
        $this->newLine();

        $name = $this->option('name') ?? $this->ask('Nombre completo');
        if (empty(trim((string) $name))) {
            $this->error('El nombre es requerido.');

            return self::FAILURE;
        }

        $email = $this->option('email') ?? $this->ask('Correo electrónico');
        if (empty(trim((string) $email))) {
            $this->error('El correo electrónico es requerido.');

            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error("Ya existe un usuario con el correo: {$email}");

            return self::FAILURE;
        }

        $password = $this->option('password') ?? $this->secret('Contraseña');
        if (strlen($password) < 8) {
            $this->error('La contraseña debe tener al menos 8 caracteres.');

            return self::FAILURE;
        }

        if (! $this->option('password')) {
            $passwordConfirm = $this->secret('Confirmar contraseña');
            if ($password !== $passwordConfirm) {
                $this->error('Las contraseñas no coinciden.');

                return self::FAILURE;
            }
        }

        $this->ensureAdminRoleExists();

        $user = User::create([
            'name' => trim($name),
            'email' => strtolower(trim($email)),
            'password' => Hash::make($password),
        ]);

        $user->assignRole('admin');

        $this->newLine();
        $this->info('Usuario administrador creado correctamente.');
        $this->table(
            ['Campo', 'Valor'],
            [
                ['ID', $user->id],
                ['Nombre', $user->name],
                ['Email', $user->email],
            ]
        );

        return self::SUCCESS;
    }

    private function ensureAdminRoleExists(): void
    {
        Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );
    }
}
