<?php
  $statusClass = match($log['status']) {
    'Pending'     => 'badge-warning',
    'On Progress' => 'badge-info',
    'Testing'     => 'badge-primary',
    'Completed'   => 'badge-success',
    default       => 'badge-secondary',
  };

  $attachUrl = null;
  $isImage = false;
  if (! empty($log['attachment'])) {
    $attachUrl = base_url('maintenance-logs/attachment/' . $log['id']);
    $ext = strtolower(pathinfo($log['attachment'], PATHINFO_EXTENSION));
    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
  }
?>

<style>
  .ticket-page {
    --tp-border: #e9edf4;
    --tp-text: #1f2937;
    --tp-muted: #6b7280;
    --tp-bg: #f8fafc;
  }
  .ticket-page .ticket-card {
    border: 1px solid var(--tp-border);
    border-radius: 14px;
    box-shadow: 0 8px 26px rgba(15, 23, 42, 0.06);
    background: #fff;
    overflow: hidden;
    margin-bottom: 16px;
  }
  .ticket-page .ticket-card .ticket-head {
    padding: 14px 18px;
    border-bottom: 1px solid var(--tp-border);
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
  }
  .ticket-page .ticket-card .ticket-body {
    padding: 16px 18px;
  }
  .ticket-page .ticket-title {
    margin: 0;
    font-size: 1.1rem;
    color: var(--tp-text);
    font-weight: 700;
    line-height: 1.35;
  }
  .ticket-page .ticket-meta {
    margin-top: 6px;
    color: var(--tp-muted);
    font-size: 12px;
  }
  .ticket-page .ticket-actions {
    gap: 8px;
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
  }
  .ticket-page .ticket-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
  }
  .ticket-page .meta-row {
    display: flex;
    justify-content: space-between;
    border-bottom: 1px dashed var(--tp-border);
    padding: 8px 0;
    gap: 12px;
  }
  .ticket-page .meta-row:last-child {
    border-bottom: 0;
  }
  .ticket-page .meta-label {
    color: var(--tp-muted);
    font-weight: 600;
    min-width: 120px;
  }
  .ticket-page .meta-value {
    color: var(--tp-text);
    text-align: right;
  }
  .ticket-page .content-box {
    border: 1px solid var(--tp-border);
    border-radius: 10px;
    padding: 12px 14px;
    min-height: 76px;
    background: var(--tp-bg);
    color: var(--tp-text);
  }
  .ticket-page .content-box p:last-child {
    margin-bottom: 0;
  }
  .ticket-page .muted {
    color: var(--tp-muted);
  }
  @media (max-width: 991.98px) {
    .ticket-page .ticket-grid {
      grid-template-columns: 1fr;
    }
    .ticket-page .meta-row {
      flex-direction: column;
      gap: 4px;
    }
    .ticket-page .meta-value {
      text-align: left;
    }
    .ticket-page .ticket-actions {
      justify-content: flex-start;
      margin-top: 10px;
    }
  }
</style>

<div class="row ticket-page">
  <div class="col-12 col-lg-10 offset-lg-1">

    <div class="ticket-card">
      <div class="ticket-head">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h2 class="ticket-title">
              Ticket #<?= $log['id'] ?> - <?= esc($log['title']) ?>
              <span class="badge <?= $statusClass ?> ml-2"><?= esc($log['status']) ?></span>
            </h2>
            <div class="ticket-meta">
              Dibuat <?= $log['created_at'] ? date('d M Y H:i', strtotime($log['created_at'])) : '-' ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="ticket-actions">
              <?php if (auth()->user()->can('logs.review')): ?>
                <?php if ($log['status'] !== 'Completed'): ?>
                <form action="<?= base_url('maintenance-logs/close-ticket/' . $log['id']) ?>" method="post" class="d-inline">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Close ticket ini sekarang?')">
                    <i class="fas fa-check-circle"></i> Close
                  </button>
                </form>
                <?php else: ?>
                <form action="<?= base_url('maintenance-logs/reopen-ticket/' . $log['id']) ?>" method="post" class="d-inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="status" value="On Progress">
                  <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Buka kembali ticket ini?')">
                    <i class="fas fa-undo"></i> Reopen
                  </button>
                </form>
                <?php endif; ?>
              <?php endif; ?>
              <?php if (auth()->user()->can('logs.edit')): ?>
              <a href="<?= base_url('maintenance-logs/edit/' . $log['id']) ?>" class="btn btn-info btn-sm">
                <i class="fas fa-edit"></i> Edit
              </a>
              <?php endif; ?>
              <a href="<?= base_url('maintenance-logs') ?>" class="btn btn-light btn-sm border">
                <i class="fas fa-arrow-left"></i> Kembali
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="ticket-body">
        <div class="ticket-grid">
          <div>
            <div class="meta-row"><div class="meta-label">Aplikasi</div><div class="meta-value"><span class="badge badge-light"><?= esc($log['app_name'] ?? '-') ?></span></div></div>
            <div class="meta-row"><div class="meta-label">Kategori</div><div class="meta-value"><span class="badge badge-info"><?= esc($log['category_name'] ?? '-') ?></span></div></div>
            <div class="meta-row"><div class="meta-label">Tanggal Ticket</div><div class="meta-value"><?= date('d F Y, H:i', strtotime($log['maintenance_date'])) ?></div></div>
            <div class="meta-row"><div class="meta-label">Teknisi</div><div class="meta-value"><?= esc($log['technician_name'] ?? '-') ?></div></div>
          </div>
          <div>
            <div class="meta-row"><div class="meta-label">Dibuat Oleh</div><div class="meta-value"><?= esc($log['creator_name'] ?? '-') ?></div></div>
            <div class="meta-row"><div class="meta-label">Downtime</div><div class="meta-value"><?php if ($log['has_downtime']): ?><span class="badge badge-danger">Ya<?= $log['downtime_duration'] ? ' - ' . $log['downtime_duration'] . ' menit' : '' ?></span><?php else: ?><span class="badge badge-success">Tidak</span><?php endif; ?></div></div>
            <div class="meta-row"><div class="meta-label">Waktu Selesai</div><div class="meta-value"><?= ! empty($log['closed_at']) ? date('d F Y, H:i', strtotime($log['closed_at'])) : '<span class="muted">-</span>' ?></div></div>
            <div class="meta-row"><div class="meta-label">Diselesaikan Oleh</div><div class="meta-value"><?= ! empty($log['closer_name']) ? esc($log['closer_name']) : '<span class="muted">-</span>' ?></div></div>
          </div>
        </div>
      </div>
    </div>

    <?php if (auth()->user()->can('logs.review')): ?>
    <div class="ticket-card">
      <div class="ticket-head">
        <h4 class="mb-0">Aksi Ticket</h4>
      </div>
      <div class="ticket-body">
        <form action="<?= base_url('maintenance-logs/update-status/' . $log['id']) ?>" method="post" class="form-inline mb-3">
          <?= csrf_field() ?>
          <label for="status" class="mr-2">Ubah Status</label>
          <select class="form-control mr-2" id="status" name="status">
            <option value="Pending" <?= $log['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="On Progress" <?= $log['status'] === 'On Progress' ? 'selected' : '' ?>>On Progress</option>
            <option value="Testing" <?= $log['status'] === 'Testing' ? 'selected' : '' ?>>Testing</option>
          </select>
          <button type="submit" class="btn btn-primary" onclick="return confirm('Yakin ingin mengubah status?')">
            <i class="fas fa-check"></i> Simpan
          </button>
        </form>

        <?php if ($log['status'] !== 'Completed'): ?>
        <form action="<?= base_url('maintenance-logs/close-ticket/' . $log['id']) ?>" method="post" class="form-inline">
          <?= csrf_field() ?>
          <label for="closed_at" class="mr-2">Tanggal Close</label>
          <input type="datetime-local" class="form-control mr-2" id="closed_at" name="closed_at"
                 value="<?= old('closed_at', date('Y-m-d\TH:i', strtotime($log['maintenance_date'] ?? 'now'))) ?>">
          <button type="submit" class="btn btn-success" onclick="return confirm('Yakin ingin close ticket ini?')">
            <i class="fas fa-check-circle"></i> Close Ticket
          </button>
        </form>
        <?php else: ?>
        <form action="<?= base_url('maintenance-logs/reopen-ticket/' . $log['id']) ?>" method="post" class="form-inline">
          <?= csrf_field() ?>
          <label for="reopen_status" class="mr-2">Reopen Ke</label>
          <select class="form-control mr-2" id="reopen_status" name="status">
            <option value="Pending" <?= old('status') === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="On Progress" <?= old('status', 'On Progress') === 'On Progress' ? 'selected' : '' ?>>On Progress</option>
            <option value="Testing" <?= old('status') === 'Testing' ? 'selected' : '' ?>>Testing</option>
          </select>
          <button type="submit" class="btn btn-secondary" onclick="return confirm('Yakin ingin buka kembali ticket ini?')">
            <i class="fas fa-undo"></i> Reopen Ticket
          </button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    <div class="ticket-grid">
      <div class="ticket-card">
        <div class="ticket-head"><h4 class="mb-0">Deskripsi Masalah</h4></div>
        <div class="ticket-body">
          <div class="content-box">
            <?php if (! empty($log['problem_description'])): ?>
              <p><?= nl2br(esc($log['problem_description'])) ?></p>
            <?php else: ?>
              <p class="muted">Belum ada deskripsi masalah.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="ticket-card">
        <div class="ticket-head"><h4 class="mb-0">Root Cause</h4></div>
        <div class="ticket-body">
          <div class="content-box">
            <?php if (! empty($log['root_cause'])): ?>
              <p><?= nl2br(esc($log['root_cause'])) ?></p>
            <?php else: ?>
              <p class="muted">Belum diisi.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="ticket-grid">
      <div class="ticket-card">
        <div class="ticket-head"><h4 class="mb-0">Tindakan / Solusi</h4></div>
        <div class="ticket-body">
          <div class="content-box">
            <?php if (! empty($log['action_taken'])): ?>
              <p><?= nl2br(esc($log['action_taken'])) ?></p>
            <?php else: ?>
              <p class="muted">Belum ada tindakan yang dicatat.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="ticket-card">
        <div class="ticket-head"><h4 class="mb-0">Attachment</h4></div>
        <div class="ticket-body">
          <?php if (! empty($log['attachment'])): ?>
            <a href="<?= $attachUrl ?>" target="_blank" class="btn btn-outline-primary btn-sm mb-2">
              <i class="fas fa-paperclip"></i> <?= basename($log['attachment']) ?>
            </a>
            <?php if ($isImage): ?>
            <div class="content-box text-center">
              <a href="<?= $attachUrl ?>" target="_blank">
                <img src="<?= $attachUrl ?>" alt="Attachment" class="img-fluid rounded" style="max-height: 320px;">
              </a>
            </div>
            <?php endif; ?>
          <?php else: ?>
            <div class="content-box"><p class="muted">Tidak ada attachment.</p></div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</div>
