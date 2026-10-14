<div style="max-width: 680px; margin: 0 auto;">
  <div class="card reveal">
    <div class="card-header">
      <h3 class="card-title">Submit Attendance Correction Request</h3>
    </div>

    <?php if (!empty($errors)): ?>
      <div style="padding: 12px; background: var(--c-status-absent-bg); color: var(--c-status-absent); border-radius: var(--radius-md); font-size: 0.88rem; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
          <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/corrections/store" method="POST">
      <?= \App\Core\Csrf::field() ?>

      <div class="form-group">
        <label class="form-label" for="attendance_id">Select Attendance Record</label>
        <select id="attendance_id" name="attendance_id" class="form-control" required>
          <option value="">-- Choose Attendance Record --</option>
          <?php foreach ($userRecords as $rec): ?>
            <option value="<?= e($rec['id']) ?>" <?= ($selectedRecord && $selectedRecord->id === (int)$rec['id']) ? 'selected' : '' ?>>
              Date: <?= e($rec['attendance_date']) ?> (Status: <?= e($rec['status']) ?> | Check-In: <?= e($rec['check_in'] ?? 'None') ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label" for="requested_check_in">Requested Check-In Time</label>
          <input type="time" id="requested_check_in" name="requested_check_in" class="form-control" value="<?= e($selectedRecord->checkIn ?? '09:00') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="requested_check_out">Requested Check-Out Time</label>
          <input type="time" id="requested_check_out" name="requested_check_out" class="form-control" value="<?= e($selectedRecord->checkOut ?? '17:00') ?>">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="reason">Reason for Correction (Required)</label>
        <textarea id="reason" name="reason" rows="4" class="form-control" placeholder="Provide a detailed explanation for requesting attendance modification (e.g. system camera error, official permission)..." required minlength="5"></textarea>
      </div>

      <div style="display: flex; gap: 12px; margin-top: 20px;">
        <button type="submit" class="btn btn-primary">Submit Correction Request</button>
        <a href="<?= BASE_URL ?>/corrections" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
