<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Daftar Kategori</h4>
        <div class="card-header-action">
          <?php if (auth()->user()->can('categories.create')): ?>
          <a href="<?= base_url('admin/categories/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Kategori
          </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-categories">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Nama Kategori</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($categories)): ?>
                <?php $no = 1; foreach ($categories as $category): ?>
                <tr>
                  <td class="text-center"><?= $no++ ?></td>
                  <td><span class="badge badge-primary"><?= esc($category['name']) ?></span></td>
                  <td><?= esc($category['description'] ?? '-') ?></td>
                  <td>
                    <?php if (auth()->user()->can('categories.edit')): ?>
                    <a href="<?= base_url('admin/categories/edit/' . $category['id']) ?>" class="btn btn-sm btn-info" title="Edit">
                      <i class="fas fa-edit"></i>
                    </a>
                    <?php endif; ?>

                    <?php if (auth()->user()->can('categories.delete')): ?>
                    <form action="<?= base_url('admin/categories/delete/' . $category['id']) ?>" method="post" class="d-inline"
                          onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
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
                  <td colspan="4" class="text-center">Belum ada data kategori.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
