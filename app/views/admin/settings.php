<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
  <!-- Settings Form Card -->
  <div class="card reveal">
    <div class="card-header">
      <h3 class="card-title">Attendance Business Rules & System Config</h3>
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

    <form action="<?= BASE_URL ?>/admin/settings/update" method="POST">
      <?= \App\Core\Csrf::field() ?>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label" for="working_start">Working Start Time</label>
          <input type="time" id="working_start" name="working_start" class="form-control" value="<?= e($settings['working_start'] ?? '09:00') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="working_end">Working End Time</label>
          <input type="time" id="working_end" name="working_end" class="form-control" value="<?= e($settings['working_end'] ?? '17:00') ?>" required>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label" for="late_threshold_minutes">Late Grace Period (Minutes)</label>
          <input type="number" id="late_threshold_minutes" name="late_threshold_minutes" class="form-control" value="<?= e($settings['late_threshold_minutes'] ?? '15') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="recognition_threshold">Recognition Distance Threshold ($D$)</label>
          <input type="number" step="0.05" id="recognition_threshold" name="recognition_threshold" class="form-control" value="<?= e($settings['recognition_threshold'] ?? '0.60') ?>" required>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label" for="default_camera">Default Camera Device</label>
          <select id="default_camera" name="default_camera" class="form-control">
            <option value="user" <?= ($settings['default_camera'] ?? '') === 'user' ? 'selected' : '' ?>>User / Front Camera</option>
            <option value="environment" <?= ($settings['default_camera'] ?? '') === 'environment' ? 'selected' : '' ?>>Environment / Back Camera</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="camera_resolution">Camera Video Resolution</label>
          <select id="camera_resolution" name="camera_resolution" class="form-control">
            <option value="640x480" <?= ($settings['camera_resolution'] ?? '') === '640x480' ? 'selected' : '' ?>>640x480 (Recommended Standard)</option>
            <option value="1280x720" <?= ($settings['camera_resolution'] ?? '') === '1280x720' ? 'selected' : '' ?>>1280x720 (HD 720p)</option>
          </select>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="margin-top: 12px;">Save System Settings</button>
    </form>
  </div>

  <!-- Holidays Management Card -->
  <div class="card reveal">
    <div class="card-header">
      <h3 class="card-title">Official Holidays</h3>
    </div>

    <form action="<?= BASE_URL ?>/admin/settings/holidays/add" method="POST" style="margin-bottom: 20px;">
      <?= \App\Core\Csrf::field() ?>
      <div class="form-group">
        <label class="form-label">Holiday Date</label>
        <input type="date" name="date" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">Holiday Name</label>
        <input type="text" name="name" class="form-control" placeholder="e.g. Revolution Day" required>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">+ Add Holiday</button>
    </form>

    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($holidays)): ?>
            <?php foreach ($holidays as $h): ?>
              <tr>
                <td><?= e($h['date']) ?></td>
                <td><strong><?= e($h['name']) ?></strong></td>
                <td>
                  <form action="<?= BASE_URL ?>/admin/settings/holidays/delete" method="POST" style="display:inline;">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="id" value="<?= e($h['id']) ?>">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete holiday?');">Del</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="3" style="text-align: center; color: var(--c-slate);">No holidays registered.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
