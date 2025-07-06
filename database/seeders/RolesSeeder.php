<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar; // Import ini untuk best practice

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Selalu reset cache di awal
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Tentukan guard yang akan kita gunakan di seluruh seeder ini
        $guardName = 'api';

        $resources = ['berita', 'toko', 'produk', 'riview', 'user']; // Tambahkan 'user' untuk policy user
        $actions = ['viewany', 'view', 'create', 'update', 'delete'];

        // Loop untuk membuat permission, sekarang DENGAN guard_name
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                // Gunakan firstOrCreate agar aman dijalankan ulang
                Permission::firstOrCreate([
                    'name' => $action . '-' . $resource,
                    'guard_name' => $guardName // <-- PERBAIKAN UTAMA DI SINI
                ]);
            }
        }

        // Buat Role Admin untuk guard 'api'
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => $guardName]);
        // Sekarang aman, karena semua permission juga memiliki guard 'api'
        $adminRole->givePermissionTo(Permission::all());
        
        // Buat Role Customer untuk guard 'api'
        $customerRole = Role::firstOrCreate(['name' => 'Customer', 'guard_name' => $guardName]);

        $customerReadPermissions = [
            'viewany-berita', 'view-berita',
            'viewany-toko',   'view-toko',
            'viewany-produk', 'view-produk',
            'viewany-riview', 'view-riview',
        ];

        $customerReviewCrudPermissions = [
            'create-riview',
            'update-riview',
            'delete-riview',
        ];

        $customerPermissions = array_merge($customerReadPermissions, $customerReviewCrudPermissions);
        // syncPermissions juga akan aman karena semua permission dan role punya guard yang sama
        $customerRole->syncPermissions($customerPermissions);
    }
}
