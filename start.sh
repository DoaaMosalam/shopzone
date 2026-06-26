#!/bin/bash
set -e

DIR="$(cd "$(dirname "$0")" && pwd)"

mkdir -p "$DIR/storage/uploads/users"
mkdir -p "$DIR/storage/uploads/products"

PORT="${PORT:-23726}"
echo "[ShopZone] Starting PHP server on port $PORT..."
exec php -S "0.0.0.0:$PORT" -t "$DIR/public" "$DIR/public/router.php"
