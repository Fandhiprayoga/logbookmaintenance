<?php
$currentUser = auth()->user();
$groups = $currentUser->getGroups();
$groupLabel = !empty($groups) ? ucfirst($groups[0]) : 'User';

$scopeLabel = $isAdmin ? 'Semua Ticket' : 'Ticket Saya';
$recentLimit = 5;
$statusTotal = max(1, (int) $totalLogs);
$pendingPct = (int) round(($pendingLogs / $statusTotal) * 100);
$progressPct = (int) round(($progressLogs / $statusTotal) * 100);
$testingPct = (int) round(($testingLogs / $statusTotal) * 100);
$completedPct = (int) round(($completedLogs / $statusTotal) * 100);
?>

<style>
  .modern-dashboard {
    --md-border: #e7edf4;
    --md-bg-soft: #f7fafe;
    --md-text: #1f2937;
    --md-muted: #6b7280;
  }
  .modern-dashboard .hero-card,
  .modern-dashboard .panel-card {
    border: 1px solid var(--md-border);
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    overflow: hidden;
    margin-bottom: 16px;
  }
  .modern-dashboard .hero-card {
    background: linear-gradient(135deg, #ffffff 0%, #f7fbff 100%);
  }
  .modern-dashboard .hero-body {
    padding: 18px;
  }
  .modern-dashboard .hero-title {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--md-text);
  }
  .modern-dashboard .hero-subtitle {
    color: var(--md-muted);
    margin-top: 6px;
  }
  .modern-dashboard .hero-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
  }
  .modern-dashboard .metric-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
  }
  .modern-dashboard .metric-item {
    border: 1px solid var(--md-border);
    border-radius: 12px;
    background: var(--md-bg-soft);
    padding: 12px;
  }
  .modern-dashboard .metric-label {
    color: var(--md-muted);
    font-size: 12px;
  }
  .modern-dashboard .metric-value {
    margin-top: 6px;
    color: var(--md-text);
    font-size: 1.25rem;
    font-weight: 700;
  }
  .modern-dashboard .panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    border-bottom: 1px solid var(--md-border);
    background: #fff;
  }
  .modern-dashboard .panel-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: var(--md-text);
  }
  .modern-dashboard .panel-body {
    padding: 14px 16px;
  }
  .modern-dashboard .status-list {
    display: grid;
    gap: 10px;
  }
  .modern-dashboard .status-row .label-line {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: var(--md-muted);
    margin-bottom: 6px;
  }
  .modern-dashboard .table td,
  .modern-dashboard .table th {
    vertical-align: middle;
  }
  .modern-dashboard .quick-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
  }
  .modern-dashboard .quick-link {
    display: block;
    border: 1px solid var(--md-border);
    border-radius: 10px;
    padding: 10px 12px;
    color: var(--md-text);
    text-decoration: none;
    background: #fff;
    transition: all 0.2s ease;
  }
  .modern-dashboard .quick-link:hover {
    text-decoration: none;
    border-color: #c9d8ea;
    background: var(--md-bg-soft);
  }
  .modern-dashboard .quick-link .icon {
    color: #4b5563;
    margin-right: 6px;
  }
  @media (max-width: 991.98px) {
    .modern-dashboard .hero-actions {
      justify-content: flex-start;
      margin-top: 10px;
    }
    .modern-dashboard .metric-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .modern-dashboard .quick-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="modern-dashboard">
  <div class="hero-card">
    <div class="hero-body">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h2 class="hero-title">Selamat Datang, <?= esc($currentUser->username) ?></h2>
          <div class="hero-subtitle">
            Role: <strong><?= $groupLabel ?></strong> • Scope Monitoring: <strong><?= $scopeLabel ?></strong>
          </div>
        </div>
        <div class="col-md-4">
          <div class="hero-actions">
            <?php if ($currentUser->can('logs.create')): ?>
            <a href="<?= base_url('maintenance-logs/create') ?>" class="btn btn-primary btn-sm">
              <i class="fas fa-plus-circle"></i> Ticket Baru
            </a>
            <?php endif; ?>
            <?php if ($currentUser->can('logs.list')): ?>
            <a href="<?= base_url('maintenance-logs') ?>" class="btn btn-light border btn-sm">
              <i class="fas fa-clipboard-list"></i> Buka Board
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if ($currentUser->can('logs.list')): ?>
  <div class="metric-grid mb-3">
    <div class="metric-item">
      <div class="metric-label">Total Ticket</div>
      <div class="metric-value"><?= $totalLogs ?></div>
    </div>
    <div class="metric-item">
      <div class="metric-label">Aktif (Pending+Progress+Testing)</div>
      <div class="metric-value"><?= $activeLogs ?></div>
    </div>
    <div class="metric-item">
      <div class="metric-label">Selesai</div>
      <div class="metric-value"><?= $completedLogs ?></div>
    </div>
    <div class="metric-item">
      <div class="metric-label">Completion Rate</div>
      <div class="metric-value"><?= $completionRate ?>%</div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-5">
      <div class="panel-card">
        <div class="panel-head">
          <h3 class="panel-title">Status Monitoring</h3>
          <span class="badge badge-light"><?= $scopeLabel ?></span>
        </div>
        <div class="panel-body">
          <div class="status-list">
            <div class="status-row">
              <div class="label-line"><span>Pending</span><span><?= $pendingLogs ?> (<?= $pendingPct ?>%)</span></div>
              <div class="progress" style="height:7px;"><div class="progress-bar bg-warning" style="width: <?= $pendingPct ?>%"></div></div>
            </div>
            <div class="status-row">
              <div class="label-line"><span>On Progress</span><span><?= $progressLogs ?> (<?= $progressPct ?>%)</span></div>
              <div class="progress" style="height:7px;"><div class="progress-bar bg-info" style="width: <?= $progressPct ?>%"></div></div>
            </div>
            <div class="status-row">
              <div class="label-line"><span>Testing</span><span><?= $testingLogs ?> (<?= $testingPct ?>%)</span></div>
              <div class="progress" style="height:7px;"><div class="progress-bar" style="background:#4b5563;width: <?= $testingPct ?>%"></div></div>
            </div>
            <div class="status-row">
              <div class="label-line"><span>Completed</span><span><?= $completedLogs ?> (<?= $completedPct ?>%)</span></div>
              <div class="progress" style="height:7px;"><div class="progress-bar bg-success" style="width: <?= $completedPct ?>%"></div></div>
            </div>
            <div class="status-row">
              <div class="label-line"><span>Ticket Berdampak Downtime</span><span><?= $downtimeLogs ?></span></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="panel-card">
        <div class="panel-head">
          <h3 class="panel-title">Ticket Terbaru (<?= $recentLimit ?>)</h3>
          <a href="<?= base_url('maintenance-logs') ?>" class="btn btn-light border btn-sm">Lihat Semua</a>
        </div>
        <div class="panel-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Judul</th>
                  <th>Status</th>
                  <th>Downtime</th>
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($recentLogs)): ?>
                  <?php foreach ($recentLogs as $log): ?>
                  <tr>
                    <td><?= date('d/m/Y H:i', strtotime($log['maintenance_date'])) ?></td>
                    <td>
                      <a href="<?= base_url('maintenance-logs/show/' . $log['id']) ?>" class="font-weight-600"><?= esc($log['title']) ?></a><br>
                      <small class="text-muted"><?= esc($log['app_name'] ?? '-') ?> • <?= esc($log['category_name'] ?? '-') ?></small>
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
                      <?php if ((int) $log['has_downtime'] === 1): ?>
                        <span class="badge badge-danger"><?= $log['downtime_duration'] ? $log['downtime_duration'] . 'm' : 'Ya' ?></span>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="text-center p-4 text-muted">Belum ada data ticket.</td>
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
    <div class="col-lg-4">
      <div class="panel-card">
        <div class="panel-head"><h3 class="panel-title">Ringkasan Akun</h3></div>
        <div class="panel-body">
          <table class="table table-sm table-borderless mb-0">
            <tr><th width="110">Username</th><td><?= esc($currentUser->username) ?></td></tr>
            <tr><th>Email</th><td><?= esc($currentUser->email) ?></td></tr>
            <tr>
              <th>Role</th>
              <td>
                <?php foreach ($groups as $group): ?>
                  <span class="badge badge-primary mr-1"><?= ucfirst($group) ?></span>
                <?php endforeach; ?>
              </td>
            </tr>
            <tr><th>Status</th><td><span class="badge badge-success">Aktif</span></td></tr>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="panel-card">
        <div class="panel-head"><h3 class="panel-title">Quick Access</h3></div>
        <div class="panel-body">
          <div class="quick-grid">
            <?php if ($currentUser->can('logs.list')): ?>
            <a href="<?= base_url('maintenance-logs') ?>" class="quick-link"><i class="fas fa-clipboard-list icon"></i> Board Ticketing</a>
            <?php endif; ?>
            <?php if ($currentUser->can('logs.create')): ?>
            <a href="<?= base_url('maintenance-logs/create') ?>" class="quick-link"><i class="fas fa-plus-circle icon"></i> Buat Ticket Baru</a>
            <?php endif; ?>
            <?php if ($currentUser->can('apps.list')): ?>
            <a href="<?= base_url('admin/apps') ?>" class="quick-link"><i class="fas fa-server icon"></i> Master Aplikasi (<?= $totalApps ?>)</a>
            <?php endif; ?>
            <?php if ($currentUser->can('categories.list')): ?>
            <a href="<?= base_url('admin/categories') ?>" class="quick-link"><i class="fas fa-tags icon"></i> Master Kategori (<?= $totalCategories ?>)</a>
            <?php endif; ?>
            <?php if ($currentUser->can('users.list')): ?>
            <a href="<?= base_url('admin/users') ?>" class="quick-link"><i class="fas fa-users icon"></i> Manajemen User (<?= (int) ($totalUsers ?? 0) ?>)</a>
            <?php endif; ?>
            <?php if ($currentUser->inGroup('superadmin')): ?>
            <a href="<?= base_url('admin/roles') ?>" class="quick-link"><i class="fas fa-user-shield icon"></i> Role & Permission</a>
            <?php endif; ?>
            <a href="<?= base_url('profile') ?>" class="quick-link"><i class="far fa-user icon"></i> Profil Saya</a>
            <?php if ($currentUser->can('admin.settings')): ?>
            <a href="<?= base_url('admin/settings') ?>" class="quick-link"><i class="fas fa-cog icon"></i> Pengaturan Sistem</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
