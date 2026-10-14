<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Register — Face Attendance') ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/prototype/assets/css/tokens.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/prototype/assets/css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 40px 20px;">

  <div class="card" style="width: 100%; max-width: 540px; padding: 36px; box-shadow: var(--shadow-lg);">
    <div style="text-align: center; margin-bottom: 24px;">
      <a href="<?= BASE_URL ?>/" aria-label="Return to homepage" style="display: inline-flex; text-decoration: none;">
        <div class="logo-icon" style="margin: 0 auto 12px; width: 44px; height: 44px; font-size: 1.4rem;">FA</div>
      </a>
      <h2>Create Account</h2>
      <p style="color: var(--c-slate); font-size: 0.9rem; margin-top: 4px;">Register as a student / employee (Role: User)</p>
    </div>

    <?php if (!empty($errors)): ?>
      <div style="padding: 12px; background: var(--c-status-absent-bg); color: var(--c-status-absent); border-radius: var(--radius-md); font-size: 0.88rem; margin-bottom: 20px;">
        <ul style="padding-left: 16px; margin: 0;">
          <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div role="alert" style="padding: 12px; background: var(--c-status-absent-bg); color: var(--c-status-absent); border-radius: var(--radius-md); font-size: 0.88rem; margin-bottom: 20px; font-weight: 600;">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/register" method="POST" enctype="multipart/form-data">
      <?= \App\Core\Csrf::field() ?>

      <div class="form-group">
        <label class="form-label" for="name">Full Name</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Anfal Hamdy" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="user@miu.edu.eg" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="identifier">Student / Employee ID</label>
          <input type="text" id="identifier" name="identifier" class="form-control" placeholder="2026-0914" required>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label" for="department">Department</label>
          <select id="department" name="department" class="form-control">
            <option value="Computer Science">Computer Science</option>
            <option value="Software Engineering">Software Engineering</option>
            <option value="Information Technology">Information Technology</option>
            <option value="Business Administration">Business Administration</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="phone">Phone Number</label>
          <input type="tel" id="phone" name="phone" class="form-control" placeholder="+20 100 123 4567">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" autocomplete="new-password" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="password_confirm">Confirm Password</label>
          <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="Confirm your password" autocomplete="new-password" required>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 8px;">Register Account</button>
    </form>

    <div style="text-align: center; margin-top: 20px; font-size: 0.88rem; color: var(--c-slate);">
      Already have an account? <a href="<?= BASE_URL ?>/login" style="font-weight: 600; color: var(--c-ink);">Sign In</a>
    </div>
  </div>

</body>
</html>
