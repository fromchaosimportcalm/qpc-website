<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>M3 — Men's Motor Ministry · Queanbeyan Presbyterian Church</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Mono:wght@300;400&display=swap"
      rel="stylesheet"
    />
    <style>
      :root {
        --cream: #f5f0e8;
        --cream-dark: #ede7d6;
        --ink: #1c1a16;
        --forest: #2d4a35;
        --forest-light: #3d6147;
        --gold: #b8862a;
        --gold-light: #d4a445;
        --warm-grey: #8a8278;
        --border: rgba(44, 74, 53, 0.18);
      }
      *,
      *::before,
      *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
      }
      html {
        scroll-behavior: smooth;
      }
      body {
        background: var(--cream);
        color: var(--ink);
        font-family: "Crimson Pro", Georgia, serif;
        font-size: 18px;
        line-height: 1.7;
        overflow-x: hidden;
      }
      body::before {
        content: "";
        position: fixed;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
        pointer-events: none;
        z-index: 1000;
      }

      /* NAV */
      nav {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 3rem;
        background: rgba(245, 240, 232, 0.92);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--border);
      }
      .nav-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        flex-shrink: 0;
      }
      .nav-logo-img {
        height: 48px;
        width: auto;
        display: block;
      }
      .nav-links {
        display: flex;
        gap: 2.5rem;
        list-style: none;
        align-items: center;
      }
      .nav-links a {
        font-family: "DM Mono", monospace;
        font-size: 0.7rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--ink);
        text-decoration: none;
        opacity: 0.7;
        transition:
          opacity 0.2s,
          color 0.2s;
      }
      .nav-links a:hover,
      .nav-links a.active {
        opacity: 1;
        color: var(--forest);
      }
      .nav-cta {
        color: var(--cream) !important;
        background: var(--forest);
        padding: 0.6rem 1.4rem;
        border-radius: 2px;
        opacity: 1 !important;
      }
      .nav-cta:hover {
        background: var(--forest-light) !important;
      }
      .nav-hamburger {
        display: none;
        flex-direction: column;
        gap: 5px;
        cursor: pointer;
        padding: 4px;
        background: none;
        border: none;
        z-index: 200;
      }
      .nav-hamburger span {
        display: block;
        width: 24px;
        height: 2px;
        background: var(--forest);
        border-radius: 2px;
        transition:
          transform 0.3s,
          opacity 0.3s;
        transform-origin: center;
      }
      .nav-hamburger.open span:nth-child(1) {
        transform: translateY(7px) rotate(45deg);
      }
      .nav-hamburger.open span:nth-child(2) {
        opacity: 0;
      }
      .nav-hamburger.open span:nth-child(3) {
        transform: translateY(-7px) rotate(-45deg);
      }
      .nav-overlay {
        position: fixed;
        inset: 0;
        background: rgba(28, 26, 22, 0.4);
        z-index: 140;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
      }
      .nav-overlay.open {
        opacity: 1;
        pointer-events: all;
      }

      /* PAGE HEADER */
      .page-header {
        padding: 9rem 5rem 5rem;
        background: var(--ink);
        position: relative;
        overflow: hidden;
      }
      .page-header::after {
        content: "";
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 60px;
        background: var(--cream);
        clip-path: ellipse(55% 100% at 50% 100%);
      }
      .page-header-inner {
        max-width: 1300px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 3rem;
        align-items: end;
        position: relative;
        z-index: 1;
      }
      .page-breadcrumb {
        font-family: "DM Mono", monospace;
        font-size: 0.62rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgba(245, 240, 232, 0.4);
        margin-bottom: 1rem;
      }
      .page-breadcrumb a {
        color: var(--gold-light);
        text-decoration: none;
      }
      .page-breadcrumb a:hover {
        color: var(--gold);
      }
      .page-eyebrow {
        font-family: "DM Mono", monospace;
        font-size: 0.68rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--gold-light);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
      }
      .page-eyebrow::before {
        content: "";
        display: block;
        width: 2rem;
        height: 1px;
        background: var(--gold);
      }
      .page-title {
        font-family: "Playfair Display", serif;
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 600;
        color: var(--cream);
        line-height: 1.1;
        margin-bottom: 0.5rem;
      }
      .page-subtitle {
        font-family: "Playfair Display", serif;
        font-size: 1.3rem;
        font-style: italic;
        color: var(--gold-light);
        margin-bottom: 1rem;
      }
      .page-lead {
        font-size: 1.1rem;
        color: rgba(245, 240, 232, 0.65);
        line-height: 1.75;
        max-width: 50ch;
      }
      .page-header-meta {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
        align-self: end;
        padding-bottom: 0.5rem;
      }
      .meta-pill {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        background: rgba(245, 240, 232, 0.06);
        border: 1px solid rgba(245, 240, 232, 0.12);
        border-radius: 2px;
        padding: 0.8rem 1.2rem;
      }
      .meta-pill-icon {
        font-size: 1rem;
        flex-shrink: 0;
      }
      .meta-pill-text .label {
        font-family: "DM Mono", monospace;
        font-size: 0.58rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(245, 240, 232, 0.35);
      }
      .meta-pill-text .value {
        font-family: "DM Mono", monospace;
        font-size: 0.68rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: rgba(245, 240, 232, 0.8);
      }

      /* ABOUT SECTION */
      .about-section {
        max-width: 1300px;
        margin: 0 auto;
        padding: 5rem 5rem 3rem;
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 6rem;
        align-items: start;
      }
      .section-label {
        font-family: "DM Mono", monospace;
        font-size: 0.65rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--gold);
        display: flex;
        align-items: center;
        gap: 0.8rem;
        margin-bottom: 1rem;
      }
      .section-label::before {
        content: "";
        display: block;
        width: 1.5rem;
        height: 1px;
        background: var(--gold);
      }
      .section-title {
        font-family: "Playfair Display", serif;
        font-size: clamp(1.8rem, 3vw, 2.5rem);
        font-weight: 600;
        line-height: 1.15;
        color: var(--forest);
        letter-spacing: -0.01em;
        margin-bottom: 1.5rem;
      }
      .about-text p {
        font-size: 1rem;
        color: var(--warm-grey);
        line-height: 1.8;
        margin-bottom: 1rem;
      }
      .about-text p strong {
        color: var(--ink);
        font-weight: 600;
      }
      .about-text blockquote {
        border-left: 3px solid var(--gold);
        padding: 1rem 1.5rem;
        margin: 1.5rem 0;
        background: var(--cream-dark);
        border-radius: 0 2px 2px 0;
      }
      .about-text blockquote p {
        font-style: italic;
        color: var(--ink);
        margin-bottom: 0;
      }
      .about-aside {
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
      }
      .aside-card {
        background: var(--cream-dark);
        border: 1px solid var(--border);
        border-radius: 2px;
        padding: 2rem;
      }
      .aside-card-label {
        font-family: "DM Mono", monospace;
        font-size: 0.6rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 0.5rem;
      }
      .aside-card-value {
        font-family: "Playfair Display", serif;
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--forest);
        margin-bottom: 0.3rem;
      }
      .aside-card-sub {
        font-size: 0.88rem;
        color: var(--warm-grey);
      }
      .btn-primary {
        font-family: "DM Mono", monospace;
        font-size: 0.72rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        background: var(--forest);
        color: var(--cream);
        padding: 1rem 2.2rem;
        text-decoration: none;
        border-radius: 2px;
        transition: background 0.2s;
        display: inline-block;
        margin-top: 0.5rem;
      }
      .btn-primary:hover {
        background: var(--forest-light);
      }

      /* GALLERY */
      .gallery-section {
        padding: 3rem 5rem 5rem;
      }
      .gallery-inner {
        max-width: 1300px;
        margin: 0 auto;
      }
      .gallery-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 2rem;
      }
      .gallery-title {
        font-family: "Playfair Display", serif;
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--forest);
      }
      .gallery-filter {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
      }
      .gallery-chip {
        font-family: "DM Mono", monospace;
        font-size: 0.62rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.4rem 0.9rem;
        border: 1px solid var(--border);
        border-radius: 100px;
        cursor: pointer;
        background: var(--cream);
        color: var(--ink);
        transition:
          background 0.2s,
          color 0.2s,
          border-color 0.2s;
      }
      .gallery-chip:hover {
        border-color: var(--forest);
        color: var(--forest);
      }
      .gallery-chip.active {
        background: var(--forest);
        color: var(--cream);
        border-color: var(--forest);
      }

      /* PHOTO GRID */
      .photo-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
      }
      .photo-tile {
        position: relative;
        overflow: hidden;
        border-radius: 2px;
        cursor: pointer;
        background: var(--cream-dark);
        aspect-ratio: 4/3;
      }
      .photo-tile img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease;
      }
      .photo-tile:hover img {
        transform: scale(1.05);
      }
      .photo-tile-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
          to top,
          rgba(28, 26, 22, 0.7) 0%,
          transparent 50%
        );
        opacity: 0;
        transition: opacity 0.3s;
      }
      .photo-tile:hover .photo-tile-overlay {
        opacity: 1;
      }
      .photo-tile-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1rem;
        transform: translateY(8px);
        opacity: 0;
        transition:
          transform 0.3s,
          opacity 0.3s;
      }
      .photo-tile:hover .photo-tile-caption {
        transform: translateY(0);
        opacity: 1;
      }
      .photo-tile-caption .cap-title {
        font-family: "Playfair Display", serif;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--cream);
        margin-bottom: 0.15rem;
      }
      .photo-tile-caption .cap-date {
        font-family: "DM Mono", monospace;
        font-size: 0.58rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: rgba(245, 240, 232, 0.6);
      }

      /* Photo placeholder state */
      .photo-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: var(--cream-dark);
        color: var(--border);
      }
      .photo-placeholder span {
        font-size: 2rem;
        opacity: 0.3;
      }
      .photo-placeholder p {
        font-family: "DM Mono", monospace;
        font-size: 0.58rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--warm-grey);
        opacity: 0.5;
        text-align: center;
        padding: 0 1rem;
      }

      /* Wide tile (spans 2 columns) */
      .photo-tile.wide {
        grid-column: span 2;
        aspect-ratio: 8/3;
      }
      .photo-tile.tall {
        grid-row: span 2;
        aspect-ratio: 3/4;
      }

      /* LIGHTBOX */
      .lightbox {
        position: fixed;
        inset: 0;
        background: rgba(28, 26, 22, 0.95);
        z-index: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
      }
      .lightbox.open {
        opacity: 1;
        pointer-events: all;
      }
      .lightbox-inner {
        position: relative;
        max-width: min(90vw, 1000px);
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        align-items: center;
      }
      .lightbox-img {
        max-width: 100%;
        max-height: 75vh;
        object-fit: contain;
        border-radius: 2px;
        display: block;
      }
      .lightbox-caption {
        margin-top: 1rem;
        text-align: center;
      }
      .lightbox-caption .cap-title {
        font-family: "Playfair Display", serif;
        font-size: 1rem;
        font-weight: 600;
        color: var(--cream);
        margin-bottom: 0.2rem;
      }
      .lightbox-caption .cap-date {
        font-family: "DM Mono", monospace;
        font-size: 0.62rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(245, 240, 232, 0.5);
      }
      .lightbox-close {
        position: absolute;
        top: -3rem;
        right: 0;
        font-family: "DM Mono", monospace;
        font-size: 0.65rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(245, 240, 232, 0.5);
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        transition: color 0.2s;
      }
      .lightbox-close:hover {
        color: var(--cream);
      }
      .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-family: "DM Mono", monospace;
        font-size: 0.7rem;
        letter-spacing: 0.1em;
        color: rgba(245, 240, 232, 0.5);
        background: none;
        border: none;
        cursor: pointer;
        padding: 1rem;
        transition: color 0.2s;
      }
      .lightbox-nav:hover {
        color: var(--cream);
      }
      .lightbox-prev {
        left: -3.5rem;
      }
      .lightbox-next {
        right: -3.5rem;
      }
      .lightbox-count {
        font-family: "DM Mono", monospace;
        font-size: 0.6rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: rgba(245, 240, 232, 0.35);
        margin-top: 0.5rem;
      }

      /* JOIN STRIP */
      .join-strip {
        background: var(--forest);
        padding: 5rem;
      }
      .join-inner {
        max-width: 1300px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5rem;
        align-items: center;
      }
      .join-left .section-label {
        color: var(--gold-light);
      }
      .join-left .section-label::before {
        background: var(--gold-light);
      }
      .join-left .section-title {
        color: var(--cream);
        margin-bottom: 1rem;
      }
      .join-left p {
        font-size: 1rem;
        color: rgba(245, 240, 232, 0.65);
        line-height: 1.75;
        max-width: 42ch;
        margin-bottom: 1.5rem;
      }
      .join-left .btn-primary {
        background: var(--gold);
      }
      .join-left .btn-primary:hover {
        background: var(--gold-light);
      }
      .join-verse {
        border-left: 3px solid var(--gold);
        padding: 1.5rem 2rem;
        background: rgba(245, 240, 232, 0.04);
      }
      .join-verse p {
        font-family: "Playfair Display", serif;
        font-size: 1.2rem;
        font-style: italic;
        color: var(--cream);
        line-height: 1.55;
        margin-bottom: 0.5rem;
      }
      .join-verse cite {
        font-family: "DM Mono", monospace;
        font-size: 0.62rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: var(--gold-light);
        font-style: normal;
      }

      /* FOOTER */
      footer {
        background: var(--ink);
        color: rgba(245, 240, 232, 0.55);
        padding: 5rem;
      }
      .footer-inner {
        max-width: 1300px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 5rem;
      }
      .footer-logo-img {
        height: 80px;
        width: auto;
        display: block;
        filter: brightness(0) invert(1) opacity(0.6);
      }
      .footer-brand .sub {
        font-family: "DM Mono", monospace;
        font-size: 0.6rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--gold);
        margin: 1rem 0 0.8rem;
        display: block;
      }
      .footer-brand p {
        font-size: 0.92rem;
        line-height: 1.7;
        max-width: 36ch;
      }
      .footer-links h4 {
        font-family: "DM Mono", monospace;
        font-size: 0.65rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 1.2rem;
      }
      .footer-links ul {
        list-style: none;
      }
      .footer-links li {
        margin-bottom: 0.7rem;
      }
      .footer-links a {
        font-size: 0.9rem;
        color: rgba(245, 240, 232, 0.5);
        text-decoration: none;
        transition: color 0.2s;
      }
      .footer-links a:hover {
        color: var(--cream);
      }
      .footer-bottom {
        max-width: 1300px;
        margin: 3rem auto 0;
        padding-top: 2rem;
        border-top: 1px solid rgba(245, 240, 232, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      .footer-bottom p {
        font-family: "DM Mono", monospace;
        font-size: 0.62rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: rgba(245, 240, 232, 0.25);
      }
      .footer-social {
        display: flex;
        gap: 1rem;
      }
      .social-link {
        width: 2rem;
        height: 2rem;
        border: 1px solid rgba(245, 240, 232, 0.1);
        border-radius: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        color: rgba(245, 240, 232, 0.4);
        text-decoration: none;
        transition:
          border-color 0.2s,
          color 0.2s;
      }
      .social-link:hover {
        border-color: var(--gold);
        color: var(--gold);
      }
      /* SPOTIFY LINK */
      .spotify-strip {
        background: var(--ink);
        padding: 3rem 5rem;
      }
      .spotify-strip-inner {
        max-width: 1300px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
      }
      .spotify-strip p {
        font-size: 1rem;
        color: rgba(245, 240, 232, 0.6);
        max-width: 48ch;
      }
      .spotify-strip p strong {
        color: var(--cream);
      }
      .btn-spotify {
        display: inline-flex;
        align-items: center;
        gap: 0.7rem;
        font-family: "DM Mono", monospace;
        font-size: 0.7rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        background: #1db954;
        color: #000;
        padding: 0.9rem 1.8rem;
        border-radius: 2px;
        text-decoration: none;
        transition: opacity 0.2s;
      }
      .btn-spotify:hover {
        opacity: 0.85;
      }
      .spotify-icon {
        width: 18px;
        height: 18px;
        fill: currentColor;
      }

      /* RESPONSIVE */
      @media (max-width: 1024px) {
        nav {
          padding: 1.2rem 2rem;
        }
        .nav-hamburger {
          display: flex;
        }
        .nav-links {
          position: fixed;
          top: 0;
          right: -100%;
          width: min(320px, 85vw);
          height: 100vh;
          background: var(--cream);
          border-left: 1px solid var(--border);
          flex-direction: column;
          align-items: flex-start;
          justify-content: center;
          padding: 3rem 2.5rem;
          gap: 0;
          transition: right 0.35s cubic-bezier(0.4, 0, 0.2, 1);
          z-index: 150;
          box-shadow: -8px 0 40px rgba(28, 26, 22, 0.12);
        }
        .nav-links.open {
          right: 0;
        }
        .nav-links li {
          width: 100%;
          border-bottom: 1px solid var(--border);
        }
        .nav-links li:last-child {
          border-bottom: none;
          margin-top: 1.5rem;
        }
        .nav-links a {
          display: block;
          padding: 1rem 0;
          font-size: 0.8rem;
          opacity: 1;
          color: var(--ink);
        }
        .nav-links .nav-cta {
          display: inline-block;
          width: auto;
          padding: 0.9rem 1.8rem;
          margin-top: 0.5rem;
        }
        .page-header {
          padding: 8rem 2rem 4rem;
        }
        .page-header-inner {
          grid-template-columns: 1fr;
          gap: 2rem;
        }
        .about-section {
          grid-template-columns: 1fr;
          gap: 3rem;
          padding: 3.5rem 2rem 2rem;
        }
        .gallery-section {
          padding: 2rem 2rem 4rem;
        }
        .photo-grid {
          grid-template-columns: 1fr 1fr;
        }
        .photo-tile.wide {
          grid-column: span 2;
        }
        .photo-tile.tall {
          grid-row: auto;
          aspect-ratio: 4/3;
        }
        .join-strip {
          padding: 3.5rem 2rem;
        }
        .join-inner {
          grid-template-columns: 1fr;
          gap: 2.5rem;
        }
        .lightbox-prev {
          left: -2rem;
        }
        .lightbox-next {
          right: -2rem;
        }
        footer {
          padding: 4rem 2rem;
        }
        .footer-inner {
          grid-template-columns: 1fr;
          gap: 3rem;
        }
        .footer-bottom {
          flex-direction: column;
          gap: 1rem;
          text-align: center;
        }
      }
      @media (max-width: 640px) {
        nav {
          padding: 1rem 1.25rem;
        }
        .nav-logo-img {
          height: 36px;
        }
        .page-header {
          padding: 7rem 1.25rem 3rem;
        }
        .about-section {
          padding: 3rem 1.25rem 2rem;
        }
        .gallery-section {
          padding: 1.5rem 1.25rem 3rem;
        }
        .photo-grid {
          grid-template-columns: 1fr;
        }
        .photo-tile.wide,
        .photo-tile.tall {
          grid-column: auto;
          grid-row: auto;
          aspect-ratio: 4/3;
        }
        .gallery-header {
          flex-direction: column;
          gap: 1rem;
          align-items: flex-start;
        }
        .lightbox-prev,
        .lightbox-next {
          display: none;
        }
        .join-strip,
        .footer {
          padding: 3rem 1.25rem;
        }
      }
    </style>
  </head>
  <body>
    <?php $activePage = 'ministries'; include 'nav.php'; ?>

    <!-- PAGE HEADER -->
    <header class="page-header">
      <div class="page-header-inner">
        <div>
          <div class="page-breadcrumb">
            <a href="ministries.php">Church Life</a> → M3
          </div>
          <div class="page-eyebrow">Men's Ministry · Every Thursday</div>
          <h1 class="page-title">M3</h1>
          <p class="page-subtitle">Men's Motor Ministry</p>
          <p class="page-lead">
            Where horsepower meets fellowship. Every Thursday night we fuel up
            with dinner, dig into discussion, then get our hands dirty in the
            workshop.
          </p>
        </div>
        <div class="page-header-meta">
          <div class="meta-pill">
            <span class="meta-pill-icon">📅</span>
            <div class="meta-pill-text">
              <div class="label">When</div>
              <div class="value">Thursdays · 6:00 pm</div>
            </div>
          </div>
          <div class="meta-pill">
            <span class="meta-pill-icon">📍</span>
            <div class="meta-pill-text">
              <div class="label">Where</div>
              <div class="value">Church Manse</div>
            </div>
          </div>
          <div class="meta-pill">
            <span class="meta-pill-icon">👤</span>
            <div class="meta-pill-text">
              <div class="label">Contact</div>
              <div class="value">Robert</div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- ABOUT -->
    <section class="about-section">
      <div class="about-text">
        <div class="section-label">About M3</div>
        <h2 class="section-title">Dinner, Discussion,<br />and the Workshop</h2>
        <p>
          M3 — the Men's Motor Ministry — began with three mates and a shared
          dream:
          <strong
            >to build something, eat something, and talk about something that
            matters.</strong
          >
          Every Thursday night at 6pm, men gather at the church manse for
          exactly that.
        </p>
        <p>
          The evening opens with dinner and a discussion — Scripture, life,
          work, faith in the day-to-day. Then it's into the workshop, where the
          current project is waiting. M3's signature build is the electric eskie
          racer — a church-built, community-enjoyed vehicle that's become
          something of a local legend.
        </p>

        <blockquote>
          <p>
            "Nothing beats the buzz of electric innovation after a hard day's
            work on a Thursday."
          </p>
        </blockquote>

        <p>
          What makes M3 work isn't the project — it's the people. Men who
          wouldn't necessarily describe themselves as church types find that
          working alongside each other with their hands creates space for real
          conversation.
          <strong>Faith, doubt, family, pressure, purpose</strong> — all of it
          comes out in the workshop.
        </p>
        <p>
          Whether you're a seasoned mechanic, a complete beginner, or just
          someone who wants a decent feed and good company on a Thursday night —
          you're welcome to join the circuit.
        </p>
        <a
          href="mailto:admin@queanbeyanpc.org?subject=M3%20Ministry%20Enquiry&body=Hi%2C%0A%0AI%20found%20M3%20on%20the%20QPC%20website%20and%20would%20like%20to%20find%20out%20more.%0A%0A"
          class="btn-primary"
          >⚡ Contact Robert to Join</a
        >
      </div>
      <div class="about-aside">
        <div class="aside-card">
          <div class="aside-card-label">Current Project</div>
          <div class="aside-card-value">The Eskie Racer</div>
          <div class="aside-card-sub">
            Electric-powered, workshop-built, 100% M3.
          </div>
        </div>
        <div class="aside-card">
          <div class="aside-card-label">The Format</div>
          <div class="aside-card-value">Dinner → Discussion → Workshop</div>
          <div class="aside-card-sub">
            Every Thursday from 6pm at the Church Manse.
          </div>
        </div>
        <div class="aside-card">
          <div class="aside-card-label">Who's It For</div>
          <div class="aside-card-value">Any man who wants to show up</div>
          <div class="aside-card-sub">
            No experience required. Appetite helps.
          </div>
        </div>
      </div>
    </section>

    <!-- GALLERY -->
    <section class="gallery-section">
      <div class="gallery-inner">
        <div class="gallery-header">
          <h2 class="gallery-title">From the Workshop</h2>
          <div class="gallery-filter" id="galleryFilter">
            <button class="gallery-chip active" data-filter="all">All</button>
            <button class="gallery-chip" data-filter="eskie">
              Eskie Racer
            </button>
            <button class="gallery-chip" data-filter="dinner">
              Dinner &amp; Discussion
            </button>
            <button class="gallery-chip" data-filter="workshop">
              Workshop
            </button>
          </div>
        </div>

        <!-- ─────────────────────────────────────────────────────────
         PHOTO GRID
         To add a photo:
           1. Add the image to images/m3/
           2. Copy any .photo-tile below and update:
              - data-category: "eskie" | "dinner" | "workshop"
              - img src, alt
              - cap-title and cap-date

         To make a tile wider: add class "wide" (spans 2 columns)
         To make a tile taller: add class "tall" (spans 2 rows)
         ───────────────────────────────────────────────────────── -->
        <div class="photo-grid" id="photoGrid">
          <!-- PLACEHOLDER TILES — replace with real photos as they come in -->

          <div class="photo-tile wide" data-category="eskie">
            <img
              src="images/ministries/m3.jpg"
              alt="Building the eskie racer"
              loading="lazy"
            />

            <div class="photo-tile-overlay"></div>
            <div class="photo-tile-caption">
              <div class="cap-title">The Eskie Racer — Build in Progress</div>
              <div class="cap-date">Add date</div>
            </div>
          </div>

          <div class="photo-tile" data-category="workshop">
            <div class="photo-placeholder">
              <span>⚡</span>
              <p>Workshop photo</p>
            </div>
            <!-- <img src="images/m3/workshop-1.jpg" alt="Workshop session" loading="lazy"> -->
            <div class="photo-tile-overlay"></div>
            <div class="photo-tile-caption">
              <div class="cap-title">Thusday Night in the Workshop</div>
              <div class="cap-date">Add date</div>
            </div>
          </div>

          <div class="photo-tile" data-category="dinner">
            <div class="photo-placeholder">
              <span>🍽️</span>
              <p>Dinner & discussion photo</p>
            </div>
            <!-- <img src="images/m3/dinner-1.jpg" alt="Thursday night dinner" loading="lazy"> -->
            <div class="photo-tile-overlay"></div>
            <div class="photo-tile-caption">
              <div class="cap-title">Thursday Night Dinner</div>
              <div class="cap-date">Add date</div>
            </div>
          </div>

          <div class="photo-tile" data-category="eskie">
            <div class="photo-placeholder">
              <span>🏎️</span>
              <p>Eskie racer photo</p>
            </div>
            <!-- <img src="images/m3/eskie-2.jpg" alt="Eskie racer" loading="lazy"> -->
            <div class="photo-tile-overlay"></div>
            <div class="photo-tile-caption">
              <div class="cap-title">The Original Eskie Racer</div>
              <div class="cap-date">Add date</div>
            </div>
          </div>

          <div class="photo-tile" data-category="workshop">
            <div class="photo-placeholder">
              <span>🛠️</span>
              <p>Workshop photo</p>
            </div>
            <!-- <img src="images/m3/workshop-2.jpg" alt="Workshop" loading="lazy"> -->
            <div class="photo-tile-overlay"></div>
            <div class="photo-tile-caption">
              <div class="cap-title">Workshop Night</div>
              <div class="cap-date">Add date</div>
            </div>
          </div>

          <div class="photo-tile" data-category="dinner">
            <div class="photo-placeholder">
              <span>👥</span>
              <p>Group photo</p>
            </div>
            <!-- <img src="images/m3/group-1.jpg" alt="M3 group" loading="lazy"> -->
            <div class="photo-tile-overlay"></div>
            <div class="photo-tile-caption">
              <div class="cap-title">The M3 Crew</div>
              <div class="cap-date">Add date</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- JOIN STRIP -->
    <section class="join-strip">
      <div class="join-inner">
        <div class="join-left">
          <div class="section-label">Join the Circuit</div>
          <h2 class="section-title">Ready to amp up<br />your Thursdays?</h2>
          <p>
            Drop us a line and Robert will be in touch with details. First
            Thursday is on us — come for dinner, stay for the workshop, see if
            it's for you.
          </p>
          <a
            href="mailto:admin@queanbeyanpc.org?subject=M3%20Ministry%20Enquiry&body=Hi%2C%0A%0AI%20found%20M3%20on%20the%20QPC%20website%20and%20would%20like%20to%20find%20out%20more.%0A%0A"
            class="btn-primary"
            >Get in Touch</a
          >
        </div>
        <div class="join-verse">
          <p>
            "Whatever you do, work heartily, as for the Lord and not for men,
            knowing that from the Lord you will receive the inheritance as your
            reward."
          </p>
          <cite>Colossians 3:23–24 · ESV</cite>
        </div>
      </div>
    </section>

    <!-- LIGHTBOX -->
    <div
      class="lightbox"
      id="lightbox"
      role="dialog"
      aria-modal="true"
      aria-label="Photo viewer"
    >
      <div class="lightbox-inner">
        <button class="lightbox-close" id="lightboxClose">✕ Close</button>
        <button class="lightbox-nav lightbox-prev" id="lightboxPrev">←</button>
        <img class="lightbox-img" id="lightboxImg" src="" alt="" />
        <button class="lightbox-nav lightbox-next" id="lightboxNext">→</button>
        <div class="lightbox-caption">
          <div class="cap-title" id="lightboxTitle"></div>
          <div class="cap-date" id="lightboxDate"></div>
        </div>
        <div class="lightbox-count" id="lightboxCount"></div>
      </div>
    </div>

    <!-- SPOTIFY STRIP -->
    <section class="spotify-strip">
      <div class="spotify-strip-inner">
        <p>
          <strong>Subscribe to the podcast</strong> and get new sermons
          automatically every week — on Spotify, Apple Podcasts, or any podcast
          app.
        </p>
        <a
          href="https://open.spotify.com/show/5XJPM6ftmGcBDK5yanFw9n"
          target="_blank"
          rel="noopener"
          class="btn-spotify"
        >
          <svg class="spotify-icon" viewBox="0 0 24 24">
            <path
              d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"
            />
          </svg>
          Listen on Spotify
        </a>
      </div>
    </section>

    <!-- FOOTER -->
    <?php include 'footer.php'; ?>

    <script>
      // ─────────────────────────────────────────────────────────────────────────────
      // GALLERY FILTER
      // ─────────────────────────────────────────────────────────────────────────────
      document
        .getElementById("galleryFilter")
        .addEventListener("click", (e) => {
          const chip = e.target.closest(".gallery-chip");
          if (!chip) return;
          document
            .querySelectorAll(".gallery-chip")
            .forEach((c) => c.classList.remove("active"));
          chip.classList.add("active");
          const filter = chip.dataset.filter;
          document.querySelectorAll(".photo-tile").forEach((tile) => {
            const match = filter === "all" || tile.dataset.category === filter;
            tile.style.display = match ? "" : "none";
          });
        });

      // ─────────────────────────────────────────────────────────────────────────────
      // LIGHTBOX
      // Only activates on tiles that have a real <img> (not placeholders)
      // ─────────────────────────────────────────────────────────────────────────────
      const lightbox = document.getElementById("lightbox");
      const lightboxImg = document.getElementById("lightboxImg");
      const lightboxTitle = document.getElementById("lightboxTitle");
      const lightboxDate = document.getElementById("lightboxDate");
      const lightboxCount = document.getElementById("lightboxCount");

      let currentIndex = 0;
      let currentTiles = [];

      function getTilesWithImages() {
        return Array.from(document.querySelectorAll(".photo-tile")).filter(
          (t) => t.style.display !== "none" && t.querySelector("img"),
        );
      }

      function openLightbox(tile) {
        currentTiles = getTilesWithImages();
        currentIndex = currentTiles.indexOf(tile);
        if (currentIndex === -1) return;
        showSlide(currentIndex);
        lightbox.classList.add("open");
        document.body.style.overflow = "hidden";
      }

      function showSlide(idx) {
        const tile = currentTiles[idx];
        const img = tile.querySelector("img");
        const title = tile.querySelector(".cap-title")?.textContent || "";
        const date = tile.querySelector(".cap-date")?.textContent || "";
        lightboxImg.src = img.src;
        lightboxImg.alt = img.alt;
        lightboxTitle.textContent = title;
        lightboxDate.textContent = date;
        lightboxCount.textContent = `${idx + 1} / ${currentTiles.length}`;
      }

      function closeLightbox() {
        lightbox.classList.remove("open");
        document.body.style.overflow = "";
      }

      document
        .getElementById("lightboxClose")
        .addEventListener("click", closeLightbox);
      document.getElementById("lightboxPrev").addEventListener("click", () => {
        currentIndex =
          (currentIndex - 1 + currentTiles.length) % currentTiles.length;
        showSlide(currentIndex);
      });
      document.getElementById("lightboxNext").addEventListener("click", () => {
        currentIndex = (currentIndex + 1) % currentTiles.length;
        showSlide(currentIndex);
      });
      lightbox.addEventListener("click", (e) => {
        if (e.target === lightbox) closeLightbox();
      });
      document.addEventListener("keydown", (e) => {
        if (!lightbox.classList.contains("open")) return;
        if (e.key === "Escape") closeLightbox();
        if (e.key === "ArrowLeft") {
          currentIndex =
            (currentIndex - 1 + currentTiles.length) % currentTiles.length;
          showSlide(currentIndex);
        }
        if (e.key === "ArrowRight") {
          currentIndex = (currentIndex + 1) % currentTiles.length;
          showSlide(currentIndex);
        }
      });

      document.querySelectorAll(".photo-tile").forEach((tile) => {
        tile.addEventListener("click", () => {
          if (tile.querySelector("img")) openLightbox(tile);
        });
      });

      // ─────────────────────────────────────────────────────────────────────────────
      // NAV
      // ─────────────────────────────────────────────────────────────────────────────
      const toggle = document.getElementById("navToggle");
      const links = document.getElementById("navLinks");
      const overlay = document.getElementById("navOverlay");
      function openMenu() {
        toggle.classList.add("open");
        links.classList.add("open");
        overlay.classList.add("open");
        document.body.style.overflow = "hidden";
      }
      function closeMenu() {
        toggle.classList.remove("open");
        links.classList.remove("open");
        overlay.classList.remove("open");
        document.body.style.overflow = "";
      }
      toggle.addEventListener("click", () =>
        links.classList.contains("open") ? closeMenu() : openMenu(),
      );
      overlay.addEventListener("click", closeMenu);
      links
        .querySelectorAll("a")
        .forEach((a) => a.addEventListener("click", closeMenu));
    </script>
  </body>
</html>
