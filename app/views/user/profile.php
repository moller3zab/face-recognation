<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
  <!-- Left Side: Profile Details & Password -->
  <div>
    <!-- Profile Info Form -->
    <div class="card reveal" style="margin-bottom: 24px;">
      <div class="card-header">
        <h3 class="card-title">User Account Information</h3>
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
            <label class="form-label" for="national_id">National ID / Code</label>
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

  <!-- Right Side: Biometric Face Profile Card -->
  <div>
    <div class="card reveal">
      <div class="card-header">
        <h3 class="card-title">Biometric Registration</h3>
      </div>

      <div style="text-align: center; padding: 20px 0;">
        <div style="width: 70px; height: 70px; border-radius: 50%; background: var(--c-sand); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
          <svg style="width: 36px; height: 36px; color: var(--c-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>

        <h4>Face Recognition Status</h4>
        <div style="margin: 12px 0;">
          <?php if (!empty($hasFaceData)): ?>
            <span class="badge badge-present" style="font-size: 0.95rem; padding: 6px 16px;">Face Registered</span>
            <p style="font-size: 0.85rem; color: var(--c-slate); margin-top: 12px;">Your face vector descriptors are stored securely in 128-dimensional embedding format.</p>
          <?php else: ?>
            <span class="badge badge-absent" style="font-size: 0.95rem; padding: 6px 16px;">Not Enrolled</span>
            <p style="font-size: 0.85rem; color: var(--c-slate); margin-top: 12px;">You have not registered your face biometric data yet.</p>
          <?php endif; ?>
        </div>

        <a href="<?= BASE_URL ?>/face/register" class="btn btn-primary" style="margin-top: 16px; width: 100%;">
          <?= !empty($hasFaceData) ? 'Re-enroll Face Biometrics' : 'Enroll Face Biometrics Now' ?>
        </a>
      </div>
    </div>
  </div>
</div>
