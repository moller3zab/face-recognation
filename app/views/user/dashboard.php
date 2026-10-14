<div class="stats-grid reveal">
  <div class="stat-card">
    <div class="stat-icon present"><svg style="width:24px;height:24px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></div>
    <div>
      <div class="stat-label">Today's Status</div>
      <div style="font-family: var(--font-heading); font-size: 1.4rem; font-weight: 800; color: var(--c-status-present);">
        <?= e($todayRecord ? $todayRecord->status : 'Not Checked In') ?>
      </div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon"><svg style="width:24px;height:24px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
    <div>
      <div class="stat-label">Check-In Time Today</div>
      <div style="font-family: var(--font-heading); font-size: 1.4rem; font-weight: 800; color: var(--c-ink);">
        <?= e($todayRecord && $todayRecord->checkIn ? $todayRecord->checkIn : '--:--') ?>
      </div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon present"><svg style="width:24px;height:24px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
    <div>
      <div class="stat-label">Monthly Attendance Rate</div>
      <div class="stat-number" data-target="<?= (int)$monthlyPercentage ?>"><?= (int)$monthlyPercentage ?></div><span style="font-size: 0.9rem; font-weight: 700;">%</span>
    </div>
  </div>
</div>

<div class="card reveal">
  <div class="card-header">
    <h3 class="card-title">Recent Personal Attendance Log</h3>
  </div>
  <div class="table-responsive">
    <table class="data-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Check In</th>
          <th>Check Out</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($recentRecords)): ?>
          <?php foreach ($recentRecords as $r): ?>
            <tr>
              <td><?= e($r['attendance_date']) ?></td>
              <td><?= e($r['check_in'] ?? '-') ?></td>
              <td><?= e($r['check_out'] ?? '-') ?></td>
              <td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= e($r['status']) ?></span></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="4" style="text-align: center; color: var(--c-slate);">No attendance logs recorded yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
