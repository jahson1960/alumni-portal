<?php
/**
 * Self-hosted page-view tracking. Fires once per real page load - never from the app's own
 * background AJAX calls, since those run via fetch() and never trigger a freshly-rendered
 * page's inline <script>. Measures *active* time (tab visible), not wall-clock since load, so
 * a tab left open in the background doesn't inflate "time on page". The duration beacon fires
 * every time the tab goes hidden (not just on final close) - each send just overwrites the
 * same row with the latest total, so a tab left hidden forever (switched away and never
 * closed) still has its time on page recorded, not just ones that get an actual pagehide.
 */
?>
<script>
  (function () {
    var pageViewId = null;
    var activeMs = 0;
    var visibleSince = document.visibilityState === 'visible' ? Date.now() : null;

    fetch(<?= json_encode(url('analytics/track')) ?>, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'path=' + encodeURIComponent(location.pathname + location.search) + '&referrer=' + encodeURIComponent(document.referrer || ''),
    })
      .then(function (r) { return r.json(); })
      .then(function (data) { pageViewId = data.id || null; })
      .catch(function () {});

    function accumulate() {
      if (visibleSince) {
        activeMs += Date.now() - visibleSince;
        visibleSince = null;
      }
    }

    function send() {
      if (!pageViewId || activeMs <= 0) return;
      var data = new FormData();
      data.append('id', pageViewId);
      data.append('seconds', Math.round(activeMs / 1000));
      navigator.sendBeacon(<?= json_encode(url('analytics/duration')) ?>, data);
    }

    document.addEventListener('visibilitychange', function () {
      if (document.visibilityState === 'hidden') {
        accumulate();
        send();
      } else {
        visibleSince = Date.now();
      }
    });
    window.addEventListener('pagehide', function () {
      accumulate();
      send();
    });
  })();
</script>
