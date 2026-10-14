<div style="display: flex; flex-direction: column; gap: 24px;">
  <!-- Admin Attendance Actions & Filters -->
  <div class="card reveal">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
      <h3 class="card-title">System Attendance Master Log</h3>
      
      <!-- Generate Absences Form -->
      <form action="<?= BASE_URL ?>/admin/attendance/generate-absences" method="POST" style="display: flex; gap: 10px; align-items: center;">
        <?= \App\Core\Csrf::field() ?>
        <input type="date" name="target_date" class="form-control" value="<?= date('Y-m-d') ?>" required style="font-size: 0.85rem; padding: 6px 10px;">
        <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Generate absence records for all active users without check-in on this date?');">
          Generate Absences
        </button>
      </form>
    </div>

    <?php if (!empty($success)): ?>
      <div style="padding: 12px; background: var(--c-status-present-bg); color: var(--c-status-present); border-radius: var(--radius-md); font-size: 0.88rem; margin-bottom: 16px; font-weight: 600;">
        <?= e($success) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div style="padding: 12px; background: var(--c-status-absent-bg); color: var(--c-status-absent); border-radius: var(--radius-md); font-size: 0.88rem; margin-bottom: 16px; font-weight: 600;">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <!-- Filter Toolbar -->
    <form action="<?= BASE_URL ?>/admin/attendance" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px;">
      <input type="date" name="date" class="form-control" value="<?= e($_GET['date'] ?? '') ?>" placeholder="Date" style="font-size: 0.85rem; padding: 6px 10px; max-width: 160px;">
      
      <select name="status" class="form-control" style="font-size: 0.85rem; padding: 6px 10px; max-width: 150px;">
        <option value="">All Statuses</option>
        <option value="Present" <?= ($_GET['status'] ?? '') === 'Present' ? 'selected' : '' ?>>Present</option>
        <option value="Late" <?= ($_GET['status'] ?? '') === 'Late' ? 'selected' : '' ?>>Late</option>
        <option value="Absent" <?= ($_GET['status'] ?? '') === 'Absent' ? 'selected' : '' ?>>Absent</option>
        <option value="Incomplete" <?= ($_GET['status'] ?? '') === 'Incomplete' ? 'selected' : '' ?>>Incomplete</option>
      </select>

      <button type="submit" class="btn btn-secondary btn-sm">Search Logs</button>
      <?php if (!empty($_GET['date']) || !empty($_GET['status'])): ?>
        <a href="<?= BASE_URL ?>/admin/attendance" class="btn btn-outline btn-sm">Reset</a>
      <?php endif; ?>
    </form>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Department</th>
            <th>Identifier</th>
            <th>Date</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Status</th>
            <th>Method</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($records)): ?>
            <?php foreach ($records as $r): ?>
              <tr>
                <td>
                  <strong><?= e($r['user_name']) ?></strong>
                </td>
                <td><?= e($r['department'] ?? 'General') ?></td>
                <td><code><?= e($r['identifier']) ?></code></td>
                <td><?= e($r['attendance_date']) ?></td>
                <td><?= e($r['check_in'] ?? '--:--') ?></td>
                <td><?= e($r['check_out'] ?? '--:--') ?></td>
                <td>
                  <span class="badge badge-<?= strtolower($r['status']) ?>">
                    <?= e($r['status']) ?>
                  </span>
                </td>
                <td><?= e($r['recognition_method'] ?? 'Face Recognition') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" style="text-align: center; color: var(--c-slate); padding: 24px;">
                No attendance logs found matching the filter criteria.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
