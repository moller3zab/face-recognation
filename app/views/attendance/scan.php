<div style="max-width: 760px; margin: 0 auto;">
  <div class="card reveal" style="text-align: center; position: relative;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
      <h3 class="card-title">Live Face Recognition Attendance</h3>
      <?php if ($hasFaceData): ?>
        <span id="scan-state-badge" class="badge badge-pending">State: Waiting for Camera</span>
      <?php else: ?>
        <span class="badge badge-absent">Face registration required</span>
      <?php endif; ?>
    </div>

    <?php if ($hasFaceData): ?>
      <!-- Camera controls and face models are rendered only after enrollment exists. -->
      <!-- Scanner Feed Frame Container -->
      <div id="scanner-frame" style="position: relative; width: 100%; max-width: 600px; height: 400px; margin: 0 auto; background: var(--c-ink); border-radius: var(--radius-lg); overflow: hidden; display: flex; align-items: center; justify-content: center; border: 2px solid var(--c-stone-soft);">
      
      <video id="webcam" autoplay muted playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); display: none;"></video>
      <canvas id="overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; transform: scaleX(-1);"></canvas>

      <!-- Corner Brackets overlay -->
      <div id="scan-bracket" style="position: absolute; width: 220px; height: 220px; border: 2px dashed rgba(223, 208, 184, 0.6); border-radius: 50%; pointer-events: none; box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.4); transition: border-color 0.3s ease;"></div>

      <!-- Animated Scan Line -->
      <div id="scan-line" style="display: none; position: absolute; width: 220px; height: 3px; background: linear-gradient(90deg, transparent, var(--c-sand), transparent); box-shadow: 0 0 8px var(--c-sand); top: 90px; animation: scanAnim 2s infinite ease-in-out;"></div>

      <!-- Placeholder Text before camera start -->
      <div id="camera-placeholder" style="text-align: center; color: var(--c-sand); padding: 20px;">
        <svg style="width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.7;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
        <p style="margin: 0; font-size: 0.95rem;">Click <strong>Start Scanner</strong> to begin real-time verification</p>
      </div>

      <!-- Live Loading Spinner -->
      <div id="model-loader" style="display: none; position: absolute; text-align: center; color: var(--c-sand); background: rgba(0,0,0,0.75); width: 100%; height: 100%; top: 0; left: 0; align-items: center; justify-content: center; flex-direction: column;">
        <div class="spinner" style="border: 3px solid rgba(255,255,255,0.2); border-top-color: var(--c-sand); border-radius: 50%; width: 36px; height: 36px; animation: spin 0.8s linear infinite; margin-bottom: 12px;"></div>
        <span>Loading AI Neural Models...</span>
      </div>

      <!-- Liveness Challenge Banner Overlay -->
      <div id="liveness-banner" style="display: none; position: absolute; top: 16px; left: 50%; transform: translateX(-50%); background: rgba(34, 40, 49, 0.9); color: var(--c-sand); padding: 8px 18px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid var(--c-stone-soft); z-index: 10;">
        Liveness Challenge: Turn head slowly to the <span id="liveness-dir-text" style="color: var(--c-sand-light); text-transform: uppercase; text-decoration: underline;"><?= e(strtoupper($livenessDirection)) ?></span>
      </div>
    </div>

    <!-- Scanner Action Controls -->
    <div style="display: flex; gap: 12px; margin-top: 20px; justify-content: center; flex-wrap: wrap;">
      <button id="btn-start-scanner" class="btn btn-primary" type="button">Start Scanner</button>
      <button id="btn-retry" class="btn btn-secondary" style="display: none;">Retry Verification</button>
    </div>

    <!-- Verification Result Card (Hidden until attendance recorded) -->
    <div id="result-card" style="display: none; margin-top: 24px; padding: 20px; background: var(--c-sand); border-radius: var(--radius-md); border: 2px solid var(--c-stone-soft); text-align: left;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
        <h4 style="margin: 0; font-size: 1.1rem;" id="result-title">Attendance Confirmed ✓</h4>
        <span id="result-badge" class="badge badge-present">Present</span>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 0.9rem;">
        <div><strong>User:</strong> <?= e($user['name']) ?></div>
        <div><strong>Date:</strong> <span id="res-date">--</span></div>
        <div><strong>Time:</strong> <span id="res-time">--</span></div>
        <div><strong>Biometric Match Distance ($D$):</strong> <span id="res-dist">--</span></div>
      </div>
      <p id="res-msg" style="margin-top: 12px; margin-bottom: 0; font-weight: 600; color: var(--c-status-present);"></p>
    </div>
    <?php else: ?>
      <div role="alert" style="padding: 20px; background: var(--c-status-absent-bg); color: var(--c-status-absent); border-radius: var(--radius-md); margin: 18px 0; text-align: left;">
        <strong>Face registration is required before attendance.</strong>
        <p style="margin: 8px 0 0;">Register your face baseline first. The attendance camera remains unavailable until your face samples are saved.</p>
      </div>
      <a class="btn btn-primary" href="<?= BASE_URL ?>/face/register">Go to Face Registration</a>
    <?php endif; ?>
  </div>
</div>

<style>
@keyframes spin {
  to { transform: rotate(360deg); }
}
@keyframes scanAnim {
  0% { top: 90px; opacity: 0.3; }
  50% { top: 290px; opacity: 0.9; }
  100% { top: 90px; opacity: 0.3; }
}
</style>

<?php if ($hasFaceData): ?>
<!-- Load local face-api.js only after enrollment exists -->
<script src="<?= BASE_URL ?>/assets/js/face-api.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const video = document.getElementById('webcam');
    const overlay = document.getElementById('overlay');
    const placeholder = document.getElementById('camera-placeholder');
    const modelLoader = document.getElementById('model-loader');
    const stateBadge = document.getElementById('scan-state-badge');
    const scanBracket = document.getElementById('scan-bracket');
    const scanLine = document.getElementById('scan-line');
    const livenessBanner = document.getElementById('liveness-banner');
    const btnStart = document.getElementById('btn-start-scanner');
    const btnRetry = document.getElementById('btn-retry');
    const resultCard = document.getElementById('result-card');

    const CSRF_TOKEN = '<?= e(\App\Core\Csrf::generateToken()) ?>';
    const MODEL_PATH = '<?= BASE_URL ?>/assets/models';
    const VERIFY_URL = (() => {
        const route = window.location.pathname.match(/^(.*)\/attendance\/scan\/?$/);
        const path = route
            ? `${route[1]}/attendance/verify`
            : '<?= BASE_URL ?>/attendance/verify';
        return new URL(path, window.location.origin).href;
    })();
    const CAMERA_MODE = '<?= e($cameraMode) ?>';
    const CAMERA_RESOLUTION = '<?= e($cameraResolution) ?>'.split('x').map(Number);
    let targetDirection = '<?= $livenessDirection ?>';

    let isModelLoaded = false;
    let stream = null;
    let scanInterval = null;
    let livenessPassed = false;
    let livenessStage = 'turn';
    let isVerifying = false;
    let isFrameProcessing = false;

    function withTimeout(promise, timeoutMs, message) {
        return new Promise((resolve, reject) => {
            const timer = window.setTimeout(() => reject(new Error(message)), timeoutMs);
            promise.then((value) => {
                window.clearTimeout(timer);
                resolve(value);
            }).catch((error) => {
                window.clearTimeout(timer);
                reject(error);
            });
        });
    }

    if (btnStart instanceof HTMLButtonElement) btnStart.addEventListener('click', async () => {
        btnStart.disabled = true;
        modelLoader.style.display = 'flex';
        stateBadge.textContent = 'State: Loading Models...';

        try {
            if (!isModelLoaded) {
                await withTimeout(Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_PATH),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_PATH),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_PATH)
                ]), 30000, 'Loading the local face models timed out. Reload the page and try again.');
                isModelLoaded = true;
            }

            modelLoader.style.display = 'none';
            stateBadge.textContent = 'State: Opening Camera...';

            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: CAMERA_RESOLUTION[0] || 640 },
                    height: { ideal: CAMERA_RESOLUTION[1] || 480 },
                    facingMode: { ideal: CAMERA_MODE }
                },
                audio: false
            });

            video.srcObject = stream;
            if (video.readyState < HTMLMediaElement.HAVE_METADATA) {
                await new Promise((resolve, reject) => {
                    video.addEventListener('loadedmetadata', resolve, { once: true });
                    video.addEventListener('error', () => reject(new Error('The camera video could not be displayed.')), { once: true });
                    window.setTimeout(() => reject(new Error('Camera startup timed out. Check camera permission and try again.')), 10000);
                });
            }
            await video.play();
            video.style.display = 'block';
            placeholder.style.display = 'none';
            btnStart.style.display = 'none';

            startScanningProcess();

        } catch (err) {
            console.error(err);
            stream?.getTracks().forEach((track) => track.stop());
            stream = null;
            video.srcObject = null;
            video.style.display = 'none';
            placeholder.style.display = 'block';
            modelLoader.style.display = 'none';
            btnStart.disabled = false;
            stateBadge.textContent = 'State: Camera or Model Error';
            stateBadge.className = 'badge badge-absent';
            alert('Could not start face scanning. Allow camera access and check that the local AI models are available. Details: ' + err.message);
        }
    });

    btnRetry.addEventListener('click', () => {
        if (scanInterval !== null) window.clearInterval(scanInterval);
        scanInterval = null;
        btnRetry.style.display = 'none';
        resultCard.style.display = 'none';
        livenessPassed = false;
        livenessStage = 'turn';
        isVerifying = false;
        isFrameProcessing = false;
        targetDirection = Math.random() < 0.5 ? 'left' : 'right';
        livenessBanner.innerHTML = `Liveness Challenge: Turn head slowly to the <span id="liveness-dir-text" style="color: var(--c-sand-light); text-transform: uppercase; text-decoration: underline;">${targetDirection.toUpperCase()}</span>`;
        livenessBanner.style.background = 'rgba(34, 40, 49, 0.9)';
        livenessBanner.style.color = 'var(--c-sand)';
        scanLine.style.display = 'block';
        startScanningProcess();
    });

    function startScanningProcess() {
        if (scanInterval !== null) window.clearInterval(scanInterval);
        scanInterval = null;
        stateBadge.textContent = 'State: Looking for face...';
        stateBadge.className = 'badge badge-pending';
        scanBracket.style.borderColor = 'rgba(223, 208, 184, 0.6)';
        scanLine.style.display = 'block';
        livenessBanner.style.display = 'none';

        scanInterval = setInterval(async () => {
            if (isVerifying || isFrameProcessing || video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA) return;
            isFrameProcessing = true;

            try {
              const detection = await withTimeout(
                  faceapi.detectSingleFace(video, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.45 }))
                      .withFaceLandmarks()
                      .withFaceDescriptor(),
                  12000,
                  'Face detection is taking too long. Check camera quality, then retry.'
              );

              if (!detection) {
                  stateBadge.textContent = 'State: Looking for face...';
                  scanBracket.style.borderColor = 'rgba(223, 208, 184, 0.6)';
                  return;
              }

              scanBracket.style.borderColor = 'var(--c-stone)';
              const landmarks = detection.landmarks;
              const nose = landmarks.getNose()[3];
              const jaw = landmarks.getJawOutline();
              const distLeft = Math.abs(nose.x - jaw[0].x);
              const distRight = Math.abs(nose.x - jaw[16].x);
              const ratio = distLeft / Math.max(distRight, 1);

              if (!livenessPassed) {
                  livenessBanner.style.display = 'block';

                  if (livenessStage === 'turn') {
                      stateBadge.textContent = `State: Liveness Challenge — Turn Head ${targetDirection.toUpperCase()}`;
                      // The preview is mirrored, but landmarks use the unmirrored camera frame.
                      const turned = targetDirection === 'left' ? ratio > 1.4 : ratio < 0.72;
                      if (turned) {
                          livenessStage = 'return';
                          livenessBanner.innerHTML = '✓ Head turn detected. Now face the camera again.';
                          livenessBanner.style.background = 'var(--c-status-present-bg)';
                          livenessBanner.style.color = 'var(--c-status-present)';
                      } else {
                          livenessBanner.style.background = 'rgba(34, 40, 49, 0.9)';
                          livenessBanner.style.color = 'var(--c-sand)';
                      }
                      return;
                  }

                  stateBadge.textContent = 'State: Face the camera again';
                  livenessBanner.textContent = 'Now face the camera again to complete the liveness check.';
                  if (ratio >= 0.82 && ratio <= 1.22) {
                      livenessPassed = true;
                      livenessBanner.textContent = '✓ Liveness check passed. Verifying your face…';
                  }
                  return;
              }

              window.clearInterval(scanInterval);
              scanInterval = null;
              isVerifying = true;
              livenessBanner.style.display = 'none';
              scanLine.style.display = 'none';
              scanBracket.style.borderColor = 'var(--c-status-present)';
              stateBadge.textContent = 'State: Verifying Identity on Server...';
              stateBadge.className = 'badge badge-pending';
              await sendVerificationToServer(Array.from(detection.descriptor));
            } catch (error) {
              console.error('Live face scan failed:', error);
              window.clearInterval(scanInterval);
              scanInterval = null;
              isVerifying = true;
              stateBadge.textContent = 'State: Scanner Error';
              stateBadge.className = 'badge badge-absent';
              btnRetry.style.display = 'inline-block';
            } finally {
              isFrameProcessing = false;
            }

        }, 400);
    }

    async function sendVerificationToServer(descriptor) {
        const controller = new AbortController();
        const requestTimeout = window.setTimeout(() => controller.abort(), 15000);
        try {
            const resp = await fetch(VERIFY_URL, {
                method: 'POST',
                credentials: 'same-origin',
                signal: controller.signal,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    csrf_token: CSRF_TOKEN,
                    descriptor: descriptor,
                    liveness_passed: true,
                    action: 'auto'
                })
            });

            const contentType = resp.headers.get('content-type') || '';
            const data = contentType.includes('application/json')
                ? await resp.json()
                : { message: `The server returned ${resp.status} instead of a JSON verification result.` };

            if (resp.ok && data.status === 'success') {
                stream?.getTracks().forEach((track) => track.stop());
                stream = null;
                video.srcObject = null;
                stateBadge.textContent = 'State: Attendance Recorded Successfully!';
                stateBadge.className = 'badge badge-present';

                document.getElementById('res-date').textContent = data.date;
                document.getElementById('res-time').textContent = data.type === 'check_in' ? data.check_in : data.check_out;
                document.getElementById('res-dist').textContent = data.distance + ' (Match Valid)';
                document.getElementById('res-msg').textContent = data.message;
                document.getElementById('result-badge').textContent = data.attendance_status;
                document.getElementById('result-badge').className = `badge badge-${data.attendance_status.toLowerCase()}`;
                
                document.getElementById('result-title').textContent = data.type === 'check_in' ? 'Check-In Confirmed ✓' : 'Check-Out Confirmed ✓';
                resultCard.style.display = 'block';

            } else {
                stateBadge.textContent = 'State: Verification Failed';
                stateBadge.className = 'badge badge-absent';

                alert('Verification Failed: ' + (data.message || 'Unknown server error'));
                btnRetry.style.display = 'inline-block';
            }
        } catch (err) {
            console.error('Attendance verification request failed at', VERIFY_URL, err);
            stateBadge.textContent = 'State: Verification Error';
            stateBadge.className = 'badge badge-absent';
            alert(err.name === 'AbortError'
                ? 'The server did not respond within 15 seconds. Your attendance was not confirmed; retry the scan.'
                : 'Server communication failed: ' + err.message);
            btnRetry.style.display = 'inline-block';
        } finally {
            window.clearTimeout(requestTimeout);
        }
    }
});
</script>
<?php endif; ?>
