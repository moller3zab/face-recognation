<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? '403 Access Denied') ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/prototype/assets/css/tokens.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/prototype/assets/css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; text-align: center;">

  <div class="card" style="max-width: 480px; padding: 48px 36px; box-shadow: var(--shadow-lg);">
    <div style="font-family: var(--font-heading); font-size: 5rem; font-weight: 800; color: var(--c-status-absent); line-height: 1;">403</div>
    <h2 style="margin-top: 12px;">Access Denied</h2>
    <p style="color: var(--c-slate); margin: 12px 0 28px;">You do not have required role permissions to access this administrative route.</p>
    <a href="<?= BASE_URL ?>/user/dashboard" class="btn btn-primary">Return to User Dashboard</a>
  </div>

</body>
</html>
