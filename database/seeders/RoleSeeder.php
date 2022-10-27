<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'Admin', 'Supervisor', 'Student'
        ];

        foreach ($types as $key => $type) {
            Role::create(['name' => $type]);
        }
    }
}
