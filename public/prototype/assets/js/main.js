/* ==========================================================================
   FACE RECOGNITION ATTENDANCE SYSTEM — PROTOTYPE INTERACTION SCRIPT
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  // Mobile Sidebar Toggle & Backdrop
  const mobileToggle = document.querySelector('.mobile-toggle');
  const sidebar = document.querySelector('.sidebar');
  
  if (sidebar) {
    let overlay = document.querySelector('.sidebar-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'sidebar-overlay';
      document.body.appendChild(overlay);
    }

    const toggleSidebar = (open) => {
      const shouldOpen = open !== undefined ? open : !sidebar.classList.contains('mobile-open');
      sidebar.classList.toggle('mobile-open', shouldOpen);
      overlay.classList.toggle('active', shouldOpen);
      if (mobileToggle) {
        mobileToggle.setAttribute('aria-expanded', String(shouldOpen));
        mobileToggle.setAttribute('aria-label', shouldOpen ? 'Close navigation' : 'Open navigation');
      }
    };

    if (mobileToggle) {
      mobileToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleSidebar();
      });
    }

    overlay.addEventListener('click', () => toggleSidebar(false));

    // Close on navigation link click on mobile
    sidebar.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => toggleSidebar(false));
    });
  }

  // Scroll Reveal Observer
  const revealElements = document.querySelectorAll('.reveal');
  if (revealElements.length > 0) {
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.1 });

    revealElements.forEach(el => revealObserver.observe(el));
  }

  // Animated Counter Effect
  const counters = document.querySelectorAll('.stat-number[data-target]');
  counters.forEach(counter => {
    const target = parseInt(counter.getAttribute('data-target'), 10);
    let count = 0;
    const increment = Math.ceil(target / 40);
    const updateCount = () => {
      count += increment;
      if (count >= target) {
        counter.innerText = target;
      } else {
        counter.innerText = count;
        setTimeout(updateCount, 30);
      }
    };
    updateCount();
  });

  // Modal Dialog Actions
  const modalTriggers = document.querySelectorAll('[data-modal-target]');
  modalTriggers.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-modal-target');
      const modal = document.getElementById(targetId);
      if (modal) modal.classList.add('active');
    });
  });

  const modalCloses = document.querySelectorAll('.modal-close, .modal-overlay');
  modalCloses.forEach(closeBtn => {
    closeBtn.addEventListener('click', (e) => {
      if (e.target === closeBtn || closeBtn.classList.contains('modal-close')) {
        const modal = closeBtn.closest('.modal-overlay');
        if (modal) modal.classList.remove('active');
      }
    });
  });
});
