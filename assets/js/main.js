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

  /* ---- Sliders (Work + Testimonials), powered by Swiper ----
     Generic init over every .swiper on the page (scoped by class, not id,
     so multiple independent carousels can coexist). Each instance reads
     its own data-per-view-* attributes -- set by the block's Inspector
     Controls, see blocks/work-slider/render.php and
     blocks/testimonial-slider/render.php -- to configure Swiper's
     responsive `breakpoints` option. The .testimonial-slider marker class
     switches to a wider tablet breakpoint (>1100px vs >900px for Work)
     before stepping up to the desktop count, matching the pre-Swiper
     breakpoints in style.css. */
  if (window.Swiper) {
    Array.prototype.forEach.call(document.querySelectorAll(".swiper"), function (el) {
      var desktop = parseInt(el.dataset.perViewDesktop, 10) || 1;
      var tablet = parseInt(el.dataset.perViewTablet, 10) || 1;
      var mobile = parseInt(el.dataset.perViewMobile, 10) || 1;
      var desktopBreakpoint = el.classList.contains("testimonial-slider") ? 1101 : 901;
      var breakpoints = {};
      breakpoints[641] = { slidesPerView: tablet };
      breakpoints[desktopBreakpoint] = { slidesPerView: desktop };

      new window.Swiper(el, {
        slidesPerView: mobile,
        spaceBetween: 20,
        speed: prefersReducedMotion ? 0 : 450,
        keyboard: { enabled: true },
        a11y: { enabled: true },
        breakpoints: breakpoints,
        navigation: {
          nextEl: el.querySelector(".swiper-button-next"),
          prevEl: el.querySelector(".swiper-button-prev"),
        },
        pagination: {
          el: el.querySelector(".swiper-pagination"),
          clickable: true,
        },
      });
    });
  }
})();
