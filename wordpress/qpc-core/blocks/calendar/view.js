// ─────────────────────────────────────────────────────────────────────────────
// CONFIGURATION
// The calendar feed address is set in Settings → Church Site. WordPress
// fetches and caches it (REST route qpc/v1/calendar); this script just reads it.
// ─────────────────────────────────────────────────────────────────────────────
const QPC_CAL = window.qpcCalendar || {};
const PROXY_URL = QPC_CAL.url || '';
const WEBCAL_URL = QPC_CAL.webcal || '';

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
  // Hide events that have already finished; ones under way show "On now".
  const now = new Date();
  const finish = ev => {
    if (ev.end) return ev.end;
    const d = new Date(ev.start);
    if (ev.allDay) d.setDate(d.getDate() + 1); else d.setHours(d.getHours() + 1);
    return d;
  };
  const upcoming = allEvents.filter(ev => finish(ev) > now).slice(0, 8);

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
      <div class="event-item-time">${ev.allDay ? 'All day' : (ev.start <= now ? 'On now' : fmtTime(ev.start))}${ev.location ? ' · ' + ev.location : ''}</div>
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
  if (!PROXY_URL || !(window.qpcCalendar && window.qpcCalendar.configured)) {
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
      <p><strong>Calendar not yet connected.</strong></p>
      <p style="margin-top:0.5rem;">The calendar feed address has not been set yet. An administrator can add it under Settings → Church Site.</p>
      <p style="margin-top:0.5rem;">In the meantime, use the subscribe links above to add the calendar to your own app.</p>
    </div>`;
  document.getElementById('upcomingList').innerHTML = `
    <p style="font-size:0.9rem;color:var(--warm-grey);">Use the subscribe links in the page header to add this calendar to your device.</p>`;
  document.getElementById('monthTitle').textContent = 'Calendar';
}

init();
