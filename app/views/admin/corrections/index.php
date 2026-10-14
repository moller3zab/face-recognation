<div class="card reveal">
  <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <h3 class="card-title">Manage Attendance Correction Requests</h3>
    
    <!-- Filter -->
    <form action="<?= BASE_URL ?>/admin/corrections" method="GET" style="display: flex; gap: 10px;">
      <select name="status" class="form-control" style="font-size: 0.85rem; padding: 6px 10px;">
        <option value="">All Statuses</option>
        <option value="Pending" <?= ($_GET['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending Review</option>
        <option value="Approved" <?= ($_GET['status'] ?? '') === 'Approved' ? 'selected' : '' ?>>Approved</option>
        <option value="Rejected" <?= ($_GET['status'] ?? '') === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
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

  <div class="table-responsive">
    <table class="data-table">
      <thead>
        <tr>
          <th>Req #</th>
          <th>User</th>
          <th>Identifier</th>
          <th>Date</th>
          <th>Requested Check In</th>
          <th>Requested Check Out</th>
          <th>Reason</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($requests)): ?>
          <?php foreach ($requests as $req): ?>
            <tr>
              <td>#<?= e($req['id']) ?></td>
              <td><strong><?= e($req['user_name']) ?></strong></td>
              <td><code><?= e($req['identifier']) ?></code></td>
              <td><?= e($req['attendance_date']) ?></td>
              <td><?= e($req['requested_check_in'] ?? '--:--') ?></td>
              <td><?= e($req['requested_check_out'] ?? '--:--') ?></td>
              <td style="max-width: 200px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="<?= e($req['reason']) ?>">
                <?= e($req['reason']) ?>
              </td>
              <td>
                <span class="badge badge-<?= strtolower($req['status'] === 'Approved' ? 'present' : ($req['status'] === 'Rejected' ? 'absent' : 'pending')) ?>">
                  <?= e($req['status']) ?>
                </span>
              </td>
              <td>
                <?php if ($req['status'] === 'Pending'): ?>
                  <div style="display: flex; gap: 6px;">
                    <!-- Approve Form -->
                    <form action="<?= BASE_URL ?>/admin/corrections/review" method="POST" style="display: inline;">
                      <?= \App\Core\Csrf::field() ?>
                      <input type="hidden" name="request_id" value="<?= e($req['id']) ?>">
                      <input type="hidden" name="status" value="Approved">
                      <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.75rem; padding: 4px 8px;" onclick="return confirm('Approve this attendance correction request?');">
                        Approve
                      </button>
                    </form>

                    <!-- Reject Form -->
                    <form action="<?= BASE_URL ?>/admin/corrections/review" method="POST" style="display: inline;">
                      <?= \App\Core\Csrf::field() ?>
                      <input type="hidden" name="request_id" value="<?= e($req['id']) ?>">
                      <input type="hidden" name="status" value="Rejected">
                      <button type="submit" class="btn btn-danger btn-sm" style="font-size: 0.75rem; padding: 4px 8px;" onclick="return confirm('Reject this attendance correction request?');">
                        Reject
                      </button>
                    </form>
                  </div>
                <?php else: ?>
                  <span style="font-size: 0.82rem; color: var(--c-slate);">Reviewed</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" style="text-align: center; color: var(--c-slate); padding: 24px;">
              No correction requests found matching filter.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
