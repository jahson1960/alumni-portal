<?php
/**
 * Admin > Analytics. The admin area has no dark mode anywhere else, so this chart is built
 * light-surface only (see dataviz skill) rather than adding a one-off dark variant that nothing
 * else in the shell matches. Palette: dataviz's validated default categorical slots 1 (blue,
 * Page Views) and 2 (orange, Unique Visitors) - re-validated for this exact pair via
 * scripts/validate_palette.js before use.
 */
$ranges = ['today' => 'Today', '7d' => 'Last 7 Days', '30d' => 'Last 30 Days', 'all' => 'All Time'];

$days = array_keys($dailyCounts);
$n = count($days);
$maxVal = 0;
foreach ($dailyCounts as $d) {
    $maxVal = max($maxVal, $d['views'], $d['visitors']);
}

$niceMax = chart_nice_max($maxVal);

$padLeft = 42;
$padRight = 10;
$padTop = 14;
$padBottom = 28;
$chartW = 800;
$chartH = 220;
$plotW = $chartW - $padLeft - $padRight;
$plotH = $chartH - $padTop - $padBottom;

$points = [];
foreach ($days as $i => $day) {
    $x = $n > 1 ? $padLeft + ($i / ($n - 1)) * $plotW : $padLeft + $plotW / 2;
    $views = $dailyCounts[$day]['views'];
    $visitors = $dailyCounts[$day]['visitors'];
    $points[] = [
        'x' => round($x, 1),
        'viewsY' => round($padTop + $plotH - ($views / $niceMax) * $plotH, 1),
        'visitorsY' => round($padTop + $plotH - ($visitors / $niceMax) * $plotH, 1),
        'date' => format_date($day, 'M j'),
        'views' => $views,
        'visitors' => $visitors,
    ];
}

$viewsPath = '';
$visitorsPath = '';
foreach ($points as $i => $p) {
    $viewsPath .= ($i === 0 ? 'M' : 'L') . $p['x'] . ',' . $p['viewsY'] . ' ';
    $visitorsPath .= ($i === 0 ? 'M' : 'L') . $p['x'] . ',' . $p['visitorsY'] . ' ';
}

$labelEvery = $n > 0 ? max(1, (int) ceil($n / 6)) : 1;
?>
<div class="flex justify-between items-center mb-4 flex-wrap gap-3">
  <h2 class="text-sm font-bold text-primary-navy">Analytics</h2>
  <div class="flex items-center gap-1">
    <?php foreach ($ranges as $key => $label): ?>
      <a href="<?= e(url('admin/analytics') . admin_qs(['range' => $key])) ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $range === $key ? 'bg-gold text-white' : 'text-slate-600 hover:bg-slate-100' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="card p-5">
    <p class="text-xs text-slate-400 mb-1">Unique Visitors</p>
    <p class="text-2xl font-bold text-primary-navy"><?= e(format_compact_number($uniqueVisitors)) ?></p>
  </div>
  <div class="card p-5">
    <p class="text-xs text-slate-400 mb-1">Page Views</p>
    <p class="text-2xl font-bold text-primary-navy"><?= e(format_compact_number($pageViews)) ?></p>
  </div>
  <div class="card p-5">
    <p class="text-xs text-slate-400 mb-1">Avg. Time on Page</p>
    <p class="text-2xl font-bold text-primary-navy"><?= e(format_duration_seconds($avgTimeOnPage)) ?></p>
  </div>
  <div class="card p-5">
    <p class="text-xs text-slate-400 mb-1">Avg. Time on Site</p>
    <p class="text-2xl font-bold text-primary-navy"><?= e(format_duration_seconds($avgTimeOnSite)) ?></p>
  </div>
</div>

<div class="card p-6 mb-6">
  <div class="flex items-center justify-between mb-4">
    <h3 class="section-title text-xs">Traffic Over Time</h3>
    <div class="flex items-center gap-4 text-xs text-slate-500">
      <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-0.5 rounded-full" style="background:#2a78d6"></span> Page Views</span>
      <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-0.5 rounded-full" style="background:#eb6834"></span> Unique Visitors</span>
    </div>
  </div>

  <?php if ($n < 2): ?>
    <p class="text-sm text-slate-400 py-10 text-center">Not enough data yet for this range.</p>
  <?php else: ?>
    <div class="relative" id="analytics-chart-wrap">
      <svg id="analytics-chart" viewBox="0 0 <?= $chartW ?> <?= $chartH ?>" class="w-full h-auto" style="max-height: 260px;">
        <?php
          // Hairline gridlines at 0/25/50/75/100% of the axis max.
          for ($g = 0; $g <= 4; $g++):
            $gy = $padTop + $plotH - ($g / 4) * $plotH;
            $gVal = (int) round($niceMax * $g / 4);
        ?>
          <line x1="<?= $padLeft ?>" y1="<?= $gy ?>" x2="<?= $chartW - $padRight ?>" y2="<?= $gy ?>" stroke="#e1e0d9" stroke-width="1"></line>
          <text x="<?= $padLeft - 8 ?>" y="<?= $gy + 3 ?>" text-anchor="end" font-size="10" fill="#898781"><?= e(format_compact_number($gVal)) ?></text>
        <?php endfor; ?>

        <?php foreach ($points as $i => $p): ?>
          <?php if ($i % $labelEvery === 0 || $i === $n - 1): ?>
            <text x="<?= $p['x'] ?>" y="<?= $chartH - 8 ?>" text-anchor="middle" font-size="10" fill="#898781"><?= e($p['date']) ?></text>
          <?php endif; ?>
        <?php endforeach; ?>

        <path d="<?= trim($viewsPath) ?>" fill="none" stroke="#2a78d6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="<?= trim($visitorsPath) ?>" fill="none" stroke="#eb6834" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>

        <circle cx="<?= $points[$n - 1]['x'] ?>" cy="<?= $points[$n - 1]['viewsY'] ?>" r="4" fill="#2a78d6" stroke="#fcfcfb" stroke-width="2"></circle>
        <circle cx="<?= $points[$n - 1]['x'] ?>" cy="<?= $points[$n - 1]['visitorsY'] ?>" r="4" fill="#eb6834" stroke="#fcfcfb" stroke-width="2"></circle>

        <line id="analytics-crosshair" x1="0" y1="<?= $padTop ?>" x2="0" y2="<?= $padTop + $plotH ?>" stroke="#c3c2b7" stroke-width="1" class="hidden"></line>
        <circle id="analytics-hover-views" r="5" fill="#2a78d6" stroke="#fcfcfb" stroke-width="2" class="hidden"></circle>
        <circle id="analytics-hover-visitors" r="5" fill="#eb6834" stroke="#fcfcfb" stroke-width="2" class="hidden"></circle>
        <rect x="<?= $padLeft ?>" y="<?= $padTop ?>" width="<?= $plotW ?>" height="<?= $plotH ?>" fill="transparent" id="analytics-hit-area"></rect>
      </svg>
      <div id="analytics-tooltip" class="hidden absolute bg-primary-navy text-white text-xs rounded-lg px-3 py-2 pointer-events-none shadow-lg" style="z-index: 10;">
        <p class="font-semibold mb-1" id="analytics-tooltip-date"></p>
        <p class="flex items-center gap-1.5"><span class="inline-block w-2.5 h-0.5 rounded-full" style="background:#2a78d6"></span> <span id="analytics-tooltip-views" class="font-bold"></span> views</p>
        <p class="flex items-center gap-1.5"><span class="inline-block w-2.5 h-0.5 rounded-full" style="background:#eb6834"></span> <span id="analytics-tooltip-visitors" class="font-bold"></span> visitors</p>
      </div>
    </div>
    <script>
      (function () {
        var points = <?= json_encode($points) ?>;
        var svg = document.getElementById('analytics-chart');
        var hitArea = document.getElementById('analytics-hit-area');
        var wrap = document.getElementById('analytics-chart-wrap');
        var crosshair = document.getElementById('analytics-crosshair');
        var hoverViews = document.getElementById('analytics-hover-views');
        var hoverVisitors = document.getElementById('analytics-hover-visitors');
        var tooltip = document.getElementById('analytics-tooltip');
        var tooltipDate = document.getElementById('analytics-tooltip-date');
        var tooltipViews = document.getElementById('analytics-tooltip-views');
        var tooltipVisitors = document.getElementById('analytics-tooltip-visitors');
        if (!svg || !hitArea || points.length === 0) return;

        function nearestPoint(svgX) {
          var closest = points[0];
          var closestDist = Math.abs(points[0].x - svgX);
          for (var i = 1; i < points.length; i++) {
            var dist = Math.abs(points[i].x - svgX);
            if (dist < closestDist) {
              closest = points[i];
              closestDist = dist;
            }
          }
          return closest;
        }

        function show(evt) {
          var rect = svg.getBoundingClientRect();
          var clientX = evt.touches ? evt.touches[0].clientX : evt.clientX;
          var svgX = ((clientX - rect.left) / rect.width) * <?= $chartW ?>;
          var p = nearestPoint(svgX);

          crosshair.setAttribute('x1', p.x);
          crosshair.setAttribute('x2', p.x);
          crosshair.classList.remove('hidden');
          hoverViews.setAttribute('cx', p.x);
          hoverViews.setAttribute('cy', p.viewsY);
          hoverViews.classList.remove('hidden');
          hoverVisitors.setAttribute('cx', p.x);
          hoverVisitors.setAttribute('cy', p.visitorsY);
          hoverVisitors.classList.remove('hidden');

          tooltipDate.textContent = p.date;
          tooltipViews.textContent = p.views.toLocaleString();
          tooltipVisitors.textContent = p.visitors.toLocaleString();

          var pxRatio = rect.width / <?= $chartW ?>;
          var left = p.x * pxRatio;
          var clampedLeft = Math.min(Math.max(left, 70), rect.width - 70);
          tooltip.style.left = clampedLeft + 'px';
          tooltip.style.top = '0px';
          tooltip.style.transform = 'translate(-50%, -100%)';
          tooltip.classList.remove('hidden');
        }

        function hide() {
          crosshair.classList.add('hidden');
          hoverViews.classList.add('hidden');
          hoverVisitors.classList.add('hidden');
          tooltip.classList.add('hidden');
        }

        hitArea.addEventListener('mousemove', show);
        hitArea.addEventListener('touchmove', show);
        hitArea.addEventListener('mouseleave', hide);
        hitArea.addEventListener('touchend', hide);
      })();
    </script>
  <?php endif; ?>
</div>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead>
      <tr>
        <th>Page</th>
        <th>Views</th>
        <th>Avg. Time on Page</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($topPages as $page): ?>
        <tr>
          <td class="font-medium text-primary-navy truncate max-w-[320px]"><?= e($page['path']) ?></td>
          <td class="text-slate-600 tabular-nums"><?= e(number_format($page['views'])) ?></td>
          <td class="text-slate-600 tabular-nums"><?= e(format_duration_seconds($page['avg_duration'])) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($topPages)): ?>
        <tr><td colspan="3" class="text-center text-slate-400 py-6">No page views recorded for this range yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
