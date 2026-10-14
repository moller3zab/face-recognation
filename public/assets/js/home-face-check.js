document.addEventListener('DOMContentLoaded', () => {
  const widget = document.getElementById('home-face-check');
  if (!widget || !window.faceapi) return;

  const video = document.getElementById('home-check-video');
  const placeholder = document.getElementById('home-check-placeholder');
  const stateBadge = document.getElementById('home-check-state');
  const message = document.getElementById('home-check-message');
  const challenge = document.getElementById('home-check-challenge');
  const startButton = document.getElementById('home-check-start');
  const stopButton = document.getElementById('home-check-stop');
  const direction = widget.dataset.direction;
  const modelPath = widget.dataset.modelPath;
  const cameraMode = ['user', 'environment'].includes(widget.dataset.cameraMode) ? widget.dataset.cameraMode : 'user';
  const resolution = /^\d{3,4}x\d{3,4}$/.test(widget.dataset.cameraResolution || '')
    ? widget.dataset.cameraResolution.split('x').map(Number)
    : [640, 480];
  let stream = null;
  let scanTimer = null;
  let modelsReady = false;
  let frameBusy = false;
  let challengeStage = 'turn';

  function setState(text, kind = 'pending') {
    stateBadge.textContent = text;
    stateBadge.className = `badge badge-${kind}`;
  }

  function releaseCamera() {
    if (scanTimer !== null) {
      window.clearInterval(scanTimer);
      scanTimer = null;
    }
    if (stream) {
      stream.getTracks().forEach((track) => track.stop());
      stream = null;
    }
    video.pause();
    video.srcObject = null;
    video.style.display = 'none';
    placeholder.style.display = 'grid';
    challenge.style.display = 'none';
    stopButton.style.display = 'none';
    startButton.style.display = 'inline-flex';
    startButton.disabled = false;
    frameBusy = false;
  }

  function faceYawRatio(landmarks) {
    const nose = landmarks.getNose()[3];
    const jaw = landmarks.getJawOutline();
    const leftDistance = Math.abs(nose.x - jaw[0].x);
    const rightDistance = Math.abs(nose.x - jaw[16].x);
    return leftDistance / Math.max(rightDistance, 1);
  }

  async function verifyDescriptor(descriptor) {
    setState('Comparing with your profile…');
    message.textContent = 'Checking the live face against your enrolled biometric profile.';

    const response = await fetch(widget.dataset.verifyUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        csrf_token: widget.dataset.csrfToken,
        descriptor: Array.from(descriptor)
      })
    });
    const result = await response.json();

    if (!response.ok || result.status !== 'success') {
      throw new Error(result.message || 'The live face could not be verified.');
    }

    releaseCamera();
    setState('Identity verified', 'present');
    message.textContent = `${result.message} Match distance D = ${result.distance}. No attendance was recorded.`;
  }

  async function scanFrame() {
    if (!stream || frameBusy || video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA) return;
    frameBusy = true;

    try {
      const detections = await faceapi
        .detectAllFaces(video, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.55 }))
        .withFaceLandmarks()
        .withFaceDescriptors();

      if (detections.length === 0) {
        setState('Looking for your face…');
        message.textContent = 'Center your face in the camera frame.';
        return;
      }
      if (detections.length !== 1) {
        setState('One face at a time');
        message.textContent = 'More than one face is visible. Make sure only you are in the frame.';
        return;
      }

      const ratio = faceYawRatio(detections[0].landmarks);
      if (challengeStage === 'turn') {
        challenge.style.display = 'block';
        challenge.textContent = `Turn your head slowly to the ${direction.toUpperCase()}`;
        setState('Liveness check');
        message.textContent = 'Follow the on-screen head-turn challenge.';

        // The preview is mirrored, but landmarks use the unmirrored camera frame.
        const turned = direction === 'left' ? ratio > 1.4 : ratio < 0.72;
        if (turned) {
          challengeStage = 'return';
          challenge.textContent = 'Now face the camera again';
        }
        return;
      }

      challenge.textContent = 'Head movement confirmed. Comparing your face…';
      if (ratio >= 0.82 && ratio <= 1.22) {
        window.clearInterval(scanTimer);
        scanTimer = null;
        await verifyDescriptor(detections[0].descriptor);
      }
    } catch (error) {
      releaseCamera();
      setState('Verification failed', 'absent');
      message.textContent = error.message || 'The face check could not be completed. Please try again.';
    } finally {
      frameBusy = false;
    }
  }

  async function startCheck() {
    startButton.disabled = true;
    setState('Loading offline face models…');
    message.textContent = 'Loading the local face models. Your camera is not active yet.';

    try {
      if (!modelsReady) {
        await Promise.all([
          faceapi.nets.ssdMobilenetv1.loadFromUri(modelPath),
          faceapi.nets.faceLandmark68Net.loadFromUri(modelPath),
          faceapi.nets.faceRecognitionNet.loadFromUri(modelPath)
        ]);
        modelsReady = true;
      }

      if (!navigator.mediaDevices?.getUserMedia) {
        throw new Error('Camera access requires localhost or a secure HTTPS connection.');
      }

      setState('Waiting for camera permission…');
      stream = await navigator.mediaDevices.getUserMedia({
        video: {
          width: { ideal: resolution[0] },
          height: { ideal: resolution[1] },
          facingMode: { ideal: cameraMode }
        },
        audio: false
      });
      video.srcObject = stream;
      await video.play();
      video.style.display = 'block';
      placeholder.style.display = 'none';
      startButton.style.display = 'none';
      stopButton.style.display = 'inline-flex';
      challengeStage = 'turn';
      setState('Looking for your face…');
      message.textContent = 'The camera stays in this browser. Only the face descriptor is sent to this local app for comparison.';
      scanTimer = window.setInterval(scanFrame, 450);
    } catch (error) {
      releaseCamera();
      setState('Camera or model unavailable', 'absent');
      if (error.name === 'NotAllowedError') {
        message.textContent = 'Camera access was denied. Allow camera access in your browser, then try again.';
      } else if (error.name === 'NotFoundError') {
        message.textContent = 'No camera was found on this device.';
      } else {
        message.textContent = error.message || 'Could not start the face check. Please try again.';
      }
    }
  }

  startButton.addEventListener('click', startCheck);
  stopButton.addEventListener('click', () => {
    releaseCamera();
    setState('Camera is off');
    message.textContent = 'Camera stopped. Start the check when you are ready.';
  });
  window.addEventListener('pagehide', releaseCamera);
});
