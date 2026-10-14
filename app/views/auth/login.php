<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Login — Face Attendance') ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/prototype/assets/css/tokens.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/prototype/assets/css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">

  <div class="card" style="width: 100%; max-width: 420px; padding: 36px; box-shadow: var(--shadow-lg);">
    <div style="text-align: center; margin-bottom: 24px;">
      <a href="<?= BASE_URL ?>/" aria-label="Return to homepage" style="display: inline-flex; text-decoration: none;">
        <div class="logo-icon" style="margin: 0 auto 12px; width: 44px; height: 44px; font-size: 1.4rem;">FA</div>
      </a>
      <h2>Welcome Back</h2>
      <p style="color: var(--c-slate); font-size: 0.9rem; margin-top: 4px;">Sign in to access your attendance ledger</p>
    </div>

    <?php if (!empty($error)): ?>
      <div style="padding: 12px; background: var(--c-status-absent-bg); color: var(--c-status-absent); border-radius: var(--radius-md); font-size: 0.88rem; margin-bottom: 20px; font-weight: 600;">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div style="padding: 12px; background: var(--c-status-present-bg); color: var(--c-status-present); border-radius: var(--radius-md); font-size: 0.88rem; margin-bottom: 20px; font-weight: 600;">
        <?= e($success) ?>
      </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/login" method="POST">
      <?= \App\Core\Csrf::field() ?>

      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" autocomplete="username" required>
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" autocomplete="current-password" required>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">Sign In</button>
    </form>

    <div style="text-align: center; margin-top: 24px; font-size: 0.88rem; color: var(--c-slate);">
      Don't have an account? <a href="<?= BASE_URL ?>/register" style="font-weight: 600; color: var(--c-ink);">Create account</a>
    </div>
  </div>

</body>
</html>
