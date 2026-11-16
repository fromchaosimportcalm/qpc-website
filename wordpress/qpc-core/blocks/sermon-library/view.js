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
    <div class="episode-top">
      <div class="episode-meta">
        <div class="episode-date">${dateLine}</div>
        <div class="episode-title">${s.title}</div>
        <div class="episode-preacher">${s.preacher || ""}</div>
        ${s.duration ? `<div class="episode-duration">⏱ ${s.duration}</div>` : ""}
      </div>
      <div class="episode-actions">
        ${s.spotifyId || s.audioUrl ? `<button class="btn-play" data-id="${s.spotifyId || s._rssGuid}" data-spotify="${s.spotifyId || ""}" data-audio="${s.audioUrl || ""}">▶ Listen</button>` : ""}
        <button class="btn-transcript" data-id="${s.spotifyId || s._rssGuid}">📄 Summary</button>
      </div>
    </div>
    <p class="episode-desc">${s.description || ""}</p>
    <div class="episode-player" id="player-${s.spotifyId || s._rssGuid}">
      <div class="episode-player-inner"></div>
    </div>
    <div class="episode-transcript" id="transcript-${s.spotifyId || s._rssGuid}">
      <div class="transcript-inner">
        <div class="transcript-label">Sermon Summary</div>
        <div class="transcript-text">
          ${
            hasTranscript
              ? s.transcript
              : `<span class="transcript-placeholder">Summary not yet available for this sermon. Check back soon${s.spotifyId ? `, or <a href="https://open.spotify.com/episode/${s.spotifyId}" target="_blank" rel="noopener" style="color:var(--forest)">listen on Spotify</a>` : ""}.` +
                (s._rssGuid
                  ? `<br><small style="font-size:0.78rem;opacity:0.6">RSS guid: ${s._rssGuid} — add to sermons.json to tag this episode.</small>`
                  : "") +
                `</span>`
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
      function render(seriesConfig, episodes) {
        const wrap = document.getElementById("sermonsWrap");
        const chips = document.getElementById("filterChips");

        // Build chips (keep the existing "All" chip, append series chips)
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

        // Build series sections
        wrap.innerHTML = "";
        seriesConfig.forEach((s) => {
          const group = episodes.filter((e) => e.series === s.id);
          if (!group.length) return;
          const sec = document.createElement("div");
          sec.className = "series-section";
          sec.dataset.series = s.id;
          sec.innerHTML = `
      <div class="series-header">
        <h2 class="series-title">${s.label}</h2>
        <span class="series-tag">${s.tag || "Series"}</span>
        <span class="series-count">${group.length} sermon${group.length !== 1 ? "s" : ""}</span>
      </div>
      <div class="episode-list"></div>
    `;
          group.forEach((ep) =>
            sec.querySelector(".episode-list").appendChild(buildCard(ep)),
          );
          wrap.appendChild(sec);
        });

        updateCount(episodes);
      }

      function updateCount(episodes) {
        const active =
          document.querySelector(".chip.active")?.dataset.series || "all";
        const n =
          active === "all"
            ? episodes.length
            : episodes.filter((e) => e.series === active).length;
        document.getElementById("sermonCount").textContent =
          `${n} sermon${n !== 1 ? "s" : ""}`;
      }

      // ─────────────────────────────────────────────────────────────────────────────
      // FILTERING — delegated so it works after render()
      // ─────────────────────────────────────────────────────────────────────────────
      let _allEpisodes = [];
      document.getElementById("filterChips").addEventListener("click", (e) => {
        const chip = e.target.closest(".chip");
        if (!chip) return;
        document
          .querySelectorAll(".chip")
          .forEach((c) => c.classList.remove("active"));
        chip.classList.add("active");
        const series = chip.dataset.series;
        document.querySelectorAll(".series-section").forEach((sec) => {
          sec.classList.toggle(
            "hidden",
            series !== "all" && sec.dataset.series !== series,
          );
        });
        updateCount(_allEpisodes);
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

        // Normalise series config: always include a "new" bucket for RSS episodes
        // Untagged sermons first (they are the newest), then series by most recent sermon.
        const allSeriesConfig = [
          { id: "new", label: "New Episodes", tag: "Untagged" },
          ...seriesConfig,
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

        _allEpisodes = allEpisodes;
        document.getElementById("loadingMsg")?.remove();
        render(allSeriesConfig, allEpisodes);
      }

      init();
