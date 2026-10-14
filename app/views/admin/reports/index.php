<div class="card reveal">
  <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <h3 class="card-title">Attendance Reports & Analytics</h3>

    <!-- Export CSV Button -->
    <a href="<?= BASE_URL ?>/admin/reports/export-csv?<?= http_build_query($_GET) ?>" class="btn btn-primary btn-sm">
      Export to CSV (Excel UTF-8)
    </a>
  </div>

  <!-- Filter Toolbar Form -->
  <form action="<?= BASE_URL ?>/admin/reports" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 20px; background: var(--c-sand-light); padding: 16px; border-radius: var(--radius-md);">
    <div>
      <label class="form-label" style="font-size: 0.78rem;">Report Type</label>
      <select name="type" class="form-control" style="font-size: 0.85rem; padding: 6px 10px;" onchange="this.form.submit()">
        <option value="daily" <?= ($reportType === 'daily') ? 'selected' : '' ?>>Daily Report</option>
        <option value="weekly" <?= ($reportType === 'weekly') ? 'selected' : '' ?>>Weekly Report</option>
        <option value="monthly" <?= ($reportType === 'monthly') ? 'selected' : '' ?>>Monthly Report</option>
        <option value="late" <?= ($reportType === 'late') ? 'selected' : '' ?>>Late Users Report</option>
        <option value="absent" <?= ($reportType === 'absent') ? 'selected' : '' ?>>Absent Users Report</option>
      </select>
    </div>

    <div>
      <label class="form-label" style="font-size: 0.78rem;">Date From</label>
      <input type="date" name="date_from" class="form-control" value="<?= e($filters['date_from'] ?? '') ?>" style="font-size: 0.85rem; padding: 6px 10px;">
    </div>

    <div>
      <label class="form-label" style="font-size: 0.78rem;">Date To</label>
      <input type="date" name="date_to" class="form-control" value="<?= e($filters['date_to'] ?? '') ?>" style="font-size: 0.85rem; padding: 6px 10px;">
    </div>

    <div>
      <label class="form-label" style="font-size: 0.78rem;">User</label>
      <select name="user_id" class="form-control" style="font-size: 0.85rem; padding: 6px 10px;">
        <option value="">All Users</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= e($u->id) ?>" <?= ((int)($filters['user_id'] ?? 0) === $u->id) ? 'selected' : '' ?>>
            <?= e($u->name) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="display: flex; align-items: flex-end; gap: 8px;">
      <button type="submit" class="btn btn-secondary btn-sm" style="height: 38px;">Generate</button>
      <a href="<?= BASE_URL ?>/admin/reports" class="btn btn-outline btn-sm" style="height: 38px; display: inline-flex; align-items: center;">Reset</a>
    </div>
  </form>

  <!-- Report Data Table -->
  <div class="table-responsive">
    <table class="data-table">
      <thead>
        <tr>
          <th>User</th>
          <th>Identifier</th>
          <th>Department</th>
          <th>Date</th>
          <th>Check In</th>
          <th>Check Out</th>
          <th>Status</th>
          <th>Recognition Method</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($records)): ?>
          <?php foreach ($records as $r): ?>
            <tr>
              <td><strong><?= e($r['user_name']) ?></strong></td>
              <td><code><?= e($r['identifier']) ?></code></td>
              <td><?= e($r['department'] ?? 'General') ?></td>
              <td><?= e($r['attendance_date']) ?></td>
              <td><?= e($r['check_in'] ?? '--:--') ?></td>
              <td><?= e($r['check_out'] ?? '--:--') ?></td>
              <td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= e($r['status']) ?></span></td>
              <td><?= e($r['recognition_method'] ?? 'Face Recognition') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" style="text-align: center; color: var(--c-slate); padding: 24px;">
              No report records found for the selected filter parameters.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
