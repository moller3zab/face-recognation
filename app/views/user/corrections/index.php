<div class="card reveal">
  <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h3 class="card-title">My Attendance Correction Requests</h3>
    <a href="<?= BASE_URL ?>/corrections/create" class="btn btn-primary btn-sm">+ New Request</a>
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

  <div class="table-responsive">
    <table class="data-table">
      <thead>
        <tr>
          <th>Req #</th>
          <th>Attendance Date</th>
          <th>Requested Check In</th>
          <th>Requested Check Out</th>
          <th>Reason</th>
          <th>Status</th>
          <th>Admin Note</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($requests)): ?>
          <?php foreach ($requests as $req): ?>
            <tr>
              <td>#<?= e($req['id']) ?></td>
              <td><strong><?= e($req['attendance_date']) ?></strong></td>
              <td><?= e($req['requested_check_in'] ?? '--:--') ?></td>
              <td><?= e($req['requested_check_out'] ?? '--:--') ?></td>
              <td style="max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;"><?= e($req['reason']) ?></td>
              <td>
                <span class="badge badge-<?= strtolower($req['status'] === 'Approved' ? 'present' : ($req['status'] === 'Rejected' ? 'absent' : 'pending')) ?>">
                  <?= e($req['status']) ?>
                </span>
              </td>
              <td><?= e($req['admin_note'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" style="text-align: center; color: var(--c-slate); padding: 24px;">
              No correction requests submitted yet.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
