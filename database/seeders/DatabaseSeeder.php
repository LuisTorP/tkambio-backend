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
        User::create([
            'name' => 'Luis Toribio Palacios',
            'email' => 'luistoribiopalacios@gmail.com',
            'password' => bcrypt('password123'),
            'date_birth_init' => '1990-01-01',
            'date_birth_end' => '1990-12-31',
        ]);

        $users = [
            [
                'name' => 'María González',
                'email' => 'maria.gonzalez@example.com',
                'password' => bcrypt('password123'),
                'date_birth_init' => '1985-03-15',
                'date_birth_end' => '1987-06-20',
            ],
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'password' => bcrypt('password123'),
                'date_birth_init' => '1992-07-22',
                'date_birth_end' => '1994-09-10',
            ],
            [
                'name' => 'Ana Martínez',
                'email' => 'ana.martinez@example.com',
                'password' => bcrypt('password123'),
                'date_birth_init' => '1988-11-05',
                'date_birth_end' => '1991-02-14',
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@example.com',
                'password' => bcrypt('password123'),
                'date_birth_init' => '1995-04-18',
                'date_birth_end' => '1997-08-25',
            ],
            [
                'name' => 'Laura Sánchez',
                'email' => 'laura.sanchez@example.com',
                'password' => bcrypt('password123'),
                'date_birth_init' => '1991-09-30',
                'date_birth_end' => '1993-12-15',
            ],
            [
                'name' => 'Roberto López',
                'email' => 'roberto.lopez@example.com',
                'password' => bcrypt('password123'),
                'date_birth_init' => '1987-12-08',
                'date_birth_end' => '1990-03-22',
            ],
            [
                'name' => 'Carmen Fernández',
                'email' => 'carmen.fernandez@example.com',
                'password' => bcrypt('password123'),
                'date_birth_init' => '1993-06-12',
                'date_birth_end' => '1995-10-05',
            ],
            [
                'name' => 'Diego Ramírez',
                'email' => 'diego.ramirez@example.com',
                'password' => bcrypt('password123'),
                'date_birth_init' => '1989-02-25',
                'date_birth_end' => '1992-05-18',
            ],
            [
                'name' => 'Sofía Torres',
                'email' => 'sofia.torres@example.com',
                'password' => bcrypt('password123'),
                'date_birth_init' => '1994-08-14',
                'date_birth_end' => '1996-11-30',
            ],
            [
                'name' => 'Miguel Díaz',
                'email' => 'miguel.diaz@example.com',
                'password' => bcrypt('password123'),
                'date_birth_init' => '1990-05-20',
                'date_birth_end' => '1992-08-12',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        User::factory(5)->create();
    }
}
