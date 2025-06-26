<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $resources = ['berita', 'toko', 'produk', 'riview'];

        $actions = ['viewany', 'view', 'create', 'update', 'delete'];

        foreach($resources as $resource){
            foreach($actions as $action){
                Permission::create(['name' => $action . '-' . $resource]);
            }
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());
        
        $customerRole = Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'web']);

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

        $customerPermission = array_merge($customerReadPermissions, $customerReviewCrudPermissions);
        $customerRole->syncPermissions($customerPermission);
    }
}
