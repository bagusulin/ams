<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',

            'asset-categories.view',
            'asset-categories.create',
            'asset-categories.update',
            'asset-categories.delete',

            'locations.view',
            'locations.create',
            'locations.update',
            'locations.delete',

            'vendors.view',
            'vendors.create',
            'vendors.update',
            'vendors.delete',

            'assets.view',
            'assets.create',
            'assets.update',
            'assets.delete',
            'assets.import',

            'assignments.view',
            'assignments.create',
            'returns.view',
            'returns.create',
            'asset-history.view',

            'maintenances.view',
            'maintenances.create',
            'maintenances.update',
            'maintenances.delete',

            'reports.assets.view',
            'reports.assets.export',
            'reports.assignments.view',
            'reports.assignments.export',
            'reports.maintenances.view',
            'reports.maintenances.export',
            'reports.rental-expiry.view',
            'reports.rental-expiry.export',

            'asset-qr.view',
            'asset-qr.print',

            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.manage',
            'audit-logs.view',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $it = Role::firstOrCreate([
            'name' => 'it',
            'guard_name' => 'web',
        ]);

        $viewer = Role::firstOrCreate([
            'name' => 'viewer',
            'guard_name' => 'web',
        ]);

        // Admin mendapatkan seluruh permission yang tersedia.
        $admin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        // IT mengelola operasional aset, tetapi tidak mengatur role.
        $itPermissions = [
            'dashboard.view',

            'asset-categories.view',
            'asset-categories.create',
            'asset-categories.update',
            'asset-categories.delete',

            'locations.view',
            'locations.create',
            'locations.update',
            'locations.delete',

            'vendors.view',
            'vendors.create',
            'vendors.update',
            'vendors.delete',

            'assets.view',
            'assets.create',
            'assets.update',
            'assets.delete',
            'assets.import',

            'assignments.view',
            'assignments.create',
            'returns.view',
            'returns.create',
            'asset-history.view',

            'maintenances.view',
            'maintenances.create',
            'maintenances.update',
            'maintenances.delete',

            'reports.assets.view',
            'reports.assets.export',
            'reports.assignments.view',
            'reports.assignments.export',
            'reports.maintenances.view',
            'reports.maintenances.export',
            'reports.rental-expiry.view',
            'reports.rental-expiry.export',

            'asset-qr.view',
            'asset-qr.print',
            'users.view',
        ];

        $it->syncPermissions($itPermissions);

        // Role PPI
        $ppi = Role::firstOrCreate([
            'name' => 'ppi',
            'guard_name' => 'web',
        ]);

        // Hak akses operasional PPI mengikuti IT,
        // kecuali akses melihat daftar user.
        $ppiPermissions = array_values(
            array_diff(
                $itPermissions,
                ['users.view', 'assets.import']
            )
        );

        $ppi->syncPermissions($ppiPermissions);

        // Viewer hanya memiliki hak baca, tanpa ekspor dan perubahan data.
        $viewerPermissions = [
            'dashboard.view',

            'asset-categories.view',
            'locations.view',
            'vendors.view',
            'assets.view',

            'assignments.view',
            'returns.view',
            'asset-history.view',
            'maintenances.view',

            'reports.assets.view',
            'reports.assignments.view',
            'reports.maintenances.view',
            'reports.rental-expiry.view',

            'asset-qr.view',
        ];

        $viewer->syncPermissions($viewerPermissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
