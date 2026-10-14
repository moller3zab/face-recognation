<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
  <!-- User Profile Card -->
  <div class="card reveal">
    <div style="text-align: center; margin-bottom: 20px;">
      <?php if (!empty($user->photo)): ?>
        <img src="<?= BASE_URL . '/' . e($user->photo) ?>" alt="Profile" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin: 0 auto 12px;">
      <?php else: ?>
        <div class="user-avatar" style="width: 80px; height: 80px; font-size: 2rem; margin: 0 auto 12px;">
          <?= e(strtoupper(substr($user->name, 0, 2))) ?>
        </div>
      <?php endif; ?>

      <h3><?= e($user->name) ?></h3>
      <a href="<?= BASE_URL ?>/admin/users/edit?id=<?= e($user->id) ?>" class="btn btn-outline btn-sm">Edit Account</a>
      <p style="color: var(--c-slate); font-size: 0.88rem;"><?= e($user->department ?? 'General Department') ?></p>
      <span class="badge badge-<?= $user->status === 'active' ? 'present' : 'absent' ?>" style="margin-top: 8px;"><?= e(ucfirst($user->status)) ?> Account</span>
    </div>

    <div style="border-top: 1px solid var(--c-sand-light); padding-top: 16px; font-size: 0.9rem;">
      <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
        <span style="color: var(--c-slate);">Identifier:</span>
        <strong><code><?= e($user->identifier) ?></code></strong>
      </div>
      <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
        <span style="color: var(--c-slate);">Email:</span>
        <strong><?= e($user->email) ?></strong>
      </div>
      <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
        <span style="color: var(--c-slate);">Role:</span>
        <span class="badge badge-present"><?= e(ucfirst($user->role)) ?></span>
      </div>
      <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
        <span style="color: var(--c-slate);">Biometric Status:</span>
        <?php if ($hasFaceData): ?>
          <span class="badge badge-present">Face Registered (128-d)</span>
        <?php else: ?>
          <span class="badge badge-pending">Unregistered</span>
        <?php endif; ?>
      </div>

      <?php if ($hasFaceData): ?>
        <form action="<?= BASE_URL ?>/admin/users/reset-face" method="POST" style="margin-top: 16px;">
          <?= \App\Core\Csrf::field() ?>
          <input type="hidden" name="user_id" value="<?= e($user->id) ?>">
          <button type="submit" class="btn btn-danger btn-sm" style="width: 100%;" onclick="return confirm('Are you sure you want to reset this user\'s face recognition data? They will be required to re-enroll.');">
            Reset Face Biometrics
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <!-- User Attendance Logs Card -->
  <div class="card reveal">
    <div class="card-header">
      <h3 class="card-title">Recent Attendance History</h3>
      <a href="<?= BASE_URL ?>/admin/users" class="btn btn-secondary btn-sm">Back to Users</a>
    </div>

    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Status</th>
            <th>Method</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($attendanceLogs)): ?>
            <?php foreach ($attendanceLogs as $log): ?>
              <tr>
                <td><?= e($log['attendance_date']) ?></td>
                <td><?= e($log['check_in'] ?? '-') ?></td>
                <td><?= e($log['check_out'] ?? '-') ?></td>
                <td><span class="badge badge-<?= strtolower($log['status']) ?>"><?= e($log['status']) ?></span></td>
                <td><?= e($log['recognition_method']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align: center; color: var(--c-slate);">No attendance history recorded.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
