<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
  <title>Thiệp Cưới - <?php echo e($config->groom_name); ?> & <?php echo e($config->bride_name); ?></title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Dancing+Script:wght@400;700&family=Great+Vibes&family=Montserrat:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400;0,700;1,400&display=swap"
    rel="stylesheet"
  />

  <!-- AOS - Animate On Scroll -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />

  <link rel="stylesheet" href="<?php echo e(asset('css/wedding.css')); ?>" />
</head>
<body>
  <?php
    $resolveUrl = fn($value, $folder) => ($value && !str_starts_with($value, 'http://') && !str_starts_with($value, 'https://'))
        ? asset($folder . '/' . $value)
        : $value;

    $heroImageUrl  = $resolveUrl($config->hero_image_url,  'hero_image_url');
    $heroImagePos  = $config->hero_image_position ?? 'center center';
    $groomImageUrl = $resolveUrl($config->groom_image_url ?? null, 'groom_image_url');
    $groomImagePos = $config->groom_image_position ?? 'center center';
    $brideImageUrl = $resolveUrl($config->bride_image_url ?? null, 'bride_image_url');
    $brideImagePos = $config->bride_image_position ?? 'center center';

    if (!empty($bankInfo['groom']['qr_url'])) {
        $bankInfo['groom']['qr_url'] = $resolveUrl($bankInfo['groom']['qr_url'], 'groom_qr_url');
    }
    if (!empty($bankInfo['bride']['qr_url'])) {
        $bankInfo['bride']['qr_url'] = $resolveUrl($bankInfo['bride']['qr_url'], 'bride_qr_url');
    }
  ?>
  <!-- CANVAS - Hiệu ứng hoa anh đào rơi -->
  <canvas id="petals-canvas"></canvas>

  <!-- CANVAS - Hiệu ứng pháo hoa -->
  <canvas id="fireworks-canvas"></canvas>

  <!-- MÀN HÌNH CHỜ - PHONG BÌ -->
  <div id="envelope-screen">
    <div class="envelope-wrapper">
      <div class="invitation-card">
        <div class="card-border-frame"></div>
        <div class="card-inner">
          <div class="card-ornament">
            <svg viewBox="0 0 120 16" class="ornament-svg"><line x1="0" y1="8" x2="40" y2="8" stroke="currentColor" stroke-width="0.5" opacity="0.3"/><circle cx="48" cy="8" r="1.5" fill="currentColor" opacity="0.2"/><circle cx="60" cy="8" r="2.5" fill="currentColor" opacity="0.3"/><circle cx="72" cy="8" r="1.5" fill="currentColor" opacity="0.2"/><line x1="80" y1="8" x2="120" y2="8" stroke="currentColor" stroke-width="0.5" opacity="0.3"/></svg>
          </div>

          <p class="card-subtitle">Wedding Invitation</p>
          <p class="card-label">TRÂN TRỌNG KÍNH MỜI</p>
          <p class="card-guest-name" id="invite-name"><?php echo e($displayName); ?></p>
          <p class="card-label-small" id="card-label-ceremony">
            <?php if($type == 2): ?>
              đến dự lễ vu quy của chúng tôi
            <?php else: ?>
              đến dự lễ thành hôn của chúng tôi
            <?php endif; ?>
          </p>

          <div class="card-divider-line"></div>

          <h1 class="card-title" id="card-title-ceremony">
            <?php if($type == 2): ?>
              Lễ Vu Quy
            <?php else: ?>
              Lễ Thành Hôn
            <?php endif; ?>
          </h1>

          <div class="card-couple-names">
            <?php if($type == 2): ?>
              <span class="bride-name"><?php echo e($config->bride_name); ?></span>
              <span class="amp">&amp;</span>
              <span class="groom-name"><?php echo e($config->groom_name); ?></span>
            <?php else: ?>
              <span class="groom-name"><?php echo e($config->groom_name); ?></span>
              <span class="amp">&amp;</span>
              <span class="bride-name"><?php echo e($config->bride_name); ?></span>
            <?php endif; ?>
          </div>

          <p class="card-date"><?php echo e($config->wedding_date->format('d')); ?> &bull; <?php echo e($config->wedding_date->format('m')); ?> &bull; <?php echo e($config->wedding_date->format('Y')); ?></p>

          <div class="card-ornament">
            <svg viewBox="0 0 120 16" class="ornament-svg"><line x1="0" y1="8" x2="40" y2="8" stroke="currentColor" stroke-width="0.5" opacity="0.3"/><circle cx="60" cy="8" r="2.5" fill="currentColor" opacity="0.25"/><line x1="80" y1="8" x2="120" y2="8" stroke="currentColor" stroke-width="0.5" opacity="0.3"/></svg>
          </div>
        </div>
      </div>

      <button id="open-btn" class="open-envelope-btn" aria-label="Mở thiệp cưới">
        <span>Mở Thiệp Cưới</span>
      </button>
    </div>
  </div>

  <!-- NỘI DUNG CHÍNH -->
  <main id="main-content" class="main-content hidden">

    <!-- NAVIGATION -->
    <nav id="main-nav" class="main-nav">
      <ul>
        <li><a href="#hero">Trang chủ</a></li>
        <li><a href="#family">Hai bên gia đình</a></li>
        <li><a href="#gallery">Ảnh cưới</a></li>
        <li><a href="#event">Sự kiện</a></li>
        <li><a href="#rsvp">Xác nhận tham dự</a></li>
        <li><a href="#gift">Mừng cưới</a></li>
      </ul>
    </nav>

    <!-- HERO SECTION -->
    <section id="hero" class="hero-section" <?php if($heroImageUrl): ?> style="background-image: url('<?php echo e($heroImageUrl); ?>'); background-position: <?php echo e($heroImagePos); ?>" <?php endif; ?>>
      <div class="hero-overlay"></div>
      <div class="hero-content" data-aos="fade-up" data-aos-duration="1200">
        <p class="hero-subtitle">We're Getting Married</p>
        <h1 class="hero-names">
          <?php if($type == 2): ?>
            <span class="hero-bride"><?php echo e($config->bride_name); ?></span>
            <span class="hero-amp">&amp;</span>
            <span class="hero-groom"><?php echo e($config->groom_name); ?></span>
          <?php else: ?>
            <span class="hero-groom"><?php echo e($config->groom_name); ?></span>
            <span class="hero-amp">&amp;</span>
            <span class="hero-bride"><?php echo e($config->bride_name); ?></span>
          <?php endif; ?>
        </h1>
        <p class="hero-date"><?php echo e($config->wedding_date->format('d')); ?> . <?php echo e($config->wedding_date->format('m')); ?> . <?php echo e($config->wedding_date->format('Y')); ?></p>
        <p class="hero-quote">"Yêu là cùng nhau bước qua mọi mùa, dù nắng hay mưa"</p>

        <!-- Đồng hồ đếm ngược -->
        <div class="countdown" id="countdown">
          <div class="countdown-item">
            <span id="cd-days" class="cd-number">00</span>
            <span class="cd-label">Ngày</span>
          </div>
          <div class="countdown-item">
            <span id="cd-hours" class="cd-number">00</span>
            <span class="cd-label">Giờ</span>
          </div>
          <div class="countdown-item">
            <span id="cd-minutes" class="cd-number">00</span>
            <span class="cd-label">Phút</span>
          </div>
          <div class="countdown-item">
            <span id="cd-seconds" class="cd-number">00</span>
            <span class="cd-label">Giây</span>
          </div>
        </div>
      </div>
    </section>

    <!-- THÔNG TIN HAI HỌ -->
    <section id="family" class="family-section">
      <div class="section-header" data-aos="fade-up">
        <h2 class="section-title">Hai Bên Gia Đình</h2>
        <div class="section-divider">❧</div>
      </div>
      <div class="family-grid">
        <?php
          $groomParentsParts = explode(' & ', $config->groom_parents);
          $brideParentsParts = explode(' & ', $config->bride_parents);
        ?>
        <?php if($type == 2): ?>
        <!-- Nhà Gái (hiển thị trước khi type=2) -->
        <div class="family-card bride-family" data-aos="fade-right" data-aos-delay="200">
          <div class="family-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V21a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/><path d="M9 22V12h6v10"/><path d="M12 3c0 0 2-2 4-1" stroke-dasharray="2 2" opacity="0.5"/><circle cx="18" cy="5" r="1.5" fill="currentColor" opacity="0.3"/></svg>
          </div>
          <h3>Nhà Gái</h3>
          <?php if(count($brideParentsParts) >= 2): ?>
            <p class="parent-name"><strong>Ông:</strong> <?php echo e($brideParentsParts[0]); ?></p>
            <p class="parent-name"><strong>Bà:</strong> <?php echo e($brideParentsParts[1]); ?></p>
          <?php else: ?>
            <p class="parent-name"><?php echo e($config->bride_parents); ?></p>
          <?php endif; ?>
          <p class="family-address"><?php echo e($config->event_address); ?></p>
          <div class="family-child">
            <p id="family-bride-ceremony">Trân trọng mời bạn đến dự lễ vu quy của con gái chúng tôi</p>
            <p class="child-name"><?php echo e($config->bride_name); ?></p>
          </div>
        </div>
        <!-- Nhà Trai -->
        <div class="family-card groom-family" data-aos="fade-left" data-aos-delay="200">
          <div class="family-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V21a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/><path d="M9 22V12h6v10"/></svg>
          </div>
          <h3>Nhà Trai</h3>
          <?php if(count($groomParentsParts) >= 2): ?>
            <p class="parent-name"><strong>Ông:</strong> <?php echo e($groomParentsParts[0]); ?></p>
            <p class="parent-name"><strong>Bà:</strong> <?php echo e($groomParentsParts[1]); ?></p>
          <?php else: ?>
            <p class="parent-name"><?php echo e($config->groom_parents); ?></p>
          <?php endif; ?>
          <p class="family-address"><?php echo e($config->event_address); ?></p>
          <div class="family-child">
            <p id="family-groom-ceremony">Trân trọng mời bạn đến dự lễ thành hôn của con trai chúng tôi</p>
            <p class="child-name"><?php echo e($config->groom_name); ?></p>
          </div>
        </div>
        <?php else: ?>
        <!-- Nhà Trai (type=1, mặc định) -->
        <div class="family-card groom-family" data-aos="fade-right" data-aos-delay="200">
          <div class="family-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V21a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/><path d="M9 22V12h6v10"/></svg>
          </div>
          <h3>Nhà Trai</h3>
          <?php if(count($groomParentsParts) >= 2): ?>
            <p class="parent-name"><strong>Ông:</strong> <?php echo e($groomParentsParts[0]); ?></p>
            <p class="parent-name"><strong>Bà:</strong> <?php echo e($groomParentsParts[1]); ?></p>
          <?php else: ?>
            <p class="parent-name"><?php echo e($config->groom_parents); ?></p>
          <?php endif; ?>
          <p class="family-address"><?php echo e($config->event_address); ?></p>
          <div class="family-child">
            <p id="family-groom-ceremony">Trân trọng mời bạn đến dự lễ thành hôn của con trai chúng tôi</p>
            <p class="child-name"><?php echo e($config->groom_name); ?></p>
          </div>
        </div>
        <!-- Nhà Gái -->
        <div class="family-card bride-family" data-aos="fade-left" data-aos-delay="200">
          <div class="family-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V21a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/><path d="M9 22V12h6v10"/><path d="M12 3c0 0 2-2 4-1" stroke-dasharray="2 2" opacity="0.5"/><circle cx="18" cy="5" r="1.5" fill="currentColor" opacity="0.3"/></svg>
          </div>
          <h3>Nhà Gái</h3>
          <?php if(count($brideParentsParts) >= 2): ?>
            <p class="parent-name"><strong>Ông:</strong> <?php echo e($brideParentsParts[0]); ?></p>
            <p class="parent-name"><strong>Bà:</strong> <?php echo e($brideParentsParts[1]); ?></p>
          <?php else: ?>
            <p class="parent-name"><?php echo e($config->bride_parents); ?></p>
          <?php endif; ?>
          <p class="family-address"><?php echo e($config->event_address); ?></p>
          <div class="family-child">
            <p id="family-bride-ceremony">Trân trọng mời bạn đến dự lễ vu quy của con gái chúng tôi</p>
            <p class="child-name"><?php echo e($config->bride_name); ?></p>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- ALBUM ẢNH CƯỚI -->
    <section id="gallery" class="gallery-section">
      <div class="section-header" data-aos="fade-up">
        <h2 class="section-title">Album Ảnh Cưới</h2>
        <div class="section-divider">✦</div>
      </div>
      <?php if($galleryPhotos->count() > 0): ?>
      <div class="gallery-grid" data-aos="fade-up" data-aos-delay="200">
        <?php $__currentLoopData = $galleryPhotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $imgSrc = $resolveUrl($photo->image_url, 'gallery');
            $layoutClass = match($photo->layout) {
              'tall' => 'g-span-1 g-tall',
              'wide' => 'g-wide',
              default => 'g-span-1',
            };
            $imgFit = $photo->object_fit ?? 'cover';
            $imgPos = $photo->object_position ?? 'center center';
          ?>
          <div class="gallery-item <?php echo e($layoutClass); ?>" data-aos="zoom-in" data-aos-delay="<?php echo e(100 + $i * 50); ?>">
            <img src="<?php echo e($imgSrc); ?>" alt="<?php echo e($photo->alt_text ?? 'Ảnh cưới ' . ($i + 1)); ?>" loading="lazy"
                 style="object-fit: <?php echo e($imgFit); ?>; object-position: <?php echo e($imgPos); ?>;" />
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php endif; ?>
    </section>

    <!-- Lightbox Overlay -->
    <div id="lightbox" class="lightbox" role="dialog" aria-label="Xem ảnh phóng to">
      <button class="lightbox-close" aria-label="Đóng">&times;</button>
      <button class="lightbox-prev" aria-label="Ảnh trước">&#10094;</button>
      <button class="lightbox-next" aria-label="Ảnh sau">&#10095;</button>
      <img id="lightbox-img" src="" alt="Ảnh phóng to" />
    </div>

    <!-- SỰ KIỆN & ĐỊA ĐIỂM -->
    <section id="event" class="event-section">
      <div class="section-header" data-aos="fade-up">
        <h2 class="section-title">Sự Kiện</h2>
        <div class="section-divider">✦</div>
      </div>
<!-- 
      <div class="event-timeline" data-aos="fade-up" data-aos-delay="200">
        <div class="timeline-item">
          <div class="timeline-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <div class="timeline-content">
            <h4>Ăn Hỏi - Dẫn Cưới</h4>
            <p class="timeline-time" style="text-align: center;">22/05</p>
          </div>
        </div>
          <div class="timeline-item">
          <div class="timeline-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
          </div>
          <div class="timeline-content">
            <h4>Khai Tiệc</h4>
            <p class="timeline-time" style="text-align: center;">23/05</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </div>
          <div class="timeline-content">
            <h4>Vu Quy - Thành Hôn</h4>
            <p class="timeline-time" style="text-align: center;">24/05</p>
          </div>
        </div>
      
      </div> -->

      <!-- LỊCH SỰ KIỆN - THÁNG 5/2026 -->
      <div class="event-calendar" data-aos="fade-up" data-aos-delay="300">
        <div class="calendar-card">
          <div class="calendar-header">
            <div class="calendar-header-deco">
              <svg class="calendar-heart-deco" viewBox="0 0 24 24" width="18" height="18"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="currentColor"/></svg>
            </div>
            <span class="calendar-month">Tháng 5</span>
            <span class="calendar-year">2026</span>
          </div>

          <div class="calendar-body">
            <div class="calendar-weekdays">
              <span>CN</span><span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span><span>T7</span>
            </div>
            <div class="calendar-days">
              
              <span class="day-cell"></span>
              <span class="day-cell"></span>
              <span class="day-cell"></span>
              <span class="day-cell"></span>
              <span class="day-cell"></span>
              <?php for($d = 1; $d <= 31; $d++): ?>
                <?php if(in_array($d, [22, 23, 24])): ?>
                  <span class="day-cell day-highlight <?php echo e($d === 24 ? 'day-main' : ''); ?>">
                    <svg class="heart-stroke" viewBox="0 0 60 55" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M30 50 C30 50 4 35 4 18 C4 8 12 2 20 6 C25 9 28 14 30 18 C32 14 35 9 40 6 C48 2 56 8 56 18 C56 35 30 50 30 50Z" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" pathLength="100"/></svg>
                    <span class="day-number"><?php echo e($d); ?></span>
                    <span class="day-label"><?php echo e($d === 22 ? 'Ăn Hỏi' : ($d === 23 ? 'Khai Tiệc' : 'Thành Hôn')); ?></span>
                  </span>
                <?php else: ?>
                  <span class="day-cell"><span class="day-number"><?php echo e($d); ?></span></span>
                <?php endif; ?>
              <?php endfor; ?>
            </div>
          </div>

          <div class="calendar-legend">
            <svg viewBox="0 0 60 55" width="16" height="15" class="legend-heart-icon" fill="none"><path d="M30 50 C30 50 4 35 4 18 C4 8 12 2 20 6 C25 9 28 14 30 18 C32 14 35 9 40 6 C48 2 56 8 56 18 C56 35 30 50 30 50Z" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="legend-text">Ngày trọng đại</span>
          </div>
        </div>
      </div>

      <div class="event-venue" data-aos="fade-up" data-aos-delay="400">
        <div class="venue-icon">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v4M12 14v4M16 14v4"/></svg>
        </div>
        <h3 class="venue-name"><?php echo e($venue['location']); ?></h3>
        <p class="venue-address"><?php echo e($venue['address']); ?></p>
        <a
          href="<?php echo e(!empty($venue['map_url']) ? $venue['map_url'] : 'https://maps.google.com/?q=' . rawurlencode(($venue['location'] ?? '') . ' ' . ($venue['address'] ?? ''))); ?>"
          target="_blank"
          rel="noopener noreferrer"
          class="btn-direction"
        >
          Chỉ đường đến
        </a>
      </div>

      <?php if($venue['map_iframe']): ?>
      <div class="event-map" data-aos="fade-up" data-aos-delay="500">
        <iframe
          src="<?php echo e($venue['map_iframe']); ?>"
          width="100%"
          height="350"
          style="border:0; border-radius: 12px;"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          title="Bản đồ"
        ></iframe>
      </div>
      <?php endif; ?>
    </section>

    <!-- RSVP - Xác nhận tham dự -->
    <section id="rsvp" class="rsvp-section">
      <div class="section-header" data-aos="fade-up">
        <h2 class="section-title">Xác Nhận Tham Dự</h2>
        <div class="section-divider">✦</div>
        <p class="section-subtitle">Sự hiện diện của bạn là niềm vinh hạnh của chúng tôi</p>
      </div>

      <form id="rsvp-form" class="rsvp-form" data-aos="fade-up" data-aos-delay="200" novalidate>
        <?php if($guest): ?>
          <input type="hidden" name="guest_id" value="<?php echo e($guest->id); ?>" />
        <?php endif; ?>
        <div class="form-group">
          <label for="rsvp-name">Họ và Tên <span class="required">*</span></label>
          <input type="text" id="rsvp-name" name="guest_name" placeholder="Nhập họ và tên..." required maxlength="100" />
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="guest-phone">Số điện thoại <span class="required">*</span></label>
            <input type="tel" id="guest-phone" name="phone_number" placeholder="0912 345 678" required pattern="[0-9]{9,11}" maxlength="11" />
          </div>
          <div class="form-group">
            <label for="guest-count">Số người đi cùng</label>
            <select id="guest-count" name="companion_count">
              <option value="0">Chỉ mình tôi</option>
              <option value="1">1 người</option>
              <option value="2">2 người</option>
              <option value="3">3 người</option>
              <option value="4">4 người</option>
              <option value="5">5+ người</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Xác nhận tham dự <span class="required">*</span></label>
          <div class="attending-options">
            <label class="attending-option">
              <input type="radio" name="is_attending" value="1" checked />
              <span class="attending-icon">✓</span>
              <span>Tôi sẽ tham dự</span>
            </label>
            <label class="attending-option">
              <input type="radio" name="is_attending" value="0" />
              <span class="attending-icon">✗</span>
              <span>Tôi không thể tham dự</span>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="guest-wish">Lời chúc đến Cô Dâu &amp; Chú Rể</label>
          <textarea id="guest-wish" name="wishes_message" rows="4" placeholder="Gửi lời chúc tốt đẹp nhất..." maxlength="500"></textarea>
        </div>
        <button type="submit" class="btn-submit" id="btn-rsvp">
          <span class="btn-text">Gửi Xác Nhận</span>
          <span class="btn-loader hidden">
            <svg class="spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="30 70"/></svg>
            Đang gửi...
          </span>
        </button>
      </form>

      <!-- Hiển thị lời chúc -->
      <div id="wishes-wall" class="wishes-wall" data-aos="fade-up" data-aos-delay="300">
        <h3>Lời Chúc Từ Khách Mời</h3>
        <div id="wishes-list" class="wishes-list">
          <?php $__currentLoopData = $wishes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wish): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="wish-card">
            <p class="wish-text">"<?php echo e($wish->wishes_message); ?>"</p>
            <p class="wish-author">— <?php echo e($wish->guest_name); ?></p>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </section>

    <!-- MỪNG CƯỚI -->
    <section id="gift" class="gift-section">
      <div class="section-header" data-aos="fade-up">
        <h2 class="section-title">Hộp Mừng Cưới</h2>
        <div class="section-divider">✦</div>
        <p class="section-subtitle">Thay cho tấm lòng, xin gửi lời chúc phúc qua đây</p>
      </div>

      <div class="gift-grid">
        <?php if($type == 2): ?>

        <?php if(isset($bankInfo['bride'])): ?>
        <!-- Cô dâu (hiển thị trước khi type=2) -->
        <div class="gift-card" data-aos="fade-right" data-aos-delay="200">
          <div class="gift-avatar">
            <?php if(!empty($brideImageUrl)): ?>
              <img src="<?php echo e($brideImageUrl); ?>" alt="<?php echo e($config->bride_name); ?>" style="width:90px;height:90px;border-radius:50%;object-fit:cover;object-position:<?php echo e($brideImagePos); ?>;" />
            <?php else: ?>
              <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M12 1c1 1.5 2 3 2 4.5a2 2 0 0 1-4 0C10 4 11 2.5 12 1z" fill="currentColor" opacity="0.15"/></svg>
            <?php endif; ?>
          </div>
          <h3>Cô Dâu</h3>
          <p class="gift-bank">Ngân hàng: <strong><?php echo e($bankInfo['bride']['bank_name']); ?></strong></p>
          <p class="gift-account">STK: <strong id="stk-bride"><?php echo e($bankInfo['bride']['account_no']); ?></strong></p>
          <p class="gift-holder">Chủ TK: <strong><?php echo e($bankInfo['bride']['account_name']); ?></strong></p>
          <button class="btn-copy" data-target="stk-bride" aria-label="Copy số tài khoản cô dâu">
            Copy STK
          </button>
          <div class="gift-qr">
            <?php if(!empty($bankInfo['bride']['qr_url'])): ?>
              <img src="<?php echo e($bankInfo['bride']['qr_url']); ?>" alt="QR Cô Dâu" loading="lazy" />
            <?php else: ?>
              <img src="https://img.vietqr.io/image/<?php echo e($bankInfo['bride']['bank_name'] === 'MB Bank' ? 'MB' : $bankInfo['bride']['bank_name']); ?>-<?php echo e($bankInfo['bride']['account_no']); ?>-compact.png?amount=0&addInfo=MungCuoi<?php echo e(Str::of($config->bride_name)->ascii()->replace(' ', '')); ?>" alt="QR Cô Dâu" loading="lazy" />
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if(isset($bankInfo['groom'])): ?>
        <!-- Chú rể -->
        <div class="gift-card" data-aos="fade-left" data-aos-delay="200">
          <div class="gift-avatar">
            <?php if(!empty($groomImageUrl)): ?>
              <img src="<?php echo e($groomImageUrl); ?>" alt="<?php echo e($config->groom_name); ?>" style="width:90px;height:90px;border-radius:50%;object-fit:cover;object-position:<?php echo e($groomImagePos); ?>;" />
            <?php else: ?>
              <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <?php endif; ?>
          </div>
          <h3>Chú Rể</h3>
          <p class="gift-bank">Ngân hàng: <strong><?php echo e($bankInfo['groom']['bank_name']); ?></strong></p>
          <p class="gift-account">STK: <strong id="stk-groom"><?php echo e($bankInfo['groom']['account_no']); ?></strong></p>
          <p class="gift-holder">Chủ TK: <strong><?php echo e($bankInfo['groom']['account_name']); ?></strong></p>
          <button class="btn-copy" data-target="stk-groom" aria-label="Copy số tài khoản chú rể">
            Copy STK
          </button>
          <div class="gift-qr">
            <?php if(!empty($bankInfo['groom']['qr_url'])): ?>
              <img src="<?php echo e($bankInfo['groom']['qr_url']); ?>" alt="QR Chú Rể" loading="lazy" />
            <?php else: ?>
              <img src="https://img.vietqr.io/image/<?php echo e($bankInfo['groom']['bank_name'] === 'Vietcombank' ? 'VCB' : $bankInfo['groom']['bank_name']); ?>-<?php echo e($bankInfo['groom']['account_no']); ?>-compact.png?amount=0&addInfo=MungCuoi<?php echo e(Str::of($config->groom_name)->ascii()->replace(' ', '')); ?>" alt="QR Chú Rể" loading="lazy" />
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php else: ?>

        <?php if(isset($bankInfo['groom'])): ?>
        <!-- Chú rể (type=1, mặc định) -->
        <div class="gift-card" data-aos="fade-right" data-aos-delay="200">
          <div class="gift-avatar">
            <?php if(!empty($groomImageUrl)): ?>
              <img src="<?php echo e($groomImageUrl); ?>" alt="<?php echo e($config->groom_name); ?>" style="width:90px;height:90px;border-radius:50%;object-fit:cover;object-position:<?php echo e($groomImagePos); ?>;" />
            <?php else: ?>
              <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <?php endif; ?>
          </div>
          <h3>Chú Rể</h3>
          <p class="gift-bank">Ngân hàng: <strong><?php echo e($bankInfo['groom']['bank_name']); ?></strong></p>
          <p class="gift-account">STK: <strong id="stk-groom"><?php echo e($bankInfo['groom']['account_no']); ?></strong></p>
          <p class="gift-holder">Chủ TK: <strong><?php echo e($bankInfo['groom']['account_name']); ?></strong></p>
          <button class="btn-copy" data-target="stk-groom" aria-label="Copy số tài khoản chú rể">
            Copy STK
          </button>
          <div class="gift-qr">
            <?php if(!empty($bankInfo['groom']['qr_url'])): ?>
              <img src="<?php echo e($bankInfo['groom']['qr_url']); ?>" alt="QR Chú Rể" loading="lazy" />
            <?php else: ?>
              <img src="https://img.vietqr.io/image/<?php echo e($bankInfo['groom']['bank_name'] === 'Vietcombank' ? 'VCB' : $bankInfo['groom']['bank_name']); ?>-<?php echo e($bankInfo['groom']['account_no']); ?>-compact.png?amount=0&addInfo=MungCuoi<?php echo e(Str::of($config->groom_name)->ascii()->replace(' ', '')); ?>" alt="QR Chú Rể" loading="lazy" />
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if(isset($bankInfo['bride'])): ?>
        <!-- Cô dâu -->
        <div class="gift-card" data-aos="fade-left" data-aos-delay="200"> 
          <div class="gift-avatar">
            <?php if(!empty($brideImageUrl)): ?>
              <img src="<?php echo e($brideImageUrl); ?>" alt="<?php echo e($config->bride_name); ?>" style="width:90px;height:90px;border-radius:50%;object-fit:cover;object-position:<?php echo e($brideImagePos); ?>;" />
            <?php else: ?>
              <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M12 1c1 1.5 2 3 2 4.5a2 2 0 0 1-4 0C10 4 11 2.5 12 1z" fill="currentColor" opacity="0.15"/></svg>
            <?php endif; ?>
          </div>
          <h3>Cô Dâu</h3>
          <p class="gift-bank">Ngân hàng: <strong><?php echo e($bankInfo['bride']['bank_name']); ?></strong></p>
          <p class="gift-account">STK: <strong id="stk-bride"><?php echo e($bankInfo['bride']['account_no']); ?></strong></p>
          <p class="gift-holder">Chủ TK: <strong><?php echo e($bankInfo['bride']['account_name']); ?></strong></p>
          <button class="btn-copy" data-target="stk-bride" aria-label="Copy số tài khoản cô dâu">
            Copy STK
          </button>
          <div class="gift-qr">
            <?php if(!empty($bankInfo['bride']['qr_url'])): ?>
              <img src="<?php echo e($bankInfo['bride']['qr_url']); ?>" alt="QR Cô Dâu" loading="lazy" />
            <?php else: ?>
              <img src="https://img.vietqr.io/image/<?php echo e($bankInfo['bride']['bank_name'] === 'MB Bank' ? 'MB' : $bankInfo['bride']['bank_name']); ?>-<?php echo e($bankInfo['bride']['account_no']); ?>-compact.png?amount=0&addInfo=MungCuoi<?php echo e(Str::of($config->bride_name)->ascii()->replace(' ', '')); ?>" alt="QR Cô Dâu" loading="lazy" />
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php endif; ?> 
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer" data-aos="fade-up">
      <div class="footer-hearts">♡</div>
      <p class="footer-names">
        <?php if($type == 2): ?>
          <?php echo e($config->bride_name); ?> ♡ <?php echo e($config->groom_name); ?>

        <?php else: ?>
          <?php echo e($config->groom_name); ?> ♡ <?php echo e($config->bride_name); ?>

        <?php endif; ?>
      </p>
      <p class="footer-date"><?php echo e($config->wedding_date->format('d')); ?> . <?php echo e($config->wedding_date->format('m')); ?> . <?php echo e($config->wedding_date->format('Y')); ?></p>
      <p class="footer-thanks">Sự hiện diện của bạn là niềm vinh hạnh của chúng tôi!
Trong khoảnh khắc ý nghĩa này, sự có mặt của bạn như một món quà quý giá, góp phần làm cho ngày hôm nay trở nên trọn vẹn và ấm áp hơn bao giờ hết.

Chúng tôi vô cùng biết ơn khi được chia sẻ niềm hạnh phúc, tiếng cười và những kỷ niệm đẹp cùng bạn. Mong rằng từng khoảnh khắc nơi đây sẽ lưu lại trong tim bạn như một dấu ấn dịu dàng, đáng nhớ.

Sự đồng hành của bạn không chỉ là niềm vui, mà còn là nguồn động viên lớn lao để chúng tôi bắt đầu hành trình mới với thật nhiều yêu thương và hy vọng. 💖</p>
    </footer>
  </main>

  <!-- TRANG TRÍ SONG HỶ - Desktop Only -->
  <div class="desktop-side-decor decor-left" aria-hidden="true">
    <div class="decor-panel">
      <div class="decor-songhy-wrap">
        <div class="decor-songhy-glow"></div>
        <svg class="decor-songhy-ring" viewBox="0 0 100 100" fill="none">
          <circle cx="50" cy="50" r="46" stroke="#c9a96e" stroke-width="0.8" stroke-dasharray="4 6" opacity="0.35"/>
          <circle cx="50" cy="50" r="40" stroke="#e8d5a3" stroke-width="0.5" opacity="0.2"/>
        </svg>
        <span class="decor-songhy">囍</span>
      </div>
    </div>
  </div>

  <div class="desktop-side-decor decor-right" aria-hidden="true">
    <div class="decor-panel">
      <div class="decor-songhy-wrap">
        <div class="decor-songhy-glow"></div>
        <svg class="decor-songhy-ring" viewBox="0 0 100 100" fill="none">
          <circle cx="50" cy="50" r="46" stroke="#d4a0a7" stroke-width="0.8" stroke-dasharray="4 6" opacity="0.35"/>
          <circle cx="50" cy="50" r="40" stroke="#e8d5a3" stroke-width="0.5" opacity="0.2"/>
        </svg>
        <span class="decor-songhy">囍</span>
      </div>
    </div>
  </div>

  <!-- NÚT NHẠC NỀN NỔI -->
  <button id="music-toggle" class="music-toggle hidden" aria-label="Bật/Tắt nhạc nền">
    <span class="music-icon playing">🎵</span>
    <span class="music-bars">
      <span></span><span></span><span></span>
    </span>
  </button>

  <audio id="bg-music" loop preload="auto">
    <?php
      $musicUrl = $config->background_music_url ?? null;
      if ($musicUrl && !str_starts_with($musicUrl, 'http://') && !str_starts_with($musicUrl, 'https://')) {
          $musicUrl = asset('bg-music/' . $musicUrl);
      }
      $musicUrl = $musicUrl ?? 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3';
    ?>
    <source src="<?php echo e($musicUrl); ?>" type="audio/mpeg" />
  </audio>

  <!-- Toast Notification -->
  <div id="toast" class="toast"></div>

  <!-- AOS Library -->
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

  <!-- Truyền dữ liệu từ server sang JS -->
  <script>
    window.weddingData = {
      weddingDate: '<?php echo e($config->wedding_date->toIso8601String()); ?>',
      guestId: <?php echo e($guest ? $guest->id : 'null'); ?>,
      displayName: <?php echo json_encode($displayName, 15, 512) ?>,
      type: <?php echo e($type); ?>,
      rsvpUrl: '<?php echo e(route('rsvp.store')); ?>',
      csrfToken: '<?php echo e(csrf_token()); ?>'
    };
  </script>
  <script src="<?php echo e(asset('js/wedding.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\thispc\Documents\wedding-site\resources\views/wedding.blade.php ENDPATH**/ ?>