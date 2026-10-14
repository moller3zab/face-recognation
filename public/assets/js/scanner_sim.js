/* ==========================================================================
   FACIAL SCANNING STATE MACHINE SIMULATION (PROTOTYPE SHOWPIECE)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  const scanBadge = document.getElementById('scan-badge-text');
  const scanLine = document.querySelector('.scan-line');
  const resultModal = document.getElementById('attendance-result-modal');
  const startBtn = document.getElementById('start-scan-btn');

  if (!scanBadge || !startBtn) return;

  const states = [
    { text: 'Initializing Camera stream...', duration: 1500 },
    { text: 'Looking for Face...', duration: 2000 },
    { text: 'Liveness Challenge: Please turn head LEFT', duration: 2500 },
    { text: 'Extracting 128-d Biometric Descriptor...', duration: 1500 },
    { text: 'Verifying Distance on Server...', duration: 1500 },
    { text: 'Identity Verified & Attendance Recorded!', duration: 1000 }
  ];

  startBtn.addEventListener('click', () => {
    startBtn.disabled = true;
    startBtn.innerText = 'Scanning Active...';
    let step = 0;

    const runStep = () => {
      if (step < states.length) {
        scanBadge.innerText = states[step].text;
        const currentDuration = states[step].duration;
        step++;
        setTimeout(runStep, currentDuration);
      } else {
        if (resultModal) {
          resultModal.classList.add('active');
        }
        startBtn.disabled = false;
        startBtn.innerText = 'Start Scanner Challenge';
        scanBadge.innerText = 'Waiting for scanner...';
      }
    };

    runStep();
  });
});
