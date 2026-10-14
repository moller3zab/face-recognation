<div class="card reveal">
  <div class="card-header">
    <h3 class="card-title">Registered Users Directory</h3>
    <div style="display: flex; gap: 12px; align-items: center;">
      <a href="<?= BASE_URL ?>/admin/users/create" class="btn btn-primary btn-sm">+ Add New User</a>
      <button class="btn btn-secondary btn-sm" onclick="document.getElementById('import-csv-modal').classList.add('active');">Import HR CSV</button>
    </div>
  </div>

  <?php if (!empty($success)): ?>
    <div style="padding: 12px; background: var(--c-status-present-bg); color: var(--c-status-present); border-radius: var(--radius-md); font-size: 0.88rem; margin-bottom: 20px; font-weight: 600;">
      <?= e($success) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div style="padding: 12px; background: var(--c-status-absent-bg); color: var(--c-status-absent); border-radius: var(--radius-md); font-size: 0.88rem; margin-bottom: 20px; font-weight: 600;">
      <?= e($error) ?>
    </div>
  <?php endif; ?>

  <!-- Search & Filters -->
  <form action="<?= BASE_URL ?>/admin/users" method="GET" style="display: flex; gap: 12px; margin-bottom: 20px;">
    <input type="text" name="search" class="form-control" placeholder="Search name, email, ID..." value="<?= e($filters['search'] ?? '') ?>" style="width: 260px;">
    <select name="role" class="form-control" style="width: 140px;">
      <option value="">All Roles</option>
      <option value="user" <?= ($filters['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
      <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>
    <select name="status" class="form-control" style="width: 140px;">
      <option value="">All Statuses</option>
      <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
      <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
    </select>
    <button type="submit" class="btn btn-secondary">Filter</button>
  </form>

  <div class="table-responsive">
    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>User Name</th>
          <th>Email</th>
          <th>Identifier</th>
          <th>Department</th>
          <th>Role</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($users)): ?>
          <?php foreach ($users as $u): ?>
            <tr>
              <td>#<?= e($u->id) ?></td>
              <td><strong><?= e($u->name) ?></strong></td>
              <td><?= e($u->email) ?></td>
              <td><code><?= e($u->identifier) ?></code></td>
              <td><?= e($u->department ?? 'General') ?></td>
              <td><span class="badge badge-<?= $u->role === 'admin' ? 'pending' : 'present' ?>"><?= e($u->role) ?></span></td>
              <td><span class="badge badge-<?= $u->status === 'active' ? 'present' : 'absent' ?>"><?= e($u->status) ?></span></td>
              <td style="display: flex; gap: 6px;">
                <a href="<?= BASE_URL ?>/admin/users/edit?id=<?= e($u->id) ?>" class="btn btn-outline btn-sm">Edit</a>
                <a href="<?= BASE_URL ?>/admin/users/show?id=<?= e($u->id) ?>" class="btn btn-secondary btn-sm">View</a>
                <form action="<?= BASE_URL ?>/admin/users/delete" method="POST" style="display:inline;">
                  <?= \App\Core\Csrf::field() ?>
                  <input type="hidden" name="id" value="<?= e($u->id) ?>">
                  <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Soft-delete this user account?');">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" style="text-align: center; color: var(--c-slate);">No user records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- CSV Import Modal -->
<div class="modal-overlay" id="import-csv-modal">
  <div class="modal-box">
    <div class="modal-header">
      <h3>Import HR CSV Users</h3>
      <button class="modal-close btn btn-secondary btn-sm" onclick="document.getElementById('import-csv-modal').classList.remove('active');">✕</button>
    </div>
    <form action="<?= BASE_URL ?>/admin/users/import-csv" method="POST" enctype="multipart/form-data">
      <?= \App\Core\Csrf::field() ?>
      <p style="color: var(--c-slate); font-size: 0.9rem; margin-bottom: 16px;">
        Upload a CSV with <code>name, email, identifier, department, phone</code>. Imported accounts stay inactive until you verify their details, set an initial password, and activate them.
      </p>
      <div class="form-group">
        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="modal-close btn btn-secondary" onclick="document.getElementById('import-csv-modal').classList.remove('active');">Cancel</button>
        <button type="submit" class="btn btn-primary">Start Import</button>
      </div>
    </form>
  </div>
</div>

<script>
  if (new URLSearchParams(window.location.search).get('import') === '1') {
    document.getElementById('import-csv-modal')?.classList.add('active');
  }
</script>
