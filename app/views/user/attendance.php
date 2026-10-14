<div class="card reveal">
  <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <h3 class="card-title">My Personal Attendance History</h3>
    
    <!-- Filter Form -->
    <form action="<?= BASE_URL ?>/user/attendance" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
      <input type="date" name="date" class="form-control" value="<?= e($_GET['date'] ?? '') ?>" style="font-size: 0.85rem; padding: 6px 10px;">
      
      <select name="status" class="form-control" style="font-size: 0.85rem; padding: 6px 10px;">
        <option value="">All Statuses</option>
        <option value="Present" <?= ($_GET['status'] ?? '') === 'Present' ? 'selected' : '' ?>>Present</option>
        <option value="Late" <?= ($_GET['status'] ?? '') === 'Late' ? 'selected' : '' ?>>Late</option>
        <option value="Absent" <?= ($_GET['status'] ?? '') === 'Absent' ? 'selected' : '' ?>>Absent</option>
        <option value="Incomplete" <?= ($_GET['status'] ?? '') === 'Incomplete' ? 'selected' : '' ?>>Incomplete</option>
      </select>

      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <?php if (!empty($_GET['date']) || !empty($_GET['status'])): ?>
        <a href="<?= BASE_URL ?>/user/attendance" class="btn btn-outline btn-sm">Clear</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="table-responsive">
    <table class="data-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Check In</th>
          <th>Check Out</th>
          <th>Status</th>
          <th>Recognition Method</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($records)): ?>
          <?php foreach ($records as $r): ?>
            <tr>
              <td><strong><?= e($r['attendance_date']) ?></strong></td>
              <td><?= e($r['check_in'] ?? '--:--') ?></td>
              <td><?= e($r['check_out'] ?? '--:--') ?></td>
              <td>
                <span class="badge badge-<?= strtolower($r['status']) ?>">
                  <?= e($r['status']) ?>
                </span>
              </td>
              <td><?= e($r['recognition_method'] ?? 'Face Recognition') ?></td>
              <td>
                <a href="<?= BASE_URL ?>/corrections/create?attendance_id=<?= e($r['id']) ?>" class="btn btn-outline btn-sm" style="font-size: 0.78rem; padding: 4px 8px;">
                  Request Correction
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align: center; color: var(--c-slate); padding: 24px;">
              No attendance records found for the selected filter.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
