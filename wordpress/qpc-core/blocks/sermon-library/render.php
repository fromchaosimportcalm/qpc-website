<?php
/**
 * Server output for the sermon-library block. The list itself is built in the browser
 * by view.js from the qpc/v1 REST data (same markup and script as the old site).
 */
defined( 'ABSPATH' ) || exit;
qpc_enqueue_block_view( 'sermon-library' );
?>
<div class="filter-bar">
      <div class="filter-bar-inner">
        <span class="filter-label">Series</span>
        <div class="filter-chips" id="filterChips">
          <button class="chip active" data-series="all">All</button>
        </div>
        <span class="sermon-count" id="sermonCount"></span>
      </div>
    </div>
<div class="sermons-wrap" id="sermonsWrap">
      <p         style="
          font-family: &quot;DM Mono&quot;, monospace;
          font-size: 0.7rem;
          letter-spacing: 0.12em;
          text-transform: uppercase;
          color: var(--warm-grey);
          padding: 3rem 0;
        "
        id="loadingMsg"
      >
        Loading sermons…
      </p>
    </div>
