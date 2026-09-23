<div class="card p-6 max-w-xl">
  <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-circle-check"></i></div>
  <h2 class="text-lg font-extrabold text-primary-navy mb-1">Alumni account created</h2>
  <p class="text-sm text-slate-500 mb-6"><?= e($result['name']) ?> can now log in with the credentials below. This password is shown only once — copy it before leaving this page.</p>

  <div class="space-y-3 mb-6">
    <div>
      <p class="text-xs text-slate-400 mb-1">Email</p>
      <p class="text-sm font-semibold text-primary-navy"><?= e($result['email']) ?></p>
    </div>
    <div>
      <p class="text-xs text-slate-400 mb-1">Temporary Password</p>
      <div class="flex items-center gap-2">
        <code id="temp-password" class="flex-1 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm font-mono text-primary-navy"><?= e($result['password']) ?></code>
        <button type="button" id="copy-password" data-value="<?= e($result['password']) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-3 !py-2 text-xs flex-shrink-0"><i class="fa-solid fa-copy"></i> Copy</button>
      </div>
      <p id="copy-password-msg" class="hidden text-xs text-emerald-600 mt-1.5">Copied!</p>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <a href="<?= e(url('admin/register-alumni')) ?>" class="btn-gold !px-4 !py-2.5 text-sm">Register Another</a>
    <a href="<?= e(url('admin/alumni')) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-4 !py-2.5 text-sm">View Alumni List</a>
  </div>
</div>

<script>
  (function () {
    var btn = document.getElementById('copy-password');
    var msg = document.getElementById('copy-password-msg');
    if (!btn) return;
    btn.addEventListener('click', function () {
      var value = btn.dataset.value;
      var done = function () {
        if (!msg) return;
        msg.classList.remove('hidden');
        setTimeout(function () { msg.classList.add('hidden'); }, 2000);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(value).then(done).catch(function () {});
      } else {
        var input = document.createElement('input');
        input.value = value;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        done();
      }
    });
  })();
</script>
