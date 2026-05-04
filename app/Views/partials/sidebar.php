<?php
$currentUser = auth()->user();
$currentUrl  = uri_string();

/**
 * Helper untuk cek apakah menu aktif
 */
function isMenuActive(string $path): string {
    $currentUrl = uri_string();
    return (strpos($currentUrl, $path) !== false) ? 'active' : '';
}

function isDropdownActive(array $paths): string {
    $currentUrl = uri_string();
    foreach ($paths as $path) {
        if (strpos($currentUrl, $path) !== false) {
            return 'active';
        }
    }
    return '';
}
?>
<div class="main-sidebar sidebar-style-1">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="<?= base_url('dashboard') ?>">CI4 RBAC</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="<?= base_url('dashboard') ?>">C4</a>
    </div>
    <ul class="sidebar-menu">

      <!-- Dashboard -->
      <li class="menu-header">Dashboard</li>
      <li class="<?= isMenuActive('dashboard') && !str_contains($currentUrl, 'admin') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('dashboard') ?>"><i class="fas fa-fire"></i> <span>Dashboard</span></a>
      </li>

      <!-- Admin Menu (hanya untuk yang punya akses admin) -->
      <?php if ($currentUser->can('admin.access')): ?>
      <li class="menu-header">Administrasi</li>

      <!-- User Management -->
      <?php if ($currentUser->can('users.list')): ?>
      <li class="<?= isMenuActive('admin/users') ?>">
        <a class="nav-link" href="<?= base_url('admin/users') ?>"><i class="fas fa-users"></i> <span>Manajemen User</span></a>
      </li>
      <?php endif; ?>

      <!-- Role Management (superadmin only) -->
      <?php if ($currentUser->inGroup('superadmin')): ?>
      <li class="nav-item dropdown <?= isDropdownActive(['admin/roles']) ?>">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-shield"></i> <span>Role & Permission</span></a>
        <ul class="dropdown-menu">
          <li class="<?= isMenuActive('admin/roles') && !str_contains($currentUrl, 'permissions') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('admin/roles') ?>">Daftar Role</a>
          </li>
          <li class="<?= isMenuActive('admin/roles/permissions') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('admin/roles/permissions') ?>">Permission Matrix</a>
          </li>
        </ul>
      </li>
      <?php endif; ?>

      <!-- Settings -->
      <?php if ($currentUser->can('admin.settings')): ?>
      <li class="<?= isMenuActive('admin/settings') ?>">
        <a class="nav-link" href="<?= base_url('admin/settings') ?>"><i class="fas fa-cog"></i> <span>Pengaturan</span></a>
      </li>
      <?php endif; ?>
      <?php endif; ?>

      <!-- Logbook Maintenance -->
      <?php if ($currentUser->can('logs.list') || $currentUser->can('apps.list') || $currentUser->can('categories.list')): ?>
      <li class="menu-header">Logbook Maintenance</li>

      <!-- Data Master -->
      <?php if ($currentUser->can('apps.list') || $currentUser->can('categories.list')): ?>
      <li class="nav-item dropdown <?= isDropdownActive(['admin/apps', 'admin/categories']) ?>">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-database"></i> <span>Data Master</span></a>
        <ul class="dropdown-menu">
          <?php if ($currentUser->can('apps.list')): ?>
          <li class="<?= isMenuActive('admin/apps') ?>">
            <a class="nav-link" href="<?= base_url('admin/apps') ?>">Master Aplikasi</a>
          </li>
          <?php endif; ?>
          <?php if ($currentUser->can('categories.list')): ?>
          <li class="<?= isMenuActive('admin/categories') ?>">
            <a class="nav-link" href="<?= base_url('admin/categories') ?>">Master Kategori</a>
          </li>
          <?php endif; ?>
        </ul>
      </li>
      <?php endif; ?>

      <!-- Ticketing -->
      <?php if ($currentUser->can('logs.list')): ?>
      <li class="<?= isMenuActive('maintenance-logs') ?>">
        <a class="nav-link" href="<?= base_url('maintenance-logs') ?>"><i class="fas fa-clipboard-list"></i> <span>Ticketing</span></a>
      </li>
      <?php endif; ?>
      <?php endif; ?>

      <!-- Profil -->
      <li class="menu-header">Akun</li>
      <li class="<?= isMenuActive('profile') ?>">
        <a class="nav-link" href="<?= base_url('profile') ?>"><i class="far fa-user"></i> <span>Profil Saya</span></a>
      </li>
      <li>
        <a class="nav-link text-danger" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
      </li>

    </ul>
  </aside>
</div>
