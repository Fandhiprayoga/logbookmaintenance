<?php
$currentUser = auth()->user();
$groups = $currentUser->getGroups();
$groupLabel = !empty($groups) ? ucfirst($groups[0]) : 'User';
?>

<h2 class="section-title">Selamat Datang, <?= esc($currentUser->username) ?>!</h2>
<p class="section-lead">Anda login sebagai <strong><?= $groupLabel ?></strong>.</p>

<!-- Statistik Sistem (Admin) -->
<div class="row">
  <?php if($currentUser->can('users.list')):?>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-primary">
        <i class="far fa-user"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Total Users</h4>
        </div>
        <div class="card-body">
          <?php
            $userModel = new \CodeIgniter\Shield\Models\UserModel();
            echo $userModel->countAllResults();
          ?>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <?php if($currentUser->can('roles.list')): ?>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-danger">
        <i class="fas fa-user-shield"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Total Roles</h4>
        </div>
        <div class="card-body">
          <?= count(config('AuthGroups')->groups) ?>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <?php if ($currentUser->can('apps.list')): ?>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-info">
        <i class="fas fa-server"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Master Aplikasi</h4>
        </div>
        <div class="card-body">
          <?= $totalApps ?>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <?php if ($currentUser->can('categories.list')): ?>
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-success">
        <i class="fas fa-tags"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Master Kategori</h4>
        </div>
        <div class="card-body">
          <?= $totalCategories ?>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- Statistik Log Maintenance -->
<?php if ($currentUser->can('logs.list')): ?>
<h2 class="section-title">
  Statistik Log Maintenance
  <?php if ($isAdmin): ?>
    <small class="text-muted">(Semua Data)</small>
  <?php else: ?>
    <small class="text-muted">(Data Saya)</small>
  <?php endif; ?>
</h2>

<div class="row">
  <div class="col-lg-3 col-md-4 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-primary">
        <i class="fas fa-clipboard-list"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Total Log</h4>
        </div>
        <div class="card-body">
          <?= $totalLogs ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-4 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-warning">
        <i class="fas fa-clock"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Pending</h4>
        </div>
        <div class="card-body">
          <?= $pendingLogs ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-4 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-info">
        <i class="fas fa-spinner"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>On Progress</h4>
        </div>
        <div class="card-body">
          <?= $progressLogs ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-4 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon" style="background-color: #6f42c1;">
        <i class="fas fa-vial"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Testing</h4>
        </div>
        <div class="card-body">
          <?= $testingLogs ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-4 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-success">
        <i class="fas fa-check-circle"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Completed</h4>
        </div>
        <div class="card-body">
          <?= $completedLogs ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-4 col-sm-6 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-danger">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Downtime</h4>
        </div>
        <div class="card-body">
          <?= $downtimeLogs ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tabel Log Terbaru -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>
          Log Maintenance Terbaru
          <?php if ($isAdmin): ?>
            <small class="text-muted">(Semua User)</small>
          <?php endif; ?>
        </h4>
        <div class="card-header-action">
          <a href="<?= base_url('maintenance-logs') ?>" class="btn btn-primary">
            Lihat Semua <i class="fas fa-chevron-right"></i>
          </a>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped mb-0">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Aplikasi</th>
                <th>Kategori</th>
                <th>Judul</th>
                <th>Status</th>
                <th>Downtime</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($recentLogs)): ?>
                <?php foreach ($recentLogs as $log): ?>
                <tr>
                  <td><?= date('d/m/Y H:i', strtotime($log['maintenance_date'])) ?></td>
                  <td><span class="badge badge-light"><?= esc($log['app_name'] ?? '-') ?></span></td>
                  <td><span class="badge badge-info"><?= esc($log['category_name'] ?? '-') ?></span></td>
                  <td>
                    <a href="<?= base_url('maintenance-logs/show/' . $log['id']) ?>"><?= esc($log['title']) ?></a>
                  </td>
                  <td>
                    <?php
                      $statusClass = match($log['status']) {
                        'Pending'     => 'badge-warning',
                        'On Progress' => 'badge-info',
                        'Testing'     => 'badge-primary',
                        'Completed'   => 'badge-success',
                        default       => 'badge-secondary',
                      };
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= esc($log['status']) ?></span>
                  </td>
                  <td>
                    <?php if ($log['has_downtime']): ?>
                      <span class="badge badge-danger"><?= $log['downtime_duration'] ? $log['downtime_duration'] . 'm' : 'Ya' ?></span>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center p-4">Belum ada log maintenance.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>



<div class="row">
  <div class="col-12 col-md-6">
    <div class="card">
      <div class="card-header">
        <h4>Informasi Akun</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <tr>
              <th>Username</th>
              <td><?= esc($currentUser->username) ?></td>
            </tr>
            <tr>
              <th>Email</th>
              <td><?= esc($currentUser->email) ?></td>
            </tr>
            <tr>
              <th>Role</th>
              <td>
                <?php foreach ($groups as $group): ?>
                  <span class="badge badge-primary"><?= ucfirst($group) ?></span>
                <?php endforeach; ?>
              </td>
            </tr>
            <tr>
              <th>Status</th>
              <td><span class="badge badge-success">Aktif</span></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6">
    <div class="card">
      <div class="card-header">
        <h4>Akses Cepat</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <?php if ($currentUser->can('users.list')): ?>
          <div class="col-6 mb-3">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-primary btn-block">
              <i class="fas fa-users"></i><br>Manajemen User
            </a>
          </div>
          <?php endif; ?>

          <?php if ($currentUser->inGroup('superadmin')): ?>
          <div class="col-6 mb-3">
            <a href="<?= base_url('admin/roles') ?>" class="btn btn-danger btn-block">
              <i class="fas fa-user-shield"></i><br>Role & Permission
            </a>
          </div>
          <?php endif; ?>

          <div class="col-6 mb-3">
            <a href="<?= base_url('profile') ?>" class="btn btn-info btn-block">
              <i class="far fa-user"></i><br>Profil Saya
            </a>
          </div>

          <?php if ($currentUser->can('logs.create')): ?>
          <div class="col-6 mb-3">
            <a href="<?= base_url('maintenance-logs/create') ?>" class="btn btn-success btn-block">
              <i class="fas fa-plus-circle"></i><br>Tambah Log
            </a>
          </div>
          <?php endif; ?>

          <?php if ($currentUser->can('logs.list')): ?>
          <div class="col-6 mb-3">
            <a href="<?= base_url('maintenance-logs') ?>" class="btn btn-primary btn-block">
              <i class="fas fa-clipboard-list"></i><br>Log Maintenance
            </a>
          </div>
          <?php endif; ?>

          <?php if ($currentUser->can('admin.settings')): ?>
          <div class="col-6 mb-3">
            <a href="<?= base_url('admin/settings') ?>" class="btn btn-warning btn-block">
              <i class="fas fa-cog"></i><br>Pengaturan
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
