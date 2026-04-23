<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Calendar — Queanbeyan Presbyterian Church</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Mono:wght@300;400&display=swap" rel="stylesheet">
<style>
:root {
  --cream: #F5F0E8; --cream-dark: #EDE7D6; --ink: #1C1A16;
  --forest: #2D4A35; --forest-light: #3D6147;
  --gold: #B8862A; --gold-light: #D4A445;
  --warm-grey: #8A8278; --border: rgba(44,74,53,0.18);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{background:var(--cream);color:var(--ink);font-family:'Crimson Pro',Georgia,serif;font-size:18px;line-height:1.7;overflow-x:hidden;}
body::before{content:'';position:fixed;inset:0;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");pointer-events:none;z-index:1000;}

/* NAV */
nav{position:fixed;top:0;left:0;right:0;z-index:100;display:flex;align-items:center;justify-content:space-between;padding:1.25rem 3rem;background:rgba(245,240,232,0.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);}
.nav-logo{display:flex;align-items:center;text-decoration:none;flex-shrink:0;}
.nav-logo-img{height:48px;width:auto;display:block;}
.nav-links{display:flex;gap:2.5rem;list-style:none;align-items:center;}
.nav-links a{font-family:'DM Mono',monospace;font-size:0.7rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--ink);text-decoration:none;opacity:0.7;transition:opacity 0.2s,color 0.2s;}
.nav-links a:hover,.nav-links a.active{opacity:1;color:var(--forest);}
.nav-cta{color:var(--cream)!important;background:var(--forest);padding:0.6rem 1.4rem;border-radius:2px;opacity:1!important;}
.nav-cta:hover{background:var(--forest-light)!important;}
.nav-hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:4px;background:none;border:none;z-index:200;}
.nav-hamburger span{display:block;width:24px;height:2px;background:var(--forest);border-radius:2px;transition:transform 0.3s,opacity 0.3s;transform-origin:center;}
.nav-hamburger.open span:nth-child(1){transform:translateY(7px) rotate(45deg);}
.nav-hamburger.open span:nth-child(2){opacity:0;}
.nav-hamburger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg);}
.nav-overlay{position:fixed;inset:0;background:rgba(28,26,22,0.4);z-index:140;opacity:0;pointer-events:none;transition:opacity 0.3s;}
.nav-overlay.open{opacity:1;pointer-events:all;}

/* PAGE HEADER */
.page-header{padding:9rem 5rem 5rem;background:var(--forest);position:relative;overflow:hidden;}
.page-header::after{content:'';position:absolute;bottom:-1px;left:0;right:0;height:60px;background:var(--cream);clip-path:ellipse(55% 100% at 50% 100%);}
.page-header-inner{max-width:1300px;margin:0 auto;position:relative;z-index:1;display:grid;grid-template-columns:1fr 1fr;gap:5rem;align-items:end;}
.page-eyebrow{font-family:'DM Mono',monospace;font-size:0.68rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--gold-light);margin-bottom:1rem;display:flex;align-items:center;gap:0.8rem;}
.page-eyebrow::before{content:'';display:block;width:2rem;height:1px;background:var(--gold);}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(2.5rem,5vw,4rem);font-weight:600;color:var(--cream);line-height:1.1;margin-bottom:0.8rem;}
.page-lead{font-size:1.1rem;color:rgba(245,240,232,0.7);line-height:1.75;max-width:44ch;}
.header-subscribe{align-self:end;}
.subscribe-label{font-family:'DM Mono',monospace;font-size:0.62rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--gold-light);margin-bottom:0.8rem;display:block;}
.subscribe-links{display:flex;flex-direction:column;gap:0.6rem;}
.subscribe-link{display:inline-flex;align-items:center;gap:0.7rem;font-family:'DM Mono',monospace;font-size:0.65rem;letter-spacing:0.08em;text-transform:uppercase;color:rgba(245,240,232,0.7);text-decoration:none;padding:0.7rem 1.2rem;border:1px solid rgba(245,240,232,0.15);border-radius:2px;background:rgba(245,240,232,0.06);transition:background 0.2s,color 0.2s,border-color 0.2s;}
.subscribe-link:hover{background:rgba(245,240,232,0.12);color:var(--cream);border-color:rgba(245,240,232,0.3);}

/* MAIN LAYOUT */
.calendar-wrap{max-width:1300px;margin:0 auto;padding:4rem 5rem 6rem;display:grid;grid-template-columns:1fr 320px;gap:4rem;align-items:start;}

/* MONTH VIEW */
.month-nav{display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;}
.month-title{font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:600;color:var(--forest);}
.month-btn{font-family:'DM Mono',monospace;font-size:0.65rem;letter-spacing:0.1em;text-transform:uppercase;background:none;border:1px solid var(--border);color:var(--forest);padding:0.6rem 1.2rem;border-radius:2px;cursor:pointer;transition:background 0.2s,color 0.2s;}
.month-btn:hover{background:var(--forest);color:var(--cream);}

.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:0;border:1px solid var(--border);border-radius:2px;overflow:hidden;}
.cal-day-header{font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:0.12em;text-transform:uppercase;text-align:center;padding:0.7rem 0.3rem;background:var(--cream-dark);color:var(--warm-grey);border-bottom:1px solid var(--border);}
.cal-cell{min-height:90px;padding:0.5rem;border-right:1px solid var(--border);border-bottom:1px solid var(--border);background:var(--cream);position:relative;vertical-align:top;}
.cal-cell:nth-child(7n){border-right:none;}
.cal-cell.other-month{background:var(--cream-dark);}
.cal-cell.other-month .cal-date{opacity:0.3;}
.cal-cell.today{background:#f0ede5;}
.cal-cell.today .cal-date{background:var(--forest);color:var(--cream);border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;}
.cal-date{font-family:'DM Mono',monospace;font-size:0.7rem;font-weight:400;color:var(--ink);margin-bottom:0.3rem;line-height:1;width:24px;height:24px;display:flex;align-items:center;justify-content:center;}
.cal-event{font-family:'DM Mono',monospace;font-size:0.55rem;letter-spacing:0.04em;padding:0.2rem 0.4rem;border-radius:2px;margin-bottom:0.2rem;cursor:pointer;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;transition:opacity 0.15s;}
.cal-event:hover{opacity:0.8;}
.cal-event.type-regular{background:rgba(45,74,53,0.12);color:var(--forest);}
.cal-event.type-special{background:rgba(184,134,42,0.15);color:#8B6010;}
.cal-event.type-recurring{background:rgba(45,74,53,0.07);color:var(--forest);}
.cal-more{font-family:'DM Mono',monospace;font-size:0.55rem;color:var(--warm-grey);padding:0.1rem 0.4rem;cursor:pointer;}
.cal-more:hover{color:var(--forest);}

/* LOADING / ERROR STATES */
.cal-loading{padding:4rem;text-align:center;}
.cal-loading-spinner{width:32px;height:32px;border:2px solid var(--border);border-top-color:var(--forest);border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 1rem;}
@keyframes spin{to{transform:rotate(360deg);}}
.cal-loading p{font-family:'DM Mono',monospace;font-size:0.65rem;letter-spacing:0.1em;text-transform:uppercase;color:var(--warm-grey);}
.cal-error{padding:2rem;background:var(--cream-dark);border:1px solid var(--border);border-radius:2px;text-align:center;}
.cal-error p{font-size:0.95rem;color:var(--warm-grey);}
.cal-error a{color:var(--forest);}

/* SIDEBAR — UPCOMING EVENTS */
.sidebar{}
.sidebar-section{margin-bottom:2.5rem;}
.sidebar-label{font-family:'DM Mono',monospace;font-size:0.62rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--gold);margin-bottom:1.2rem;display:flex;align-items:center;gap:0.8rem;}
.sidebar-label::before{content:'';display:block;width:1.2rem;height:1px;background:var(--gold);}
.event-list{display:flex;flex-direction:column;gap:0;}
.event-item{padding:1rem 0;border-bottom:1px solid var(--border);cursor:pointer;transition:background 0.15s;}
.event-item:last-child{border-bottom:none;}
.event-item:hover .event-item-title{color:var(--forest-light);}
.event-item-date{font-family:'DM Mono',monospace;font-size:0.58rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--gold);margin-bottom:0.2rem;}
.event-item-title{font-family:'Playfair Display',serif;font-size:1rem;font-weight:600;color:var(--forest);line-height:1.3;transition:color 0.15s;}
.event-item-time{font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:0.08em;text-transform:uppercase;color:var(--warm-grey);margin-top:0.15rem;}
.event-item-location{font-size:0.85rem;color:var(--warm-grey);margin-top:0.2rem;}

/* EVENT DETAIL PANEL */
.event-detail{background:var(--cream-dark);border:1px solid var(--border);border-radius:2px;padding:1.5rem;display:none;}
.event-detail.visible{display:block;}
.event-detail-label{font-family:'DM Mono',monospace;font-size:0.58rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--gold);margin-bottom:0.5rem;}
.event-detail-title{font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:600;color:var(--forest);margin-bottom:0.8rem;line-height:1.3;}
.event-detail-meta{display:flex;flex-direction:column;gap:0.3rem;margin-bottom:1rem;}
.event-detail-meta span{font-family:'DM Mono',monospace;font-size:0.62rem;letter-spacing:0.08em;text-transform:uppercase;color:var(--warm-grey);}
.event-detail-desc{font-size:0.92rem;color:var(--ink);line-height:1.65;}
.event-detail-close{font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:0.1em;text-transform:uppercase;color:var(--warm-grey);background:none;border:none;cursor:pointer;margin-top:1rem;padding:0;}
.event-detail-close:hover{color:var(--forest);}

/* ADD TO CALENDAR */
.add-to-cal{display:flex;gap:0.5rem;margin-top:1rem;flex-wrap:wrap;}
.add-to-cal a{font-family:'DM Mono',monospace;font-size:0.58rem;letter-spacing:0.08em;text-transform:uppercase;color:var(--forest);border:1px solid var(--border);padding:0.4rem 0.8rem;border-radius:2px;text-decoration:none;transition:background 0.15s,color 0.15s;}
.add-to-cal a:hover{background:var(--forest);color:var(--cream);}

  /* SPOTIFY LINK */
  .spotify-strip{background:var(--ink);padding:3rem 5rem;}
  .spotify-strip-inner{max-width:1300px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:2rem;}
  .spotify-strip p{font-size:1rem;color:rgba(245,240,232,0.6);max-width:48ch;}
  .spotify-strip p strong{color:var(--cream);}
  .btn-spotify{display:inline-flex;align-items:center;gap:0.7rem;font-family:'DM Mono',monospace;font-size:0.7rem;letter-spacing:0.1em;text-transform:uppercase;background:#1DB954;color:#000;padding:0.9rem 1.8rem;border-radius:2px;text-decoration:none;transition:opacity 0.2s;}
  .btn-spotify:hover{opacity:0.85;}
  .spotify-icon{width:18px;height:18px;fill:currentColor;}

  /* FOOTER */
  footer{background:var(--ink);color:rgba(245,240,232,0.55);padding:5rem;border-top:1px solid rgba(255,255,255,0.04);}
  .footer-inner{max-width:1300px;margin:0 auto;display:grid;grid-template-columns:2fr 1fr 1fr;gap:5rem;}
  .footer-logo-img{height:80px;width:auto;display:block;filter:brightness(0) invert(1) opacity(0.6);}
  .footer-brand .sub{font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:0.18em;text-transform:uppercase;color:var(--gold);margin:1rem 0 0.8rem;display:block;}
  .footer-brand p{font-size:0.92rem;line-height:1.7;max-width:36ch;}
  .footer-links h4{font-family:'DM Mono',monospace;font-size:0.65rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--gold);margin-bottom:1.2rem;}
  .footer-links ul{list-style:none;}
  .footer-links li{margin-bottom:0.7rem;}
  .footer-links a{font-size:0.9rem;color:rgba(245,240,232,0.5);text-decoration:none;transition:color 0.2s;}
  .footer-links a:hover{color:var(--cream);}
  .footer-bottom{max-width:1300px;margin:3rem auto 0;padding-top:2rem;border-top:1px solid rgba(245,240,232,0.08);display:flex;justify-content:space-between;align-items:center;}
  .footer-bottom p{font-family:'DM Mono',monospace;font-size:0.62rem;letter-spacing:0.1em;text-transform:uppercase;color:rgba(245,240,232,0.25);}
  .footer-social{display:flex;gap:1rem;}
  .social-link{width:2rem;height:2rem;border:1px solid rgba(245,240,232,0.1);border-radius:2px;display:flex;align-items:center;justify-content:center;font-size:0.8rem;color:rgba(245,240,232,0.4);text-decoration:none;transition:border-color 0.2s,color 0.2s;}
  .social-link:hover{border-color:var(--gold);color:var(--gold);}
  .social-link { width: 2rem; height: 2rem; border: 1px solid rgba(245, 240, 232, 0.1); border-radius: 2px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: rgba(245, 240, 232, 0.4); text-decoration: none; transition: border-color 0.2s, color 0.2s; }
  .social-link:hover { border-color: var(--gold); color: var(--gold); }
  .nav-overlay { position: fixed; inset: 0; background: rgba(28,26,22,0.4); z-index: 140; opacity: 0; pointer-events: none; transition: opacity 0.3s; }
  .nav-overlay.open { opacity: 1; pointer-events: all; }

/* RESPONSIVE */
@media(max-width:1024px){
  nav{padding:1.2rem 2rem;}
  .nav-hamburger{display:flex;}
  .nav-links{position:fixed;top:0;right:-100%;width:min(320px,85vw);height:100vh;background:var(--cream);border-left:1px solid var(--border);flex-direction:column;align-items:flex-start;justify-content:center;padding:3rem 2.5rem;gap:0;transition:right 0.35s cubic-bezier(0.4,0,0.2,1);z-index:150;box-shadow:-8px 0 40px rgba(28,26,22,0.12);}
  .nav-links.open{right:0;}
  .nav-links li{width:100%;border-bottom:1px solid var(--border);}
  .nav-links li:last-child{border-bottom:none;margin-top:1.5rem;}
  .nav-links a{display:block;padding:1rem 0;font-size:0.8rem;opacity:1;color:var(--ink);}
  .nav-links .nav-cta{display:inline-block;width:auto;padding:0.9rem 1.8rem;margin-top:0.5rem;}
  .page-header{padding:8rem 2rem 4rem;}
  .page-header-inner{grid-template-columns:1fr;gap:2rem;}
  .calendar-wrap{grid-template-columns:1fr;padding:3rem 2rem 5rem;}
  footer{padding:4rem 2rem;}
  .footer-inner{grid-template-columns:1fr;gap:3rem;}
  .footer-bottom{flex-direction:column;gap:1rem;text-align:center;}
}
@media(max-width:640px){
  nav{padding:1rem 1.25rem;}
  .nav-logo-img{height:36px;}
  .page-header{padding:7rem 1.25rem 3rem;}
  .calendar-wrap{padding:2rem 1.25rem 4rem;}
  .cal-cell{min-height:60px;padding:0.3rem;}
  .cal-event{display:none;}
  .cal-cell.has-event::after{content:'•';color:var(--gold);font-size:0.7rem;position:absolute;bottom:4px;left:50%;transform:translateX(-50%);}
  footer{padding:3rem 1.25rem;}
}
</style>
</head>
<body>

<!-- NAV -->
<?php $activePage = 'calendar'; include 'nav.php'; ?>

<!-- PAGE HEADER -->
<header class="page-header">
  <div class="page-header-inner">
    <div>
      <div class="page-eyebrow">What's On · Queanbeyan Presbyterian Church</div>
      <h1 class="page-title">Calendar</h1>
      <p class="page-lead">Services, ministries, and events - updated live from the church calendar. Subscribe to get events automatically in your calendar app.</p>
    </div>
    <div class="header-subscribe">
      <span class="subscribe-label">Subscribe to this calendar</span>
      <div class="subscribe-links">
        <a href="webcal://p69-caldav.icloud.com/published/2/MTM0OTU4Njg0ODEzNDk1OAiKAMI78sHP0UEgYBo74ceckrXowy2-fiJvsH_5i6YL8bt9A16z_rVJrL1OG3ip5Zc7DI0b1b_ukcWJ5iMtMsg" class="subscribe-link">
          📅 Apple Calendar
        </a>
        <a href="https://calendar.google.com/calendar/r?cid=webcal%3A%2F%2Fp69-caldav.icloud.com%2Fpublished%2F2%2FMTMwOTU4Njg0ODEzNDk1OAiKAMI78sHP0UEgYBo74ceckrXowy2-fiJvsH_5i6YL8bt9A16z_rVJrL1OG3ip5Zc7DI0b1b_ukcWJ5iMtMsg" class="subscribe-link" target="_blank" rel="noopener">
          📅 Google Calendar
        </a>
        <a href="webcal://p69-caldav.icloud.com/published/2/MTM0OTU4Njg0ODEzNDk1OAiKAMI78sHP0UEgYBo74ceckrXowy2-fiJvsH_5i6YL8bt9A16z_rVJrL1OG3ip5Zc7DI0b1b_ukcWJ5iMtMsg" class="subscribe-link">
          📅 Other Calendar App
        </a>
      </div>
    </div>
  </div>
</header>

<!-- CALENDAR -->
<div class="calendar-wrap">

  <!-- MONTH GRID -->
  <div>
    <div class="month-nav">
      <button class="month-btn" id="prevMonth">← Prev</button>
      <h2 class="month-title" id="monthTitle">Loading…</h2>
      <button class="month-btn" id="nextMonth">Next →</button>
    </div>
    <div id="calContainer">
      <div class="cal-loading">
        <div class="cal-loading-spinner"></div>
        <p>Loading calendar…</p>
      </div>
    </div>
  </div>

  <!-- SIDEBAR -->
  <aside class="sidebar">

    <!-- EVENT DETAIL (shown when event clicked) -->
    <div class="event-detail" id="eventDetail">
      <div class="event-detail-label">Event</div>
      <div class="event-detail-title" id="detailTitle"></div>
      <div class="event-detail-meta" id="detailMeta"></div>
      <div class="event-detail-desc" id="detailDesc"></div>
      <div class="add-to-cal" id="detailAddCal"></div>
      <button class="event-detail-close" id="detailClose">✕ Close</button>
    </div>

    <!-- UPCOMING -->
    <div class="sidebar-section">
      <div class="sidebar-label">Coming Up</div>
      <div class="event-list" id="upcomingList">
        <div class="cal-loading" style="padding:2rem 0;">
          <div class="cal-loading-spinner" style="width:20px;height:20px;border-width:1.5px;"></div>
        </div>
      </div>
    </div>

  </aside>
</div>

<!-- SPOTIFY STRIP -->
<section class="spotify-strip">
  <div class="spotify-strip-inner">
    <p><strong>Subscribe to the podcast</strong> and get new sermons automatically every week — on Spotify, Apple Podcasts, or any podcast app.</p>
    <a href="https://open.spotify.com/show/5XJPM6ftmGcBDK5yanFw9n" target="_blank" rel="noopener" class="btn-spotify">
      <svg class="spotify-icon" viewBox="0 0 24 24"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>
      Listen on Spotify
    </a>
  </div>
</section>

<!-- FOOTER -->
<?php include 'footer.php'; ?>

<script>
// ─────────────────────────────────────────────────────────────────────────────
// CONFIGURATION
// Replace PROXY_URL with your Cloudflare Worker URL once deployed.
// Until then the page falls back to showing the subscribe links only.
// ─────────────────────────────────────────────────────────────────────────────
const PROXY_URL = 'https://qpc-calendar-proxy.YOUR-SUBDOMAIN.workers.dev';
// e.g. 'https://qpc-calendar-proxy.clinton.workers.dev'

const WEBCAL_URL = 'webcal://p69-caldav.icloud.com/published/2/MTM0OTU4Njg0ODEzNDk1OAiKAMI78sHP0UEgYBo74ceckrXowy2-fiJvsH_5i6YL8bt9A16z_rVJrL1OG3ip5Zc7DI0b1b_ukcWJ5iMtMsg';

// ─────────────────────────────────────────────────────────────────────────────
// ICS PARSER
// Handles VEVENT blocks, RRULE (recurring), multi-day events, timezones
// ─────────────────────────────────────────────────────────────────────────────
function parseICS(icsText) {
  const events = [];
  // Unfold lines (ICS spec: lines can be wrapped with CRLF + space)
  const text = icsText.replace(/\r\n[ \t]/g, '').replace(/\n[ \t]/g, '');
  const lines = text.split(/\r?\n/);

  let inEvent = false;
  let current = {};

  for (const line of lines) {
    if (line === 'BEGIN:VEVENT') { inEvent = true; current = {}; continue; }
    if (line === 'END:VEVENT') {
      if (inEvent && current.DTSTART) events.push({ ...current });
      inEvent = false; continue;
    }
    if (!inEvent) continue;

    const colonIdx = line.indexOf(':');
    if (colonIdx === -1) continue;
    const key = line.substring(0, colonIdx).split(';')[0].trim();
    const params = line.substring(0, colonIdx);
    const val = line.substring(colonIdx + 1).trim();

    switch (key) {
      case 'SUMMARY':    current.title = unescapeICS(val); break;
      case 'DESCRIPTION': current.description = unescapeICS(val); break;
      case 'LOCATION':   current.location = unescapeICS(val); break;
      case 'UID':        current.uid = val; break;
      case 'DTSTART':    current.DTSTART = val; current.start = parseICSDate(val, params); current.allDay = !val.includes('T'); break;
      case 'DTEND':      current.DTEND = val; current.end = parseICSDate(val, params); break;
      case 'RRULE':      current.rrule = val; break;
      case 'EXDATE':     current.exdates = (current.exdates || []).concat(val.split(',').map(d => parseICSDate(d, ''))); break;
    }
  }

  // Expand recurring events for the next 12 months
  const expanded = [];
  const now = new Date(); now.setHours(0,0,0,0);
  const horizon = new Date(now); horizon.setMonth(horizon.getMonth() + 12);

  for (const ev of events) {
    if (!ev.rrule) {
      if (ev.start >= now || (ev.end && ev.end >= now)) expanded.push(ev);
      continue;
    }
    const occurrences = expandRRule(ev, now, horizon);
    expanded.push(...occurrences);
  }

  expanded.sort((a, b) => a.start - b.start);
  return expanded;
}

function unescapeICS(s) {
  return s.replace(/\\n/g, '\n').replace(/\\,/g, ',').replace(/\\;/g, ';').replace(/\\\\/g, '\\');
}

function parseICSDate(val, params) {
  // Strip VALUE=DATE and TZID params from key if passed through
  val = val.trim();
  if (val.length === 8) {
    // All-day: YYYYMMDD
    return new Date(
      parseInt(val.slice(0,4)),
      parseInt(val.slice(4,6)) - 1,
      parseInt(val.slice(6,8))
    );
  }
  // DateTime: YYYYMMDDTHHMMSS[Z]
  const y = parseInt(val.slice(0,4));
  const mo = parseInt(val.slice(4,6)) - 1;
  const d = parseInt(val.slice(6,8));
  const h = parseInt(val.slice(9,11));
  const mi = parseInt(val.slice(11,13));
  const s = parseInt(val.slice(13,15));
  if (val.endsWith('Z')) return new Date(Date.UTC(y, mo, d, h, mi, s));
  return new Date(y, mo, d, h, mi, s);
}

function expandRRule(ev, from, to) {
  const parts = {};
  ev.rrule.split(';').forEach(p => {
    const [k, v] = p.split('=');
    parts[k] = v;
  });

  const freq = parts.FREQ;
  const count = parts.COUNT ? parseInt(parts.COUNT) : 999;
  const until = parts.UNTIL ? parseICSDate(parts.UNTIL, '') : to;
  const interval = parseInt(parts.INTERVAL || '1');
  const byDay = parts.BYDAY ? parts.BYDAY.split(',') : null;

  const duration = ev.end ? ev.end - ev.start : 0;
  const results = [];
  let current = new Date(ev.start);
  let n = 0;

  while (current <= until && current <= to && n < count && results.length < 200) {
    if (current >= from) {
      // Check EXDATE
      const isExcluded = (ev.exdates || []).some(ex =>
        ex.getFullYear() === current.getFullYear() &&
        ex.getMonth() === current.getMonth() &&
        ex.getDate() === current.getDate()
      );
      if (!isExcluded) {
        results.push({
          ...ev,
          start: new Date(current),
          end: ev.end ? new Date(current.getTime() + duration) : null,
          rrule: null // don't re-expand
        });
      }
    }

    // Advance
    const next = new Date(current);
    if (freq === 'DAILY')   next.setDate(next.getDate() + interval);
    else if (freq === 'WEEKLY')  next.setDate(next.getDate() + 7 * interval);
    else if (freq === 'MONTHLY') next.setMonth(next.getMonth() + interval);
    else if (freq === 'YEARLY')  next.setFullYear(next.getFullYear() + interval);
    else break;

    if (next.getTime() === current.getTime()) break; // safety
    current = next;
    n++;
  }

  return results;
}

// ─────────────────────────────────────────────────────────────────────────────
// CALENDAR STATE
// ─────────────────────────────────────────────────────────────────────────────
let allEvents = [];
let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

// ─────────────────────────────────────────────────────────────────────────────
// FORMATTING HELPERS
// ─────────────────────────────────────────────────────────────────────────────
const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const DAYS   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

function fmtDate(d, opts = {}) {
  if (!d) return '';
  const defaults = { weekday:'short', day:'numeric', month:'short', year:'numeric' };
  return d.toLocaleDateString('en-AU', { ...defaults, ...opts });
}

function fmtTime(d) {
  if (!d) return '';
  return d.toLocaleTimeString('en-AU', { hour:'numeric', minute:'2-digit', hour12:true });
}

function eventCategory(ev) {
  const t = (ev.title || '').toLowerCase();
  if (t.includes('sunday') || t.includes('service') || t.includes('worship')) return 'type-recurring';
  if (t.includes('advent') || t.includes('christmas') || t.includes('easter') || t.includes('special')) return 'type-special';
  return 'type-regular';
}

// ─────────────────────────────────────────────────────────────────────────────
// RENDER CALENDAR GRID
// ─────────────────────────────────────────────────────────────────────────────
function renderMonth(month, year) {
  document.getElementById('monthTitle').textContent = `${MONTHS[month]} ${year}`;

  const container = document.getElementById('calContainer');
  const grid = document.createElement('div');
  grid.className = 'cal-grid';

  // Day headers
  DAYS.forEach(d => {
    const h = document.createElement('div');
    h.className = 'cal-day-header';
    h.textContent = d;
    grid.appendChild(h);
  });

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const daysInPrev = new Date(year, month, 0).getDate();
  const today = new Date(); today.setHours(0,0,0,0);

  // Events this month + overflow
  const monthStart = new Date(year, month, 1);
  const monthEnd   = new Date(year, month + 1, 1);

  const monthEvents = allEvents.filter(ev => {
    const end = ev.end || new Date(ev.start.getTime() + 86400000);
    return ev.start < monthEnd && end >= monthStart;
  });

  // Group by date string
  const byDate = {};
  monthEvents.forEach(ev => {
    const d = ev.start;
    const key = `${d.getFullYear()}-${d.getMonth()}-${d.getDate()}`;
    (byDate[key] = byDate[key] || []).push(ev);
  });

  let cellCount = 0;

  // Leading cells from previous month
  for (let i = firstDay - 1; i >= 0; i--) {
    const cell = makeCell(daysInPrev - i, false, false, []);
    cell.classList.add('other-month');
    grid.appendChild(cell);
    cellCount++;
  }

  // Current month
  for (let d = 1; d <= daysInMonth; d++) {
    const cellDate = new Date(year, month, d);
    const isToday = cellDate.getTime() === today.getTime();
    const key = `${year}-${month}-${d}`;
    const events = byDate[key] || [];
    const cell = makeCell(d, true, isToday, events);
    if (events.length > 0) cell.classList.add('has-event');
    grid.appendChild(cell);
    cellCount++;
  }

  // Trailing cells
  const remaining = 42 - cellCount;
  for (let d = 1; d <= remaining; d++) {
    const cell = makeCell(d, false, false, []);
    cell.classList.add('other-month');
    grid.appendChild(cell);
  }

  container.innerHTML = '';
  container.appendChild(grid);
}

function makeCell(day, currentMonth, isToday, events) {
  const cell = document.createElement('div');
  cell.className = 'cal-cell' + (isToday ? ' today' : '');

  const dateEl = document.createElement('div');
  dateEl.className = 'cal-date';
  dateEl.textContent = day;
  cell.appendChild(dateEl);

  const MAX_VISIBLE = 2;
  events.slice(0, MAX_VISIBLE).forEach(ev => {
    const el = document.createElement('div');
    el.className = `cal-event ${eventCategory(ev)}`;
    el.textContent = ev.allDay ? ev.title : `${fmtTime(ev.start)} ${ev.title}`;
    el.title = ev.title;
    el.addEventListener('click', () => showDetail(ev));
    cell.appendChild(el);
  });

  if (events.length > MAX_VISIBLE) {
    const more = document.createElement('div');
    more.className = 'cal-more';
    more.textContent = `+${events.length - MAX_VISIBLE} more`;
    more.addEventListener('click', () => showDetail(events[MAX_VISIBLE]));
    cell.appendChild(more);
  }

  return cell;
}

// ─────────────────────────────────────────────────────────────────────────────
// UPCOMING EVENTS SIDEBAR
// ─────────────────────────────────────────────────────────────────────────────
function renderUpcoming() {
  const list = document.getElementById('upcomingList');
  const now = new Date(); now.setHours(0,0,0,0);
  const upcoming = allEvents.filter(ev => ev.start >= now).slice(0, 8);

  if (upcoming.length === 0) {
    list.innerHTML = '<p style="font-size:0.9rem;color:var(--warm-grey);">No upcoming events found.</p>';
    return;
  }

  list.innerHTML = '';
  upcoming.forEach(ev => {
    const item = document.createElement('div');
    item.className = 'event-item';
    item.innerHTML = `
      <div class="event-item-date">${fmtDate(ev.start, {weekday:'short',day:'numeric',month:'short'})}</div>
      <div class="event-item-title">${ev.title}</div>
      <div class="event-item-time">${ev.allDay ? 'All day' : fmtTime(ev.start)}${ev.location ? ' · ' + ev.location : ''}</div>
    `;
    item.addEventListener('click', () => showDetail(ev));
    list.appendChild(item);
  });
}

// ─────────────────────────────────────────────────────────────────────────────
// EVENT DETAIL PANEL
// ─────────────────────────────────────────────────────────────────────────────
function showDetail(ev) {
  const panel = document.getElementById('eventDetail');
  document.getElementById('detailTitle').textContent = ev.title;

  const meta = document.getElementById('detailMeta');
  const dateStr = fmtDate(ev.start, {weekday:'long',day:'numeric',month:'long',year:'numeric'});
  const timeStr = ev.allDay ? 'All day' : `${fmtTime(ev.start)}${ev.end ? ' – ' + fmtTime(ev.end) : ''}`;
  meta.innerHTML = `
    <span>📅 ${dateStr}</span>
    <span>⏱ ${timeStr}</span>
    ${ev.location ? `<span>📍 ${ev.location}</span>` : ''}
  `;

  document.getElementById('detailDesc').textContent = ev.description || '';

  // Add to calendar links
  const addCal = document.getElementById('detailAddCal');
  addCal.innerHTML = `
    <a href="${WEBCAL_URL}" title="Subscribe to the full QPC calendar">+ Subscribe to Calendar</a>
  `;

  panel.classList.add('visible');
  panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

document.getElementById('detailClose').addEventListener('click', () => {
  document.getElementById('eventDetail').classList.remove('visible');
});

// ─────────────────────────────────────────────────────────────────────────────
// MONTH NAVIGATION
// ─────────────────────────────────────────────────────────────────────────────
document.getElementById('prevMonth').addEventListener('click', () => {
  currentMonth--;
  if (currentMonth < 0) { currentMonth = 11; currentYear--; }
  renderMonth(currentMonth, currentYear);
});

document.getElementById('nextMonth').addEventListener('click', () => {
  currentMonth++;
  if (currentMonth > 11) { currentMonth = 0; currentYear++; }
  renderMonth(currentMonth, currentYear);
});

// ─────────────────────────────────────────────────────────────────────────────
// FETCH & INITIALISE
// ─────────────────────────────────────────────────────────────────────────────
async function init() {
  // Check if proxy URL has been configured
  if (PROXY_URL.includes('YOUR-SUBDOMAIN')) {
    showProxyNotice();
    return;
  }

  try {
    const res = await fetch(PROXY_URL);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const icsText = await res.text();
    allEvents = parseICS(icsText);
    renderMonth(currentMonth, currentYear);
    renderUpcoming();
  } catch (err) {
    document.getElementById('calContainer').innerHTML = `
      <div class="cal-error">
        <p>Could not load calendar data. <a href="${WEBCAL_URL}">Subscribe directly</a> to add events to your calendar app.</p>
        <p style="margin-top:0.5rem;font-size:0.8rem;color:var(--warm-grey);">Error: ${err.message}</p>
      </div>`;
    document.getElementById('upcomingList').innerHTML = '';
  }
}

function showProxyNotice() {
  document.getElementById('calContainer').innerHTML = `
    <div class="cal-error">
      <p><strong>Calendar proxy not yet configured.</strong></p>
      <p style="margin-top:0.5rem;">Deploy the Cloudflare Worker and update the <code>PROXY_URL</code> constant at the top of this page's script. See the deployment instructions included with the site files.</p>
      <p style="margin-top:0.5rem;">In the meantime, use the subscribe links above to add the calendar to your own app.</p>
    </div>`;
  document.getElementById('upcomingList').innerHTML = `
    <p style="font-size:0.9rem;color:var(--warm-grey);">Use the subscribe links in the page header to add this calendar to your device.</p>`;
  document.getElementById('monthTitle').textContent = 'Calendar';
}

init();

// ─────────────────────────────────────────────────────────────────────────────
// NAV
// ─────────────────────────────────────────────────────────────────────────────<?php include 'nav-js.php'; ?>
</script>
</body>
</html>
