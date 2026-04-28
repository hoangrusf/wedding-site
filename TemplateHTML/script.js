/* ==========================================================
   WEDDING INVITATION - SCRIPT.JS
   Logic: Mở thiệp, Countdown, Nhạc nền, Gallery Lightbox,
          RSVP form, Copy STK, Hiệu ứng hoa rơi, AOS
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

  // Detect mobile devices
  const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

  // ============================================================
  // 0. TÊN KHÁCH MỜI TỪ URL (Dynamic URL Parameter)
  // ============================================================
  // URL ví dụ: thiepcuoi.com?invite=Hoàng
  //            thiepcuoi.com?invite=Gia%20Đình%20Anh%20Tuấn
  //            thiepcuoi.com?invite=Loan&type=1  → Lễ Thành Hôn
  //            thiepcuoi.com?invite=Loan&type=2  → Lễ Vu Quy
  (function setURLParams() {
    const params = new URLSearchParams(window.location.search);

    // --- Tên khách mời ---
    const inviteName = params.get('invite');
    const guestEl = document.getElementById('invite-name');
    if (guestEl) {
      if (inviteName && inviteName.trim().length > 0) {
        guestEl.textContent = inviteName.trim();
      } else {
        guestEl.textContent = 'Bạn và Người thương';
      }
    }

    // --- Loại lễ: type=1 (Thành Hôn, mặc định), type=2 (Vu Quy) ---
    // Mặc định HTML: thiệp bìa = "Lễ Thành Hôn", nhà trai = "lễ thành hôn", nhà gái = "lễ vu quy"
    // type=2: thiệp bìa đổi thành "Lễ Vu Quy", cả hai họ đều "lễ vu quy"
    const type = params.get('type');
    const titleEl = document.getElementById('card-title-ceremony');
    const labelEl = document.getElementById('card-label-ceremony');
    const groomFamilyEl = document.getElementById('family-groom-ceremony');
    const brideFamilyEl = document.getElementById('family-bride-ceremony');
    if (type === '2') {
      if (titleEl) titleEl.textContent = 'Lễ Vu Quy';
      if (labelEl) labelEl.textContent = 'đến dự lễ vu quy của chúng tôi';
      if (groomFamilyEl) groomFamilyEl.textContent = 'Trân trọng mời bạn đến dự lễ vu quy của con trai chúng tôi';
      if (brideFamilyEl) brideFamilyEl.textContent = 'Trân trọng mời bạn đến dự lễ vu quy của con gái chúng tôi';
    }
    // type=1 (hoặc không có type): giữ mặc định HTML
  })();

  // ============================================================
  // 1. MỞ THIỆP (Envelope Open Animation - Smoother)
  // ============================================================
  openBtn.addEventListener('click', () => {
    // Hiệu ứng thiệp nâng lên
    const card = document.querySelector('.invitation-card');
    if (card) {
      card.style.transition = 'transform 0.8s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.8s ease';
      card.style.transform = 'translateY(-20px) scale(1.02)';
    }

    // Fade out nút mượt mà
    openBtn.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    openBtn.style.opacity = '0';
    openBtn.style.transform = 'scale(0.9)';
    openBtn.style.pointerEvents = 'none';

    // Sau 1s → fade out toàn bộ envelope screen + hiện nội dung
    setTimeout(() => {
      envelopeScreen.classList.add('fade-out');

      // Chuẩn bị nội dung chính
      mainContent.classList.remove('hidden');
      mainContent.style.opacity = '0';
      mainContent.style.transition = 'opacity 1.2s cubic-bezier(0.4, 0, 0.2, 1)';

      // Chờ envelope biến mất rồi fade in
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          mainContent.style.opacity = '1';
        });
      });

      // Hiện navigation với delay
      setTimeout(() => mainNav.classList.add('visible'), 600);

      // Hiện nút nhạc + bắt đầu phát nhạc
      musicToggle.classList.remove('hidden');
      playMusic();

      // Bắt đầu hiệu ứng hoa rơi
      startPetals();
      petalsCanvas.classList.add('active');

      // Khởi tạo AOS
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
          // Autoplay bị chặn → đánh dấu paused
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
  // ✏️ THAY NGÀY CƯỚI Ở ĐÂY (format: 'YYYY-MM-DDTHH:MM:SS')
  const weddingDate = new Date('2026-08-15T17:00:00').getTime();
  let countdownInterval = null;

  function updateCountdown() {
    const now = Date.now();
    const diff = weddingDate - now;

    if (diff <= 0) {
      document.getElementById('cd-days').textContent = '🎉';
      document.getElementById('cd-hours').textContent = '--';
      document.getElementById('cd-minutes').textContent = '--';
      document.getElementById('cd-seconds').textContent = '--';
      if (countdownInterval) {
        clearInterval(countdownInterval);
        countdownInterval = null;
      }
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
  countdownInterval = setInterval(updateCountdown, 1000);

  // Pause countdown when tab is not visible
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      if (countdownInterval) {
        clearInterval(countdownInterval);
        countdownInterval = null;
      }
    } else {
      if (!countdownInterval) {
        updateCountdown();
        countdownInterval = setInterval(updateCountdown, 1000);
      }
    }
  });

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

  // Đóng khi click nền đen
  lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) closeLightbox();
  });

  // Phím tắt
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
  // 5. RSVP FORM
  // ============================================================
  rsvpForm.addEventListener('submit', handleRSVP);

  function handleRSVP(e) {
    e.preventDefault();

    const nameInput = document.getElementById('rsvp-name');
    const phoneInput = document.getElementById('guest-phone');
    const name = nameInput.value.trim();
    const phone = phoneInput.value.trim();
    const guests = document.getElementById('guest-count').value;
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

    /*
     * ✏️ GẮN API Ở ĐÂY
     * ---------------------------------------------------
     * Ví dụ gửi data đến Google Sheets qua Google Apps Script:
     *
     * fetch('https://script.google.com/macros/s/YOUR_SCRIPT_ID/exec', {
     *   method: 'POST',
     *   body: JSON.stringify({ name, phone, guests, wish }),
     *   headers: { 'Content-Type': 'application/json' }
     * })
     * .then(res => res.json())
     * .then(data => { ... })
     * .catch(err => { ... });
     *
     * Hoặc gửi Email qua EmailJS:
     * emailjs.send('service_id', 'template_id', { name, phone, guests, wish });
     * ---------------------------------------------------
     */

    // Giả lập gửi (setTimeout 1.5s)
    setTimeout(() => {
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
    }, 1500);
  }

  function addWishToWall(name, wish) {
    const wishesList = document.getElementById('wishes-list');
    const card = document.createElement('div');
    card.className = 'wish-card';
    // Encode text to prevent XSS
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
        // Fallback cho trình duyệt cũ
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

  // Throttle scroll event for better performance
  let scrollTimeout;
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

  function throttledScroll() {
    if (scrollTimeout) return;
    scrollTimeout = setTimeout(() => {
      onScroll();
      scrollTimeout = null;
    }, 100); // Throttle to max once per 100ms
  }

  window.addEventListener('scroll', throttledScroll, { passive: true });

  // Smooth scroll cho nav links
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
  let isPageVisible = true;

  function resizeCanvas() {
    petalsCanvas.width = window.innerWidth;
    petalsCanvas.height = window.innerHeight;
  }

  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  // Pause animation when tab is not visible (saves battery & CPU)
  document.addEventListener('visibilitychange', () => {
    isPageVisible = !document.hidden;
    if (isPageVisible && petals.length > 0) {
      animatePetals();
    } else if (!isPageVisible && animationFrameId) {
      cancelAnimationFrame(animationFrameId);
      animationFrameId = null;
    }
  });

  class Petal {
    constructor() {
      this.reset();
      // Bắt đầu ở vị trí ngẫu nhiên trên toàn màn hình
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
      // Màu cánh hoa - hồng nhạt đến đậm
      const hue = Math.floor(Math.random() * 20) + 340; // 340->360 (hồng)
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
      // Vẽ cánh hoa bằng 2 đường cong bezier
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
    if (!isPageVisible) return; // Don't animate when tab is hidden
    
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
