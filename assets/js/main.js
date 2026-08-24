(function () {
  "use strict";

  var prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ---- Mobile nav toggle ---- */
  var navToggle = document.getElementById("navToggle");
  var sidebar = document.getElementById("sidebar");
  var scrim = document.getElementById("scrim");

  function closeNav() {
    sidebar.classList.remove("open");
    scrim.classList.remove("open");
    navToggle.setAttribute("aria-expanded", "false");
  }

  function toggleNav() {
    var isOpen = sidebar.classList.toggle("open");
    scrim.classList.toggle("open", isOpen);
    navToggle.setAttribute("aria-expanded", String(isOpen));
  }

  navToggle.addEventListener("click", toggleNav);
  scrim.addEventListener("click", closeNav);
  document.querySelectorAll(".sidebar-nav a").forEach(function (link) {
    link.addEventListener("click", closeNav);
  });

  /* ---- Scroll-spy active nav highlighting ---- */
  function navTarget(link) {
    return link.hash ? link.hash.slice(1) : null;
  }

  var navLinks = Array.prototype.filter.call(
    document.querySelectorAll(".sidebar-nav a"),
    function (link) { return !!navTarget(link); }
  );
  var sections = navLinks.map(function (link) {
    return document.getElementById(navTarget(link));
  }).filter(Boolean);

  function setActive(id) {
    navLinks.forEach(function (link) {
      link.classList.toggle("active", navTarget(link) === id);
    });
  }

  if ("IntersectionObserver" in window && sections.length) {
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            setActive(entry.target.id);
          }
        });
      },
      { rootMargin: "-40% 0px -50% 0px", threshold: 0 }
    );
    sections.forEach(function (section) { observer.observe(section); });
  }

  /* ---- Hero typed role cycle ---- */
  var roles = [
    "Full-Stack Web Developer",
    "Shopify Specialist",
    "WordPress Engineer",
    "Laravel Developer"
  ];
  var typedEl = document.getElementById("typedRole");

  if (typedEl && !prefersReducedMotion) {
    var roleIndex = 0;
    var charIndex = roles[0].length;
    var deleting = false;

    function tick() {
      var current = roles[roleIndex];

      if (!deleting) {
        charIndex++;
        if (charIndex > current.length) {
          deleting = true;
          setTimeout(tick, 1800);
          return;
        }
      } else {
        charIndex--;
        if (charIndex < 0) {
          deleting = false;
          roleIndex = (roleIndex + 1) % roles.length;
          charIndex = 0;
        }
      }

      typedEl.textContent = roles[roleIndex].slice(0, charIndex);
      setTimeout(tick, deleting ? 35 : 65);
    }

    typedEl.textContent = roles[0];
    setTimeout(tick, 2200);
  }

  /* ---- Work slider ---- */
  var slider = document.getElementById("workSlider");
  if (slider) {
    var track = document.getElementById("sliderTrack");
    var slides = Array.prototype.slice.call(track.children);
    var prevBtn = document.getElementById("sliderPrev");
    var nextBtn = document.getElementById("sliderNext");
    var dotsWrap = document.getElementById("sliderDots");
    var current = 0;
    var perView = 1;
    var pageCount = 1;

    function getPerView() {
      var raw = getComputedStyle(slider).getPropertyValue("--per-view");
      var n = parseInt(raw, 10);
      return n > 0 ? n : 1;
    }

    function buildDots() {
      dotsWrap.innerHTML = "";
      for (var i = 0; i < pageCount; i++) {
        (function (i) {
          var dot = document.createElement("button");
          dot.type = "button";
          dot.setAttribute("aria-label", "Go to slide group " + (i + 1));
          dot.addEventListener("click", function () { goTo(i); });
          dotsWrap.appendChild(dot);
        })(i);
      }
    }

    function render() {
      var maxStart = Math.max(0, slides.length - perView);
      var startIndex = Math.min(current * perView, maxStart);
      track.style.transform = "translateX(-" + startIndex * (100 / perView) + "%)";
      Array.prototype.forEach.call(dotsWrap.children, function (dot, i) {
        dot.classList.toggle("active", i === current);
      });
    }

    function goTo(i) {
      current = (i + pageCount) % pageCount;
      render();
    }

    function refreshLayout() {
      var newPerView = getPerView();
      if (newPerView === perView && dotsWrap.children.length) return;
      perView = newPerView;
      pageCount = Math.max(1, Math.ceil(slides.length / perView));
      current = Math.min(current, pageCount - 1);
      buildDots();
      render();
    }

    prevBtn.addEventListener("click", function () { goTo(current - 1); });
    nextBtn.addEventListener("click", function () { goTo(current + 1); });

    slider.setAttribute("tabindex", "0");
    slider.addEventListener("keydown", function (e) {
      if (e.key === "ArrowLeft") goTo(current - 1);
      if (e.key === "ArrowRight") goTo(current + 1);
    });

    var touchStartX = null;
    track.addEventListener("touchstart", function (e) { touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener("touchend", function (e) {
      if (touchStartX === null) return;
      var delta = e.changedTouches[0].clientX - touchStartX;
      if (Math.abs(delta) > 40) goTo(current + (delta < 0 ? 1 : -1));
      touchStartX = null;
    });

    window.addEventListener("resize", refreshLayout);
    refreshLayout();
  }
})();
