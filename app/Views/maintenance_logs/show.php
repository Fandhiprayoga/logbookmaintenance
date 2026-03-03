<div class="row">
  <div class="col-12 col-md-10 offset-md-1">

    <!-- Info Utama -->
    <div class="card">
      <div class="card-header">
        <h4>
          <?= esc($log['title']) ?>
          <?php
            $statusClass = match($log['status']) {
              'Pending'     => 'badge-warning',
              'On Progress' => 'badge-info',
              'Testing'     => 'badge-primary',
              'Completed'   => 'badge-success',
              default       => 'badge-secondary',
            };
          ?>
          <span class="badge <?= $statusClass ?> ml-2"><?= esc($log['status']) ?></span>
        </h4>
        <div class="card-header-action">
          <?php if (auth()->user()->can('logs.edit')): ?>
          <a href="<?= base_url('maintenance-logs/edit/' . $log['id']) ?>" class="btn btn-info">
            <i class="fas fa-edit"></i> Edit
          </a>
          <?php endif; ?>
          <a href="<?= base_url('maintenance-logs') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-sm table-borderless">
              <tr>
                <td class="font-weight-bold" width="180">Log ID</td>
                <td>#<?= $log['id'] ?></td>
              </tr>
              <tr>
                <td class="font-weight-bold">Aplikasi</td>
                <td><span class="badge badge-light"><?= esc($log['app_name'] ?? '-') ?></span></td>
              </tr>
              <tr>
                <td class="font-weight-bold">Kategori</td>
                <td><span class="badge badge-info"><?= esc($log['category_name'] ?? '-') ?></span></td>
              </tr>
              <tr>
                <td class="font-weight-bold">Tanggal Maintenance</td>
                <td><?= date('d F Y, H:i', strtotime($log['maintenance_date'])) ?></td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-sm table-borderless">
              <tr>
                <td class="font-weight-bold" width="180">Teknisi</td>
                <td><?= esc($log['technician_name'] ?? '-') ?></td>
              </tr>
              <tr>
                <td class="font-weight-bold">Dibuat oleh</td>
                <td><?= esc($log['creator_name'] ?? '-') ?></td>
              </tr>
              <tr>
                <td class="font-weight-bold">Downtime</td>
                <td>
                  <?php if ($log['has_downtime']): ?>
                    <span class="badge badge-danger">
                      <i class="fas fa-exclamation-triangle"></i> Ya
                      <?= $log['downtime_duration'] ? '- ' . $log['downtime_duration'] . ' menit' : '' ?>
                    </span>
                  <?php else: ?>
                    <span class="badge badge-success">Tidak ada downtime</span>
                  <?php endif; ?>
                </td>
              </tr>
              <tr>
                <td class="font-weight-bold">Attachment</td>
                <td>
                  <?php if (!empty($log['attachment'])): ?>
                    <?php
                      $attachUrl = base_url('maintenance-logs/attachment/' . $log['id']);
                      $ext = strtolower(pathinfo($log['attachment'], PATHINFO_EXTENSION));
                      $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                    ?>
                    <a href="<?= $attachUrl ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-paperclip"></i> <?= basename($log['attachment']) ?>
                    </a>
                  <?php else: ?>
                    <span class="text-muted">Tidak ada</span>
                  <?php endif; ?>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Preview Attachment Gambar -->
    <?php if (!empty($log['attachment'])): ?>
    <?php
      $attachUrl = base_url('maintenance-logs/attachment/' . $log['id']);
      $ext = strtolower(pathinfo($log['attachment'], PATHINFO_EXTENSION));
      $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    ?>
    <?php if ($isImage): ?>
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-image text-info"></i> Preview Attachment</h4>
      </div>
      <div class="card-body text-center">
        <a href="<?= $attachUrl ?>" target="_blank">
          <img src="<?= $attachUrl ?>" alt="Attachment" class="img-fluid rounded shadow" style="max-height: 500px;">
        </a>
        <p class="text-muted mt-2"><small>Klik gambar untuk membuka di tab baru</small></p>
      </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Deskripsi Masalah -->
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-bug text-danger"></i> Deskripsi Masalah</h4>
      </div>
      <div class="card-body">
        <?php if (!empty($log['problem_description'])): ?>
          <p><?= nl2br(esc($log['problem_description'])) ?></p>
        <?php else: ?>
          <p class="text-muted">Belum ada deskripsi masalah.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Root Cause -->
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-search text-warning"></i> Root Cause (Akar Masalah)</h4>
      </div>
      <div class="card-body">
        <?php if (!empty($log['root_cause'])): ?>
          <p><?= nl2br(esc($log['root_cause'])) ?></p>
        <?php else: ?>
          <p class="text-muted">Belum diisi. Isi setelah analisis selesai.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Tindakan / Solusi -->
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-wrench text-success"></i> Tindakan / Solusi</h4>
      </div>
      <div class="card-body">
        <?php if (!empty($log['action_taken'])): ?>
          <p><?= nl2br(esc($log['action_taken'])) ?></p>
        <?php else: ?>
          <p class="text-muted">Belum ada tindakan yang dicatat.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Update Status (Review) -->
    <?php if (auth()->user()->can('logs.review')): ?>
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-clipboard-check text-primary"></i> Update Status (Review)</h4>
      </div>
      <div class="card-body">
        <form action="<?= base_url('maintenance-logs/update-status/' . $log['id']) ?>" method="post" class="form-inline">
          <?= csrf_field() ?>
          <div class="form-group mr-3">
            <label for="status" class="mr-2">Ubah Status:</label>
            <select class="form-control" id="status" name="status">
              <option value="Pending" <?= $log['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
              <option value="On Progress" <?= $log['status'] === 'On Progress' ? 'selected' : '' ?>>On Progress</option>
              <option value="Testing" <?= $log['status'] === 'Testing' ? 'selected' : '' ?>>Testing</option>
              <option value="Completed" <?= $log['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary" onclick="return confirm('Yakin ingin mengubah status?')">
            <i class="fas fa-check"></i> Update Status
          </button>
        </form>
      </div>
    </div>
    <?php endif; ?>

    <!-- Timeline -->
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-history"></i> Informasi Waktu</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p><strong>Dibuat:</strong> <?= $log['created_at'] ? date('d F Y, H:i', strtotime($log['created_at'])) : '-' ?></p>
          </div>
          <div class="col-md-6">
            <p><strong>Terakhir diperbarui:</strong> <?= $log['updated_at'] ? date('d F Y, H:i', strtotime($log['updated_at'])) : '-' ?></p>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
