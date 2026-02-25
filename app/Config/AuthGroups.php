<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Kontrol penuh terhadap seluruh sistem.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Administrator harian sistem.',
        ],
        'manager' => [
            'title'       => 'Manager',
            'description' => 'Manajer yang dapat melihat laporan dan mengelola data.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'Pengguna umum dengan akses terbatas.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     */
    public array $permissions = [
        // Admin area
        'admin.access'        => 'Dapat mengakses area admin',
        'admin.settings'      => 'Dapat mengakses pengaturan sistem',

        // User management
        'users.list'          => 'Dapat melihat daftar pengguna',
        'users.create'        => 'Dapat membuat pengguna baru',
        'users.edit'          => 'Dapat mengedit pengguna',
        'users.delete'        => 'Dapat menghapus pengguna',
        'users.manage-roles'  => 'Dapat mengatur role pengguna',

        // Role management
        'roles.list'          => 'Dapat melihat daftar role',
        'roles.create'        => 'Dapat membuat role baru',
        'roles.edit'          => 'Dapat mengedit role',
        'roles.delete'        => 'Dapat menghapus role',

        // Dashboard
        'dashboard.access'    => 'Dapat mengakses dashboard',
        'dashboard.stats'     => 'Dapat melihat statistik',

        // Reports
        'reports.view'        => 'Dapat melihat laporan',
        'reports.export'      => 'Dapat mengekspor laporan',

        // Master Aplikasi
        'apps.list'           => 'Dapat melihat daftar aplikasi',
        'apps.create'         => 'Dapat membuat aplikasi baru',
        'apps.edit'           => 'Dapat mengedit aplikasi',
        'apps.delete'         => 'Dapat menghapus aplikasi',

        // Master Kategori
        'categories.list'     => 'Dapat melihat daftar kategori',
        'categories.create'   => 'Dapat membuat kategori baru',
        'categories.edit'     => 'Dapat mengedit kategori',
        'categories.delete'   => 'Dapat menghapus kategori',

        // Log Maintenance
        'logs.list'           => 'Dapat melihat daftar log maintenance',
        'logs.create'         => 'Dapat membuat log maintenance baru',
        'logs.edit'           => 'Dapat mengedit log maintenance',
        'logs.delete'         => 'Dapat menghapus log maintenance',
        'logs.review'         => 'Dapat mereview dan menutup log maintenance',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*',
            'roles.*',
            'dashboard.*',
            'reports.*',
            'apps.*',
            'categories.*',
            'logs.*',
        ],
        'admin' => [
            'admin.access',
            'users.list',
            'users.create',
            'users.edit',
            'users.delete',
            'dashboard.*',
            'reports.*',
            'apps.*',
            'categories.*',
            'logs.*',
        ],
        'manager' => [
            'admin.access',
            'users.list',
            'dashboard.*',
            'reports.*',
            'apps.list',
            'categories.list',
            'logs.list',
            'logs.review',
        ],
        'user' => [
            'dashboard.access',
            'logs.list',
            'logs.create',
            'logs.edit',
        ],
    ];
}
