<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Daftar Aplikasi</h4>
        <div class="card-header-action">
          <?php if (auth()->user()->can('apps.create')): ?>
          <a href="<?= base_url('admin/apps/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Aplikasi
          </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="table-apps">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Nama Aplikasi</th>
                <th>URL / Repo</th>
                <th>Stack Tech</th>
                <th>Penanggung Jawab</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($apps)): ?>
                <?php $no = 1; foreach ($apps as $app): ?>
                <tr>
                  <td class="text-center"><?= $no++ ?></td>
                  <td><?= esc($app['name']) ?></td>
                  <td>
                    <?php if (!empty($app['url'])): ?>
                      <a href="<?= esc($app['url']) ?>" target="_blank" class="text-primary">
                        <i class="fas fa-external-link-alt"></i> <?= esc($app['url']) ?>
                      </a>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($app['tech_stack'])): ?>
                      <?php foreach (explode(',', $app['tech_stack']) as $tech): ?>
                        <span class="badge badge-info"><?= esc(trim($tech)) ?></span>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td><?= esc($app['pic'] ?? '-') ?></td>
                  <td>
                    <?php if (auth()->user()->can('apps.edit')): ?>
                    <a href="<?= base_url('admin/apps/edit/' . $app['id']) ?>" class="btn btn-sm btn-info" title="Edit">
                      <i class="fas fa-edit"></i>
                    </a>
                    <?php endif; ?>

                    <?php if (auth()->user()->can('apps.delete')): ?>
                    <form action="<?= base_url('admin/apps/delete/' . $app['id']) ?>" method="post" class="d-inline"
                          onsubmit="return confirm('Yakin ingin menghapus aplikasi ini?')">
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
                  <td colspan="6" class="text-center">Belum ada data aplikasi.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
