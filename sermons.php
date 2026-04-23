<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sermons — Queanbeyan Presbyterian Church</title>
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
.page-header-inner{max-width:1300px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:5rem;align-items:end;position:relative;z-index:1;}
.page-eyebrow{font-family:'DM Mono',monospace;font-size:0.68rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--gold-light);margin-bottom:1rem;display:flex;align-items:center;gap:0.8rem;}
.page-eyebrow::before{content:'';display:block;width:2rem;height:1px;background:var(--gold);}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(2.5rem,5vw,4rem);font-weight:600;color:var(--cream);line-height:1.1;margin-bottom:0.8rem;}
.page-lead{font-size:1.1rem;color:rgba(245,240,232,0.7);line-height:1.75;max-width:44ch;}
.spotify-follow{display:flex;flex-direction:column;gap:1rem;align-self:end;}
.spotify-show-embed{border-radius:4px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.3);}

/* SHARED */
.section-label{font-family:'DM Mono',monospace;font-size:0.65rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--gold);display:flex;align-items:center;gap:0.8rem;margin-bottom:1rem;}
.section-label::before{content:'';display:block;width:1.5rem;height:1px;background:var(--gold);}

/* FILTER BAR */
.filter-bar{padding:2.5rem 5rem;background:var(--cream-dark);border-bottom:1px solid var(--border);position:sticky;top:72px;z-index:50;}
.filter-bar-inner{max-width:1300px;margin:0 auto;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;}
.filter-label{font-family:'DM Mono',monospace;font-size:0.62rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--warm-grey);white-space:nowrap;}
.filter-chips{display:flex;gap:0.6rem;flex-wrap:wrap;}
.chip{font-family:'DM Mono',monospace;font-size:0.65rem;letter-spacing:0.08em;text-transform:uppercase;padding:0.5rem 1.1rem;border:1px solid var(--border);border-radius:100px;cursor:pointer;background:var(--cream);color:var(--ink);transition:background 0.2s,color 0.2s,border-color 0.2s;}
.chip:hover{border-color:var(--forest);color:var(--forest);}
.chip.active{background:var(--forest);color:var(--cream);border-color:var(--forest);}
.sermon-count{font-family:'DM Mono',monospace;font-size:0.62rem;letter-spacing:0.1em;text-transform:uppercase;color:var(--warm-grey);margin-left:auto;}

/* SERIES SECTIONS */
.sermons-wrap{max-width:1300px;margin:0 auto;padding:3rem 5rem 6rem;}
.series-section{margin-bottom:4rem;}
.series-section.hidden{display:none;}
.series-header{display:flex;align-items:baseline;gap:1.5rem;margin-bottom:2rem;padding-bottom:1rem;border-bottom:2px solid var(--border);}
.series-title{font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:600;color:var(--forest);}
.series-count{font-family:'DM Mono',monospace;font-size:0.62rem;letter-spacing:0.1em;text-transform:uppercase;color:var(--warm-grey);}
.series-tag{font-family:'DM Mono',monospace;font-size:0.58rem;letter-spacing:0.12em;text-transform:uppercase;padding:0.3rem 0.8rem;border-radius:100px;border:1px solid var(--border);color:var(--warm-grey);background:var(--cream-dark);}

/* EPISODE CARDS */
.episode-list{display:flex;flex-direction:column;gap:1.5rem;}
.episode-card{background:var(--cream);border:1px solid var(--border);border-radius:2px;overflow:hidden;transition:box-shadow 0.2s,border-color 0.2s;}
.episode-card:hover{border-color:rgba(45,74,53,0.3);box-shadow:0 4px 20px rgba(28,26,22,0.06);}
.episode-card.hidden{display:none;}

/* TOP ROW: meta + toggle */
.episode-top{display:grid;grid-template-columns:1fr auto;gap:1.5rem;padding:1.8rem 2rem 0;align-items:start;}
.episode-meta{}
.episode-date{font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--gold);margin-bottom:0.4rem;}
.episode-title{font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:600;color:var(--forest);line-height:1.3;margin-bottom:0.3rem;}
.episode-preacher{font-family:'DM Mono',monospace;font-size:0.65rem;letter-spacing:0.1em;text-transform:uppercase;color:var(--warm-grey);}
.episode-duration{font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:0.08em;color:var(--warm-grey);margin-top:0.3rem;}
.episode-actions{display:flex;flex-direction:column;gap:0.5rem;align-items:flex-end;padding-top:0.2rem;}
.btn-play{font-family:'DM Mono',monospace;font-size:0.65rem;letter-spacing:0.1em;text-transform:uppercase;background:var(--forest);color:var(--cream);border:none;padding:0.7rem 1.4rem;border-radius:2px;cursor:pointer;transition:background 0.2s;white-space:nowrap;}
.btn-play:hover{background:var(--forest-light);}
.btn-transcript{font-family:'DM Mono',monospace;font-size:0.62rem;letter-spacing:0.08em;text-transform:uppercase;background:transparent;color:var(--warm-grey);border:1px solid var(--border);padding:0.5rem 1rem;border-radius:2px;cursor:pointer;transition:color 0.2s,border-color 0.2s;white-space:nowrap;}
.btn-transcript:hover{color:var(--forest);border-color:var(--forest);}

/* DESCRIPTION */
.episode-desc{padding:0.8rem 2rem 0;font-size:0.95rem;color:var(--warm-grey);line-height:1.65;max-width:80ch;}

/* PLAYER — collapses/expands */
.episode-player{max-height:0;overflow:hidden;transition:max-height 0.4s ease;}
.episode-player.open{max-height:200px;}
.episode-player-inner{padding:1.2rem 2rem 0;}
.episode-player iframe{display:block;border-radius:4px;}

/* TRANSCRIPT DRAWER */
.episode-transcript{max-height:0;overflow:hidden;transition:max-height 0.5s ease;}
.episode-transcript.open{max-height:600px;overflow-y:auto;}
.transcript-inner{padding:1.5rem 2rem;border-top:1px solid var(--border);margin-top:1.2rem;background:var(--cream-dark);}
.transcript-label{font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:0.18em;text-transform:uppercase;color:var(--gold);margin-bottom:0.8rem;}
.transcript-text{font-size:0.95rem;color:var(--ink);line-height:1.8;}
.transcript-placeholder{font-size:0.92rem;color:var(--warm-grey);font-style:italic;}

/* BOTTOM PADDING */
.episode-foot{height:1.8rem;}

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
  .page-header-inner{grid-template-columns:1fr;gap:2.5rem;}
  .filter-bar{padding:1.5rem 2rem;top:64px;}
  .sermons-wrap{padding:2.5rem 2rem 5rem;}
  .episode-top{grid-template-columns:1fr;gap:1rem;}
  .episode-actions{flex-direction:row;align-items:center;}
  .spotify-strip{padding:3rem 2rem;}
  .spotify-strip-inner{flex-direction:column;align-items:flex-start;}
  footer{padding:4rem 2rem;}
  .footer-inner{grid-template-columns:1fr;gap:3rem;}
  .footer-bottom{flex-direction:column;gap:1rem;text-align:center;}
}
@media(max-width:640px){
  nav{padding:1rem 1.25rem;}
  .nav-logo-img{height:36px;}
  .page-header{padding:7rem 1.25rem 3rem;}
  .filter-bar{padding:1.2rem 1.25rem;}
  .filter-chips{gap:0.4rem;}
  .chip{font-size:0.6rem;padding:0.4rem 0.8rem;}
  .sermons-wrap{padding:2rem 1.25rem 4rem;}
  .episode-top{padding:1.4rem 1.4rem 0;}
  .episode-desc{padding:0.6rem 1.4rem 0;}
  .episode-player-inner{padding:1rem 1.4rem 0;}
  .transcript-inner{padding:1.2rem 1.4rem;}
  .episode-actions{flex-wrap:wrap;}
  footer{padding:3rem 1.25rem;}
  .footer-bottom{text-align:center;}
}
</style>
</head>
<body>

<!-- NAV -->
<?php $activePage = 'sermons'; include 'nav.php'; ?>

<!-- PAGE HEADER -->
<header class="page-header">
  <div class="page-header-inner">
    <div>
      <div class="page-eyebrow">Weekly Preaching · Queanbeyan Presbyterian Church</div>
      <h1 class="page-title">Sermons</h1>
      <p class="page-lead">Expository preaching from the Word of God, working through books of the Bible, Sunday by Sunday. Listen online or subscribe wherever you get your podcasts.</p>
    </div>
    <div class="spotify-follow">
      <div class="section-label" style="color:var(--gold-light);margin-bottom:0.5rem;">Subscribe &amp; Follow</div>
      <div class="spotify-show-embed">
        <iframe
          src="https://open.spotify.com/embed/show/5XJPM6ftmGcBDK5yanFw9n?utm_source=generator&theme=0"
          width="100%" height="152" frameborder="0"
          allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
          loading="lazy"
          title="Queanbeyan Presbyterian Podcast on Spotify">
        </iframe>
      </div>
    </div>
  </div>
</header>

<!-- FILTER BAR -->
<div class="filter-bar">
  <div class="filter-bar-inner">
    <span class="filter-label">Series</span>
    <div class="filter-chips" id="filterChips">
      <button class="chip active" data-series="all">All</button>
      <button class="chip" data-series="acts">Acts</button>
      <button class="chip" data-series="psalms">Psalms</button>
      <button class="chip" data-series="galatians">Galatians</button>
      <button class="chip" data-series="advent">Advent</button>
    </div>
    <span class="sermon-count" id="sermonCount"></span>
  </div>
</div>

<!-- SERMONS -->
<div class="sermons-wrap">

  <!-- ── ACTS ───────────────────────────────────────────────── -->
  <div class="series-section" data-series="acts" id="series-acts">
    <div class="series-header">
      <h2 class="series-title">Acts</h2>
      <span class="series-tag">Series</span>
      <span class="series-count" id="count-acts"></span>
    </div>
    <div class="episode-list" id="list-acts"></div>
  </div>

  <!-- ── PSALMS ─────────────────────────────────────────────── -->
  <div class="series-section" data-series="psalms" id="series-psalms">
    <div class="series-header">
      <h2 class="series-title">Psalms</h2>
      <span class="series-tag">Series</span>
      <span class="series-count" id="count-psalms"></span>
    </div>
    <div class="episode-list" id="list-psalms"></div>
  </div>

  <!-- ── GALATIANS ──────────────────────────────────────────── -->
  <div class="series-section" data-series="galatians" id="series-galatians">
    <div class="series-header">
      <h2 class="series-title">Galatians</h2>
      <span class="series-tag">Series</span>
      <span class="series-count" id="count-galatians"></span>
    </div>
    <div class="episode-list" id="list-galatians"></div>
  </div>

  <!-- ── ADVENT ─────────────────────────────────────────────── -->
  <div class="series-section" data-series="advent" id="series-advent">
    <div class="series-header">
      <h2 class="series-title">Advent</h2>
      <span class="series-tag">Series</span>
      <span class="series-count" id="count-advent"></span>
    </div>
    <div class="episode-list" id="list-advent"></div>
  </div>

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
// SERMON DATA
// To add a new sermon: copy any object below and update the fields.
// spotifyId: the episode ID from the Spotify URL
//   e.g. https://open.spotify.com/episode/29pYfTiK1lzQRkHVI6XMvS → "29pYfTiK1lzQRkHVI6XMvS"
// transcript: paste summary/transcript text, or leave "" for the placeholder
// ─────────────────────────────────────────────────────────────────────────────
const SERMONS = [
  {
    id: "29pYfTiK1lzQRkHVI6XMvS",
    series: "psalms",
    title: "Psalm 22: Psalms Jesus Fulfilled and Quoted",
    preacher: "Pastor Robert McMullan",
    date: "10 March 2025",
    duration: "26 min",
    scripture: "Psalm 22",
    description: "Exploring how David's words of suffering and trust anticipate the suffering of Christ and find their fulfilment in the promises revealed in the New Testament. Extended: includes baptism testimony.",
    transcript: ""
  },
  {
    id: "3LKaE03OPaW2v2Bs9HjjQp",
    series: "acts",
    title: "Acts 28",
    preacher: "Pastor Robert McMullan",
    date: "2 March 2025",
    duration: "30 min",
    scripture: "Acts 28",
    description: "Paul reaches Rome after shipwreck and proclaims the kingdom of God with boldness despite chains. From Malta's kindness to witness in the empire's heart, God's purposes advance and his word remains unhindered.",
    transcript: ""
  },
  {
    id: "1e1g4k1Pil66YSAvVuTPEp",
    series: "acts",
    title: "Acts 27",
    preacher: "Pastor Robert McMullan",
    date: "26 February 2025",
    duration: "24 min",
    scripture: "Acts 27",
    description: "Paul's voyage to Rome is overtaken by storm and shipwreck, yet through trial and danger God preserves every life on board, demonstrating his sovereign care amid uncertainty.",
    transcript: ""
  },
  {
    id: "5zch6UPh40eEGMVZ0oYZ3C",
    series: "acts",
    title: "Acts 26",
    preacher: "Pastor Robert McMullan",
    date: "18 February 2025",
    duration: "24 min",
    scripture: "Acts 26",
    description: "Paul gives his defence before King Agrippa. He recounts his former life, his encounter with the risen Christ, and his mission to proclaim repentance and resurrection hope to all.",
    transcript: ""
  },
  {
    id: "2kLkKDfuIPsnzOGCNSAyNQ",
    series: "acts",
    title: "Acts 25",
    preacher: "Pastor Robert McMullan",
    date: "10 February 2025",
    duration: "22 min",
    scripture: "Acts 25",
    description: "Paul stands before the new governor Festus. Facing renewed accusations, Paul appeals to Caesar, and Festus consults King Agrippa about the case, highlighting the dispute over Jesus' resurrection.",
    transcript: ""
  },
  {
    id: "3NNJEhbSGNWukINKRoPzsn",
    series: "acts",
    title: "Acts 24",
    preacher: "Pastor Robert McMullan",
    date: "1 February 2025",
    duration: "24 min",
    scripture: "Acts 24",
    description: "Paul makes his defence before the Roman governor Felix, speaks of faith in Christ and the hope of the resurrection, and remains in custody as the legal process unfolds.",
    transcript: ""
  },
  {
    id: "0lOfXMD2D4KKtr3g6OJ9P0",
    series: "psalms",
    title: "Psalms 42 & 43: Look Up When You Are Down",
    preacher: "Peter Gobbo",
    date: "29 January 2025",
    duration: "17 min",
    scripture: "Psalms 42–43",
    description: "Giving voice to deep spiritual longing and distress, while calling God's people to honest lament, renewed hope, and confidence in the Lord who remains their help and their God.",
    transcript: ""
  },
  {
    id: "0MNkKFVpbgkp6K5EfUR1T6",
    series: "galatians",
    title: "Galatians 4:8–31: Slave or Son",
    preacher: "Patrick Jacob",
    date: "21 January 2025",
    duration: "31 min",
    scripture: "Galatians 4:8–31",
    description: "Paul contrasts slavery and freedom, urging believers to live as children of promise rather than returning to bondage.",
    transcript: ""
  },
  {
    id: "7eSMDVjQ5Y4D8Mr2ZagMgY",
    series: "psalms",
    title: "Psalm 90",
    preacher: "Pastor Robert McMullan",
    date: "12 January 2025",
    duration: "21 min",
    scripture: "Psalm 90",
    description: "Reflecting on the eternal nature of God, the brevity of human life, and the call to live wisely before him.",
    transcript: ""
  },
  {
    id: "0NbCYUXyuNhBSdgAP5TmnT",
    series: "psalms",
    title: "Psalm 104",
    preacher: "Pastor Robert McMullan",
    date: "8 January 2025",
    duration: "26 min",
    scripture: "Psalm 104",
    description: "Celebrating God's wisdom and power displayed in creation, and calling his people to praise the Lord who sustains all life.",
    transcript: ""
  },
  {
    id: "4aA7c52sv9O1bWOJZpIIcW",
    series: "psalms",
    title: "Psalm 103: New Year — Thankfulness",
    preacher: "Pastor Robert McMullan",
    date: "8 January 2025",
    duration: "23 min",
    scripture: "Psalm 103",
    description: "Calling God's people to remember his mercy, faithfulness, and enduring grace as the new year begins.",
    transcript: ""
  },
  {
    id: "6JnSXrHcby5qQxyne4ie6x",
    series: "advent",
    title: "Advent 4: Love",
    preacher: "Pastor Robert McMullan",
    date: "8 January 2025",
    duration: "11 min",
    scripture: "Isaiah 60:1–3; John 1:9–14",
    description: "Reflecting on God's light and love revealed in the coming of Christ into the world.",
    transcript: ""
  }
];

// ─────────────────────────────────────────────────────────────────────────────
// RENDER
// ─────────────────────────────────────────────────────────────────────────────
function buildCard(s) {
  const card = document.createElement('div');
  card.className = 'episode-card';
  card.dataset.series = s.series;
  card.dataset.id = s.id;

  const hasTranscript = s.transcript && s.transcript.trim().length > 0;

  card.innerHTML = `
    <div class="episode-top">
      <div class="episode-meta">
        <div class="episode-date">${s.date} · ${s.scripture}</div>
        <div class="episode-title">${s.title}</div>
        <div class="episode-preacher">${s.preacher}</div>
        <div class="episode-duration">⏱ ${s.duration}</div>
      </div>
      <div class="episode-actions">
        <button class="btn-play" data-id="${s.id}">▶ Listen</button>
        <button class="btn-transcript" data-id="${s.id}">
          ${hasTranscript ? '📄 Summary' : '📄 Summary'}
        </button>
      </div>
    </div>
    <p class="episode-desc">${s.description}</p>
    <div class="episode-player" id="player-${s.id}">
      <div class="episode-player-inner"></div>
    </div>
    <div class="episode-transcript" id="transcript-${s.id}">
      <div class="transcript-inner">
        <div class="transcript-label">Sermon Summary</div>
        <div class="transcript-text">
          ${hasTranscript
            ? s.transcript
            : `<span class="transcript-placeholder">Summary not yet available for this sermon. Check back soon, or <a href="https://open.spotify.com/episode/${s.id}" target="_blank" rel="noopener" style="color:var(--forest)">listen on Spotify</a>.</span>`
          }
        </div>
      </div>
    </div>
    <div class="episode-foot"></div>
  `;
  return card;
}

// Populate lists
const lists = { acts: [], psalms: [], galatians: [], advent: [] };
SERMONS.forEach(s => { if (lists[s.series]) lists[s.series].push(s); });

Object.entries(lists).forEach(([series, episodes]) => {
  const container = document.getElementById(`list-${series}`);
  const countEl   = document.getElementById(`count-${series}`);
  if (!container) return;
  episodes.forEach(s => container.appendChild(buildCard(s)));
  if (countEl) countEl.textContent = `${episodes.length} sermon${episodes.length !== 1 ? 's' : ''}`;
});

updateCount();

// ─────────────────────────────────────────────────────────────────────────────
// FILTERING
// ─────────────────────────────────────────────────────────────────────────────
document.getElementById('filterChips').addEventListener('click', e => {
  const chip = e.target.closest('.chip');
  if (!chip) return;
  document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
  chip.classList.add('active');
  const series = chip.dataset.series;

  document.querySelectorAll('.series-section').forEach(sec => {
    sec.classList.toggle('hidden', series !== 'all' && sec.dataset.series !== series);
  });

  updateCount();
});

function updateCount() {
  const activeSeries = document.querySelector('.chip.active')?.dataset.series || 'all';
  const total = activeSeries === 'all'
    ? SERMONS.length
    : SERMONS.filter(s => s.series === activeSeries).length;
  document.getElementById('sermonCount').textContent = `${total} sermon${total !== 1 ? 's' : ''}`;
}

// ─────────────────────────────────────────────────────────────────────────────
// PLAYER TOGGLE — lazy-load Spotify embed on first click
// ─────────────────────────────────────────────────────────────────────────────
document.addEventListener('click', e => {
  // Play button
  const playBtn = e.target.closest('.btn-play');
  if (playBtn) {
    const id = playBtn.dataset.id;
    const playerEl = document.getElementById(`player-${id}`);
    const inner = playerEl.querySelector('.episode-player-inner');
    const isOpen = playerEl.classList.contains('open');

    // Close all other players first
    document.querySelectorAll('.episode-player.open').forEach(p => {
      if (p.id !== `player-${id}`) {
        p.classList.remove('open');
        const b = document.querySelector(`.btn-play[data-id="${p.id.replace('player-','')}"]`);
        if (b) b.textContent = '▶ Listen';
      }
    });

    if (isOpen) {
      playerEl.classList.remove('open');
      playBtn.textContent = '▶ Listen';
    } else {
      // Lazy-load iframe
      if (!inner.querySelector('iframe')) {
        inner.innerHTML = `<iframe
          src="https://open.spotify.com/embed/episode/${id}?utm_source=generator&theme=0"
          width="100%" height="152" frameborder="0"
          allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
          loading="lazy">
        </iframe>`;
      }
      playerEl.classList.add('open');
      playBtn.textContent = '■ Close';
    }
    return;
  }

  // Transcript button
  const transcriptBtn = e.target.closest('.btn-transcript');
  if (transcriptBtn) {
    const id = transcriptBtn.dataset.id;
    const transcriptEl = document.getElementById(`transcript-${id}`);
    const isOpen = transcriptEl.classList.contains('open');
    transcriptEl.classList.toggle('open', !isOpen);
    transcriptBtn.textContent = isOpen ? '📄 Summary' : '✕ Close';
    return;
  }
});

// ─────────────────────────────────────────────────────────────────────────────
// NAV
// ─────────────────────────────────────────────────────────────────────────────
const toggle = document.getElementById('navToggle');
const links  = document.getElementById('navLinks');
const overlay = document.getElementById('navOverlay');
function openMenu() { toggle.classList.add('open'); links.classList.add('open'); overlay.classList.add('open'); document.body.style.overflow='hidden'; }
function closeMenu() { toggle.classList.remove('open'); links.classList.remove('open'); overlay.classList.remove('open'); document.body.style.overflow=''; }
toggle.addEventListener('click', () => links.classList.contains('open') ? closeMenu() : openMenu());
overlay.addEventListener('click', closeMenu);
links.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));
</script>
</body>
</html>
