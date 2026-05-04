<?php $progressPct = $totalCount > 0 ? (int) round(($doneCount / $totalCount) * 100) : 0; ?>

<style>
  .daily-work-board {
    --dw-border: #e7edf4;
    --dw-bg: #f7fafe;
    --dw-text: #1f2937;
    --dw-muted: #6b7280;
  }
  .daily-work-board .dw-card {
    border: 1px solid var(--dw-border);
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    margin-bottom: 14px;
    overflow: hidden;
  }
  .daily-work-board .dw-head {
    padding: 14px 16px;
    border-bottom: 1px solid var(--dw-border);
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
  }
  .daily-work-board .dw-title {
    margin: 0;
    font-size: 1.08rem;
    font-weight: 700;
    color: var(--dw-text);
  }
  .daily-work-board .dw-subtitle {
    font-size: 12px;
    color: var(--dw-muted);
    margin-top: 6px;
  }
  .daily-work-board .dw-body {
    padding: 14px 16px;
  }
  .daily-work-board .stat-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
    margin-bottom: 12px;
  }
  .daily-work-board .stat-item {
    border: 1px solid var(--dw-border);
    border-radius: 10px;
    background: var(--dw-bg);
    padding: 10px;
  }
  .daily-work-board .stat-label {
    font-size: 12px;
    color: var(--dw-muted);
  }
  .daily-work-board .stat-value {
    margin-top: 4px;
    font-size: 1.12rem;
    font-weight: 700;
    color: var(--dw-text);
  }
  .daily-work-board .task-list {
    display: grid;
    gap: 10px;
  }
  .daily-work-board .task-item {
    border: 1px solid var(--dw-border);
    border-radius: 10px;
    padding: 10px 12px;
    background: #fff;
  }
  .daily-work-board .task-item.done {
    background: #f8fbf8;
    border-color: #d7ead7;
  }
  .daily-work-board .task-title {
    margin: 0;
    font-weight: 600;
    color: var(--dw-text);
  }
  .daily-work-board .task-title.done {
    text-decoration: line-through;
    color: #4b5563;
  }
  .daily-work-board .task-notes {
    margin-top: 4px;
    color: var(--dw-muted);
    font-size: 13px;
    white-space: pre-line;
  }
  .daily-work-board .task-actions {
    display: flex;
    gap: 6px;
    justify-content: flex-end;
    align-items: center;
  }
  .daily-work-board .drag-handle {
    cursor: grab;
    user-select: none;
    color: #64748b;
    border: 1px solid var(--dw-border);
    border-radius: 8px;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fff;
  }
  .daily-work-board .task-item.sortable-ghost {
    opacity: 0.6;
  }
  .daily-work-board .task-item.sortable-chosen {
    border-color: #b6d0f0;
    box-shadow: 0 8px 18px rgba(59, 130, 246, 0.14);
  }
  @media (max-width: 991.98px) {
    .daily-work-board .stat-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="row daily-work-board">
  <div class="col-12 col-lg-10 offset-lg-1">
    <div class="dw-card">
      <div class="dw-head">
        <div class="row align-items-center">
          <div class="col-md-7">
            <h2 class="dw-title">Log Kerjaan Harian</h2>
            <div class="dw-subtitle">Catat task harian Anda seperti to-do list, lalu centang saat selesai.</div>
          </div>
          <div class="col-md-5 text-md-right mt-2 mt-md-0">
            <form action="<?= base_url('daily-work-logs') ?>" method="get" class="form-inline justify-content-md-end">
              <label for="date" class="mr-2 text-muted">Tanggal</label>
              <input type="date" id="date" name="date" class="form-control" value="<?= esc($selectedDate) ?>" onchange="this.form.submit()">
            </form>
          </div>
        </div>
      </div>
      <div class="dw-body">
        <div class="stat-grid">
          <div class="stat-item">
            <div class="stat-label">Total Task</div>
            <div class="stat-value"><?= $totalCount ?></div>
          </div>
          <div class="stat-item">
            <div class="stat-label">Selesai</div>
            <div class="stat-value"><?= $doneCount ?></div>
          </div>
          <div class="stat-item">
            <div class="stat-label">Progress</div>
            <div class="stat-value"><?= $progressPct ?>%</div>
          </div>
        </div>
        <div class="progress" style="height: 8px;">
          <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progressPct ?>%;" aria-valuenow="<?= $progressPct ?>" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      </div>
    </div>

    <div class="dw-card">
      <div class="dw-head">
        <h3 class="dw-title">Tambah Task</h3>
      </div>
      <div class="dw-body">
        <form action="<?= base_url('daily-work-logs/store') ?>" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="work_date" value="<?= esc($selectedDate) ?>">
          <div class="form-group">
            <label for="title">Task</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" placeholder="Contoh: Review ticket bug login" required>
          </div>
          <div class="form-group">
            <label for="notes">Catatan (opsional)</label>
            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Detail singkat pekerjaan...\"><?= old('notes') ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
        </form>
      </div>
    </div>

    <div class="dw-card">
      <div class="dw-head">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
          <h3 class="dw-title mb-0">Daftar Task - <?= date('d M Y', strtotime($selectedDate)) ?></h3>
          <small class="text-muted mt-2 mt-md-0">Drag task yang belum selesai untuk ubah prioritas</small>
        </div>
      </div>
      <div class="dw-body">
        <form id="reorder-form" action="<?= base_url('daily-work-logs/reorder') ?>" method="post" class="mb-3">
          <?= csrf_field() ?>
          <input type="hidden" name="work_date" value="<?= esc($selectedDate) ?>">
          <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-sort"></i> Simpan Urutan Task</button>
        </form>

        <div class="task-list" id="task-list-sortable">
          <?php if (! empty($items)): ?>
            <?php foreach ($items as $item): ?>
            <div class="task-item <?= (int) $item['is_done'] === 1 ? 'done' : '' ?>" data-task-id="<?= (int) $item['id'] ?>" data-is-done="<?= (int) $item['is_done'] ?>">
              <div class="row align-items-start">
                <div class="col-md-8">
                  <div class="d-flex align-items-center">
                    <?php if ((int) $item['is_done'] === 0): ?>
                    <span class="drag-handle mr-2" title="Drag untuk ubah urutan"><i class="fas fa-grip-vertical"></i></span>
                    <?php endif; ?>
                    <h4 class="task-title <?= (int) $item['is_done'] === 1 ? 'done' : '' ?>"><?= esc($item['title']) ?></h4>
                  </div>
                  <?php if (! empty($item['notes'])): ?>
                  <div class="task-notes"><?= esc($item['notes']) ?></div>
                  <?php endif; ?>
                </div>
                <div class="col-md-4 mt-2 mt-md-0">
                  <div class="task-actions">
                    <form action="<?= base_url('daily-work-logs/toggle/' . $item['id']) ?>" method="post" class="d-inline">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm <?= (int) $item['is_done'] === 1 ? 'btn-secondary' : 'btn-success' ?>">
                        <i class="fas <?= (int) $item['is_done'] === 1 ? 'fa-undo' : 'fa-check' ?>"></i>
                        <?= (int) $item['is_done'] === 1 ? 'Undo' : 'Done' ?>
                      </button>
                    </form>
                    <form action="<?= base_url('daily-work-logs/delete/' . $item['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus task ini?')">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-center text-muted p-3">Belum ada task untuk tanggal ini.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
<script>
window.addEventListener('load', function () {
  const listEl = document.getElementById('task-list-sortable');
  const reorderForm = document.getElementById('reorder-form');

  if (!listEl || !reorderForm || typeof Sortable === 'undefined') {
    return;
  }

  Sortable.create(listEl, {
    handle: '.drag-handle',
    animation: 150,
    draggable: '.task-item[data-is-done="0"]'
  });

  reorderForm.addEventListener('submit', function () {
    const oldInputs = reorderForm.querySelectorAll('input[name="ordered_ids[]"]');
    oldInputs.forEach(function (el) { el.remove(); });

    const cards = listEl.querySelectorAll('.task-item[data-is-done="0"]');
    cards.forEach(function (card) {
      const id = card.getAttribute('data-task-id');
      if (!id) {
        return;
      }

      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'ordered_ids[]';
      input.value = id;
      reorderForm.appendChild(input);
    });
  });
});
</script>
