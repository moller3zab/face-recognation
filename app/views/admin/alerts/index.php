<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
  <!-- Left: System Alerts List -->
  <div class="card reveal">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
      <h3 class="card-title">System Alerts Log</h3>

      <!-- Filter Form -->
      <form action="<?= BASE_URL ?>/admin/alerts" method="GET" style="display: flex; gap: 8px;">
        <select name="type" class="form-control" style="font-size: 0.85rem; padding: 6px 10px;">
          <option value="">All Types</option>
          <option value="Late" <?= ($_GET['type'] ?? '') === 'Late' ? 'selected' : '' ?>>Late</option>
          <option value="Absent" <?= ($_GET['type'] ?? '') === 'Absent' ? 'selected' : '' ?>>Absent</option>
          <option value="Correction Request" <?= ($_GET['type'] ?? '') === 'Correction Request' ? 'selected' : '' ?>>Correction Req</option>
          <option value="System" <?= ($_GET['type'] ?? '') === 'System' ? 'selected' : '' ?>>System</option>
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
            <th>Target User</th>
            <th>Type</th>
            <th>Title</th>
            <th>Message</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($alerts)): ?>
            <?php foreach ($alerts as $a): ?>
              <tr>
                <td><strong><?= e($a['user_name']) ?></strong> (<code><?= e($a['identifier']) ?></code>)</td>
                <td><span class="badge badge-pending"><?= e($a['type']) ?></span></td>
                <td><?= e($a['title']) ?></td>
                <td style="max-width: 200px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="<?= e($a['message']) ?>"><?= e($a['message']) ?></td>
                <td><?= e($a['created_at']) ?></td>
                <td>
                  <form action="<?= BASE_URL ?>/admin/alerts/delete" method="POST" style="display:inline;">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="alert_id" value="<?= e($a['id']) ?>">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this alert?');">Del</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" style="text-align: center; color: var(--c-slate);">No alerts recorded.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Right: Broadcast / Send Notification Form -->
  <div class="card reveal">
    <div class="card-header">
      <h3 class="card-title">Send System Notification</h3>
    </div>

    <form action="<?= BASE_URL ?>/admin/alerts/send" method="POST">
      <?= \App\Core\Csrf::field() ?>

      <div class="form-group">
        <label class="form-label" for="user_id">Target Recipient</label>
        <select id="user_id" name="user_id" class="form-control">
          <option value="0">Broadcast to ALL Active Users</option>
          <?php foreach ($users as $u): ?>
            <option value="<?= e($u->id) ?>"><?= e($u->name) ?> (<?= e($u->identifier) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" for="title">Notification Title</label>
        <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Schedule Update" required>
      </div>

      <div class="form-group">
        <label class="form-label" for="message">Message Content</label>
        <textarea id="message" name="message" rows="4" class="form-control" placeholder="Write your announcement or alert message here..." required></textarea>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%;">Send Notification</button>
    </form>
  </div>
</div>
