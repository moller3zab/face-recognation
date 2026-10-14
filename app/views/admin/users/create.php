<?php $formUser = $user ?? null; ?>
<div class="card reveal" style="max-width: 680px; margin: 0 auto;">
  <div class="card-header">
    <h3 class="card-title"><?= $formUser ? 'Edit User Account' : 'Create New User Account' ?></h3>
    <a href="<?= BASE_URL ?>/admin/users" class="btn btn-secondary btn-sm">Back to List</a>
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

  <form action="<?= BASE_URL ?>/admin/users/<?= $formUser ? 'update' : 'store' ?>" method="POST" enctype="multipart/form-data">
    <?= \App\Core\Csrf::field() ?>
    <?php if ($formUser): ?>
      <input type="hidden" name="id" value="<?= e($formUser->id) ?>">
    <?php endif; ?>

    <div class="form-group">
      <label class="form-label" for="name">Full Name</label>
      <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Mariam Tamer" value="<?= e($formUser?->name ?? '') ?>" required>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="user@miu.edu.eg" value="<?= e($formUser?->email ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label" for="identifier">Student / Employee ID</label>
        <input type="text" id="identifier" name="identifier" class="form-control" placeholder="2026-0812" value="<?= e($formUser?->identifier ?? '') ?>" required>
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
      <div class="form-group">
        <label class="form-label" for="role">Role Permission</label>
        <select id="role" name="role" class="form-control">
          <option value="user" <?= ($formUser?->role ?? 'user') === 'user' ? 'selected' : '' ?>>user</option>
          <option value="admin" <?= ($formUser?->role ?? '') === 'admin' ? 'selected' : '' ?>>admin</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" for="status">Account Status</label>
        <select id="status" name="status" class="form-control">
          <option value="active" <?= ($formUser?->status ?? 'active') === 'active' ? 'selected' : '' ?>>active</option>
          <option value="inactive" <?= ($formUser?->status ?? '') === 'inactive' ? 'selected' : '' ?>>inactive</option>
        </select>
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
      <div class="form-group">
        <label class="form-label" for="department">Department</label>
        <input type="text" id="department" name="department" class="form-control" placeholder="e.g. Computer Science" value="<?= e($formUser?->department ?? '') ?>">
      </div>

      <div class="form-group">
        <label class="form-label" for="phone">Phone</label>
        <input type="tel" id="phone" name="phone" class="form-control" placeholder="e.g. +20 100 000 0000" value="<?= e($formUser?->phone ?? '') ?>">
      </div>

      <div class="form-group">
        <label class="form-label" for="password"><?= $formUser ? 'New Password (optional)' : 'Initial Password' ?></label>
        <input type="password" id="password" name="password" class="form-control" placeholder="<?= $formUser ? 'Leave blank to keep current password' : '••••••••' ?>" <?= $formUser ? '' : 'required' ?> minlength="6" autocomplete="new-password">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="photo">Optional Profile Photo Upload</label>
      <input type="file" id="photo" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
      <?php if ($formUser?->photo): ?><p style="font-size: 0.82rem; color: var(--c-slate);">A new photo is optional; leave empty to keep the current one.</p><?php endif; ?>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
      <a href="<?= BASE_URL ?>/admin/users" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary"><?= $formUser ? 'Save Changes' : 'Create User Account' ?></button>
    </div>
  </form>
</div>
