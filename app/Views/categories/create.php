<div class="row">
  <div class="col-12 col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h4>Tambah Kategori Baru</h4>
      </div>
      <div class="card-body">
        <form action="<?= base_url('admin/categories/store') ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-group">
            <label for="name">Nama Kategori <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required
                   placeholder="Contoh: Bug Fix, Security Patch, Server Upgrade">
          </div>

          <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
          </div>

          <div class="form-group text-right">
            <a href="<?= base_url('admin/categories') ?>" class="btn btn-secondary mr-1">Batal</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
