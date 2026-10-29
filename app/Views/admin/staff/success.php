<div class="max-w-lg mx-auto">
  <div class="card p-8 text-center">
    <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 text-emerald-600 ring-8 ring-emerald-50/50 flex items-center justify-center text-2xl mb-5">
      <i class="fa-solid fa-circle-check"></i>
    </div>
    <h2 class="text-xl font-extrabold text-primary-navy mb-2"><?= e($result['role']) ?> account created</h2>
    <p class="text-sm text-slate-500 leading-relaxed mb-6">
      <span class="font-semibold text-primary-navy"><?= e($result['name']) ?></span> can now sign in. Login credentials have been emailed to
      <span class="font-semibold text-primary-navy"><?= e($result['email']) ?></span>.
    </p>

    <div class="flex items-center justify-center gap-2.5 bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-xs text-slate-500 mb-7">
      <i class="fa-solid fa-envelope-circle-check text-gold"></i>
      Email sent with a one-time temporary password and a sign-in link.
    </div>

    <div class="flex items-center justify-center gap-3">
      <a href="<?= e(url('admin/create-staff')) ?>" class="btn-gold !px-5 !py-2.5 text-sm">Create Another</a>
      <a href="<?= e(url('admin/alumni')) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-5 !py-2.5 text-sm">View Staff List</a>
    </div>
  </div>
</div>
