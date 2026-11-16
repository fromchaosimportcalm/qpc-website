/* Block editor: give the editing canvas the same page-style classes the live page
   gets (qpc-page-<style>, has-own-header…), so pages look the same while editing.
   Follows the Template choice live. Data comes from functions.php (qpcPageStyles). */
(function (wp) {
  var cfg = window.qpcPageStyles || { styles: {}, flags: {}, frontPageId: 0 };
  var applied = [];

  function currentStyle() {
    var ed = wp.data.select('core/editor');
    if (!ed || ed.getCurrentPostType() !== 'page') return '';
    var tpl = ed.getEditedPostAttribute('template') || '';
    if (tpl.indexOf('qpc-style-') === 0 && cfg.styles[tpl.slice(10)]) return tpl.slice(10);
    var slug = ed.getCurrentPostId() === cfg.frontPageId ? 'home' : ed.getEditedPostAttribute('slug');
    return cfg.styles[slug] ? slug : '';
  }

  function classesFor(style) {
    if (!style) return [];
    var out = ['qpc-page-' + style];
    Object.keys(cfg.flags).forEach(function (flag) { if (cfg.flags[flag].indexOf(style) !== -1) out.push(flag); });
    return out;
  }

  function apply() {
    var frame = document.querySelector('iframe[name="editor-canvas"]');
    var doc = frame && frame.contentDocument ? frame.contentDocument : document;
    // Editor styles are prefixed with .editor-styles-wrapper (the canvas body), so
    // page-scoped rules need the class on an element inside it: the block list root.
    var targets = [doc.body, doc.querySelector('.is-root-container')].filter(Boolean);
    var want = classesFor(currentStyle());
    targets.forEach(function (el) {
      applied.forEach(function (c) { if (want.indexOf(c) === -1) el.classList.remove(c); });
      want.forEach(function (c) { el.classList.add(c); });
    });
    applied = want;
  }

  wp.domReady(function () {
    wp.data.subscribe(apply);
    setInterval(apply, 1500); // the canvas iframe can be re-created (device preview etc.)
  });
})(window.wp);
