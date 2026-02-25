<div class="row">
  <div class="col-12 col-md-10 offset-md-1">
    <div class="card">
      <div class="card-header">
        <h4>Edit Log Maintenance</h4>
      </div>
      <div class="card-body">
        <form action="<?= base_url('maintenance-logs/update/' . $log['id']) ?>" method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="app_id">Aplikasi <span class="text-danger">*</span></label>
                <select class="form-control" id="app_id" name="app_id" required>
                  <option value="">-- Pilih Aplikasi --</option>
                  <?php foreach ($apps as $app): ?>
                    <option value="<?= $app['id'] ?>" <?= old('app_id', $log['app_id']) == $app['id'] ? 'selected' : '' ?>>
                      <?= esc($app['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="category_id">Kategori <span class="text-danger">*</span></label>
                <select class="form-control" id="category_id" name="category_id" required>
                  <option value="">-- Pilih Kategori --</option>
                  <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= old('category_id', $log['category_id']) == $cat['id'] ? 'selected' : '' ?>>
                      <?= esc($cat['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="maintenance_date">Tanggal & Waktu Maintenance <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" id="maintenance_date" name="maintenance_date"
                       value="<?= old('maintenance_date', date('Y-m-d\TH:i', strtotime($log['maintenance_date']))) ?>" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="technician_id">Teknisi</label>
                <select class="form-control" id="technician_id" name="technician_id">
                  <option value="">-- Pilih Teknisi --</option>
                  <?php foreach ($users as $user): ?>
                    <option value="<?= $user->id ?>" <?= old('technician_id', $log['technician_id']) == $user->id ? 'selected' : '' ?>>
                      <?= esc($user->username) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="title">Judul / Subject <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title"
                   value="<?= old('title', $log['title']) ?>" required>
          </div>

          <div class="form-group">
            <label for="problem_description">Deskripsi Masalah</label>
            <textarea class="form-control" id="problem_description" name="problem_description" rows="4"><?= old('problem_description', $log['problem_description']) ?></textarea>
          </div>

          <div class="form-group">
            <label for="root_cause">Root Cause (Akar Masalah)</label>
            <textarea class="form-control" id="root_cause" name="root_cause" rows="3"><?= old('root_cause', $log['root_cause']) ?></textarea>
          </div>

          <div class="form-group">
            <label for="action_taken">Tindakan / Solusi</label>
            <textarea class="form-control" id="action_taken" name="action_taken" rows="4"><?= old('action_taken', $log['action_taken']) ?></textarea>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status">
                  <?php $currentStatus = old('status', $log['status']); ?>
                  <option value="Pending" <?= $currentStatus === 'Pending' ? 'selected' : '' ?>>Pending</option>
                  <option value="On Progress" <?= $currentStatus === 'On Progress' ? 'selected' : '' ?>>On Progress</option>
                  <option value="Testing" <?= $currentStatus === 'Testing' ? 'selected' : '' ?>>Testing</option>
                  <option value="Completed" <?= $currentStatus === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="has_downtime">Downtime?</label>
                <?php $currentDowntime = old('has_downtime', $log['has_downtime']); ?>
                <select class="form-control" id="has_downtime" name="has_downtime" onchange="toggleDowntime()">
                  <option value="0" <?= $currentDowntime == '0' ? 'selected' : '' ?>>Tidak</option>
                  <option value="1" <?= $currentDowntime == '1' ? 'selected' : '' ?>>Ya</option>
                </select>
              </div>
            </div>
            <div class="col-md-4" id="downtime-duration-group" style="<?= $currentDowntime == '1' ? '' : 'display:none;' ?>">
              <div class="form-group">
                <label for="downtime_duration">Durasi Downtime (menit)</label>
                <input type="number" class="form-control" id="downtime_duration" name="downtime_duration"
                       value="<?= old('downtime_duration', $log['downtime_duration']) ?>" min="0">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="attachment">Attachment (Screenshot/Log)</label>
            <?php if (!empty($log['attachment'])): ?>
              <div class="mb-2">
                <span class="text-muted">File saat ini:</span>
                <a href="<?= base_url('maintenance-logs/attachment/' . $log['id']) ?>" target="_blank">
                  <i class="fas fa-paperclip"></i> <?= basename($log['attachment']) ?>
                </a>
              </div>
            <?php endif; ?>
            <input type="file" class="form-control-file" id="attachment" name="attachment"
                   accept=".jpg,.jpeg,.png,.gif,.pdf,.log,.txt,.zip">
            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah attachment</small>
          </div>

          <div class="form-group text-right">
            <a href="<?= base_url('maintenance-logs') ?>" class="btn btn-secondary mr-1">Batal</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Perbarui
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function toggleDowntime() {
  var val = document.getElementById('has_downtime').value;
  var group = document.getElementById('downtime-duration-group');
  group.style.display = val === '1' ? '' : 'none';
  if (val === '0') {
    document.getElementById('downtime_duration').value = '';
  }
}
</script>
