#!/usr/bin/env bash
set -euo pipefail

# === SETTINGS ================================================================
APP_DIR="$HOME/higher_calling"

# Prefer PHP 8.3 -> 8.2 -> fallback to "php"
PHP="/opt/cpanel/ea-php83/root/usr/bin/php"
[ -x "$PHP" ] || PHP="/opt/cpanel/ea-php82/root/usr/bin/php"
[ -x "$PHP" ] || PHP="$(command -v php || echo php)"

COMPOSER="/opt/cpanel/composer/bin/composer"   # optional; we skip if missing

# Where to look for build zip (either place works)
BUILD_ZIP_ROOT="$APP_DIR/build.zip"
BUILD_ZIP_PUBLIC="$APP_DIR/public/build/build.zip"

# === HELPERS ================================================================
ts() { date +"%F %T"; }
log() { echo -e "\n[$(ts)] $*"; }
die() { echo -e "\n[ERROR $(ts)] $*" >&2; exit 1; }

# === START ==================================================================
cd "$APP_DIR" || die "Cannot cd into $APP_DIR"

log "Using PHP: $($PHP -v | head -n1)"
if [ -f "$COMPOSER" ]; then
  log "Using Composer: $($PHP $COMPOSER --version || true)"
else
  log "Composer not found at $COMPOSER (skipping composer install)"
fi

# 0) Optional: Composer install IF composer exists
if [ -f "$COMPOSER" ]; then
  log "Installing PHP deps (no-dev)..."
  $PHP $COMPOSER install --no-interaction --no-dev --prefer-dist --optimize-autoloader || {
    log "Composer failed; continuing without (if vendor/ exists, you’re fine)."
  }
fi

# 1) FRONTEND ASSETS from build.zip
install_build () {
  local ZIP="$1"
  log "Installing frontend from: $ZIP"
  local stamp; stamp="$(date +%F_%H-%M-%S)"

  if [ -d "public/build" ]; then
    mv public/build "public/build.bak_${stamp}"
    log "Backed up previous build to public/build.bak_${stamp}"
  fi

  mkdir -p public/build
  if unzip -oq "$ZIP" -d public/build; then
    log "Unzipped into public/build"
  else
    log "Unzip failed; restoring previous build if available"
    rm -rf public/build || true
    local latest
    latest="$(ls -1dt public/build.bak_* 2>/dev/null | head -n1 || true)"
    if [ -n "$latest" ]; then
      mv "$latest" public/build
      log "Restored $latest"
    else
      die "No previous build to restore."
    fi
  fi
  # clean up stray zip if placed under public/build
  [ "$ZIP" = "$BUILD_ZIP_PUBLIC" ] && rm -f "$BUILD_ZIP_PUBLIC" || true
}

if   [ -f "$BUILD_ZIP_ROOT"   ]; then install_build "$BUILD_ZIP_ROOT"
elif [ -f "$BUILD_ZIP_PUBLIC" ]; then install_build "$BUILD_ZIP_PUBLIC"
else
  log "No build.zip found (looked in $BUILD_ZIP_ROOT and $BUILD_ZIP_PUBLIC). Keeping existing assets."
fi

# 2) Remove vite hot marker just in case
[ -f "public/hot" ] && { log "Removing public/hot"; rm -f public/hot; }

# 3) DB MIGRATIONS
log "Running migrations..."
$PHP artisan migrate --force

# 4) CACHES
log "Clearing caches..."
$PHP artisan optimize:clear

log "Rebuilding caches..."
$PHP artisan optimize

# 5) Quick checks
log "Routes (profile/follow/read):"
$PHP artisan route:list | grep -Ei 'profile|follow|read' || true

log "Manifest check:"
if [ -f public/build/manifest.json ]; then
  css_file=$(grep -oE '"assets/app-[^"]+\.css"' public/build/manifest.json | head -n1 | tr -d '"')
  js_file=$(grep -oE '"assets/app-[^"]+\.js"'  public/build/manifest.json | head -n1 | tr -d '"')
  echo "  CSS: ${css_file:-not found}"
  echo "  JS : ${js_file:-not found}"
else
  echo "  manifest.json is missing!"
fi

log "Done."

