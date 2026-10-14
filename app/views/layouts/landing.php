<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Face Recognition Attendance System') ?></title>
  <meta name="description" content="Next-generation AI Face Recognition Attendance System with liveness detection, dynamic rules, and automated analytics.">
  <link rel="stylesheet" href="<?= BASE_URL ?>/prototype/assets/css/tokens.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/prototype/assets/css/style.css">
</head>
<body style="background: var(--c-sand); color: var(--c-ink); font-family: var(--font-body); margin: 0; padding: 0;">

  <!-- Navbar -->
  <header style="display: flex; justify-content: space-between; align-items: center; padding: 20px 40px; background: rgba(223, 208, 184, 0.85); backdrop-filter: blur(10px); position: sticky; top: 0; z-index: 100; border-bottom: 1px solid var(--c-stone-soft);">
    <div style="display: flex; align-items: center; gap: 12px;">
      <div style="width: 38px; height: 38px; background: var(--c-ink); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--c-sand); font-weight: 800; font-size: 1.2rem;">
        FA
      </div>
      <span style="font-family: var(--font-heading); font-size: 1.2rem; font-weight: 800; color: var(--c-ink);">Face Attendance</span>
    </div>

    <div style="display: flex; gap: 16px; align-items: center;">
      <?php if (!empty($isLoggedIn)): ?>
        <a href="<?= BASE_URL . (\App\Core\Auth::isAdmin() ? '/admin/dashboard' : '/user/dashboard') ?>" class="btn btn-primary">
          Go to Dashboard →
        </a>
        <form action="<?= BASE_URL ?>/logout" method="POST" style="margin: 0;">
          <?= \App\Core\Csrf::field() ?>
          <button type="submit" class="btn btn-outline">Log out</button>
        </form>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/login" class="btn btn-outline">Sign In</a>
        <a href="<?= BASE_URL ?>/register" class="btn btn-primary">Register Account</a>
      <?php endif; ?>
    </div>
  </header>

  <main>
    <?= $content ?>
  </main>

  <!-- Footer -->
  <footer style="background: var(--c-ink); color: var(--c-sand); padding: 40px; text-align: center; border-top: 1px solid var(--c-slate); margin-top: 60px;">
    <p style="margin: 0 0 8px 0; font-family: var(--font-heading); font-weight: 700; font-size: 1.1rem;">
      Face Recognition Attendance System
    </p>
    <p style="margin: 0; font-size: 0.88rem; color: var(--c-stone);">
      Software Engineering University Project © 2026. All rights reserved.
    </p>
  </footer>

  <script src="<?= BASE_URL ?>/prototype/assets/js/main.js"></script>
</body>
</html>
