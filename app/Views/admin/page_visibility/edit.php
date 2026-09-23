<p class="text-sm text-slate-500 mb-4">"Visible To" controls who can access the page's URL directly. "Show in Menu" separately controls whether its link appears in the desktop and mobile navigation — you can keep a page reachable by direct link while hiding it from the menu (or vice versa, within who "Visible To" allows).</p>

<form method="POST" action="<?= e(url('admin/page-visibility')) ?>" class="card overflow-x-auto">
  <?= csrf_field() ?>
  <table class="table-base">
    <thead>
      <tr>
        <th>Page</th>
        <th>Visible To</th>
        <th>Show in Menu</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pages as $page): ?>
        <tr>
          <td class="font-medium text-primary-navy"><?= e($page['label']) ?></td>
          <td>
            <select name="audience[<?= e($page['page_key']) ?>]" class="form-input max-w-xs">
              <option value="public" <?= $page['audience'] === 'public' ? 'selected' : '' ?>>Public (everyone, including visitors)</option>
              <option value="alumni" <?= $page['audience'] === 'alumni' ? 'selected' : '' ?>>Logged-in Alumni Only</option>
              <option value="editor" <?= $page['audience'] === 'editor' ? 'selected' : '' ?>>Editors & Admins Only</option>
              <option value="admin" <?= $page['audience'] === 'admin' ? 'selected' : '' ?>>Admin Only (hidden from everyone else)</option>
            </select>
          </td>
          <td>
            <label class="flex items-center gap-2">
              <input type="checkbox" name="show_in_nav[<?= e($page['page_key']) ?>]" value="1" <?= !empty($page['show_in_nav']) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
              <span class="text-xs text-slate-500">In desktop &amp; mobile menu</span>
            </label>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="p-5 border-t border-slate-100">
    <h3 class="text-sm font-bold text-primary-navy mb-1">Tab Visibility</h3>
    <p class="text-xs text-slate-400 mb-4">Hide an individual tab within a tabbed page — e.g. just "Interview Prep" — without hiding the rest of the page. Hiding every tab on a page falls back to a "not found" page.</p>
    <?php
      $tabPageLabels = ['resources' => 'Career Resources', 'jobs' => 'Job Board (Careers)', 'events' => 'Events', 'events_mine' => 'My Events'];
    ?>
    <div class="space-y-4">
      <?php foreach ($tabsByPage as $pageKey => $tabs): ?>
        <div>
          <h4 class="text-xs font-extrabold text-slate-500 uppercase tracking-wide mb-2"><?= e($tabPageLabels[$pageKey] ?? $pageKey) ?></h4>
          <div class="flex flex-wrap gap-x-6 gap-y-2">
            <?php foreach ($tabs as $t): ?>
              <label class="flex items-center gap-2">
                <input type="checkbox" name="tab_visible[<?= e($pageKey) ?>][<?= e($t['tab_key']) ?>]" value="1" <?= !empty($t['is_visible']) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
                <span class="text-xs text-slate-600"><?= e($t['label']) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="p-5 border-t border-slate-100">
    <h3 class="text-sm font-bold text-primary-navy mb-1">Individual Links</h3>
    <p class="text-xs text-slate-400 mb-4">Hide one specific menu link without affecting others that lead to the same page — e.g. hide "Create a Poll" while keeping "Start a Discussion" (both currently point to the Community Feed).</p>
    <?php
      $linkPageLabels = [
          'directory' => 'Alumni Network — Directory', 'connections' => 'Alumni Network — Connections',
          'feed' => 'Alumni Network — Engage', 'messages' => 'Messages', 'mentorship' => 'Mentorship',
          'businesses' => 'Alumni Businesses', 'jobs' => 'Careers', 'events' => 'Events',
          'events_mine' => 'My Events', 'knowledge' => 'Knowledge Hub', 'resources' => 'Career Resources',
          'benefits' => 'Benefits', 'give' => 'Give Back', 'news' => 'News & Blog',
          'spotlight' => 'Alumni Spotlight', 'map' => 'Alumni Map', 'other' => 'Admin & Miscellaneous',
      ];
    ?>
    <div class="space-y-2">
      <?php foreach ($linksByPage as $pageKey => $links): ?>
        <details class="group border border-slate-100 rounded-lg">
          <summary class="flex items-center justify-between gap-2 px-4 py-2.5 cursor-pointer list-none marker:hidden [&::-webkit-details-marker]:hidden">
            <span class="text-xs font-extrabold text-slate-600 uppercase tracking-wide"><?= e($linkPageLabels[$pageKey] ?? $pageKey) ?></span>
            <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform group-open:rotate-180"></i>
          </summary>
          <div class="flex flex-wrap gap-x-6 gap-y-2 px-4 pb-4">
            <?php foreach ($links as $link): ?>
              <label class="flex items-center gap-2">
                <input type="checkbox" name="link_visible[<?= e($link['link_key']) ?>]" value="1" <?= !empty($link['is_visible']) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
                <span class="text-xs text-slate-600"><?= e($link['label']) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="p-4 border-t border-slate-100">
    <button type="submit" class="btn-gold">Save Visibility Settings</button>
  </div>
</form>
