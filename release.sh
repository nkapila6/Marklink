#!/usr/bin/env bash
set -euo pipefail

PLUGIN_DIR="marklink"
VERSION=$(grep "Version:" "$PLUGIN_DIR/marklink.php" | head -1 | sed 's/.*Version:[[:space:]]*//' | tr -d '[:space:]')
OUTPUT="marklink-${VERSION}.zip"

if [ -f "$OUTPUT" ]; then
    rm "$OUTPUT"
fi

zip -r "$OUTPUT" "$PLUGIN_DIR" \
    -x "*/.DS_Store" \
    -x "*/.gitkeep" \
    -x "*.DS_Store"

echo "Created $OUTPUT"
unzip -l "$OUTPUT"
