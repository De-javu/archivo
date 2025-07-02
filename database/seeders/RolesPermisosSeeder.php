<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {  
        
        $admin = Role::firstOrCreate(['name' => 'admin']);// Crea el rol 'admin' si no existe       
        $user = Role::firstOrCreate(['name' => 'user']);  // Crea el rol 'user' si no existe
       
        $crear = Permission::firstOrCreate(['name' => 'crear carpetas']);  // Crea el permiso 'crear carpetas' si no existe
        $eliminar = Permission::firstOrCreate(['name' => 'eliminar carpeta']);// Crea el permiso 'eliminar carpetas' si no existe
        
        $admin->givePermissionTo([$crear, $eliminar]);// Asigna ambos permisos al rol 'admin'
        $user->givePermissionTo($crear); // Asigna solo el permiso de crear carpetas al rol 'user'

         // Asigna el rol admin solo al usuario "andres"
        $andres = \App\Models\User::where('email', 'andrespardo5151@gmail.com')->first();
            if ($andres) {
                          $andres->assignRole('admin');
                         }

    }
}
