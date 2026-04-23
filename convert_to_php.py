#!/usr/bin/env python3
"""
QPC Site — HTML to PHP Converter
Converts all .html pages to .php, replacing:
  - inline nav HTML  -> <?php $activePage = 'X'; include 'nav.php'; ?>
  - inline footer HTML -> <?php include 'footer.php'; ?>
  - .html hrefs -> .php hrefs (internal links only)
  - nav JS block -> single include reference
  - <!DOCTYPE html> -> adds <?php at top if needed

Run from the directory containing your HTML files.
Output: same filename with .php extension, original .html untouched.
"""

import re, os
from pathlib import Path

# Map filename stem -> activePage value
PAGE_MAP = {
    'index':        'home',
    'ministries':   'ministries',
    'history':      'history',
    'team':         'team',
    'sermons':      'sermons',
    'calendar':     'calendar',
    'hymns':        'hymns',
    'visit':        'visit',
    'm3':           'ministries',
    'playgroup':    'ministries',
    'mens-brekkie': 'ministries',
    'prayer':       'ministries',
    'bible-study':  'ministries',
    'ladies-craft': 'ministries',
}

# The nav block to replace — matches from nav-overlay div through </nav>
NAV_RE = re.compile(
    r'<div class="nav-overlay"[^>]*>.*?</nav>',
    re.DOTALL
)

# The footer block to replace
FOOTER_RE = re.compile(
    r'<footer>.*?</footer>',
    re.DOTALL
)

# Nav JS block — the toggle/hamburger JS at the bottom of each page
NAV_JS_RE = re.compile(
    r'(?://\s*[-─]+\s*NAV\s*[-─]+.*?\n)?'
    r'\s*const toggle\s*=.*?'
    r'links\.querySelectorAll\(\'a\'\)\.forEach\(a=>a\.addEventListener\(\'click\',closeMenu\)\);',
    re.DOTALL
)

def convert_file(html_path, out_dir):
    stem     = html_path.stem
    page_key = PAGE_MAP.get(stem, 'home')

    content  = html_path.read_text(encoding='utf-8')

    # 1. Replace nav block
    nav_include = (
        f'<?php $activePage = \'{page_key}\'; include \'nav.php\'; ?>'
    )
    content = NAV_RE.sub(nav_include, content, count=1)

    # 2. Replace footer block
    footer_include = "<?php include 'footer.php'; ?>"
    content = FOOTER_RE.sub(footer_include, content, count=1)

    # 3. Replace nav JS with include (nav.php handles its own JS via nav-js.php)
    #    We keep the script tag but replace just the nav functions
    content = NAV_JS_RE.sub("<?php include 'nav-js.php'; ?>", content, count=1)

    # 4. Rewrite internal .html hrefs to .php
    #    Only rewrite known internal pages, not external URLs
    internal_pages = list(PAGE_MAP.keys()) + [
        'qpc-concept', 'sermons', 'calendar', 'hymns',
    ]
    def rewrite_href(m):
        href = m.group(1)
        for page in internal_pages:
            if href == f'{page}.html':
                return f'href="{page}.php"'
        return m.group(0)
    content = re.sub(r'href="([^"]+\.html)"', rewrite_href, content)

    # 5. Rewrite onclick window.location .html -> .php
    content = re.sub(
        r"window\.location='([^']+)\.html'",
        lambda m: f"window.location='{m.group(1)}.php'",
        content
    )

    # 6. Change DOCTYPE line to include opening PHP tag for future use
    #    (optional — uncomment if you want PHP at the very top)
    # content = content.replace('<!DOCTYPE html>', '<?php ?>\n<!DOCTYPE html>')

    out_path = out_dir / f'{stem}.php'
    out_path.write_text(content, encoding='utf-8')
    return out_path

def main():
    html_files = sorted(Path('.').glob('*.html'))
    # Exclude any concept/prototype files
    html_files = [f for f in html_files if 'concept' not in f.stem]

    if not html_files:
        print('No .html files found. Run from your site root.')
        return

    out_dir = Path('.')
    print(f'Converting {len(html_files)} files...\n')

    for f in html_files:
        out = convert_file(f, out_dir)
        print(f'  {f.name:30s} -> {out.name}')

    print(f'\nDone. nav.php and footer.php must be in the same directory.')
    print('Also copy nav-js.php alongside the other includes.')

if __name__ == '__main__':
    main()
