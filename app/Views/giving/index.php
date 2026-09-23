<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_460px] gap-6 items-stretch mb-8">
    <div>
      <div class="flex items-center gap-3 mb-2">
        <div class="w-11 h-11 rounded-full bg-amber-100 text-gold flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-heart"></i></div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-primary-navy">Give Back</h1>
      </div>
      <p class="text-sm text-slate-500 mb-6 max-w-md">Your support empowers lives, fuels innovation, and strengthens the RBS community.</p>

      <div class="flex flex-wrap gap-x-8 gap-y-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-people-group"></i></div>
          <div>
            <p class="text-lg font-extrabold text-primary-navy leading-none">&#8358;<?= number_format($totalRaised) ?></p>
            <p class="text-xs text-slate-500 mt-1">Total Raised</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-gift"></i></div>
          <div>
            <p class="text-lg font-extrabold text-primary-navy leading-none"><?= (int) $activeCount ?></p>
            <p class="text-xs text-slate-500 mt-1">Active Campaigns</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-hand-holding-heart"></i></div>
          <div>
            <p class="text-lg font-extrabold text-primary-navy leading-none"><?= (int) $causesCount ?></p>
            <p class="text-xs text-slate-500 mt-1">Giving Causes</p>
          </div>
        </div>
      </div>
    </div>

    <div class="card !bg-indigo-50 !border-indigo-100 p-6 flex items-center gap-5">
      <div class="flex-shrink-0">
        <svg viewBox="0 0 160 140" class="w-24 h-24 md:w-32 md:h-32">
          <path d="M18 118 Q6 98 18 82" stroke="#C7D2E5" stroke-width="5" fill="none" stroke-linecap="round"/>
          <path d="M142 112 Q154 92 140 78" stroke="#C7D2E5" stroke-width="5" fill="none" stroke-linecap="round"/>
          <circle cx="132" cy="30" r="6" fill="#C7D2E5"/>
          <circle cx="24" cy="55" r="4" fill="#C7D2E5"/>
          <path d="M80 22 C58 2 26 24 38 56 C47 80 80 102 80 102 C80 102 113 80 122 56 C134 24 102 2 80 22 Z" fill="#D9A441"/>
          <path d="M42 108 C54 92 68 90 80 90 L80 132 L37 132 Z" fill="#0B1F3A"/>
          <path d="M118 108 C106 92 92 90 80 90 L80 132 L123 132 Z" fill="#12294A"/>
          <path d="M32 120 C36 102 50 96 56 100 L52 132 L28 130 Z" fill="#C99B6E"/>
          <path d="M128 120 C124 102 110 96 104 100 L108 132 L132 130 Z" fill="#8B5E3C"/>
        </svg>
      </div>
      <div>
        <h3 class="text-lg font-extrabold text-primary-navy leading-snug">Together, we create lasting impact.</h3>
        <p class="text-xs text-slate-600 mt-2 leading-relaxed">Every contribution—big or small—helps build opportunities for future business leaders.</p>
      </div>
    </div>
  </div>

  <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div>
      <h2 class="text-lg font-bold text-primary-navy">Our Campaigns</h2>
      <div class="w-8 h-0.5 bg-gold rounded-full mt-1.5"></div>
    </div>
    <div class="flex items-center gap-2">
      <form method="GET" action="<?= e(url('give')) ?>">
        <?php if ($q !== ''): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
        <select name="sort" onchange="this.form.submit()" class="form-input text-sm !pr-9 min-w-[160px]">
          <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>All Campaigns</option>
          <option value="most_funded" <?= $sort === 'most_funded' ? 'selected' : '' ?>>Most Funded</option>
          <option value="least_funded" <?= $sort === 'least_funded' ? 'selected' : '' ?>>Least Funded</option>
        </select>
      </form>
      <form method="GET" action="<?= e(url('give')) ?>" class="relative">
        <?php if ($sort !== 'newest'): ?><input type="hidden" name="sort" value="<?= e($sort) ?>"><?php endif; ?>
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search campaigns..." class="form-input !pl-9 text-sm w-52 sm:w-56">
        <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <?php foreach ($campaigns as $i => $campaign): ?>
      <?php
        $pct = $campaign['goal_amount'] > 0 ? min(100, round($campaign['raised_amount'] / $campaign['goal_amount'] * 100)) : 0;
        $colors = campaign_color($campaign['title'], $i);
      ?>
      <div class="card p-5">
        <div class="flex items-start justify-between gap-3 mb-4">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-11 h-11 rounded-full <?= e($colors['bg']) ?> <?= e($colors['text']) ?> flex items-center justify-center flex-shrink-0"><i class="fa-solid <?= e(campaign_icon($campaign['title'], $i)) ?>"></i></div>
            <div class="min-w-0">
              <h4 class="text-base font-bold text-primary-navy"><?= e($campaign['title']) ?></h4>
              <p class="text-xs text-slate-500 mt-1"><?= e($campaign['description']) ?></p>
            </div>
          </div>
          <span class="<?= e($colors['badge']) ?> rounded-lg text-center flex-shrink-0 flex flex-col items-center px-3 py-1.5">
            <span class="text-sm font-extrabold leading-none"><?= $pct ?>%</span>
            <span class="text-[0.6rem] font-semibold">Funded</span>
          </span>
        </div>

        <div class="w-full bg-slate-100 rounded-full h-2 mb-2">
          <div class="bg-gold h-2 rounded-full" style="width: <?= $pct ?>%"></div>
        </div>
        <div class="flex justify-between text-xs text-slate-600 mb-4">
          <span>&#8358;<?= number_format((float) $campaign['raised_amount']) ?> raised</span>
          <span><?= $pct ?>% of &#8358;<?= number_format((float) $campaign['goal_amount']) ?></span>
        </div>

        <?php $alreadyGiven = in_array((int) $campaign['id'], $confirmedCampaignIds, true); ?>
        <?php if ($alreadyGiven): ?>
          <div class="flex items-center gap-3">
            <span class="badge bg-emerald-100 text-emerald-700 inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check"></i> Marked as Given</span>
            <button type="button" id="reveal-give-btn-<?= $campaign['id'] ?>" class="text-xs text-slate-500 hover:text-gold hover:underline" onclick="document.getElementById('reveal-give-btn-<?= $campaign['id'] ?>').classList.add('hidden'); document.getElementById('reveal-give-panel-<?= $campaign['id'] ?>').classList.remove('hidden');">View donation methods</button>
          </div>
        <?php else: ?>
          <button type="button" id="reveal-give-btn-<?= $campaign['id'] ?>" class="btn-gold !px-4 !py-2 text-xs flex items-center gap-2" onclick="document.getElementById('reveal-give-btn-<?= $campaign['id'] ?>').classList.add('hidden'); document.getElementById('reveal-give-panel-<?= $campaign['id'] ?>').classList.remove('hidden');"><i class="fa-solid fa-heart"></i> Give to this Campaign</button>
        <?php endif; ?>

        <div id="reveal-give-panel-<?= $campaign['id'] ?>" class="hidden mt-3 border border-slate-200 rounded-lg p-3.5 bg-slate-50">
          <?php if ($donationMethods): ?>
            <p class="text-xs font-semibold text-primary-navy mb-2">Give via:</p>
            <div class="space-y-2 mb-3">
              <?php foreach ($donationMethods as $method): ?>
                <div class="bg-white border border-slate-200 rounded-lg p-2.5 text-xs">
                  <p class="font-bold text-primary-navy"><?= e($method['label']) ?></p>
                  <?php if ($method['bank_name']): ?><p class="text-slate-500">Bank: <?= e($method['bank_name']) ?></p><?php endif; ?>
                  <?php if ($method['account_name']): ?><p class="text-slate-500">Account Name: <?= e($method['account_name']) ?></p><?php endif; ?>
                  <p class="text-slate-700 font-semibold">Account Number: <?= e($method['account_number']) ?></p>
                  <?php if ($method['sort_code']): ?><p class="text-slate-500">Sort Code: <?= e($method['sort_code']) ?></p><?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
            <p class="text-[0.68rem] text-slate-400 mb-3">Please reference "<?= e($campaign['title']) ?>" in your transfer.</p>
          <?php endif; ?>
          <p class="text-xs text-slate-600 mb-3">Questions? Reach out to <strong class="text-primary-navy"><?= e($contactEmail) ?></strong>.</p>
          <?php if ($alreadyGiven): ?>
            <p class="text-xs font-semibold text-emerald-600 flex items-center gap-1.5"><i class="fa-solid fa-circle-check"></i> Thanks — you've marked this campaign as given.</p>
          <?php elseif (\App\Core\Auth::check()): ?>
            <form method="POST" action="<?= e(url('give/campaigns/' . $campaign['id'] . '/confirm')) ?>">
              <?= csrf_field() ?>
              <button type="submit" class="btn bg-emerald-600 !text-white hover:bg-emerald-700 !px-4 !py-2 text-xs flex items-center gap-2"><i class="fa-solid fa-check"></i> I've Made This Donation</button>
            </form>
          <?php else: ?>
            <a href="<?= e(url('login')) ?>" class="btn bg-slate-200 !text-slate-700 hover:bg-slate-300 !px-4 !py-2 text-xs inline-flex items-center gap-2">Log in to Mark as Given</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (empty($campaigns)): ?>
      <p class="text-sm text-slate-400 col-span-full">No campaigns found<?= $q !== '' ? ' matching "' . e($q) . '"' : '' ?>.</p>
    <?php endif; ?>
  </div>

  <div class="card !bg-amber-50 !border-amber-100 p-5 mt-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-white text-gold flex items-center justify-center flex-shrink-0 border border-amber-200"><i class="fa-solid fa-shield-halved"></i></div>
      <div>
        <p class="text-sm font-bold text-primary-navy">Secure. Transparent. Impactful.</p>
        <p class="text-xs text-slate-500">Your donations are secure and go directly toward making a real difference.</p>
      </div>
    </div>
    <a href="<?= e(url('give/causes/make-a-donation')) ?>" class="text-sm font-semibold text-gold hover:underline flex items-center gap-1.5 flex-shrink-0">Learn how we use donations <i class="fa-solid fa-arrow-right"></i></a>
  </div>

  <?php
  $groupLabels = ['support' => 'Support the Future', 'involve' => 'Get Involved', 'impact' => 'Create Impact'];
  $causesByGroup = [];
  foreach ($causes as $cause) {
      $causesByGroup[$cause['column_group']][] = $cause;
  }
  ?>
  <?php if ($causesByGroup): ?>
    <div class="mt-10">
      <h2 class="text-lg font-bold text-primary-navy">Ways to Give</h2>
      <div class="w-8 h-0.5 bg-gold rounded-full mt-1.5"></div>
    </div>
    <?php foreach ($groupLabels as $groupKey => $groupLabel): if (empty($causesByGroup[$groupKey])) continue; ?>
      <h3 class="text-xs font-extrabold text-primary-navy uppercase tracking-wide mb-3 mt-6"><?= e($groupLabel) ?></h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($causesByGroup[$groupKey] as $cause): ?>
          <a href="<?= e(url('give/causes/' . $cause['slug'])) ?>" class="card p-4 flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-[var(--menu-accent-50)] text-[var(--menu-accent-600)] flex items-center justify-center flex-shrink-0"><i class="<?= e($cause['icon']) ?>"></i></div>
            <div class="min-w-0">
              <h4 class="text-sm font-bold text-primary-navy"><?= e($cause['title']) ?></h4>
              <p class="text-xs text-slate-500"><?= e($cause['description']) ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
