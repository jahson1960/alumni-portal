<?php
$heroHeight = max(300, min(900, (int) ($settings['hero_height'] ?? 480)));
$heroIntervalMs = max(3, min(20, (int) ($settings['hero_interval'] ?? 6))) * 1000;
$heroTransition = $settings['hero_transition'] ?? 'fade';

$alignClasses = [
    'left' => ['justify-start', 'text-left', 'items-start'],
    'center' => ['justify-center', 'text-center', 'items-center'],
    'right' => ['justify-end', 'text-right', 'items-end'],
];
?>
<?php if ($heroSlides): ?>
<section class="relative bg-primary-navy text-white overflow-hidden" id="hero-carousel" data-transition="<?= e($heroTransition) ?>" style="height: <?= $heroHeight ?>px; --hero-height: <?= $heroHeight ?>px;">
  <?php foreach ($heroSlides as $i => $slide):
    [$justify, $textAlign, $itemsAlign] = $alignClasses[$slide['content_align'] ?? 'left'] ?? $alignClasses['left'];
    $btn1Link = $slide['btn1_link'] ?: 'jobs';
    $btn2Link = $slide['btn2_link'] ?: (\App\Core\Auth::check() ? 'profile/edit' : 'register');
  ?>
    <div class="hero-slide absolute inset-0 bg-cover bg-center <?= $i === 0 ? 'is-active' : '' ?>"
         style="background-image: linear-gradient(90deg, rgba(9,26,46,1) 0%, rgba(9,26,46,1) 30%, rgba(9,26,46,0.7) 50%, rgba(9,26,46,0.3) 100%), url('<?= e($slide['image']) ?>');">
      <div class="max-w-[1280px] mx-auto px-4 md:px-8 py-16 md:py-20 h-full flex <?= $justify ?>">
        <div class="max-w-xl flex flex-col <?= $itemsAlign ?> <?= $textAlign ?>">
          <h1 class="text-3xl md:text-4xl font-bold leading-tight mb-4">
            <?= e($slide['title']) ?>
            <span class="block text-gold"><?= e($slide['highlight']) ?></span>
          </h1>
          <p class="font-medium mb-2"><?= e($slide['subtitle']) ?></p>
          <p class="text-sm text-slate-300 mb-8"><?= e($slide['description']) ?></p>
          <div class="flex flex-wrap gap-4">
            <?php if (!empty($slide['btn1_show'])): ?>
              <a href="<?= e(str_starts_with($btn1Link, 'http') ? $btn1Link : url($btn1Link)) ?>" class="btn-gold"><?= e($slide['btn1_text'] ?: 'Explore Opportunities') ?></a>
            <?php endif; ?>
            <?php if (!empty($slide['btn2_show'])): ?>
              <a href="<?= e(str_starts_with($btn2Link, 'http') ? $btn2Link : url($btn2Link)) ?>" class="btn-outline-white"><?= e($slide['btn2_text'] ?: 'Update Your Profile') ?></a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if (count($heroSlides) > 1): ?>
    <button type="button" id="hero-prev" aria-label="Previous slide" class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center"><i class="fa-solid fa-chevron-left"></i></button>
    <button type="button" id="hero-next" aria-label="Next slide" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center"><i class="fa-solid fa-chevron-right"></i></button>
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
      <?php foreach ($heroSlides as $i => $slide): ?>
        <button type="button" class="hero-dot w-2.5 h-2.5 rounded-full <?= $i === 0 ? 'bg-gold' : 'bg-white/40' ?>" data-slide-index="<?= $i ?>" aria-label="Go to slide <?= $i + 1 ?>"></button>
      <?php endforeach; ?>
    </div>
    <script>
      (function () {
        var root = document.getElementById('hero-carousel');
        var slides = root.querySelectorAll('.hero-slide');
        var dots = root.querySelectorAll('.hero-dot');
        var current = 0;
        var timer;

        function show(index) {
          slides[current].classList.remove('is-active');
          dots[current].classList.remove('bg-gold');
          dots[current].classList.add('bg-white/40');

          current = (index + slides.length) % slides.length;

          slides[current].classList.add('is-active');
          dots[current].classList.remove('bg-white/40');
          dots[current].classList.add('bg-gold');
        }

        function next() { show(current + 1); }
        function prev() { show(current - 1); }
        function restart() {
          clearInterval(timer);
          timer = setInterval(next, <?= $heroIntervalMs ?>);
        }

        document.getElementById('hero-next').addEventListener('click', function () { next(); restart(); });
        document.getElementById('hero-prev').addEventListener('click', function () { prev(); restart(); });
        dots.forEach(function (dot) {
          dot.addEventListener('click', function () { show(parseInt(dot.dataset.slideIndex, 10)); restart(); });
        });

        restart();
      })();
    </script>
  <?php endif; ?>
</section>
<?php endif; ?>

<main class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">

  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-10">
    <a href="<?= e(url('directory')) ?>" class="card p-5 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all">
      <div class="w-11 h-11 rounded-lg bg-slate-100 text-primary-navy flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid fa-user-group"></i></div>
      <div class="flex-1 min-w-0">
        <h4 class="text-sm font-bold text-primary-navy">Alumni Directory</h4>
        <p class="text-xs text-slate-500">Connect with alumni like you</p>
      </div>
      <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
    </a>
    <a href="<?= e(url('jobs')) ?>" class="card p-5 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all">
      <div class="w-11 h-11 rounded-lg bg-slate-100 text-primary-navy flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid fa-briefcase"></i></div>
      <div class="flex-1 min-w-0">
        <h4 class="text-sm font-bold text-primary-navy">Job Portal</h4>
        <p class="text-xs text-slate-500">Browse latest job opportunities</p>
      </div>
      <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
    </a>
    <a href="<?= e(url('mentorship')) ?>" class="card p-5 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all">
      <div class="w-11 h-11 rounded-lg bg-slate-100 text-primary-navy flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid fa-people-arrows"></i></div>
      <div class="flex-1 min-w-0">
        <h4 class="text-sm font-bold text-primary-navy">Mentorship</h4>
        <p class="text-xs text-slate-500">Find a mentor or become one</p>
      </div>
      <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
    </a>
    <a href="<?= e(url('events')) ?>" class="card p-5 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all">
      <div class="w-11 h-11 rounded-lg bg-slate-100 text-primary-navy flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid fa-calendar-days"></i></div>
      <div class="flex-1 min-w-0">
        <h4 class="text-sm font-bold text-primary-navy">Events</h4>
        <p class="text-xs text-slate-500">Join upcoming events and reunions</p>
      </div>
      <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
    </a>
    <a href="<?= e(url('resources')) ?>" class="card p-5 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all">
      <div class="w-11 h-11 rounded-lg bg-slate-100 text-primary-navy flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid fa-file-lines"></i></div>
      <div class="flex-1 min-w-0">
        <h4 class="text-sm font-bold text-primary-navy">Resources</h4>
        <p class="text-xs text-slate-500">Access career tools and materials</p>
      </div>
      <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[2fr_2fr_1.3fr] gap-6">

    <!-- News & Blog -->
    <div class="flex flex-col">
      <div class="section-header">
        <h3 class="section-title">News & Blog</h3>
        <a href="<?= e(url('news')) ?>" class="section-link">View all articles &rarr;</a>
      </div>

      <?php if ($featuredNews): $featCat = \App\Models\Category::forNews((int) $featuredNews['id'])[0] ?? null; ?>
        <a href="<?= e(url('news/' . $featuredNews['slug'])) ?>" class="card overflow-hidden block mb-4 shadow hover:shadow-lg hover:-translate-y-0.5 transition-all group">
          <div class="relative">
            <img src="<?= e($featuredNews['image']) ?>" alt="<?= e($featuredNews['title']) ?>" class="w-full h-40 object-cover">
            <span class="badge-gold absolute top-3 left-3 flex items-center gap-1"><i class="fa-solid fa-star text-[0.6rem]"></i> FEATURED</span>
          </div>
          <div class="p-4">
            <?php if ($featCat): ?><p class="text-[0.68rem] font-extrabold text-amber-600 uppercase tracking-wide mb-1"><?= e($featCat['name']) ?></p><?php endif; ?>
            <h4 class="text-sm font-bold leading-snug mb-2 text-primary-navy line-clamp-2 group-hover:text-gold transition-colors"><?= e($featuredNews['title']) ?></h4>
            <p class="text-xs text-slate-500 line-clamp-2 mb-3"><?= e($featuredNews['excerpt']) ?></p>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3 text-[0.68rem] text-slate-400">
                <span class="flex items-center gap-1"><i class="fa-regular fa-calendar"></i> <?= e(format_date($featuredNews['published_at'])) ?></span>
                <span class="flex items-center gap-1"><i class="fa-regular fa-clock"></i> <?= reading_time_minutes($featuredNews['body']) ?> min read</span>
              </div>
              <span class="w-7 h-7 rounded-full bg-gold text-white flex items-center justify-center flex-shrink-0 text-xs group-hover:bg-primary-navy transition-colors"><i class="fa-solid fa-arrow-right"></i></span>
            </div>
          </div>
        </a>
      <?php endif; ?>

      <div class="space-y-4 flex-1">
        <?php foreach ($newsList as $post): $postCat = \App\Models\Category::forNews((int) $post['id'])[0] ?? null; ?>
          <a href="<?= e(url('news/' . $post['slug'])) ?>" class="card overflow-hidden flex items-stretch gap-0 min-h-[76px] shadow hover:shadow-lg hover:-translate-y-0.5 transition-all group">
            <img src="<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>" class="w-[76px] flex-shrink-0 object-cover">
            <div class="min-w-0 flex-1 flex items-center justify-between gap-2 py-2 px-3">
              <div class="min-w-0">
                <?php if ($postCat): ?><span class="badge-blue mb-1 inline-block"><?= e(strtoupper($postCat['name'])) ?></span><?php endif; ?>
                <h5 class="text-xs font-bold leading-tight line-clamp-2 text-primary-navy group-hover:text-gold transition-colors"><?= e($post['title']) ?></h5>
                <div class="text-[0.65rem] text-slate-400 mt-1 flex items-center gap-1"><i class="fa-regular fa-calendar"></i> <?= e(format_date($post['published_at'])) ?></div>
              </div>
              <i class="fa-solid fa-arrow-right text-gold text-xs flex-shrink-0"></i>
            </div>
          </a>
        <?php endforeach; ?>
        <?php if (!$featuredNews && empty($newsList)): ?>
          <p class="text-sm text-slate-400">No news posts yet.</p>
        <?php endif; ?>
      </div>

      <a href="<?= e(url('news')) ?>" class="btn-dark !rounded-full flex items-center justify-center gap-2"><i class="fa-solid fa-list-ul"></i> Browse All News <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <!-- Jobs -->
    <div class="flex flex-col">
      <div class="section-header">
        <h3 class="section-title">Latest Job Opportunities</h3>
        <a href="<?= e(url('jobs')) ?>" class="section-link">View all jobs &rarr;</a>
      </div>

      <div class="space-y-4 flex-1">
        <?php foreach ($jobs as $job): ?>
          <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="card p-4 flex items-center gap-3 relative min-h-[92px] shadow hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <?php if ($job['is_featured']): ?>
              <span class="absolute top-3 right-3 badge-gold flex items-center gap-1"><i class="fa-solid fa-star text-[0.6rem]"></i> FEATURED</span>
            <?php endif; ?>
            <?= job_logo_html($job, 'w-12 h-12 !rounded-lg') ?>
            <div class="min-w-0 flex-1">
              <h4 class="text-sm font-bold truncate text-primary-navy"><?= e($job['title']) ?></h4>
              <p class="text-xs text-slate-500 truncate mb-1.5"><?= e($job['company']) ?></p>
              <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[0.68rem] text-slate-500">
                <span class="flex items-center gap-1"><i class="fa-solid fa-location-dot"></i> <?= e($job['location']) ?></span>
                <span class="text-slate-300">|</span>
                <span class="flex items-center gap-1"><i class="fa-solid fa-briefcase"></i> <?= e($job['job_type']) ?></span>
                <span class="text-slate-300">|</span>
                <span class="flex items-center gap-1"><i class="fa-regular fa-clock"></i> <?= e(time_ago($job['posted_at'])) ?></span>
              </div>
            </div>
            <i class="fa-solid fa-arrow-right text-gold text-xs flex-shrink-0"></i>
          </a>
        <?php endforeach; ?>
        <?php if (empty($jobs)): ?>
          <p class="text-sm text-slate-400">No open positions right now.</p>
        <?php endif; ?>
      </div>

      <a href="<?= e(url('jobs')) ?>" class="btn-dark !rounded-full flex items-center justify-center gap-2"><i class="fa-solid fa-magnifying-glass"></i> Browse All Jobs <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <!-- Events -->
    <div class="flex flex-col">
      <div class="section-header">
        <h3 class="section-title">Upcoming Events</h3>
        <a href="<?= e(url('events')) ?>" class="section-link">View all &rarr;</a>
      </div>

      <div class="space-y-4 flex-1 mb-6">
        <?php foreach ($events as $event): ?>
          <a href="<?= e(url('events/' . $event['id'])) ?>" class="card p-4 flex items-center gap-3 min-h-[92px] shadow hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="bg-primary-navy text-white rounded-lg p-1.5 text-center w-12 h-12 flex flex-col items-center justify-center flex-shrink-0">
              <span class="text-[0.55rem] font-bold uppercase leading-none"><?= e(format_date($event['event_date'], 'M')) ?></span>
              <span class="text-base font-extrabold leading-none my-0.5"><?= e(format_date($event['event_date'], 'd')) ?></span>
              <span class="text-[0.5rem] font-semibold uppercase leading-none text-slate-300"><?= e(format_date($event['event_date'], 'D')) ?></span>
            </div>
            <div class="min-w-0 flex-1">
              <h5 class="text-sm font-bold truncate text-primary-navy mb-1"><?= e($event['title']) ?></h5>
              <p class="text-[0.68rem] text-slate-500 flex items-center gap-1.5 mb-0.5">
                <i class="<?= $event['is_virtual'] ? 'fa-solid fa-video' : 'fa-solid fa-location-dot' ?>"></i>
                <span class="truncate"><?= e($event['is_virtual'] ? 'Virtual Event' : $event['location']) ?></span>
              </p>
              <?php if (!empty($event['event_time'])): ?>
                <p class="text-[0.68rem] text-slate-500 flex items-center gap-1.5"><i class="fa-regular fa-clock"></i> <?= e($event['event_time']) ?></p>
              <?php endif; ?>
            </div>
            <i class="fa-solid fa-arrow-right text-gold text-xs flex-shrink-0"></i>
          </a>
        <?php endforeach; ?>
        <?php if (empty($events)): ?>
          <p class="text-sm text-slate-400 py-2">No upcoming events.</p>
        <?php endif; ?>
      </div>

      <div class="relative bg-primary-navy text-white rounded-xl p-6 overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full border-4 border-gold/20"></div>
        <div class="absolute right-8 -bottom-10 w-24 h-24 rounded-full border-4 border-gold/20"></div>
        <div class="relative">
          <div class="w-11 h-11 rounded-full bg-white/10 flex items-center justify-center mb-3 text-gold text-lg"><i class="fa-solid fa-user-group"></i></div>
          <h4 class="text-base font-extrabold mb-1.5"><?= e($settings['cta_title'] ?? 'Become a Mentor') ?></h4>
          <p class="text-xs text-slate-300 mb-4 leading-relaxed"><?= e($settings['cta_subtitle'] ?? 'Share your experience. Inspire the next generation.') ?></p>
          <a href="<?= e(url(\App\Core\Auth::check() ? 'directory' : 'register')) ?>" class="btn-gold !rounded-full w-full text-center flex items-center justify-center gap-2">Become a Mentor <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </div>
    </div>

  </div>
</main>
