// Compare old static pages with their WordPress versions, pixel by pixel.
//   node wordpress/tools/visual-diff.mjs [outDir] [slug ...]
// Needs: Playground on :9400 and the static site on :9500 (python3 -m http.server 9500).
// Writes <slug>-<width>-{old,new,diff}.png and prints where the pages differ.
import puppeteer from 'puppeteer-core';
import { spawn } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import { PNG } from 'pngjs';
import pixelmatch from 'pixelmatch';

const CHROME = '/mnt/c/Program Files/Google/Chrome/Application/chrome.exe';
const OUT = process.argv[2] || 'visual-diff';
const ONLY = process.argv.slice(3);
const PAGES = {
  'index.html': '/', 'ministries.html': '/church-life/', 'history.html': '/our-history/', 'team.html': '/our-team/',
  'visit.html': '/plan-your-visit/', 'bible-study.html': '/bible-study/', 'mens-brekkie.html': '/mens-brekkie/',
  'playgroup.html': '/playgroup/', 'prayer.html': '/prayer/', 'ladies-craft.html': '/ladies-craft/',
  'christian-singles.html': '/christian-singles/', 'm3.html': '/m3/', 'christmas-gala.html': '/christmas-gala/',
};
const WIDTHS = [1400, 390];
// Hide the logged-in admin bar so both versions line up.
const NO_ADMIN = '#wpadminbar{display:none!important}html{margin-top:0!important}.admin-bar{--nav-offset:0px!important}';
// Freeze things that legitimately differ run to run.
const FREEZE = '*,*::before,*::after{animation:none!important;transition:none!important;caret-color:transparent!important}iframe{visibility:hidden!important}';

fs.mkdirSync(OUT, { recursive: true });
const chrome = spawn(CHROME, ['--headless=new', '--hide-scrollbars', '--remote-debugging-port=9334', '--user-data-dir=C:\\Temp\\qpc-chrome-diff', 'about:blank'], { stdio: 'ignore' });
await new Promise((r) => setTimeout(r, 3000));
const browser = await puppeteer.connect({ browserURL: 'http://127.0.0.1:9334' });
const page = await browser.newPage();
await page.goto('http://127.0.0.1:9400/', { waitUntil: 'networkidle2' }); // Playground auto-login

async function shot(url, width, css) {
  await page.setViewport({ width, height: 900 });
  await page.goto(url, { waitUntil: 'networkidle2', timeout: 90000 });
  await page.addStyleTag({ content: css });
  // Trigger any scroll-reveal effects, then return to the top.
  await page.evaluate(async () => { for (let y = 0; y < document.body.scrollHeight; y += 600) { window.scrollTo(0, y); await new Promise((r) => setTimeout(r, 40)); } window.scrollTo(0, 0); });
  // Wait for lazy-loaded images to finish.
  await page.evaluate(() => Promise.all([...document.images].map((i) => (i.loading = 'eager', i.complete ? 0 : new Promise((r) => { i.onload = i.onerror = r; setTimeout(r, 5000); })))));
  await new Promise((r) => setTimeout(r, 500));
  return PNG.sync.read(Buffer.from(await page.screenshot({ fullPage: true })));
}

function crop(png, w, h) {
  const out = new PNG({ width: w, height: h });
  PNG.bitblt(png, out, 0, 0, w, h, 0, 0);
  return out;
}

for (const [file, wp] of Object.entries(PAGES)) {
  const slug = file.replace('.html', '');
  if (ONLY.length && !ONLY.includes(slug)) continue;
  for (const width of WIDTHS) {
    const a = await shot(`http://127.0.0.1:9500/${file}`, width, FREEZE);
    const b = await shot(`http://127.0.0.1:9400${wp}`, width, FREEZE + NO_ADMIN);
    const w = Math.min(a.width, b.width), h = Math.min(a.height, b.height);
    const A = crop(a, w, h), Bc = crop(b, w, h), D = new PNG({ width: w, height: h });
    const n = pixelmatch(A.data, Bc.data, D.data, w, h, { threshold: 0.15 });
    // Rows (in 20px bands) where the pages differ noticeably.
    const bands = [];
    for (let y = 0; y < h; y += 20) {
      let c = 0;
      for (let yy = y; yy < Math.min(y + 20, h); yy++) for (let x = 0; x < w; x++) { const i = (yy * w + x) * 4; if (D.data[i] === 255 && D.data[i + 1] === 0) c++; }
      if (c > w * 20 * 0.01) bands.push(y);
    }
    const ranges = [];
    for (const y of bands) { const last = ranges[ranges.length - 1]; if (last && y - last[1] <= 40) last[1] = y + 20; else ranges.push([y, y + 20]); }
    for (const [k, img] of [['old', a], ['new', b], ['diff', D]]) fs.writeFileSync(path.join(OUT, `${slug}-${width}-${k}.png`), PNG.sync.write(img));
    const pct = ((n / (w * h)) * 100).toFixed(1);
    console.log(`${slug.padEnd(18)} ${String(width).padStart(4)}px  height old ${a.height} / new ${b.height}  diff ${pct}%  ${ranges.length ? 'at y=' + ranges.slice(0, 6).map((r) => r.join('-')).join(', ') + (ranges.length > 6 ? ' …' : '') : ''}`);
  }
}
await browser.close();
chrome.kill();
