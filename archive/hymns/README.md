# Hymn Archive (archived)

The hymn archive page that was built for the old static site but never linked
from it. It is **not** part of the WordPress site; it's kept here for reference
and for Haven.

- `index.html`: the page (search, A–Z, lyrics, piano recordings). It loads
  `hymns.json` from the same folder, so open it from a web server rather than
  as a file, e.g. `python3 -m http.server` in this folder.
- `hymns.json`: 352 hymns with verses, key, tags and the recording filename.
- Recordings are streamed from a Cloudflare R2 bucket
  (`pub-b35d5e363bc845b3949a0c406b4aad7f.r2.dev`). If that bucket is moved or
  closed, update `AUDIO_BASE_URL` at the top of the script in `index.html`.

Some lyrics are copyright (e.g. Sovereign Grace Music); keep any published copy
internal.
