<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;
use App\Services\MenuBuilder;

$currentPath = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');
if (BASE_URL !== '' && str_starts_with($currentPath, BASE_URL)) {
    $currentPath = substr($currentPath, strlen(BASE_URL));
}
$currentPath = '/' . ltrim($currentPath, '/');
$currentPath = rtrim($currentPath, '/') ?: '/';
$currentUser = Auth::user() ?? [];
$currentRole = Auth::role() ?? 'user';
$navigation = (new MenuBuilder())->buildMenu($currentRole, $currentPath);
$displayName = (string) ($currentUser['name'] ?? 'Account');
$avatarInitial = function_exists('mb_substr') ? mb_substr($displayName, 0, 1) : substr($displayName, 0, 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Face Attendance System') ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/prototype/assets/css/tokens.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/prototype/assets/css/style.css?v=responsive-face-registration-v2">
</head>
<body>
  <div class="app-layout">
    <aside class="sidebar" id="app-sidebar" aria-label="Main navigation">
      <div class="sidebar-header">
        <a href="<?= BASE_URL ?>/" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
          <span class="logo-icon" aria-hidden="true">FA</span>
          <span>
            <span class="logo-text" style="display: block;">Face Attendance</span>
            <span class="logo-sub" style="display: block;"> <?= e(ucfirst($currentRole)) ?> Portal</span>
          </span>
        </a>
      </div>

      <nav aria-label="Main navigation">
        <?= $navigation ?>
      </nav>

      <div class="sidebar-footer">
        <div class="user-avatar" aria-hidden="true">
          <?php if (!empty($currentUser['photo'])): ?>
            <img src="<?= BASE_URL ?>/<?= e($currentUser['photo']) ?>" alt="<?= e($displayName) ?>">
          <?php else: ?>
            <?= e($avatarInitial) ?>
          <?php endif; ?>
        </div>
        <div class="user-info">
          <div class="user-name"><?= e($displayName) ?></div>
          <div class="user-role"><?= e($currentRole) ?></div>
        </div>
        <form action="<?= BASE_URL ?>/logout" method="POST" style="margin: 0;">
          <?= Csrf::field() ?>
          <button type="submit" class="menu-link" style="border: 0; background: transparent; cursor: pointer; padding: 8px;" aria-label="Sign out" title="Sign out">
            <span aria-hidden="true">↪</span>
          </button>
        </form>
      </div>
    </aside>

    <div class="main-wrapper">
      <header class="navbar">
        <div class="navbar-left">
          <button class="mobile-toggle" type="button" aria-controls="app-sidebar" aria-expanded="false" aria-label="Open navigation">☰</button>
          <h1 class="page-title-nav" style="margin: 0;"><?= e($title ?? 'Face Attendance') ?></h1>
        </div>
        <div class="navbar-right">
          <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/" aria-label="Go to public homepage">Home</a>
          <form action="<?= BASE_URL ?>/logout" method="POST" style="margin: 0;">
            <?= Csrf::field() ?>
            <button type="submit" class="btn btn-outline btn-sm">Log out</button>
          </form>
        </div>
      </header>
      <main class="content-body">
        <?= $content ?>
      </main>
    </div>
  </div>

  <script src="<?= BASE_URL ?>/prototype/assets/js/main.js"></script>
</body>
</html>
