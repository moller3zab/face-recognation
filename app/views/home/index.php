<!-- Hero Section -->
<section style="padding: 80px 40px; text-align: center; max-width: 1000px; margin: 0 auto;">
  <div style="display: inline-block; padding: 6px 16px; background: var(--c-paper); border: 1px solid var(--c-stone-soft); border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-bottom: 24px; color: var(--c-slate);">
  </div>

  <h1 style="font-family: var(--font-heading); font-size: 3.2rem; font-weight: 800; line-height: 1.15; margin: 0 0 20px 0; color: var(--c-ink); letter-spacing: -0.02em;">
    Smart Attendance.<br><span style="color: var(--c-stone);">Powered by Your Face.</span>
  </h1>

  <p style="font-size: 1.2rem; color: var(--c-slate); max-width: 680px; margin: 0 auto 36px auto; line-height: 1.6;">
    Eliminate manual roll calls and proxy attendance with real-time AI face recognition, head-turn liveness verification, and dynamic attendance rule enforcement.
  </p>

  <div style="display: flex; gap: 16px; justify-content: center; margin-bottom: 60px;">
    <?php if (!empty($isLoggedIn)): ?>
      <a href="<?= BASE_URL . (\App\Core\Auth::isAdmin() ? '/admin/dashboard' : '/user/dashboard') ?>" class="btn btn-primary" style="font-size: 1.05rem; padding: 14px 28px;">
        Open Your Dashboard →
      </a>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/register" class="btn btn-primary" style="font-size: 1.05rem; padding: 14px 28px;">
        Get Started Now
      </a>
      <a href="<?= BASE_URL ?>/login" class="btn btn-outline" style="font-size: 1.05rem; padding: 14px 28px;">
        Sign In
      </a>
    <?php endif; ?>
  </div>

  <div class="card" style="max-width: 620px; margin: 0 auto; padding: 20px; text-align: left;">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px;">
      <h2 style="font-family: var(--font-heading); font-size: 1.15rem; margin: 0;">Live identity check</h2>
      <span id="home-check-state" class="badge badge-pending" aria-live="polite">Camera is off</span>
    </div>

    <?php if ($isLoggedIn && $hasFaceData): ?>
      <div id="home-face-check" data-model-path="<?= BASE_URL ?>/assets/models"
           data-verify-url="<?= BASE_URL ?>/attendance/verify-identity"
           data-csrf-token="<?= e($csrfToken) ?>"
           data-camera-mode="<?= e($cameraMode) ?>"
           data-camera-resolution="<?= e($cameraResolution) ?>"
           data-direction="<?= random_int(0, 1) === 0 ? 'left' : 'right' ?>">
        <div style="position: relative; background: var(--c-ink); border-radius: var(--radius-md); overflow: hidden; aspect-ratio: 4 / 3;">
          <video id="home-check-video" autoplay muted playsinline style="display: none; width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
          <div id="home-check-placeholder" style="position: absolute; inset: 0; display: grid; place-items: center; color: var(--c-sand); text-align: center; padding: 24px;">
            <p style="margin: 0;">Start the check when you are ready. Your camera stays off until then.</p>
          </div>
          <div aria-hidden="true" style="position: absolute; width: 180px; height: 180px; max-width: 60%; max-height: 60%; border: 2px dashed rgba(223,208,184,.8); border-radius: 50%; inset: 50% auto auto 50%; transform: translate(-50%,-50%); pointer-events: none;"></div>
          <div id="home-check-challenge" style="display: none; position: absolute; left: 12px; right: 12px; top: 12px; padding: 8px 12px; border-radius: 20px; background: rgba(34,40,49,.9); color: var(--c-sand); text-align: center; font-weight: 700;"></div>
        </div>
        <p id="home-check-message" aria-live="polite" style="min-height: 24px; margin: 12px 0; color: var(--c-slate);">Your face descriptor is compared with your registered profile on this app's server. No photo or video is saved.</p>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          <button id="home-check-start" class="btn btn-primary" type="button">Start identity check</button>
          <button id="home-check-stop" class="btn btn-outline" type="button" style="display: none;">Stop camera</button>
        </div>
      </div>
      <script src="<?= BASE_URL ?>/assets/js/face-api.min.js"></script>
      <script src="<?= BASE_URL ?>/assets/js/home-face-check.js?v=mirrored-liveness-fix-2" defer></script>
    <?php elseif ($isLoggedIn): ?>
      <p style="margin: 0 0 14px; color: var(--c-slate);">Register your face first to compare a live scan with your account.</p>
      <a class="btn btn-primary" href="<?= BASE_URL ?>/face/register">Register your face</a>
    <?php else: ?>
      <p style="margin: 0 0 14px; color: var(--c-slate);">Sign in to compare a live scan with your enrolled face profile. This check will not record attendance.</p>
      <a class="btn btn-primary" href="<?= BASE_URL ?>/login">Sign in to verify</a>
    <?php endif; ?>
  </div>
</section>

<!-- Features Grid -->
<section style="background: var(--c-paper); padding: 80px 40px; border-top: 1px solid var(--c-stone-soft); border-bottom: 1px solid var(--c-stone-soft);">
  <div style="max-width: 1100px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 48px;">
      <h2 style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 800; margin: 0 0 12px 0;">
        Core System Features
      </h2>
      <p style="color: var(--c-slate); margin: 0;">Designed for high performance, strict security, and human-centric design.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
      <div class="card reveal" style="background: var(--c-sand);">
        <h3 style="font-family: var(--font-heading); font-size: 1.2rem; margin-top: 0;">Local Face Verification</h3>
        <p style="color: var(--c-slate); font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">
          Uses 128-dimensional facial embedding descriptors generated directly in the browser via offline `face-api.js` models.
        </p>
      </div>

      <div class="card reveal" style="background: var(--c-sand);">
        <h3 style="font-family: var(--font-heading); font-size: 1.2rem; margin-top: 0;">Head-Turn Liveness Challenge</h3>
        <p style="color: var(--c-slate); font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">
          Prevents proxy attendance photos by requiring a real-time random head turn verification (68-point facial landmarks yaw ratio).
        </p>
      </div>

      <div class="card reveal" style="background: var(--c-sand);">
        <h3 style="font-family: var(--font-heading); font-size: 1.2rem; margin-top: 0;">Configurable Business Rules</h3>
        <p style="color: var(--c-slate); font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">
          Custom working hours, grace periods, holidays, and recognition distance thresholds ($D$) calculated in real-time.
        </p>
      </div>

      <div class="card reveal" style="background: var(--c-sand);">
        <h3 style="font-family: var(--font-heading); font-size: 1.2rem; margin-top: 0;">Automated System Alerts</h3>
        <p style="color: var(--c-slate); font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">
          Instant notifications for late arrivals, recorded absences, and correction request approvals with navbar badge indicators.
        </p>
      </div>

      <div class="card reveal" style="background: var(--c-sand);">
        <h3 style="font-family: var(--font-heading); font-size: 1.2rem; margin-top: 0;">Idempotent Absence Cron</h3>
        <p style="color: var(--c-slate); font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">
          Automated CLI cron script and admin trigger that generates absence records for active users while respecting holidays.
        </p>
      </div>

      <div class="card reveal" style="background: var(--c-sand);">
        <h3 style="font-family: var(--font-heading); font-size: 1.2rem; margin-top: 0;">UTF-8 BOM CSV Exports</h3>
        <p style="color: var(--c-slate); font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">
          One-click HR-friendly CSV exports for daily, weekly, monthly, late, and absent reports with full Excel Arabic support.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Live Stats Section -->
<section style="padding: 60px 40px; text-align: center;">
  <div style="max-width: 800px; margin: 0 auto; display: flex; justify-content: space-around; flex-wrap: wrap; gap: 24px;">
    <div>
      <div style="font-family: var(--font-heading); font-size: 2.8rem; font-weight: 800; color: var(--c-ink);"><?= e($stats['totalUsers']) ?></div>
      <div style="color: var(--c-slate); font-weight: 600; font-size: 0.95rem;">Active Users Enrolled</div>
    </div>
    <div>
      <div style="font-family: var(--font-heading); font-size: 2.8rem; font-weight: 800; color: var(--c-status-present);"><?= e($stats['presentToday']) ?></div>
      <div style="color: var(--c-slate); font-weight: 600; font-size: 0.95rem;">Present Today</div>
    </div>
    <div>
      <div style="font-family: var(--font-heading); font-size: 2.8rem; font-weight: 800; color: var(--c-stone);"><?= e($stats['pendingCorrections']) ?></div>
      <div style="color: var(--c-slate); font-weight: 600; font-size: 0.95rem;">Pending Correction Reviews</div>
    </div>
  </div>
</section>

