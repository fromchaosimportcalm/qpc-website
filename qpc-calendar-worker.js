/**
 * QPC Calendar Proxy — Cloudflare Worker
 *
 * Fetches the iCloud public calendar ICS feed and returns it
 * with CORS headers so the calendar page can read it from the browser.
 *
 * Deploy to: Cloudflare Workers (free tier)
 * Worker name: qpc-calendar-proxy (or anything you like)
 *
 * After deploying, your Worker URL will be:
 *   https://qpc-calendar-proxy.YOUR-SUBDOMAIN.workers.dev
 *
 * Paste that URL into calendar.html where indicated.
 */

const ICS_URL = 'https://p69-caldav.icloud.com/published/2/MTM0OTU4Njg0ODEzNDk1OAiKAMI78sHP0UEgYBo74ceckrXowy2-fiJvsH_5i6YL8bt9A16z_rVJrL1OG3ip5Zc7DI0b1b_ukcWJ5iMtMsg';

const CORS_HEADERS = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Methods': 'GET, OPTIONS',
  'Access-Control-Allow-Headers': 'Content-Type',
};

export default {
  async fetch(request) {
    // Handle preflight
    if (request.method === 'OPTIONS') {
      return new Response(null, { headers: CORS_HEADERS });
    }

    try {
      const response = await fetch(ICS_URL, {
        headers: {
          'User-Agent': 'QPC-Calendar/1.0',
          'Accept': 'text/calendar',
        },
        // Cache for 30 minutes — balances freshness with request limits
        cf: { cacheTtl: 1800, cacheEverything: true }
      });

      if (!response.ok) {
        return new Response(`Failed to fetch calendar: ${response.status}`, {
          status: 502,
          headers: CORS_HEADERS
        });
      }

      const icsText = await response.text();

      return new Response(icsText, {
        status: 200,
        headers: {
          ...CORS_HEADERS,
          'Content-Type': 'text/calendar; charset=utf-8',
          'Cache-Control': 'public, max-age=1800', // 30 min browser cache
        }
      });

    } catch (err) {
      return new Response(`Worker error: ${err.message}`, {
        status: 500,
        headers: CORS_HEADERS
      });
    }
  }
};
