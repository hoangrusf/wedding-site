/* ==========================================================
   WEDDING INVITATION - SCRIPT.JS (Laravel Dynamic Version)
   Logic: Mở thiệp, Countdown, Nhạc nền, Gallery Lightbox,
          RSVP form (API), Copy STK, Hiệu ứng hoa rơi, AOS
   ========================================================== */

document.addEventListener('DOMContentLoaded', () => {
  // ---- DOM Elements ----
  const envelopeScreen = document.getElementById('envelope-screen');
  const openBtn = document.getElementById('open-btn');
  const mainContent = document.getElementById('main-content');
  const mainNav = document.getElementById('main-nav');
  const musicToggle = document.getElementById('music-toggle');
  const bgMusic = document.getElementById('bg-music');
  const petalsCanvas = document.getElementById('petals-canvas');
  const lightbox = document.getElementById('lightbox');
  const lightboxImg = document.getElementById('lightbox-img');
  const rsvpForm = document.getElementById('rsvp-form');
  const toast = document.getElementById('toast');

  // Dữ liệu từ server (truyền qua window.weddingData)
  const wData = window.weddingData || {};

  // ============================================================
  // 1. MỞ THIỆP (Envelope Open Animation)
  // ============================================================
  openBtn.addEventListener('click', () => {
    const card = document.querySelector('.invitation-card');
    if (card) {
      card.style.transition = 'transform 0.8s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.8s ease';
      card.style.transform = 'translateY(-20px) scale(1.02)';
    }

    openBtn.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    openBtn.style.opacity = '0';
    openBtn.style.transform = 'scale(0.9)';
    openBtn.style.pointerEvents = 'none';

    setTimeout(() => {
      envelopeScreen.classList.add('fade-out');

      mainContent.classList.remove('hidden');
      mainContent.style.opacity = '0';
      mainContent.style.transition = 'opacity 1.2s cubic-bezier(0.4, 0, 0.2, 1)';

      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          mainContent.style.opacity = '1';
        });
      });

      setTimeout(() => mainNav.classList.add('visible'), 600);

      musicToggle.classList.remove('hidden');
      playMusic();

      startPetals();
      petalsCanvas.classList.add('active');

      AOS.init({
        duration: 900,
        easing: 'ease-out-cubic',
        once: true,
        offset: 80,
      });
    }, 1000);
  });

  // ============================================================
  // 2. NHẠC NỀN (Background Music)
  // ============================================================
  let isMusicPlaying = false;

  function playMusic() {
    bgMusic.volume = 0.4;
    const playPromise = bgMusic.play();
    if (playPromise !== undefined) {
      playPromise
        .then(() => {
          isMusicPlaying = true;
          musicToggle.classList.remove('paused');
        })
        .catch(() => {
          isMusicPlaying = false;
          musicToggle.classList.add('paused');
        });
    }
  }

  musicToggle.addEventListener('click', () => {
    if (isMusicPlaying) {
      bgMusic.pause();
      isMusicPlaying = false;
      musicToggle.classList.add('paused');
    } else {
      bgMusic.play().then(() => {
        isMusicPlaying = true;
        musicToggle.classList.remove('paused');
      }).catch(() => {});
    }
  });

  // ============================================================
  // 3. ĐỒNG HỒ ĐẾM NGƯỢC (Countdown Timer)
  // ============================================================
  const weddingDate = new Date(wData.weddingDate).getTime();

  function updateCountdown() {
    const now = Date.now();
    const diff = weddingDate - now;

    if (diff <= 0) {
      document.getElementById('cd-days').textContent = '🎉';
      document.getElementById('cd-hours').textContent = '--';
      document.getElementById('cd-minutes').textContent = '--';
      document.getElementById('cd-seconds').textContent = '--';
      return;
    }

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const minutes = Math.floor((diff / (1000 * 60)) % 60);
    const seconds = Math.floor((diff / 1000) % 60);

    document.getElementById('cd-days').textContent = String(days).padStart(2, '0');
    document.getElementById('cd-hours').textContent = String(hours).padStart(2, '0');
    document.getElementById('cd-minutes').textContent = String(minutes).padStart(2, '0');
    document.getElementById('cd-seconds').textContent = String(seconds).padStart(2, '0');
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);

  // ============================================================
  // 4. GALLERY LIGHTBOX
  // ============================================================
  const galleryItems = document.querySelectorAll('.gallery-item');
  const galleryImages = Array.from(galleryItems).map(item => item.querySelector('img').src);
  let currentImageIndex = 0;

  galleryItems.forEach((item, index) => {
    item.addEventListener('click', () => {
      currentImageIndex = index;
      openLightbox(galleryImages[index]);
    });
  });

  function openLightbox(src) {
    lightboxImg.src = src;
    lightbox.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    lightbox.classList.remove('open');
    document.body.style.overflow = '';
  }

  document.querySelector('.lightbox-close').addEventListener('click', closeLightbox);

  document.querySelector('.lightbox-prev').addEventListener('click', () => {
    currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
    lightboxImg.src = galleryImages[currentImageIndex];
  });

  document.querySelector('.lightbox-next').addEventListener('click', () => {
    currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
    lightboxImg.src = galleryImages[currentImageIndex];
  });

  lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) closeLightbox();
  });

  document.addEventListener('keydown', (e) => {
    if (!lightbox.classList.contains('open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') {
      currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
      lightboxImg.src = galleryImages[currentImageIndex];
    }
    if (e.key === 'ArrowRight') {
      currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
      lightboxImg.src = galleryImages[currentImageIndex];
    }
  });

  // ============================================================
  // 5. RSVP FORM — GỬI DỮ LIỆU QUA LARAVEL API
  // ============================================================
  rsvpForm.addEventListener('submit', handleRSVP);

  function handleRSVP(e) {
    e.preventDefault();

    const nameInput = document.getElementById('rsvp-name');
    const phoneInput = document.getElementById('guest-phone');
    const name = nameInput.value.trim();
    const phone = phoneInput.value.trim();
    const companionCount = document.getElementById('guest-count').value;
    const wish = document.getElementById('guest-wish').value.trim();

    // Validation
    if (!name) {
      showToast('Vui lòng nhập họ và tên');
      nameInput.focus();
      return;
    }
    if (!phone || !/^[0-9]{9,11}$/.test(phone)) {
      showToast('Vui lòng nhập số điện thoại hợp lệ (9-11 chữ số)');
      phoneInput.focus();
      return;
    }

    const btnSubmit = document.getElementById('btn-rsvp');
    const btnText = btnSubmit.querySelector('.btn-text');
    const btnLoader = btnSubmit.querySelector('.btn-loader');

    // Show loading
    btnSubmit.disabled = true;
    btnText.classList.add('hidden');
    btnLoader.classList.remove('hidden');

    // Build payload
    const payload = {
      guest_name: name,
      phone_number: phone,
      is_attending: true,
      companion_count: parseInt(companionCount) || 0,
      wishes_message: wish || null,
    };

    // Gắn guest_id nếu có
    if (wData.guestId) {
      payload.guest_id = wData.guestId;
    }

    // Gửi request đến Laravel API
    fetch(wData.rsvpUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': wData.csrfToken,
      },
      body: JSON.stringify(payload),
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(err => { throw err; });
      }
      return response.json();
    })
    .then(data => {
      // Ẩn loading
      btnSubmit.disabled = false;
      btnText.classList.remove('hidden');
      btnLoader.classList.add('hidden');

      // Thêm lời chúc vào wall
      if (wish) {
        addWishToWall(name, wish);
      }

      showToast('Cảm ơn bạn! Xác nhận đã được ghi nhận 💕');
      rsvpForm.reset();
    })
    .catch(err => {
      btnSubmit.disabled = false;
      btnText.classList.remove('hidden');
      btnLoader.classList.add('hidden');

      // Hiển thị lỗi validation nếu có
      if (err && err.errors) {
        const firstError = Object.values(err.errors)[0];
        showToast(firstError[0] || 'Có lỗi xảy ra, vui lòng thử lại');
      } else {
        showToast('Có lỗi xảy ra, vui lòng thử lại');
      }
    });
  }

  function addWishToWall(name, wish) {
    const wishesList = document.getElementById('wishes-list');
    const card = document.createElement('div');
    card.className = 'wish-card';
    const safeWish = escapeHTML(wish);
    const safeName = escapeHTML(name);
    card.innerHTML = `
      <p class="wish-text">"${safeWish}"</p>
      <p class="wish-author">— ${safeName}</p>
    `;
    wishesList.prepend(card);
  }

  // ============================================================
  // 6. COPY SỐ TÀI KHOẢN
  // ============================================================
  document.querySelectorAll('.btn-copy').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-target');
      const targetEl = document.getElementById(targetId);
      if (!targetEl) return;

      const stk = targetEl.textContent.trim();

      navigator.clipboard.writeText(stk).then(() => {
        btn.classList.add('copied');
        btn.textContent = '✅ Đã copy!';
        showToast('Đã copy STK: ' + stk);

        setTimeout(() => {
          btn.classList.remove('copied');
          btn.textContent = '📋 Copy STK';
        }, 2000);
      }).catch(() => {
        const textarea = document.createElement('textarea');
        textarea.value = stk;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        try {
          document.execCommand('copy');
          btn.classList.add('copied');
          btn.textContent = '✅ Đã copy!';
          showToast('Đã copy STK: ' + stk);
          setTimeout(() => {
            btn.classList.remove('copied');
            btn.textContent = '📋 Copy STK';
          }, 2000);
        } catch (_) {
          showToast('Không thể copy. Vui lòng copy thủ công.');
        }
        document.body.removeChild(textarea);
      });
    });
  });

  // ============================================================
  // 7. NAVIGATION - Active link on scroll
  // ============================================================
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.main-nav a');

  function onScroll() {
    const scrollY = window.scrollY + 120;
    sections.forEach(section => {
      const top = section.offsetTop;
      const height = section.offsetHeight;
      const id = section.getAttribute('id');
      if (scrollY >= top && scrollY < top + height) {
        navLinks.forEach(link => {
          link.classList.remove('active');
          if (link.getAttribute('href') === '#' + id) {
            link.classList.add('active');
          }
        });
      }
    });
  }

  window.addEventListener('scroll', onScroll, { passive: true });

  navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const targetId = link.getAttribute('href');
      const target = document.querySelector(targetId);
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ============================================================
  // 8. HIỆU ỨNG HOA ANH ĐÀO RƠI (Cherry Blossom Petals Canvas)
  // ============================================================
  const ctx = petalsCanvas.getContext('2d');
  let petals = [];
  let animationFrameId = null;

  function resizeCanvas() {
    petalsCanvas.width = window.innerWidth;
    petalsCanvas.height = window.innerHeight;
  }

  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  class Petal {
    constructor() {
      this.reset();
      this.y = Math.random() * petalsCanvas.height;
    }

    reset() {
      this.x = Math.random() * petalsCanvas.width;
      this.y = -20;
      this.size = Math.random() * 10 + 6;
      this.speedY = Math.random() * 1.2 + 0.5;
      this.speedX = Math.random() * 0.8 - 0.4;
      this.rotation = Math.random() * Math.PI * 2;
      this.rotationSpeed = (Math.random() - 0.5) * 0.02;
      this.opacity = Math.random() * 0.4 + 0.3;
      this.wobble = Math.random() * 2;
      this.wobbleSpeed = Math.random() * 0.02 + 0.01;
      const hue = Math.floor(Math.random() * 20) + 340;
      const sat = Math.floor(Math.random() * 30) + 50;
      const light = Math.floor(Math.random() * 20) + 75;
      this.color = `hsla(${hue}, ${sat}%, ${light}%, ${this.opacity})`;
    }

    update() {
      this.y += this.speedY;
      this.x += this.speedX + Math.sin(this.wobble) * 0.5;
      this.wobble += this.wobbleSpeed;
      this.rotation += this.rotationSpeed;

      if (this.y > petalsCanvas.height + 20) {
        this.reset();
      }
    }

    draw() {
      ctx.save();
      ctx.translate(this.x, this.y);
      ctx.rotate(this.rotation);
      ctx.fillStyle = this.color;
      ctx.beginPath();
      ctx.moveTo(0, 0);
      ctx.bezierCurveTo(
        this.size / 2, -this.size / 2,
        this.size, this.size / 4,
        0, this.size
      );
      ctx.bezierCurveTo(
        -this.size, this.size / 4,
        -this.size / 2, -this.size / 2,
        0, 0
      );
      ctx.fill();
      ctx.restore();
    }
  }

  function startPetals() {
    const petalCount = window.innerWidth < 768 ? 25 : 40;
    petals = [];
    for (let i = 0; i < petalCount; i++) {
      petals.push(new Petal());
    }
    animatePetals();
  }

  function animatePetals() {
    ctx.clearRect(0, 0, petalsCanvas.width, petalsCanvas.height);
    petals.forEach(petal => {
      petal.update();
      petal.draw();
    });
    animationFrameId = requestAnimationFrame(animatePetals);
  }

  // ============================================================
  // 9. TOAST NOTIFICATION
  // ============================================================
  function showToast(message, duration = 3000) {
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => {
      toast.classList.remove('show');
    }, duration);
  }

  // ============================================================
  // 10. HELPER - XSS Prevention
  // ============================================================
  function escapeHTML(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }
});
