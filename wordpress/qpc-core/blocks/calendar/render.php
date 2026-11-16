<?php
/**
 * Server output for the calendar block. The list itself is built in the browser
 * by view.js from the qpc/v1 REST data (same markup and script as the old site).
 */
defined( 'ABSPATH' ) || exit;
qpc_enqueue_block_view( 'calendar' );
?>
<div class="calendar-wrap">

  
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

  
  <aside class="sidebar">

    
    <div class="event-detail" id="eventDetail">
      <div class="event-detail-label">Event</div>
      <div class="event-detail-title" id="detailTitle"></div>
      <div class="event-detail-meta" id="detailMeta"></div>
      <div class="event-detail-desc" id="detailDesc"></div>
      <div class="add-to-cal" id="detailAddCal"></div>
      <button class="event-detail-close" id="detailClose">✕ Close</button>
    </div>

    
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
