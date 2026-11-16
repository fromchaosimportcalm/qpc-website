// ─────────────────────────────────────────────────────────────────────────────
      // CARD BUILDER
      // ─────────────────────────────────────────────────────────────────────────────
      function formatDate(iso) {
        if (!iso) return "";
        const d = new Date(iso + "T00:00:00");
        if (isNaN(d)) return iso;
        return d.toLocaleDateString("en-AU", {
          day: "numeric",
          month: "long",
          year: "numeric",
        });
      }

      function buildCard(s) {
        const card = document.createElement("div");
        card.className = "episode-card";
        card.dataset.series = s.series;
        card.dataset.id = s.spotifyId;

        const hasTranscript = s.transcript && s.transcript.trim().length > 0;
        const dateLine = [formatDate(s.date), s.scripture]
          .filter(Boolean)
          .join(" · ");

        card.innerHTML = `
    <div class="episode-top${s.image ? " has-art" : ""}">
      ${s.image ? `<img class="episode-art" src="${s.image}" alt="" loading="lazy" width="96" height="96">` : ""}
      <div class="episode-meta">
        <div class="episode-date">${dateLine}</div>
        <div class="episode-title">${s.title}</div>
        <div class="episode-preacher">${[s.preacher, _series[s.series] && s.series !== "new" ? _series[s.series].label : ""].filter(Boolean).join(" · ")}</div>
        ${s.duration ? `<div class="episode-duration">⏱ ${s.duration}</div>` : ""}
      </div>
      <div class="episode-actions">
        ${s.spotifyId || s.audioUrl ? `<button class="btn-play" data-id="${s.spotifyId || s._rssGuid}" data-spotify="${s.spotifyId || ""}" data-audio="${s.audioUrl || ""}">▶ Listen</button>` : ""}
        <button class="btn-transcript" data-id="${s.spotifyId || s._rssGuid}">📄 Summary</button>
      </div>
    </div>
    <div class="episode-player" id="player-${s.spotifyId || s._rssGuid}">
      <div class="episode-player-inner"></div>
    </div>
    <div class="episode-transcript" id="transcript-${s.spotifyId || s._rssGuid}">
      <div class="transcript-inner">
        <div class="transcript-label">Sermon Summary</div>
        <div class="transcript-text">
          ${
            hasTranscript || s.description
              ? (s.description ? `<p>${s.description}</p>` : "") + (hasTranscript ? s.transcript : "")
              : `<span class="transcript-placeholder">Summary not yet available for this sermon. Check back soon${s.spotifyId ? `, or <a href="https://open.spotify.com/episode/${s.spotifyId}" target="_blank" rel="noopener" style="color:var(--forest)">listen on Spotify</a>` : ""}.</span>`
          }
        </div>
      </div>
    </div>
    <div class="episode-foot"></div>
  `;
        return card;
      }

      // ─────────────────────────────────────────────────────────────────────────────
      // RSS HELPERS
      // ─────────────────────────────────────────────────────────────────────────────
      function parseDuration(raw) {
        if (!raw) return "";
        if (raw.includes(":")) {
          const parts = raw.split(":").map(Number);
          const mins = parts.length === 3 ? parts[0] * 60 + parts[1] : parts[0];
          return `${mins} min`;
        }
        const secs = parseInt(raw, 10);
        return isNaN(secs) ? raw : `${Math.round(secs / 60)} min`;
      }

      function textOf(el, selector) {
        return el.querySelector(selector)?.textContent?.trim() || "";
      }

      async function fetchRSS(url) {
        try {
          const res = await fetch(url, { mode: "cors" });
          if (!res.ok) return [];
          const xml = new DOMParser().parseFromString(
            await res.text(),
            "text/xml",
          );
          return Array.from(xml.querySelectorAll("item")).map((item) => {
            const guid = textOf(item, "guid");
            const link = textOf(item, "link") || guid;
            const match = link.match(/episode\/([A-Za-z0-9]+)/);
            const rawDate = textOf(item, "pubDate");
            const isoDate = rawDate
              ? new Date(rawDate).toISOString().slice(0, 10)
              : "";
            return {
              _rssGuid: guid,
              spotifyId: match ? match[1] : "",
              series: "new",
              title: textOf(item, "title"),
              preacher: "",
              date: isoDate,
              duration: parseDuration(textOf(item, "itunes\\:duration")),
              scripture: "",
              description: (
                textOf(item, "itunes\\:summary") || textOf(item, "description")
              )
                .replace(/<[^>]+>/g, "")
                .trim(),
              transcript: "",
            };
          });
        } catch {
          return [];
        }
      }

      // ─────────────────────────────────────────────────────────────────────────────
      // RENDER
      // ─────────────────────────────────────────────────────────────────────────────
      // One list, newest sermon first. The chips (books in biblical order, then any
      // other series, then "Other" for untagged sermons) filter that same list.
      let _series = {};
      function render(seriesConfig, episodes) {
        const wrap = document.getElementById("sermonsWrap");
        const chips = document.getElementById("filterChips");

        _series = {};
        seriesConfig.forEach((s) => (_series[s.id] = s));

        chips
          .querySelectorAll('.chip:not([data-series="all"])')
          .forEach((c) => c.remove());
        seriesConfig.forEach((s) => {
          if (!episodes.some((e) => e.series === s.id)) return;
          const chip = document.createElement("button");
          chip.className = "chip";
          chip.dataset.series = s.id;
          chip.textContent = s.label;
          chips.appendChild(chip);
        });

        wrap.innerHTML = `
      <div class="series-section">
        <div class="series-header">
          <h2 class="series-title" id="sermonListTitle"></h2>
          <span class="series-tag" id="sermonListTag"></span>
          <span class="series-count" id="sermonListCount"></span>
        </div>
        <div class="episode-list"></div>
      </div>`;
        const list = wrap.querySelector(".episode-list");
        [...episodes]
          .sort((a, b) => (b.date || "").localeCompare(a.date || ""))
          .forEach((ep) => list.appendChild(buildCard(ep)));

        applyFilter("all");
      }

      function applyFilter(series) {
        let n = 0;
        document.querySelectorAll("#sermonsWrap .episode-card").forEach((card) => {
          const show = series === "all" || card.dataset.series === series;
          card.classList.toggle("hidden", !show);
          if (show) n++;
        });
        const s = _series[series];
        document.getElementById("sermonListTitle").textContent =
          series === "all" ? "Latest Sermons" : s ? s.label : "";
        document.getElementById("sermonListTag").textContent =
          series === "all" ? "Newest first" : (s && s.tag) || "Series";
        const count = `${n} sermon${n !== 1 ? "s" : ""}`;
        document.getElementById("sermonListCount").textContent = count;
        document.getElementById("sermonCount").textContent = count;
      }

      // ─────────────────────────────────────────────────────────────────────────────
      // FILTERING — delegated so it works after render()
      // ─────────────────────────────────────────────────────────────────────────────
      document.getElementById("filterChips").addEventListener("click", (e) => {
        const chip = e.target.closest(".chip");
        if (!chip) return;
        document
          .querySelectorAll(".chip")
          .forEach((c) => c.classList.remove("active"));
        chip.classList.add("active");
        applyFilter(chip.dataset.series);
      });

      // ─────────────────────────────────────────────────────────────────────────────
      // PLAYER TOGGLE — lazy-load Spotify embed on first click
      // ─────────────────────────────────────────────────────────────────────────────
      document.addEventListener("click", (e) => {
        const playBtn = e.target.closest(".btn-play");
        if (playBtn) {
          const id = playBtn.dataset.id;
          const playerEl = document.getElementById(`player-${id}`);
          const inner = playerEl.querySelector(".episode-player-inner");
          const isOpen = playerEl.classList.contains("open");

          document.querySelectorAll(".episode-player.open").forEach((p) => {
            if (p !== playerEl) {
              p.querySelector("audio")?.pause();
              p.classList.remove("open");
              const otherId = p.id.replace("player-", "");
              const b = document.querySelector(
                `.btn-play[data-id="${otherId}"]`,
              );
              if (b) b.textContent = "▶ Listen";
            }
          });

          if (isOpen) {
            playerEl.querySelector("audio")?.pause();
            playerEl.classList.remove("open");
            playBtn.textContent = "▶ Listen";
          } else {
            if (!inner.querySelector("iframe, audio") && !playBtn.dataset.spotify) {
              // No Spotify link: play the podcast MP3 directly.
              inner.innerHTML = `<audio controls autoplay preload="none" style="width:100%" src="${playBtn.dataset.audio}"></audio>`;
            }
            if (!inner.querySelector("iframe, audio")) {
              inner.innerHTML = `<iframe
          src="https://open.spotify.com/embed/episode/${playBtn.dataset.spotify}?utm_source=generator&theme=0"
          width="100%" height="152" frameborder="0"
          allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
          loading="lazy">
        </iframe>`;
            }
            playerEl.classList.add("open");
            playBtn.textContent = "■ Close";
          }
          return;
        }

        const transcriptBtn = e.target.closest(".btn-transcript");
        if (transcriptBtn) {
          const id = transcriptBtn.dataset.id;
          const transcriptEl = document.getElementById(`transcript-${id}`);
          const isOpen = transcriptEl.classList.contains("open");
          transcriptEl.classList.toggle("open", !isOpen);
          transcriptBtn.textContent = isOpen ? "📄 Summary" : "✕ Close";
          return;
        }
      });

      // ─────────────────────────────────────────────────────────────────────────────
      // INIT — load JSON, optionally merge RSS
      // ─────────────────────────────────────────────────────────────────────────────
      async function init() {
        let data;
        try {
          const res = await fetch(window.qpcSermons.url);
          if (!res.ok) throw new Error(res.status);
          data = await res.json();
        } catch (err) {
          document.getElementById("loadingMsg").textContent =
            "Could not load sermons — please try again later.";
          console.error("Sermon list fetch failed:", err);
          return;
        }

        const { series: seriesConfig, episodes: jsonEpisodes, rssUrl } = data;

        // Untagged sermons (no Bible-book series) are grouped under "Other", last.
        const allSeriesConfig = [
          ...seriesConfig,
          { id: "new", label: "Other", tag: "Not in a series" },
        ];

        let allEpisodes = [...jsonEpisodes];

        if (rssUrl) {
          const rssEpisodes = await fetchRSS(rssUrl);
          if (rssEpisodes.length) {
            const knownIds = new Set(
              jsonEpisodes.map((e) => e.spotifyId).filter(Boolean),
            );
            const knownGuids = new Set(
              jsonEpisodes.map((e) => e._rssGuid).filter(Boolean),
            );
            const novel = rssEpisodes.filter(
              (e) =>
                !(e.spotifyId && knownIds.has(e.spotifyId)) &&
                !(e._rssGuid && knownGuids.has(e._rssGuid)),
            );
            allEpisodes = [...allEpisodes, ...novel];
          }
        }

        document.getElementById("loadingMsg")?.remove();
        render(allSeriesConfig, allEpisodes);
      }

      init();
