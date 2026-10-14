<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
  <!-- Left Side: Profile Details & Password -->
  <div>
    <!-- Profile Info Form -->
    <div class="card reveal" style="margin-bottom: 24px;">
      <div class="card-header">
        <h3 class="card-title">Administrator Profile Information</h3>
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

      <?php if (!empty($errors)): ?>
        <div style="padding: 12px; background: var(--c-status-absent-bg); color: var(--c-status-absent); border-radius: var(--radius-md); font-size: 0.88rem; margin-bottom: 20px;">
          <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($errors as $err): ?>
              <li><?= e($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="<?= BASE_URL ?>/profile/update" method="POST" enctype="multipart/form-data">
        <?= \App\Core\Csrf::field() ?>

        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 24px;">
          <img src="<?= e(!empty($user->photo) ? BASE_URL . '/' . $user->photo : BASE_URL . '/prototype/assets/images/avatar-placeholder.png') ?>" alt="Profile Photo" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--c-accent);">
          <div>
            <label class="form-label" for="photo">Change Avatar Photo</label>
            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" class="form-control" style="font-size: 0.85rem;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= e($user->name) ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" class="form-control" value="<?= e($user->email) ?>" disabled style="background: rgba(0,0,0,0.03);">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div class="form-group">
            <label class="form-label" for="national_id">National / Employee ID</label>
            <input type="text" id="national_id" class="form-control" value="<?= e($user->identifier) ?>" disabled style="background: rgba(0,0,0,0.03);">
          </div>
          <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?= e($user->phone ?? '') ?>">
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 12px;">Save Profile Changes</button>
      </form>
    </div>

    <!-- Security & Password Card -->
    <div class="card reveal">
      <div class="card-header">
        <h3 class="card-title">Security & Password</h3>
      </div>

      <form action="<?= BASE_URL ?>/profile/change-password" method="POST">
        <?= \App\Core\Csrf::field() ?>

        <div class="form-group">
          <label class="form-label" for="current_password">Current Password</label>
          <input type="password" id="current_password" name="current_password" class="form-control" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div class="form-group">
            <label class="form-label" for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
          </div>
          <div class="form-group">
            <label class="form-label" for="new_password_confirm">Confirm New Password</label>
            <input type="password" id="new_password_confirm" name="new_password_confirm" class="form-control" required minlength="6">
          </div>
        </div>

        <button type="submit" class="btn btn-secondary" style="margin-top: 12px;">Update Password</button>
      </form>
    </div>
  </div>

  <!-- Right Side: Account Summary & Status Card -->
  <div>
    <div class="card reveal">
      <div class="card-header">
        <h3 class="card-title">Role & Status</h3>
      </div>

      <div style="text-align: center; padding: 20px 0;">
        <div style="width: 70px; height: 70px; border-radius: 50%; background: var(--c-sand); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
          <svg style="width: 36px; height: 36px; color: var(--c-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>

        <h4>Administrator Account</h4>
        <div style="margin: 12px 0;">
          <span class="badge badge-present" style="font-size: 0.95rem; padding: 6px 16px;">Super Admin Privileges</span>
        </div>
        <p style="font-size: 0.85rem; color: var(--c-slate); margin-top: 12px;">
          Full access to system settings, user management, reports, and attendance overrides.
        </p>
      </div>
    </div>
  </div>
</div>
