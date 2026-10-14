<div class="card reveal">
  <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <h3 class="card-title">My Alerts & System Notifications</h3>

    <div style="display: flex; gap: 12px; align-items: center;">
      <!-- Mark All Read Form -->
      <form action="<?= BASE_URL ?>/alerts/mark-all-read" method="POST">
        <?= \App\Core\Csrf::field() ?>
        <button type="submit" class="btn btn-secondary btn-sm">Mark All as Read</button>
      </form>

      <!-- Filter Form -->
      <form action="<?= BASE_URL ?>/alerts" method="GET" style="display: flex; gap: 8px;">
        <select name="type" class="form-control" style="font-size: 0.85rem; padding: 6px 10px;">
          <option value="">All Types</option>
          <option value="Late" <?= ($_GET['type'] ?? '') === 'Late' ? 'selected' : '' ?>>Late Arrival</option>
          <option value="Absent" <?= ($_GET['type'] ?? '') === 'Absent' ? 'selected' : '' ?>>Absence</option>
          <option value="Correction Approved" <?= ($_GET['type'] ?? '') === 'Correction Approved' ? 'selected' : '' ?>>Correction Approved</option>
          <option value="Correction Rejected" <?= ($_GET['type'] ?? '') === 'Correction Rejected' ? 'selected' : '' ?>>Correction Rejected</option>
          <option value="System" <?= ($_GET['type'] ?? '') === 'System' ? 'selected' : '' ?>>System Notification</option>
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Filter</button>
      </form>
    </div>
  </div>

  <?php if (!empty($success)): ?>
    <div style="padding: 12px; background: var(--c-status-present-bg); color: var(--c-status-present); border-radius: var(--radius-md); font-size: 0.88rem; margin-bottom: 16px; font-weight: 600;">
      <?= e($success) ?>
    </div>
  <?php endif; ?>

  <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 16px;">
    <?php if (!empty($alerts)): ?>
      <?php foreach ($alerts as $a): ?>
        <div style="padding: 16px; border-radius: var(--radius-md); background: <?= !empty($a['is_read']) ? 'var(--c-sand-light)' : 'var(--c-paper)' ?>; border-left: 4px solid <?= str_contains($a['type'], 'Approved') || $a['type'] === 'Present' ? 'var(--c-status-present)' : (str_contains($a['type'], 'Rejected') || $a['type'] === 'Absent' ? 'var(--c-status-absent)' : 'var(--c-accent)') ?>; display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
          <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
              <span class="badge badge-<?= str_contains($a['type'], 'Approved') ? 'present' : (str_contains($a['type'], 'Rejected') || $a['type'] === 'Absent' ? 'absent' : 'pending') ?>">
                <?= e($a['type']) ?>
              </span>
              <h4 style="margin: 0; font-size: 0.98rem; font-weight: 700;"><?= e($a['title']) ?></h4>
              <?php if (empty($a['is_read'])): ?>
                <span style="font-size: 0.7rem; background: var(--c-accent); color: #fff; padding: 2px 8px; border-radius: 10px; font-weight: 700;">NEW</span>
              <?php endif; ?>
            </div>
            <p style="margin: 0; font-size: 0.88rem; color: var(--c-slate); line-height: 1.5;"><?= e($a['message']) ?></p>
            <span style="font-size: 0.78rem; color: var(--c-stone); margin-top: 8px; display: inline-block;">
              <?= e($a['created_at']) ?>
            </span>
          </div>

          <?php if (empty($a['is_read'])): ?>
            <form action="<?= BASE_URL ?>/alerts/mark-read" method="POST">
              <?= \App\Core\Csrf::field() ?>
              <input type="hidden" name="alert_id" value="<?= e($a['id']) ?>">
              <button type="submit" class="btn btn-outline btn-sm" style="font-size: 0.78rem; padding: 4px 10px;">Mark Read</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div style="text-align: center; padding: 32px; color: var(--c-slate);">
        No notifications found.
      </div>
    <?php endif; ?>
  </div>
</div>
