<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions grouped by module
        $permissions = [
            // Sessions / Programme
            'sessions.view', 'sessions.create', 'sessions.update', 'sessions.delete',
            'sessions.start', 'sessions.end',

            // Rapporteur notes
            'notes.create', 'notes.view', 'notes.update',

            // AI insights
            'insights.view', 'insights.validate', 'insights.publish',

            // Resolutions
            'resolutions.view', 'resolutions.create', 'resolutions.publish',

            // Deals
            'deals.view', 'deals.create', 'deals.update', 'deals.delete',

            // Speakers
            'speakers.view', 'speakers.manage',

            // Feedback
            'feedback.view', 'feedback.submit',

            // Social
            'social.view', 'social.manage',

            // Incidents (security)
            'incidents.view', 'incidents.report', 'incidents.acknowledge',
            'incidents.respond', 'incidents.resolve',

            // Command centre
            'command.view', 'command.broadcast',

            // Reports
            'reports.view', 'reports.export',

            // Users / RBAC
            'users.view', 'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        $analyst = Role::firstOrCreate(['name' => 'analyst', 'guard_name' => 'web']);
        $analyst->syncPermissions([
            'sessions.view', 'notes.view', 'insights.view', 'insights.validate', 'insights.publish',
            'resolutions.view', 'resolutions.publish', 'deals.view', 'deals.update',
            'feedback.view', 'social.view', 'reports.view', 'reports.export',
        ]);

        $rapporteur = Role::firstOrCreate(['name' => 'rapporteur', 'guard_name' => 'web']);
        $rapporteur->syncPermissions([
            'sessions.view', 'notes.create', 'notes.view', 'notes.update',
            'speakers.view',
        ]);

        $security = Role::firstOrCreate(['name' => 'security', 'guard_name' => 'web']);
        $security->syncPermissions([
            'incidents.view', 'incidents.acknowledge', 'incidents.respond', 'incidents.resolve',
            'command.view',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions([
            'sessions.view', 'insights.view', 'resolutions.view', 'deals.view',
            'feedback.view', 'social.view', 'reports.view',
        ]);
    }
}
