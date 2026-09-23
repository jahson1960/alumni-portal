<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <div class="section-header">
    <h1 class="section-title text-base">Our Alumni Around the World</h1>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div>
      <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">By Country</h3>
      <div class="card divide-y divide-slate-100">
        <?php foreach ($countryCounts as $row): ?>
          <a href="<?= e(url('map?country=' . urlencode($row['country']))) ?>" class="flex items-center justify-between px-5 py-3 <?= $selectedCountry === $row['country'] ? 'bg-slate-50' : '' ?>">
            <span class="text-sm font-semibold text-primary-navy"><?= e($row['country']) ?></span>
            <span class="text-sm text-slate-500"><?= (int) $row['total'] ?></span>
          </a>
        <?php endforeach; ?>
        <?php if (empty($countryCounts)): ?>
          <p class="text-sm text-slate-400 px-5 py-4">No location data yet.</p>
        <?php endif; ?>
      </div>
    </div>

    <div>
      <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">
        <?= $selectedCountry ? 'Cities in ' . e($selectedCountry) : 'Select a country to see cities' ?>
      </h3>
      <div class="card divide-y divide-slate-100">
        <?php foreach ($cityCounts as $row): ?>
          <a href="<?= e(url('directory?country=' . urlencode($selectedCountry) . '&city=' . urlencode($row['city']))) ?>" class="flex items-center justify-between px-5 py-3">
            <span class="text-sm font-semibold text-primary-navy"><?= e($row['city']) ?></span>
            <span class="text-sm text-slate-500"><?= (int) $row['total'] ?></span>
          </a>
        <?php endforeach; ?>
        <?php if ($selectedCountry && empty($cityCounts)): ?>
          <p class="text-sm text-slate-400 px-5 py-4">No city data for this country yet.</p>
        <?php endif; ?>
        <?php if (!$selectedCountry): ?>
          <p class="text-sm text-slate-400 px-5 py-4">Click a country on the left.</p>
        <?php endif; ?>
      </div>
      <?php if ($selectedCountry): ?>
        <a href="<?= e(url('directory?country=' . urlencode($selectedCountry))) ?>" class="section-link mt-3 inline-block">View all alumni in <?= e($selectedCountry) ?> &rarr;</a>
      <?php endif; ?>
    </div>
  </div>
</div>
