<div class="row">
  <div class="col-12 col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h4>Tambah Aplikasi Baru</h4>
      </div>
      <div class="card-body">
        <form action="<?= base_url('admin/apps/store') ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-group">
            <label for="name">Nama Aplikasi <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
          </div>

          <div class="form-group">
            <label for="url">URL / Repo Link</label>
            <input type="text" class="form-control" id="url" name="url" value="<?= old('url') ?>" placeholder="https://...">
          </div>

          <div class="form-group">
            <label for="tech_stack">Stack Tech (Bahasa Pemrograman)</label>
            <input type="text" class="form-control" id="tech_stack" name="tech_stack" value="<?= old('tech_stack') ?>" placeholder="PHP, JavaScript, Python (pisahkan dengan koma)">
            <small class="form-text text-muted">Pisahkan beberapa teknologi dengan koma</small>
          </div>

          <div class="form-group">
            <label for="pic">Penanggung Jawab</label>
            <input type="text" class="form-control" id="pic" name="pic" value="<?= old('pic') ?>">
          </div>

          <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
          </div>

          <div class="form-group text-right">
            <a href="<?= base_url('admin/apps') ?>" class="btn btn-secondary mr-1">Batal</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
