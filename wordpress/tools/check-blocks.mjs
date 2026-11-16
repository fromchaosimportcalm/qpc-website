// Opens every page in the block editor and lists blocks the editor considers invalid.
//   node wordpress/tools/check-blocks.mjs   (Playground must be running on :9400)
import puppeteer from 'puppeteer-core';
import { spawn } from 'node:child_process';

const CHROME = '/mnt/c/Program Files/Google/Chrome/Application/chrome.exe';
const BASE = 'http://127.0.0.1:9400';
const chrome = spawn(CHROME, ['--headless=new', '--remote-debugging-port=9333', '--user-data-dir=C:\\Temp\\qpc-chrome', 'about:blank'], { stdio: 'ignore' });
await new Promise((r) => setTimeout(r, 3000));
const browser = await puppeteer.connect({ browserURL: 'http://127.0.0.1:9333' });
const page = await browser.newPage();
await page.goto(BASE + '/', { waitUntil: 'networkidle2' }); // Playground auto-login
const ids = await page.evaluate(async (base) => (await (await fetch(base + '/wp-json/wp/v2/pages?per_page=50&_fields=id,slug')).json()), BASE);
let bad = 0;
for (const { id, slug } of ids) {
  await page.goto(`${BASE}/wp-admin/post.php?post=${id}&action=edit`, { waitUntil: 'networkidle2', timeout: 90000 });
  await page.waitForFunction(() => window.wp?.data?.select('core/block-editor')?.getBlocks().length > 0, { timeout: 60000 });
  const invalid = await page.evaluate(() => {
    const out = [];
    const walk = (bs) => bs.forEach((b) => { if (!b.isValid) out.push(`${b.name} ${b.attributes.className || ''}`); walk(b.innerBlocks); });
    walk(wp.data.select('core/block-editor').getBlocks());
    return out;
  });
  bad += invalid.length;
  console.log(`${slug.padEnd(20)} ${invalid.length ? 'INVALID: ' + invalid.join(' | ') : 'ok'}`);
}
console.log(bad ? `${bad} invalid block(s)` : 'All blocks valid.');
await browser.close(); chrome.kill();
