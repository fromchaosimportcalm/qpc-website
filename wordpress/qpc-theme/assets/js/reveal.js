/* Fade items in as they scroll into view (Our History timeline). The CSS only
   hides them once this script has run (body.reveal-ready), so if it ever fails
   the content still shows, and it always shows in the editor. */
(function () {
  var items = document.querySelectorAll('.timeline-event');
  if (!items.length || !('IntersectionObserver' in window)) return;
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); }
    });
  }, { threshold: 0.15 });
  items.forEach(function (el) { io.observe(el); });
  document.body.classList.add('reveal-ready');
})();
