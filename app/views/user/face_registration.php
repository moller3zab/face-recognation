<div class="face-registration-layout" style="display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 24px;">
  <!-- Camera Scanner Area -->
  <div class="card reveal" style="position: relative;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
      <h3 class="card-title">Multi-Sample Face Registration</h3>
      <span id="scan-status" class="badge badge-pending">Camera Idle</span>
    </div>

    <?php if (!empty($success)): ?>
      <div style="padding: 12px; background: var(--c-status-present-bg); color: var(--c-status-present); border-radius: var(--radius-md); margin-bottom: 16px; font-weight: 600;">
        <?= e($success) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div style="padding: 12px; background: var(--c-status-absent-bg); color: var(--c-status-absent); border-radius: var(--radius-md); margin-bottom: 16px; font-weight: 600;">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <!-- Camera View Container -->
    <div style="position: relative; width: 100%; max-width: 640px; height: 420px; margin: 0 auto; background: var(--c-ink); border-radius: var(--radius-lg); overflow: hidden; display: flex; align-items: center; justify-content: center; border: 2px solid var(--c-stone-soft);">
      
      <video id="webcam" autoplay muted playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); display: none;"></video>
      <canvas id="overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; transform: scaleX(-1);"></canvas>

      <!-- Corner Brackets overlay -->
      <div style="position: absolute; width: 220px; height: 220px; border: 2px dashed rgba(223, 208, 184, 0.6); border-radius: 50%; pointer-events: none; box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.35);"></div>

      <!-- Placeholder Text before camera start -->
      <div id="camera-placeholder" style="text-align: center; color: var(--c-sand); padding: 20px;">
        <svg style="width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.7;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
        <p style="margin: 0; font-size: 0.95rem;">Click <strong>Start Camera</strong> to begin enrollment</p>
      </div>

      <!-- Live Loading Spinner -->
      <div id="model-loader" style="display: none; position: absolute; text-align: center; color: var(--c-sand); background: rgba(0,0,0,0.7); width: 100%; height: 100%; top: 0; left: 0; align-items: center; justify-content: center; flex-direction: column;">
        <div class="spinner" style="border: 3px solid rgba(255,255,255,0.2); border-top-color: var(--c-sand); border-radius: 50%; width: 36px; height: 36px; animation: spin 0.8s linear infinite; margin-bottom: 12px;"></div>
        <span>Loading AI Neural Models...</span>
      </div>
    </div>

    <div id="sample-preview-panel" class="card" style="display: none; max-width: 640px; margin: 16px auto 0; padding: 16px; text-align: center;">
      <p style="margin: 0 0 12px; font-weight: 700;">Review this photo</p>
      <img id="sample-preview-image" alt="Temporary face sample preview" style="display: block; width: 100%; max-height: 260px; object-fit: contain; background: var(--c-ink); border-radius: var(--radius-md); transform: scaleX(-1);">
      <p style="margin: 12px 0 0; color: var(--c-slate); font-size: 0.88rem;">If your face is clear, approve this sample. Otherwise retake it. The photo is only shown here and is not uploaded.</p>
    </div>

    <!-- Scanner Action Controls -->
    <div style="display: flex; gap: 12px; margin-top: 20px; justify-content: center; flex-wrap: wrap;">
      <button id="btn-start-cam" class="btn btn-primary">Start Camera</button>
      <button id="btn-capture-sample" class="btn btn-secondary" disabled><span id="capture-label">Capture Sample</span> (<span id="sample-count">0</span>/3)</button>
      <button id="btn-confirm-sample" class="btn btn-primary" type="button" style="display: none;">Use This Photo</button>
      <button id="btn-reset-samples" class="btn btn-outline" style="display: none;">Reset Samples</button>
      <button id="btn-save-descriptor" class="btn btn-primary" style="display: none;">Save Face Enrollment</button>
    </div>
  </div>

  <!-- Instruction Guide & Samples Summary -->
  <div>
    <div class="card reveal" style="margin-bottom: 24px;">
      <div class="card-header">
        <h3 class="card-title">Registration Guidelines</h3>
      </div>
      <ul style="padding-left: 20px; font-size: 0.88rem; line-height: 1.6; color: var(--c-slate); margin: 0;">
        <li>Ensure your face is well-lit from the front.</li>
        <li>Position your face inside the central circular guide.</li>
        <li>Only <strong>one face</strong> must be visible in the camera frame.</li>
        <li>We take <strong>3 quality samples</strong> and average the 128-dimensional embedding vector.</li>
        <li>No actual images are saved; only biometric math vectors.</li>
      </ul>
    </div>

    <div class="card reveal">
      <div class="card-header">
        <h3 class="card-title">Captured Samples</h3>
      </div>
      <div id="samples-container" style="display: flex; gap: 10px; flex-wrap: wrap; min-height: 60px; align-items: center; justify-content: center; background: var(--c-sand); padding: 12px; border-radius: var(--radius-md);">
        <span style="font-size: 0.82rem; color: var(--c-stone);">No face samples captured yet.</span>
      </div>
    </div>
  </div>
</div>

<style>
@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>

<!-- Load local face-api.js script -->
<script src="<?= BASE_URL ?>/assets/js/face-api.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const video = document.getElementById('webcam');
    const overlay = document.getElementById('overlay');
    const placeholder = document.getElementById('camera-placeholder');
    const modelLoader = document.getElementById('model-loader');
    const scanStatus = document.getElementById('scan-status');
    const btnStartCam = document.getElementById('btn-start-cam');
    const btnCapture = document.getElementById('btn-capture-sample');
    const captureLabel = document.getElementById('capture-label');
    const btnConfirm = document.getElementById('btn-confirm-sample');
    const btnReset = document.getElementById('btn-reset-samples');
    const btnSave = document.getElementById('btn-save-descriptor');
    const sampleCountEl = document.getElementById('sample-count');
    const samplesContainer = document.getElementById('samples-container');
    const previewPanel = document.getElementById('sample-preview-panel');
    const previewImage = document.getElementById('sample-preview-image');

    const CSRF_TOKEN = '<?= e(\App\Core\Csrf::generateToken()) ?>';
    const MODEL_PATH = '<?= BASE_URL ?>/assets/models';
    const CAMERA_MODE = '<?= e($cameraMode) ?>';
    const CAMERA_RESOLUTION = '<?= e($cameraResolution) ?>'.split('x').map(Number);
    const REQUIRED_SAMPLES = 3;

    let isModelLoaded = false;
    let stream = null;
    let capturedDescriptors = [];
    let pendingDescriptor = null;

    btnStartCam.addEventListener('click', async () => {
        btnStartCam.disabled = true;
        modelLoader.style.display = 'flex';
        scanStatus.textContent = 'Loading Models...';

        try {
            if (!isModelLoaded) {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_PATH),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_PATH),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_PATH)
                ]);
                isModelLoaded = true;
            }

            modelLoader.style.display = 'none';
            scanStatus.textContent = 'Opening Camera...';

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
            if (video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA) {
                await new Promise((resolve, reject) => {
                    const timeout = window.setTimeout(() => reject(new Error('The camera did not provide a video frame in time. Please retry.')), 8000);
                    video.addEventListener('loadeddata', () => {
                        window.clearTimeout(timeout);
                        resolve();
                    }, { once: true });
                });
            }
            video.style.display = 'block';
            placeholder.style.display = 'none';

            scanStatus.textContent = 'Camera Active';
            scanStatus.className = 'badge badge-present';
            btnCapture.disabled = false;
            btnStartCam.style.display = 'none';

        } catch (err) {
            console.error('Camera/Model Error:', err);
            stream?.getTracks().forEach((track) => track.stop());
            stream = null;
            video.srcObject = null;
            video.style.display = 'none';
            placeholder.style.display = 'block';
            modelLoader.style.display = 'none';
            btnStartCam.disabled = false;
            scanStatus.textContent = 'Error Opening Camera';
            scanStatus.className = 'badge badge-absent';
            alert('Could not access camera or load face recognition models: ' + err.message);
        }
    });

    btnCapture.addEventListener('click', async () => {
        if (!isModelLoaded || !stream || btnCapture.disabled) return;

        btnCapture.disabled = true;
        btnConfirm.style.display = 'none';
        previewPanel.style.display = 'none';
        pendingDescriptor = null;
        captureLabel.textContent = 'Analyzing Photo...';
        scanStatus.textContent = 'Analyzing Face...';

        let detections;
        let timeoutId;
        try {
            if (video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA || !video.videoWidth || !video.videoHeight) {
                throw new Error('The camera is still preparing a frame. Wait a moment and retry.');
            }

            const timeout = new Promise((_, reject) => {
                timeoutId = window.setTimeout(() => reject(new Error('Face analysis took too long. Check your lighting and retry.')), 20000);
            });
            detections = await Promise.race([
                faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.45 }))
                .withFaceLandmarks()
                .withFaceDescriptors(),
                timeout
            ]);
        } catch (error) {
            console.error('Face sample capture failed:', error);
            captureLabel.textContent = capturedDescriptors.length > 0 ? 'Capture Next Sample' : 'Capture Sample';
            scanStatus.textContent = error.message || 'Could not analyze photo. Please retry.';
            scanStatus.className = 'badge badge-absent';
            btnCapture.disabled = false;
            return;
        } finally {
            window.clearTimeout(timeoutId);
        }

        if (detections.length !== 1) {
            scanStatus.textContent = detections.length === 0
                ? 'No face found. Center your face and retry.'
                : 'More than one face found. Make sure you are alone and retry.';
            scanStatus.className = 'badge badge-absent';
            captureLabel.textContent = capturedDescriptors.length > 0 ? 'Capture Next Sample' : 'Capture Sample';
            btnCapture.disabled = false;
            return;
        }

        const detection = detections[0];
        if (!detection?.descriptor || detection.descriptor.length !== 128) {
            scanStatus.textContent = 'Face details could not be read. Improve lighting and retry.';
            scanStatus.className = 'badge badge-absent';
            captureLabel.textContent = capturedDescriptors.length > 0 ? 'Capture Next Sample' : 'Capture Sample';
            btnCapture.disabled = false;
            return;
        }

        if (!video.videoWidth || !video.videoHeight) {
            scanStatus.textContent = 'Camera frame is not ready. Please retry.';
            scanStatus.className = 'badge badge-absent';
            captureLabel.textContent = capturedDescriptors.length > 0 ? 'Capture Next Sample' : 'Capture Sample';
            btnCapture.disabled = false;
            return;
        }

        pendingDescriptor = Array.from(detection.descriptor);
        const snapshot = document.createElement('canvas');
        snapshot.width = video.videoWidth;
        snapshot.height = video.videoHeight;
        snapshot.getContext('2d').drawImage(video, 0, 0, snapshot.width, snapshot.height);
        previewImage.src = snapshot.toDataURL('image/jpeg', 0.88);
        previewPanel.style.display = 'block';
        captureLabel.textContent = 'Retake Photo';
        scanStatus.textContent = 'Review photo, then approve it';
        scanStatus.className = 'badge badge-pending';
        btnConfirm.style.display = 'inline-flex';
        btnCapture.disabled = false;
    });

    btnConfirm.addEventListener('click', () => {
        if (!pendingDescriptor) return;
        capturedDescriptors.push(pendingDescriptor);
        pendingDescriptor = null;
        previewImage.removeAttribute('src');
        previewPanel.style.display = 'none';

        // Update UI
        sampleCountEl.textContent = capturedDescriptors.length;
        if (capturedDescriptors.length === 1) {
            samplesContainer.innerHTML = '';
        }

        const sampleBadge = document.createElement('div');
        sampleBadge.style.padding = '6px 12px';
        sampleBadge.style.background = 'var(--c-paper)';
        sampleBadge.style.border = '1px solid var(--c-stone-soft)';
        sampleBadge.style.borderRadius = 'var(--radius-sm)';
        sampleBadge.style.fontSize = '0.8rem';
        sampleBadge.style.fontWeight = '600';
        sampleBadge.textContent = `Sample #${capturedDescriptors.length} Approved ✓`;
        samplesContainer.appendChild(sampleBadge);

        btnReset.style.display = 'inline-block';
        btnConfirm.style.display = 'none';

        if (capturedDescriptors.length >= REQUIRED_SAMPLES) {
            btnCapture.style.display = 'none';
            btnSave.style.display = 'inline-block';
            scanStatus.textContent = 'All Samples Captured!';
            scanStatus.className = 'badge badge-present';
        } else {
            captureLabel.textContent = 'Capture Next Sample';
            btnCapture.disabled = false;
            scanStatus.textContent = 'Camera Active';
            scanStatus.className = 'badge badge-present';
        }
    });

    btnReset.addEventListener('click', () => {
        capturedDescriptors = [];
        pendingDescriptor = null;
        previewImage.removeAttribute('src');
        previewPanel.style.display = 'none';
        sampleCountEl.textContent = '0';
        samplesContainer.innerHTML = '<span style="font-size: 0.82rem; color: var(--c-stone);">No face samples captured yet.</span>';
        btnSave.style.display = 'none';
        btnReset.style.display = 'none';
        btnCapture.style.display = 'inline-block';
        captureLabel.textContent = 'Capture Sample';
        btnCapture.disabled = false;
        btnConfirm.style.display = 'none';
        btnConfirm.disabled = false;
        btnSave.disabled = false;
        scanStatus.textContent = 'Camera Active';
    });

    btnSave.addEventListener('click', async () => {
        if (capturedDescriptors.length === 0) return;

        btnSave.disabled = true;
        scanStatus.textContent = 'Saving Biometrics...';

        // Average 128 floats across all captured samples
        const sampleCount = capturedDescriptors.length;
        const averaged = new Array(128).fill(0);

        for (let i = 0; i < 128; i++) {
            let sum = 0;
            for (let j = 0; j < sampleCount; j++) {
                sum += capturedDescriptors[j][i];
            }
            averaged[i] = sum / sampleCount;
        }

        const controller = new AbortController();
        const requestTimeout = window.setTimeout(() => controller.abort(), 20000);
        try {
            const resp = await fetch('<?= BASE_URL ?>/face/store', {
                method: 'POST',
                credentials: 'same-origin',
                signal: controller.signal,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    csrf_token: CSRF_TOKEN,
                    descriptor: averaged
                })
            });
            const contentType = resp.headers.get('content-type') || '';
            const data = contentType.includes('application/json')
                ? await resp.json()
                : { status: 'error', message: `The server returned ${resp.status} instead of a save confirmation.` };
            window.clearTimeout(requestTimeout);

            if (resp.ok && data.status === 'success') {
                alert(data.message);
                window.location.href = '<?= BASE_URL ?>/user/profile';
            } else {
                alert('Error: ' + data.message);
                btnSave.disabled = false;
                scanStatus.textContent = 'Save Failed';
            }
        } catch (e) {
            window.clearTimeout(requestTimeout);
            console.error(e);
            alert(e.name === 'AbortError'
                ? 'Saving timed out. Your face enrollment was not confirmed. Please try again.'
                : 'Failed to save face enrollment: ' + e.message);
            btnSave.disabled = false;
            scanStatus.textContent = 'Save Failed';
        }
    });
});
</script>
