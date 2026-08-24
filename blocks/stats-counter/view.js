(function () {
  "use strict";

  var prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var DURATION = 1200;

  function animateCount(el) {
    var target = parseInt(el.getAttribute("data-count-to"), 10) || 0;
    var suffix = el.getAttribute("data-suffix") || "";

    if (prefersReducedMotion) {
      el.textContent = target + suffix;
      return;
    }

    var start = null;
    function step(timestamp) {
      if (start === null) start = timestamp;
      var progress = Math.min((timestamp - start) / DURATION, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(eased * target) + suffix;
      if (progress < 1) {
        window.requestAnimationFrame(step);
      }
    }
    window.requestAnimationFrame(step);
  }

  var counters = document.querySelectorAll(".stat-num[data-count-to]");
  if (!counters.length) return;

  if (!("IntersectionObserver" in window)) {
    Array.prototype.forEach.call(counters, animateCount);
    return;
  }

  var observer = new IntersectionObserver(
    function (entries, obs) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCount(entry.target);
          obs.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.4 }
  );

  Array.prototype.forEach.call(counters, function (el) {
    observer.observe(el);
  });
})();
