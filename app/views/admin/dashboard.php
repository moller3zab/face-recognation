<!-- Animated Stat Cards Grid -->
<div class="stats-grid reveal" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px;">
  <div class="stat-card">
    <div class="stat-icon"><svg style="width:24px;height:24px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
    <div>
      <div class="stat-label">Active Users</div>
      <div class="stat-number"><?= e($stats['totalUsers']) ?></div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon present"><svg style="width:24px;height:24px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></div>
    <div>
      <div class="stat-label">Present Today</div>
      <div class="stat-number" style="color: var(--c-status-present);"><?= e($stats['presentToday']) ?></div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon late"><svg style="width:24px;height:24px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
    <div>
      <div class="stat-label">Late Arrival Today</div>
      <div class="stat-number" style="color: var(--c-status-late);"><?= e($stats['lateToday']) ?></div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon absent"><svg style="width:24px;height:24px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></div>
    <div>
      <div class="stat-label">Absent Today</div>
      <div class="stat-number" style="color: var(--c-status-absent);"><?= e($stats['absentToday']) ?></div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon"><svg style="width:24px;height:24px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
    <div>
      <div class="stat-label">Pending Corrections</div>
      <div class="stat-number"><?= e($stats['pendingCorrections']) ?></div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
  <!-- Daily Trend Chart -->
  <div class="card reveal">
    <div class="card-header">
      <h3 class="card-title">7-Day Attendance Trend</h3>
    </div>
    <div style="height: 260px; position: relative;">
      <canvas id="dailyTrendChart"></canvas>
    </div>
  </div>

  <!-- Status Ratio Doughnut Chart -->
  <div class="card reveal">
    <div class="card-header">
      <h3 class="card-title">Overall Status Distribution</h3>
    </div>
    <div style="height: 260px; position: relative; display: flex; align-items: center; justify-content: center;">
      <canvas id="statusDistChart"></canvas>
    </div>
  </div>
</div>

<!-- Recent Activity Grids -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
  <!-- Recent Check-Ins -->
  <div class="card reveal">
    <div class="card-header">
      <h3 class="card-title">Recent Check-Ins</h3>
    </div>
    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Check In</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($recentAttendance)): ?>
            <?php foreach ($recentAttendance as $ra): ?>
              <tr>
                <td><strong><?= e($ra['user_name']) ?></strong></td>
                <td><?= e($ra['check_in'] ?? '--:--') ?></td>
                <td><span class="badge badge-<?= strtolower($ra['status']) ?>"><?= e($ra['status']) ?></span></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="3" style="text-align: center; color: var(--c-slate);">No recent attendance logs.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pending Correction Requests -->
  <div class="card reveal">
    <div class="card-header">
      <h3 class="card-title">Pending Corrections</h3>
      <a href="<?= BASE_URL ?>/admin/corrections" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($pendingCorrections)): ?>
            <?php foreach ($pendingCorrections as $pc): ?>
              <tr>
                <td><strong><?= e($pc['user_name']) ?></strong></td>
                <td><?= e($pc['attendance_date']) ?></td>
                <td><a href="<?= BASE_URL ?>/admin/corrections" class="btn btn-outline btn-sm" style="font-size: 0.75rem; padding: 4px 8px;">Review</a></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="3" style="text-align: center; color: var(--c-slate);">No pending requests.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Load local Chart.js if available or CDN fallback -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const trendData = <?= json_encode($chartData['dailyTrend']) ?>;
    const distData = <?= json_encode($chartData['statusDist']) ?>;

    const labels = trendData.map(d => d.attendance_date);
    const presentCounts = trendData.map(d => d.present_count);
    const lateCounts = trendData.map(d => d.late_count);
    const absentCounts = trendData.map(d => d.absent_count);

    // Line Chart
    const ctxTrend = document.getElementById('dailyTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                { label: 'Present', data: presentCounts, borderColor: '#4F7A5A', backgroundColor: 'rgba(79, 122, 90, 0.1)', fill: true, tension: 0.3 },
                { label: 'Late', data: lateCounts, borderColor: '#B7791F', backgroundColor: 'rgba(183, 121, 31, 0.1)', fill: true, tension: 0.3 },
                { label: 'Absent', data: absentCounts, borderColor: '#A8403A', backgroundColor: 'rgba(168, 64, 58, 0.1)', fill: true, tension: 0.3 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });

    // Doughnut Chart
    const ctxDist = document.getElementById('statusDistChart').getContext('2d');
    const distLabels = distData.map(d => d.status);
    const distTotals = distData.map(d => d.total);
    const distColors = distLabels.map(l => l === 'Present' ? '#4F7A5A' : (l === 'Late' ? '#B7791F' : (l === 'Absent' ? '#A8403A' : '#948979')));

    new Chart(ctxDist, {
        type: 'doughnut',
        data: {
            labels: distLabels,
            datasets: [{ data: distTotals, backgroundColor: distColors }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
