<link rel="stylesheet" href="<?= base_url('assets/modules/datatables/datatables.min.css') ?>">

<style>
  .tickets-board {
    --tb-border: #e7edf3;
    --tb-bg: #f6f9fc;
    --tb-text: #1f2937;
    --tb-muted: #6b7280;
  }
  .tickets-board .board-card {
    border: 1px solid var(--tb-border);
    border-radius: 14px;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    background: #fff;
    overflow: hidden;
  }
  .tickets-board .board-head {
    padding: 16px 18px;
    border-bottom: 1px solid var(--tb-border);
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
  }
  .tickets-board .board-title {
    margin: 0;
    font-size: 1.08rem;
    font-weight: 700;
    color: var(--tb-text);
  }
  .tickets-board .board-subtitle {
    margin-top: 6px;
    color: var(--tb-muted);
    font-size: 12px;
  }
  .tickets-board .head-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
  }
  .tickets-board .board-body {
    padding: 16px 18px;
  }
  .tickets-board .stats-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 14px;
  }
  .tickets-board .stat-item {
    border: 1px solid var(--tb-border);
    border-radius: 10px;
    padding: 10px 12px;
    background: var(--tb-bg);
  }
  .tickets-board .stat-label {
    font-size: 12px;
    color: var(--tb-muted);
  }
  .tickets-board .stat-value {
    margin-top: 4px;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--tb-text);
  }
  .tickets-board .toolbar {
    display: grid;
    grid-template-columns: 1fr 220px;
    gap: 10px;
    margin-bottom: 12px;
  }
  .tickets-board .toolbar .form-control {
    border-radius: 10px;
    min-height: 40px;
    border-color: #dce4ee;
  }
  .tickets-board #table-logs {
    border-collapse: separate;
    border-spacing: 0;
  }
  .tickets-board #table-logs thead th {
    background: #f8fbff;
    color: #4b5563;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 1px solid var(--tb-border);
  }
  .tickets-board #table-logs tbody td {
    vertical-align: middle;
  }
  .tickets-board #table-logs tbody tr:hover {
    background: #fbfdff;
  }
  .tickets-board .btn-group .btn {
    border-radius: 8px;
    margin-right: 4px;
  }
  .tickets-board .btn-group .btn:last-child {
    margin-right: 0;
  }
  .tickets-board .dataTables_info,
  .tickets-board .dataTables_paginate {
    margin-top: 8px;
  }
  @media (max-width: 991.98px) {
    .tickets-board .stats-grid {
      grid-template-columns: 1fr;
    }
    .tickets-board .toolbar {
      grid-template-columns: 1fr;
    }
    .tickets-board .head-actions {
      justify-content: flex-start;
      margin-top: 10px;
    }
  }
</style>

<div class="row tickets-board">
  <div class="col-12">
    <div class="board-card">
      <div class="board-head">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h2 class="board-title">Ticketing Board</h2>
            <div class="board-subtitle">Pantau progres ticket, lakukan close/reopen, dan cari data lebih cepat.</div>
          </div>
          <div class="col-md-4 head-actions">
          <?php if (auth()->user()->can('logs.create')): ?>
            <a href="<?= base_url('maintenance-logs/create') ?>" class="btn btn-primary">
              <i class="fas fa-plus"></i> Ticket Baru
            </a>
          <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="board-body">
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-label">Total Ticket</div>
            <div class="stat-value" id="stat-total">0</div>
          </div>
          <div class="stat-item">
            <div class="stat-label">Sedang Berjalan</div>
            <div class="stat-value" id="stat-open">0</div>
          </div>
          <div class="stat-item">
            <div class="stat-label">Selesai</div>
            <div class="stat-value" id="stat-completed">0</div>
          </div>
        </div>

        <div class="toolbar">
          <input type="text" id="ticket-search" class="form-control" placeholder="Cari judul, aplikasi, teknisi...">
          <select id="ticket-status-filter" class="form-control">
            <option value="">Semua Status</option>
            <option value="Pending">Pending</option>
            <option value="On Progress">On Progress</option>
            <option value="Testing">Testing</option>
            <option value="Completed">Completed</option>
          </select>
        </div>

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
                <th>Selesai</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
window.addEventListener('load', function () {
  const canEdit = <?= auth()->user()->can('logs.edit') ? 'true' : 'false' ?>;
  const canDelete = <?= auth()->user()->can('logs.delete') ? 'true' : 'false' ?>;
  const canReview = <?= auth()->user()->can('logs.review') ? 'true' : 'false' ?>;
  const csrfTokenName = <?= json_encode(csrf_token()) ?>;
  const csrfHash = <?= json_encode(csrf_hash()) ?>;

  function escapeHtml(value) {
    return $('<div>').text(value == null ? '' : String(value)).html();
  }

  function statusBadge(status) {
    switch (status) {
      case 'Pending':
        return 'badge-warning';
      case 'On Progress':
        return 'badge-info';
      case 'Testing':
        return 'badge-primary';
      case 'Completed':
        return 'badge-success';
      default:
        return 'badge-secondary';
    }
  }

  function actionButtons(row) {
    let html = '<div class="btn-group" role="group">';

    html += '<a href="' + row.show_url + '" class="btn btn-sm btn-primary" title="Detail" data-toggle="tooltip">'
      + '<i class="fas fa-eye"></i>'
      + '</a>';

    if (canEdit) {
      html += '<a href="' + row.edit_url + '" class="btn btn-sm btn-warning" title="Edit" data-toggle="tooltip">'
        + '<i class="fas fa-edit"></i>'
        + '</a>';
    }

    if (canReview && row.status !== 'Completed') {
      html += '<form action="' + row.close_url + '" method="post" class="d-inline">'
        + '<input type="hidden" name="' + csrfTokenName + '" value="' + csrfHash + '">'
        + '<button type="submit" class="btn btn-sm btn-success" title="Close Ticket" data-toggle="tooltip" '
        + 'onclick="return confirm(\'Close ticket sekarang dengan waktu saat ini?\')">'
        + '<i class="fas fa-check-circle"></i>'
        + '</button>'
        + '</form>';
    }

    if (canReview && row.status === 'Completed') {
      html += '<form action="' + row.reopen_url + '" method="post" class="d-inline">'
        + '<input type="hidden" name="' + csrfTokenName + '" value="' + csrfHash + '">'
        + '<input type="hidden" name="status" value="On Progress">'
        + '<button type="submit" class="btn btn-sm btn-secondary" title="Reopen Ticket" data-toggle="tooltip" '
        + 'onclick="return confirm(\'Buka kembali ticket ini?\')">'
        + '<i class="fas fa-undo"></i>'
        + '</button>'
        + '</form>';
    }

    if (canDelete) {
      const formId = 'delete-log-' + row.id;
      html += '<a href="javascript:void(0)" class="btn btn-sm btn-danger" title="Hapus" data-toggle="tooltip" '
        + 'onclick="if(confirm(\'Yakin ingin menghapus log ini?\')){document.getElementById(\'' + formId + '\').submit();}">'
        + '<i class="fas fa-trash"></i>'
        + '</a>';
      html += '<form id="' + formId + '" action="' + row.delete_url + '" method="post" class="d-none">'
        + '<input type="hidden" name="' + csrfTokenName + '" value="' + csrfHash + '">'
        + '</form>';
    }

    html += '</div>';

    return html;
  }

  function updateStats(rows) {
    let openCount = 0;
    let completedCount = 0;

    rows.forEach(function (row) {
      if (row.status === 'Completed') {
        completedCount += 1;
      } else {
        openCount += 1;
      }
    });

    document.getElementById('stat-total').textContent = rows.length;
    document.getElementById('stat-open').textContent = openCount;
    document.getElementById('stat-completed').textContent = completedCount;
  }

  function registerStatusFilter() {
    if (!$.fn.dataTable || !$.fn.dataTable.ext || !$.fn.dataTable.ext.search) {
      return;
    }

    if (window.__ticketStatusFilterRegistered) {
      return;
    }

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData) {
      if (settings.nTable.id !== 'table-logs') {
        return true;
      }

      const statusFilterEl = document.getElementById('ticket-status-filter');
      const statusFilter = statusFilterEl ? statusFilterEl.value : '';
      if (!statusFilter) {
        return true;
      }

      // rowData tersedia di beberapa versi DataTables; fallback ke kolom status dari data tabel.
      const rowStatus = rowData && rowData.status ? rowData.status : (data && data[6] ? String(data[6]).replace(/<[^>]*>/g, '').trim() : '');

      return rowStatus === statusFilter;
    });

    window.__ticketStatusFilterRegistered = true;
  }

  function initTable() {
    registerStatusFilter();

    const table = $('#table-logs').DataTable({
      ajax: {
        url: <?= json_encode(base_url('maintenance-logs/data')) ?>,
        dataSrc: function (json) {
          const rows = Array.isArray(json.data) ? json.data : [];
          updateStats(rows);
          return rows;
        }
      },
      dom: "rt<'row align-items-center mt-3'<'col-md-5'i><'col-md-7'p>>",
      order: [[1, 'desc']],
      pageLength: 10,
      responsive: true,
      language: {
        emptyTable: 'Belum ada data ticket.',
        processing: 'Memuat data...'
      },
      columns: [
        {
          data: null,
          className: 'text-center',
          orderable: false,
          searchable: false,
          render: function (data, type, row, meta) {
            return meta.row + 1;
          }
        },
        {
          data: 'maintenance_date_raw',
          render: function (data, type, row) {
            if (type === 'display' || type === 'filter') {
              return escapeHtml(row.maintenance_date || '-');
            }

            return data || '';
          }
        },
        {
          data: 'app_name',
          render: function (data) {
            return '<span class="badge badge-light">' + escapeHtml(data || '-') + '</span>';
          }
        },
        {
          data: 'category_name',
          render: function (data) {
            return '<span class="badge badge-info">' + escapeHtml(data || '-') + '</span>';
          }
        },
        {
          data: 'title',
          render: function (data, type, row) {
            return '<a href="' + row.show_url + '" class="text-primary">' + escapeHtml(data || '-') + '</a>';
          }
        },
        {
          data: 'technician_name',
          render: function (data) {
            return escapeHtml(data || '-');
          }
        },
        {
          data: 'status',
          render: function (data) {
            const safeStatus = escapeHtml(data || '-');
            return '<span class="badge ' + statusBadge(data) + '">' + safeStatus + '</span>';
          }
        },
        {
          data: null,
          orderable: false,
          searchable: false,
          render: function (data, type, row) {
            if (row.has_downtime) {
              const label = row.downtime_duration ? escapeHtml(row.downtime_duration + ' menit') : 'Ya';
              return '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> ' + label + '</span>';
            }

            return '<span class="badge badge-success">Tidak</span>';
          }
        },
        {
          data: 'closed_at',
          render: function (data, type) {
            if (!data) {
              return '<span class="text-muted">-</span>';
            }

            if (type === 'sort' || type === 'type') {
              return data;
            }

            const d = new Date(data.replace(' ', 'T'));
            const pad = n => String(n).padStart(2, '0');

            return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear()
              + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
          }
        },
        {
          data: null,
          className: 'text-center',
          orderable: false,
          searchable: false,
          render: function (data, type, row) {
            return actionButtons(row);
          }
        }
      ],
      drawCallback: function () {
        $('[data-toggle="tooltip"]').tooltip();
      }
    });

    $('#ticket-search').on('keyup', function () {
      table.search(this.value).draw();
    });

    $('#ticket-status-filter').on('change', function () {
      table.draw();
    });
  }

  if ($.fn.DataTable) {
    initTable();
    return;
  }

  const dataTablesScript = document.createElement('script');
  dataTablesScript.src = <?= json_encode(base_url('assets/modules/datatables/datatables.min.js')) ?>;
  dataTablesScript.onload = initTable;
  document.body.appendChild(dataTablesScript);
});
</script>
