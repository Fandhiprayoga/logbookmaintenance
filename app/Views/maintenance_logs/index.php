<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Daftar Log Maintenance</h4>
        <div class="card-header-action">
          <?php if (auth()->user()->can('logs.create')): ?>
          <a href="<?= base_url('maintenance-logs/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Log
          </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-logs">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Tanggal</th>
                <th>Aplikasi</th>
                <th>Kategori</th>
                <th>Judul</th>
                <th>Teknisi</th>
                <th>Status</th>
                <th>Downtime</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($logs)): ?>
                <?php $no = 1; foreach ($logs as $log): ?>
                <tr>
                  <td class="text-center"><?= $no++ ?></td>
                  <td><?= date('d/m/Y H:i', strtotime($log['maintenance_date'])) ?></td>
                  <td><span class="badge badge-light"><?= esc($log['app_name'] ?? '-') ?></span></td>
                  <td><span class="badge badge-info"><?= esc($log['category_name'] ?? '-') ?></span></td>
                  <td>
                    <a href="<?= base_url('maintenance-logs/show/' . $log['id']) ?>" class="text-primary">
                      <?= esc($log['title']) ?>
                    </a>
                  </td>
                  <td><?= esc($log['technician_name'] ?? '-') ?></td>
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
                      <span class="badge badge-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= $log['downtime_duration'] ? $log['downtime_duration'] . ' menit' : 'Ya' ?>
                      </span>
                    <?php else: ?>
                      <span class="badge badge-success">Tidak</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a href="<?= base_url('maintenance-logs/show/' . $log['id']) ?>" class="btn btn-sm btn-primary" title="Detail">
                      <i class="fas fa-eye"></i>
                    </a>

                    <?php if (auth()->user()->can('logs.edit')): ?>
                    <a href="<?= base_url('maintenance-logs/edit/' . $log['id']) ?>" class="btn btn-sm btn-info" title="Edit">
                      <i class="fas fa-edit"></i>
                    </a>
                    <?php endif; ?>

                    <?php if (auth()->user()->can('logs.delete')): ?>
                    <form action="<?= base_url('maintenance-logs/delete/' . $log['id']) ?>" method="post" class="d-inline"
                          onsubmit="return confirm('Yakin ingin menghapus log ini?')">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="text-center">Belum ada data log maintenance.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
