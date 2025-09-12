 <?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario super admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@marketclub.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'is_active' => true,
                'phone' => '+573001234567',
                'country' => 'Colombia',
            ]
        );

        // Crear usuario admin adicional
        $adminUser2 = User::firstOrCreate(
            ['email' => 'admin2@marketclub.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'phone' => '+573001234568',
                'country' => 'Colombia',
            ]
        );

        // Crear algunos usuarios de prueba (clientes)
        $customers = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan@example.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'is_active' => true,
                'phone' => '+573001234569',
                'country' => 'Colombia',
            ],
            [
                'name' => 'María García',
                'email' => 'maria@example.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'is_active' => true,
                'phone' => '+573001234570',
                'country' => 'Colombia',
            ],
            [
                'name' => 'Carlos López',
                'email' => 'carlos@example.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'is_active' => true,
                'phone' => '+573001234571',
                'country' => 'Colombia',
            ],
        ];

        foreach ($customers as $customerData) {
            User::firstOrCreate(
                ['email' => $customerData['email']],
                $customerData
            );
        }

        $this->command->info('Usuarios creados exitosamente:');
        $this->command->info('- Super Admin: admin@marketclub.com (admin123)');
        $this->command->info('- Admin: admin2@marketclub.com (admin123)');
        $this->command->info('- Clientes de prueba: juan@example.com, maria@example.com, carlos@example.com (password123)');
    }
}