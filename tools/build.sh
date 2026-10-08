#!/bin/sh
# Pravi minifikovane fajlove (*.min.css / *.min.js) koje tema učitava kad SCRIPT_DEBUG nije uključen.
# Zahteva Node.js (esbuild se preuzima preko npx). Pokreće se iz foldera teme: sh tools/build.sh
set -e
cd "$(dirname "$0")/.."
for f in critical main; do npx --yes esbuild "assets/css/$f.css" --minify --outfile="assets/css/$f.min.css" --log-level=warning; done
for f in core motion map contact; do npx --yes esbuild "assets/js/$f.js" --minify --target=es2018 --outfile="assets/js/$f.min.js" --log-level=warning; done
echo "gotovo"
