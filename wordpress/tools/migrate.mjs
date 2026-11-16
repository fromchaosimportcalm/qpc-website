#!/usr/bin/env node
/**
 * One-off migration: static *.html pages → WordPress block content + CSS.
 *
 *   node wordpress/tools/migrate.mjs            (run from the repo root)
 *
 * Writes:
 *   wordpress/qpc-core/data/pages/<slug>.html          block markup for each page
 *   wordpress/qpc-theme/assets/css/sections/<slug>.css page-specific CSS
 *   wordpress/tools/migrate-report.txt                 things that need a human look
 *
 * Shared CSS (nav, footer, buttons, page header…) is NOT copied; it lives in
 * qpc-theme/assets/css/base.css. Any selector defined differently by two pages
 * is scoped to its page (.qpc-page-<slug>) so pages can't break each other.
 */
import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import { parse, NodeType } from 'node-html-parser';
import postcss from 'postcss';

const ROOT = process.cwd();
const OUT_PAGES = path.join(ROOT, 'wordpress/qpc-core/data/pages');
const OUT_CSS = path.join(ROOT, 'wordpress/qpc-theme/assets/css/sections');
const BASE_CSS = path.join(ROOT, 'wordpress/qpc-theme/assets/css/base.css');

/** static file → WordPress slug ('' = front page) */
const PAGES = {
  'index.html': 'home',
  'ministries.html': 'church-life',
  'history.html': 'our-history',
  'team.html': 'our-team',
  'sermons.html': 'sermons',
  'calendar.html': 'calendar',
  'visit.html': 'plan-your-visit',
  'bible-study.html': 'bible-study',
  'mens-brekkie.html': 'mens-brekkie',
  'playgroup.html': 'playgroup',
  'prayer.html': 'prayer',
  'ladies-craft.html': 'ladies-craft',
  'christian-singles.html': 'christian-singles',
  'm3.html': 'm3',
  'christmas-gala.html': 'christmas-gala',
};

const report = [];
const note = (page, msg) => report.push(`[${page}] ${msg}`);

/* ------------------------------------------------------------------ */
/* Block serialisation                                                 */
/* ------------------------------------------------------------------ */

const json = (o) => {
  const keys = Object.keys(o).filter((k) => o[k] !== undefined && o[k] !== '');
  if (!keys.length) return '';
  const clean = Object.fromEntries(keys.map((k) => [k, o[k]]));
  return ' ' + JSON.stringify(clean).replace(/--/g, '\\u002d\\u002d').replace(/</g, '\\u003c').replace(/>/g, '\\u003e').replace(/&/g, '\\u0026');
};
const cls = (...c) => c.filter(Boolean).join(' ');
const attr = (name, v) => (v ? ` ${name}="${v}"` : '');

const B = {
  paragraph: (inner, className, id) =>
    `<!-- wp:paragraph${json({ className })} -->\n<p${attr('class', className)}${attr('id', id)}>${inner}</p>\n<!-- /wp:paragraph -->`,
  heading: (level, inner, className, id) =>
    `<!-- wp:heading${json({ level: level === 2 ? undefined : level, className })} -->\n<h${level} class="${cls('wp-block-heading', className)}"${attr('id', id)}>${inner}</h${level}>\n<!-- /wp:heading -->`,
  group: (tagName, inner, className, id) => {
    const t = ['div', 'header', 'main', 'section', 'article', 'aside', 'footer'].includes(tagName) ? tagName : 'div';
    return `<!-- wp:group${json({ tagName: t === 'div' ? undefined : t, className, layout: { type: 'default' } })} -->\n<${t} class="${cls('wp-block-group', className)}"${attr('id', id)}>${inner.join('\n\n')}</${t}>\n<!-- /wp:group -->`;
  },
  html: (raw) => `<!-- wp:html -->\n${raw.trim()}\n<!-- /wp:html -->`,
  buttons: (buttons, className) =>
    `<!-- wp:buttons${json({ className })} -->\n<div class="${cls('wp-block-buttons', className)}">${buttons.join('\n\n')}</div>\n<!-- /wp:buttons -->`,
  button: ({ href, text, style, extra, target, rel }) => {
    const className = cls(style && `is-style-${style}`, extra);
    return `<!-- wp:button${json({ className })} -->\n<div class="${cls('wp-block-button', className)}"><a class="wp-block-button__link wp-element-button" href="${href}"${attr('target', target)}${attr('rel', rel)}>${text}</a></div>\n<!-- /wp:button -->`;
  },
  list: (ordered, items, className) => {
    const t = ordered ? 'ol' : 'ul';
    const li = items.map((i) => `<!-- wp:list-item -->\n<li>${i}</li>\n<!-- /wp:list-item -->`).join('\n\n');
    return `<!-- wp:list${json({ ordered: ordered || undefined, className })} -->\n<${t} class="${cls('wp-block-list', className)}">${li}</${t}>\n<!-- /wp:list -->`;
  },
  image: ({ src, alt, caption, className, lightbox }) =>
    `<!-- wp:image${json({ className, lightbox: lightbox ? { enabled: true } : undefined })} -->\n<figure class="${cls('wp-block-image', className)}"><img src="${src}" alt="${(alt || '').replace(/"/g, '&quot;')}"/>${caption ? `<figcaption class="wp-element-caption">${caption}</figcaption>` : ''}</figure>\n<!-- /wp:image -->`,
  details: (summary, inner, className) =>
    `<!-- wp:details${json({ className })} -->\n<details class="${cls('wp-block-details', className)}"><summary>${summary}</summary>${inner.join('\n\n')}</details>\n<!-- /wp:details -->`,
  quote: (inner, citation, className) =>
    `<!-- wp:quote${json({ className })} -->\n<blockquote class="${cls('wp-block-quote', className)}">${inner.join('\n\n')}${citation ? `<cite>${citation}</cite>` : ''}</blockquote>\n<!-- /wp:quote -->`,
  gallery: (images, className) =>
    `<!-- wp:gallery${json({ linkTo: 'none', className })} -->\n<figure class="${cls('wp-block-gallery has-nested-images columns-default is-cropped', className)}">${images.join('\n\n')}</figure>\n<!-- /wp:gallery -->`,
  dynamic: (name, attrs = {}) => `<!-- wp:${name}${json(attrs)} /-->`,
};

/* ------------------------------------------------------------------ */
/* HTML → blocks                                                       */
/* ------------------------------------------------------------------ */

const INLINE = new Set(['a', 'strong', 'em', 'b', 'i', 'br', 'span', 'small', 'code', 'sup', 'sub', 'mark', 'time', 'abbr', 'u', 's']);
const RAW = new Set(['svg', 'iframe', 'button', 'input', 'form', 'select', 'textarea', 'video', 'audio', 'canvas', 'script', 'object', 'table']);
const BTN = { 'btn-primary': 'primary', 'btn-secondary': 'secondary', 'btn-spotify': 'spotify', 'btn-gold': 'gold' };

let inlineCss = []; // [className, declarations]
let pageSlug = '';

const tag = (el) => (el.rawTagName || '').toLowerCase();
const classes = (el) => (el.getAttribute?.('class') || '').split(/\s+/).filter(Boolean);
const isEl = (n) => n.nodeType === NodeType.ELEMENT_NODE;
const isText = (n) => n.nodeType === NodeType.TEXT_NODE;
const isBlank = (n) => isText(n) && !n.rawText.trim();

function rewriteLinks(html) {
  return html.replace(/(href|src)="([^"]*)"/g, (m, a, url) => `${a}="${rewriteUrl(url)}"`);
}
function rewriteUrl(url) {
  const m = url.match(/^(?:\.?\/)?([a-z0-9-]+)\.html(#.*)?$/i);
  if (m && PAGES[m[1] + '.html'] !== undefined) {
    const slug = PAGES[m[1] + '.html'];
    return (slug === 'home' ? '/' : `/${slug}/`) + (m[2] || '');
  }
  return url; // images/... are swapped for Media Library URLs by the importer
}

/** Turn an element's inline style="" into a generated class (keeps the look, keeps markup clean). */
function styleClass(el) {
  const style = el.getAttribute('style');
  if (!style) return '';
  const decl = style.trim().replace(/;?\s*$/, ';');
  if (/display:\s*none/i.test(decl)) note(pageSlug, `element hidden with inline display:none: <${tag(el)} class="${classes(el).join(' ')}">`);
  const name = 'u-' + crypto.createHash('md5').update(decl).digest('hex').slice(0, 6);
  inlineCss.push([name, decl]);
  return name;
}

const allInline = (el) => el.childNodes.every((n) => isText(n) || (isEl(n) && INLINE.has(tag(n)) && !buttonStyle(n) && allInline(n)));
const buttonStyle = (el) => tag(el) === 'a' && classes(el).map((c) => BTN[c]).find(Boolean);

function innerInline(el) {
  return rewriteLinks(el.innerHTML.replace(/\s+/g, ' ').replace(/\s*<br\s*\/?>\s*/g, '<br>').trim());
}

function toButton(a) {
  const style = buttonStyle(a);
  const extra = classes(a).filter((c) => !BTN[c]);
  if (extra.length) note(pageSlug, `button extra classes moved to wrapper: ${extra.join(' ')}`);
  return B.button({
    href: rewriteUrl(a.getAttribute('href') || '#'),
    text: innerInline(a),
    style,
    extra: cls(...extra, styleClass(a)),
    target: a.getAttribute('target'),
    rel: a.getAttribute('rel'),
  });
}

/** Convert a list of sibling nodes into blocks, grouping runs of buttons and stray text. */
function convertChildren(nodes) {
  const out = [];
  let buttons = [];
  let text = [];
  const flush = () => {
    if (buttons.length) out.push(B.buttons(buttons));
    buttons = [];
    const t = text.join('').trim();
    if (t) out.push(B.paragraph(rewriteLinks(t)));
    text = [];
  };
  for (const n of nodes) {
    if (isBlank(n)) continue;
    if (isText(n) || (isEl(n) && INLINE.has(tag(n)) && tag(n) !== 'a' && tag(n) !== 'span')) {
      text.push(isText(n) ? n.rawText.replace(/\s+/g, ' ') : n.outerHTML);
      continue;
    }
    if (isEl(n) && buttonStyle(n)) { if (text.length) flush(); buttons.push(toButton(n)); continue; }
    flush();
    const b = convert(n);
    if (b) out.push(b);
  }
  flush();
  return out;
}

function convert(el) {
  if (!isEl(el)) return null;
  const t = tag(el);
  const id = el.getAttribute('id');

  // Section-level replacements handled by page hooks first (they may also edit classes).
  for (const hook of HOOKS) {
    const r = hook(el);
    if (r !== undefined) return r;
  }
  const c = classes(el);

  const className = cls(...c, styleClass(el));

  if (RAW.has(t) || [...Object.keys(el.attributes)].some((a) => a.startsWith('data-'))) {
    if (t !== 'iframe' && t !== 'svg') note(pageSlug, `kept as Custom HTML: <${t} class="${c.join(' ')}"${id ? ` id="${id}"` : ''}>`);
    return B.html(rewriteLinks(el.outerHTML));
  }
  if (/^h[1-6]$/.test(t)) return B.heading(+t[1], innerInline(el), className, id);
  if (t === 'p') return B.paragraph(innerInline(el), className, id);
  if (t === 'img') return B.image({ src: el.getAttribute('src'), alt: el.getAttribute('alt'), className });
  if (t === 'ul' || t === 'ol') {
    const items = el.childNodes.filter(isEl).map((li) => (allInline(li) ? innerInline(li) : null));
    if (items.some((i) => i === null)) { note(pageSlug, `list with block content kept as HTML (.${c.join('.')})`); return B.html(rewriteLinks(el.outerHTML)); }
    return B.list(t === 'ol', items, className);
  }
  if (t === 'details') {
    const summary = el.querySelector('summary');
    const q = summary.querySelector('.faq-q-text')?.innerHTML || summary.text;
    const rest = el.childNodes.filter((n) => n !== summary);
    return B.details(q.trim(), convertChildren(rest), className);
  }
  if (t === 'figure') {
    const img = el.querySelector('img');
    const cap = el.querySelector('figcaption');
    if (img && el.childNodes.filter(isEl).every((n) => n === img || n === cap)) {
      return B.image({ src: img.getAttribute('src'), alt: img.getAttribute('alt'), caption: cap && innerInline(cap), className: cls(className, ...classes(img)), lightbox: true });
    }
  }
  if (t === 'blockquote') {
    const cite = el.querySelector('cite');
    if (allInline(el) && !cite) return B.paragraph(innerInline(el), cls('is-quote', 'was-div', className));
    const inner = convertChildren(el.childNodes.filter((n) => n !== cite));
    return B.quote(inner, cite && innerInline(cite), className);
  }
  if (t === 'a') {
    if (allInline(el)) return B.paragraph(rewriteLinks(el.outerHTML.replace(/\s+/g, ' ')));
    // A whole card that is a link → an editable group, with an invisible link
    // stretched over the card (see .card-link in base.css) so it stays clickable.
    const href = rewriteUrl(el.getAttribute('href') || '#');
    const label = (el.querySelector('h1,h2,h3,h4,[class*="title"]')?.text || 'Read more').trim().replace(/\s+/g, ' ');
    const inner = convertChildren(el.childNodes);
    inner.push(B.paragraph(`<a href="${href}">${label}</a>`, 'card-link'));
    return B.group('div', inner, cls(className, 'is-linked-card'), id);
  }
  // A box holding just one image becomes the image block itself (the box's class
  // goes on the block's <figure>), so ".box img" rules size it exactly as before.
  const only = el.childNodes.filter((n) => !isBlank(n));
  if (['div', 'span'].includes(t) && only.length === 1 && isEl(only[0]) && tag(only[0]) === 'img') {
    return B.image({ src: only[0].getAttribute('src'), alt: only[0].getAttribute('alt'), className: cls(className, ...classes(only[0]), styleClass(only[0])) });
  }
  if (t === 'cite' && allInline(el)) return B.paragraph(innerInline(el), cls('is-cite', 'was-div', className), id);
  // Inline-only containers become paragraphs; anything else becomes a group.
  if (allInline(el) && el.text.trim()) return B.paragraph(innerInline(el), cls(className, 'was-div'), id);
  if (!el.childNodes.some((n) => !isBlank(n))) {
    // Empty decorative element (overlay, dot…) — keep as HTML so CSS still applies.
    return B.html(el.outerHTML);
  }
  const kids = el.childNodes.filter((n) => !isBlank(n));
  if (kids.length && kids.every((n) => isEl(n) && buttonStyle(n))) return B.buttons(kids.map(toButton), className);
  return B.group(t, convertChildren(el.childNodes), className, id);
}

/* ------------------------------------------------------------------ */
/* Page-specific hooks: dynamic sections and things that change form   */
/* Return undefined to fall through, null to drop, or block markup.     */
/* ------------------------------------------------------------------ */

let noticesInserted = false;
const HOOKS = [
  // Lightboxes, gallery filters, loading messages: replaced by WordPress features.
  (el) => (['lightbox', 'gallery-filter', 'nav-overlay', 'loading-msg'].some((c) => classes(el).includes(c)) || ['lightbox', 'histLightbox', 'loadingMsg'].includes(el.getAttribute('id')) ? null : undefined),
  // Expired one-off Joint Service content.
  (el) => (classes(el).includes('joint-service-only') ? (note(pageSlug, 'dropped expired Joint Service element'), null) : undefined),
  (el) => {
    if (!classes(el).includes('regular-service-only')) return undefined;
    el.setAttribute('class', classes(el).filter((c) => !['regular-service-only', 'is-hidden'].includes(c)).join(' '));
    return undefined;
  },
  // Homepage notices (gala banner) → Notices block (the banner itself becomes a Notice post).
  (el) => {
    if (!classes(el).includes('gala-banner')) return undefined;
    if (noticesInserted) return null;
    noticesInserted = true;
    return B.dynamic('qpc/notices');
  },
  (el) => (classes(el).includes('votd-section') ? B.dynamic('qpc/verse-of-the-day') : undefined),
  // Sermons / calendar: the blocks render their own filters, search and lists.
  (el) => (['filter-bar', 'controls'].some((c) => classes(el).includes(c)) ? null : undefined),
  (el) => (el.getAttribute('id') === 'sermonsWrap' ? B.dynamic('qpc/sermon-library') : undefined),
  (el) => (classes(el).includes('calendar-wrap') ? B.dynamic('qpc/calendar') : undefined),
  // Ministry photo grids → core Gallery with the built-in "expand on click" lightbox.
  (el) => {
    if (!classes(el).includes('photo-grid')) return undefined;
    const imgs = el.querySelectorAll('.photo-tile').filter((t) => t.querySelector('img')).map((tile) => {
      const img = tile.querySelector('img');
      const title = tile.querySelector('.cap-title')?.text.trim();
      const date = tile.querySelector('.cap-date')?.text.trim();
      return B.image({ src: img.getAttribute('src'), alt: img.getAttribute('alt'), caption: [title, date].filter(Boolean).join(' · '), lightbox: true });
    });
    const placeholders = el.querySelectorAll('.photo-tile').length - imgs.length;
    if (placeholders) note(pageSlug, `gallery: ${imgs.length} photo(s) kept, ${placeholders} empty placeholder tile(s) dropped`);
    return imgs.length ? B.gallery(imgs, 'photo-gallery') : B.paragraph('<em>Photos coming soon.</em>', 'photo-gallery-empty');
  },
];

/* ------------------------------------------------------------------ */
/* CSS                                                                 */
/* ------------------------------------------------------------------ */

const SELECTOR_REWRITES = [
  [/(^|[\s>+~,(])nav(?![-\w])/g, '$1.site-nav'],
  [/(^|[\s>+~,(])footer(?![-\w])/g, '$1.site-footer'],
  [/(^|[\s>+~,(])a\.(ministry-card|teaser-card)(?![-\w])/g, '$1.$2'],
  // Old <div>s are now <p> blocks (class "was-div"): "x p" rules must not hit them.
  [/(^|[\s>+~,(])p(?![-\w])/g, '$1p:not(.was-div)'],
  // Tags that became paragraph blocks keep their look via a class.
  [/(^|[\s>+~,(])blockquote(?![-\w])/g, '$1:is(blockquote,.is-quote)'],
  [/(^|[\s>+~,(])cite(?![-\w])/g, '$1:is(cite,.is-cite)'],
  [/\.btn-primary(?![-\w])/g, ':is(.btn-primary,.is-style-primary>.wp-block-button__link)'],
  [/\.btn-secondary(?![-\w])/g, ':is(.btn-secondary,.is-style-secondary>.wp-block-button__link)'],
  [/\.btn-spotify(?![-\w])/g, ':is(.btn-spotify,.is-style-spotify>.wp-block-button__link)'],
];

/** base.css rules: "ctx§selector" → normalised declarations. */
function baseRules() {
  const map = new Map();
  postcss.parse(fs.readFileSync(BASE_CSS, 'utf8')).walkRules((r) => r.selectors.forEach((s) => map.set(ctx(r) + '§' + norm(s), ndecls(r))));
  return map;
}
const ndecls = (rule) => decls(rule).toLowerCase().replace(/\s+/g, '').replace(/,\s*/g, ',');
const SHARED = /^\.?(spotify-|btn-)/;
// Identical on every page → always from base.css; a page's copy is dropped.
const ALWAYS_BASE = /^\.?(site-nav|site-footer|nav-|footer-|social-link)/;
// Each page tends to style these its own way. If a page does, base.css's
// defaults are switched off for it (body class) and its own rules are used.
const FAMILIES = {
  'has-own-header': /^\.?(page-header|page-eyebrow|page-title|page-lead)/,
  'has-own-headings': /^\.?(section-label|section-title|section-body)/,
};
const familyPages = Object.fromEntries(Object.keys(FAMILIES).map((k) => [k, new Set()]));
const norm = (s) => s.replace(/\s+/g, ' ').replace(/\s*([>+~,])\s*/g, '$1').trim();
const ctx = (rule) => { const a = []; let p = rule.parent; while (p && p.type !== 'root') { if (p.type === 'atrule') a.unshift(`@${p.name} ${p.params}`.replace(/\s+/g, '')); p = p.parent; } return a.join('|'); };
const decls = (rule) => rule.nodes.filter((n) => n.type === 'decl').map((d) => `${d.prop}:${d.value}${d.important ? '!important' : ''}`).join(';');

const BASE_ONLY = /^(:root|\*|html|body|img)/;

/* ------------------------------------------------------------------ */
/* Run                                                                 */
/* ------------------------------------------------------------------ */

fs.mkdirSync(OUT_PAGES, { recursive: true });
const base = baseRules();
const forceScope = new Set(); // slug§ctx§selector: page overrides of shared styles
const pageCss = {}; // slug → postcss Root
const pageTitles = {}; // slug → title for the Template menu
const seen = new Map(); // ctx+selector → { decl, slugs:Set }

for (const [file, slug] of Object.entries(PAGES)) {
  pageSlug = slug;
  noticesInserted = false;
  const src = fs.readFileSync(path.join(ROOT, file), 'utf8');
  const doc = parse(src, { comment: false, blockTextElements: { script: true, style: true } });
  pageTitles[slug] = slug === 'home' ? 'Homepage' : (doc.querySelector('title')?.text || slug).split(/\s+[—·|-]\s+Queanbeyan/)[0].trim() || 'Home';

  // --- markup: everything between <nav> and <footer>
  const body = doc.querySelector('body') || doc;
  const start = body.childNodes;
  const content = [];
  let inside = false;
  for (const n of (body.querySelector('nav')?.parentNode || body).childNodes) {
    if (isEl(n) && tag(n) === 'nav') { inside = true; continue; }
    if (isEl(n) && tag(n) === 'footer') break;
    if (inside && !(isEl(n) && ['script', 'style'].includes(tag(n)))) content.push(n);
  }
  if (!content.length) note(slug, 'NO CONTENT FOUND between nav and footer');
  const blocks = convertChildren(content);
  fs.writeFileSync(path.join(OUT_PAGES, `${slug}.html`), blocks.join('\n\n') + '\n');

  // --- css
  const css = doc.querySelectorAll('style').map((s) => s.innerHTML).join('\n');
  const root = postcss.parse(css);
  root.walkRules((rule) => {
    if (rule.parent.type === 'atrule' && /keyframes/.test(rule.parent.name)) return;
    rule.selectors = rule.selectors.map((s) => SELECTOR_REWRITES.reduce((acc, [re, rep]) => acc.replace(re, rep), s));
    // Drop what base.css already says; keep a page's *changes* to shared styles, scoped to that page.
    const keep = rule.selectors.filter((s) => {
      if (BASE_ONLY.test(s.trim())) return false;
      const bare = s.replace(/^:is\(/, '').trim();
      if (ALWAYS_BASE.test(bare)) return false;
      const key = ctx(rule) + '§' + norm(s);
      for (const [flag, re] of Object.entries(FAMILIES)) {
        if (re.test(bare)) { familyPages[flag].add(slug); forceScope.add(slug + '§' + key); return true; }
      }
      if (base.has(key) && base.get(key) === ndecls(rule)) return false;
      if (base.has(key) || SHARED.test(s.replace(/^:is\(/, '').trim())) forceScope.add(slug + '§' + key);
      return true;
    });
    if (!keep.length) { rule.remove(); return; }
    rule.selectors = keep;
  });
  root.walkAtRules((a) => { if (!a.nodes?.length && a.name !== 'import') a.remove(); });
  root.walkRules((rule) => {
    if (rule.parent.type === 'atrule' && /keyframes/.test(rule.parent.name)) return;
    for (const s of rule.selectors) {
      const key = ctx(rule) + '§' + norm(s);
      const d = decls(rule);
      const e = seen.get(key) || { decls: new Set(), slugs: new Set() };
      e.decls.add(d); e.slugs.add(slug); seen.set(key, e);
    }
  });
  // Images referenced from CSS ship with the theme (assets/images/...).
  root.walkDecls((d) => {
    d.value = d.value.replace(/url\((['"]?)\/?images\/([^'")]+)\1\)/g, (m, q, file) => {
      const dest = path.join(ROOT, 'wordpress/qpc-theme/assets/images', file);
      fs.mkdirSync(path.dirname(dest), { recursive: true });
      fs.copyFileSync(path.join(ROOT, 'images', file), dest);
      return `url(../../images/${file})`;
    });
  });
  // inline styles → classes
  // !important: an inline style="" beat every stylesheet rule, so its class must too.
  for (const [name, decl] of inlineCss) {
    const r = postcss.parse(`.${name}{${decl}}`);
    r.walkDecls((d) => { d.important = true; });
    root.append(r);
  }
  inlineCss = [];
  pageCss[slug] = root;
}

// Every page-specific rule is scoped to its page style (.qpc-page-<style>), so a
// page's own rules keep exactly the cascade they had in the original file and
// can't leak into other pages. Pages pick their style from the Template menu.
let scoped = 0;
for (const [slug, root] of Object.entries(pageCss)) {
  root.walkRules((rule) => {
    if (rule.parent.type === 'atrule' && /keyframes/.test(rule.parent.name)) return;
    rule.selectors = rule.selectors.map((s) => { scoped++; return `.qpc-page-${slug} ${s}`; });
  });
  root.walkAtRules((a) => { if (a.nodes && !a.nodes.length) a.remove(); });
  const out = root.toString().replace(/\n{3,}/g, '\n\n').trim();
  if (out) fs.writeFileSync(path.join(OUT_CSS, `${slug}.css`), `/* Page style "${slug}", migrated from the static site (${Object.keys(PAGES).find((f) => PAGES[f] === slug)}).\n   Generated by wordpress/tools/migrate.mjs; put manual changes in ../fixes.css. */\n\n${out}\n`);
}

// Page styles (offered in the editor's Template menu) and which of them bring
// their own header / heading styles. Read by the theme.
const php = (v, ind = '') => Array.isArray(v)
  ? `array(${v.map((x) => php(x)).join(', ')})`
  : typeof v === 'object'
    ? `array(\n${Object.entries(v).map(([k, x]) => `${ind}\t${php(k)} => ${php(x, ind + '\t')},`).join('\n')}\n${ind})`
    : `'${String(v).replace(/\\/g, '\\\\').replace(/'/g, "\\'")}'`;
fs.writeFileSync(path.join(ROOT, 'wordpress/qpc-theme/inc/page-style-flags.php'),
  `<?php\n// Generated by wordpress/tools/migrate.mjs.\n// styles: page styles migrated from the old site (Template menu → "Page style: …").\n// has-own-*: styles that bring their own header/headings, so base.css defaults are off.\nreturn ${php({
    styles: Object.fromEntries(Object.entries(pageTitles).sort()),
    ...Object.fromEntries(Object.entries(familyPages).map(([k, v]) => [k, [...v].sort()])),
  })};\n`);
report.unshift(`${scoped} selector(s) scoped to their page style.\n`);
fs.writeFileSync(path.join(ROOT, 'wordpress/tools/migrate-report.txt'), report.join('\n') + '\n');
console.log(report.slice(0, 1).join('') + `Wrote ${Object.keys(PAGES).length} pages. Report: wordpress/tools/migrate-report.txt (${report.length - 1} notes)`);
