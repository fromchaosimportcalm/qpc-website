#!/usr/bin/env bash
# Local preview of the WordPress version in WordPress Playground (no PHP/MySQL install needed).
#   bash wordpress/tools/serve.sh      → http://127.0.0.1:9400  (logged in as admin)
set -uo pipefail
cd "$(dirname "$0")/.."
REPO="$(cd .. && pwd)"
# Downloading WordPress occasionally fails ("fetch failed"); retry a few times.
for attempt in 1 2 3; do
npx --yes @wp-playground/cli@3.1.61 server \
  --port=9400 \
  --mount="$PWD/qpc-theme:/wordpress/wp-content/themes/qpc" \
  --mount="$PWD/qpc-core:/wordpress/wp-content/plugins/qpc-core" \
  --mount="$REPO/images:/wordpress/wp-content/qpc-import/images" \
  --blueprint="$PWD/blueprint.json" "$@"
status=$?
[ $status -eq 0 ] && exit 0
echo "Playground exited ($status); retrying ($attempt/3)..." >&2
sleep 3
done
exit 1
