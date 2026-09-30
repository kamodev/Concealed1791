#!/usr/bin/env bash
# Spin up a WordPress demo site for this theme, with demo content.
#
# Uses PHP's built-in server and SQLite, so no MySQL or Docker is needed.
# Requirements: php 7.4+ (with pdo_sqlite), git, curl, unzip.
#
#   bin/demo-server.sh                          # http://localhost:8080 (admin / admin)
#   PORT=9000 bin/demo-server.sh                # different port
#   HOST=0.0.0.0 SITE_URL=http://192.168.1.20:8080 bin/demo-server.sh   # reachable from your network
#   WITH_WOOCOMMERCE=0 bin/demo-server.sh       # skip WooCommerce
#   PLUGINS="elementor" bin/demo-server.sh      # wordpress.org plugins to add (default below)
#   RESET=1 bin/demo-server.sh                  # delete the demo site and start over
#   WP_DIR=/tmp/c1791-demo bin/demo-server.sh
#
# WooCommerce comes from its GitHub release. Elementor, Amelia (Lite) and
# FunnelKit come from wordpress.org; if that can't be reached they are
# skipped with a warning. MailPoet needs MySQL/MariaDB, so it isn't installed
# here; test it on a MySQL site (or in the WordPress Playground demo, see README).
set -euo pipefail

THEME_DIR="$(cd "$(dirname "$0")/.." && pwd)"
THEME_SLUG="concealed1791"
# An absolute path without "..": WordPress refuses to create folders under paths containing "../".
WP_DIR="${WP_DIR:-$(cd "$THEME_DIR/.." && pwd)/concealed1791-demo}"
mkdir -p "$WP_DIR"
WP_DIR="$(cd "$WP_DIR" && pwd)"
HOST="${HOST:-localhost}"
PORT="${PORT:-8080}"
SITE_URL="${SITE_URL:-http://localhost:$PORT}"
WP_VERSION="${WP_VERSION:-7.1.2}"
SQLITE_VERSION="${SQLITE_VERSION:-v2.2.3}"
WPCLI_VERSION="${WPCLI_VERSION:-2.12.0}"
WITH_WOOCOMMERCE="${WITH_WOOCOMMERCE:-1}"
WC_VERSION="${WC_VERSION:-11.1.2}"
PLUGINS="${PLUGINS:-elementor ameliabooking funnel-builder}"

if [ "${RESET:-0}" = "1" ] && [ -d "$WP_DIR" ]; then
	echo "Removing $WP_DIR ..."
	rm -rf "$WP_DIR"
fi

WP_CLI="$WP_DIR/wp-cli.phar"
wp() { php -d memory_limit=512M "$WP_CLI" --path="$WP_DIR/wp" --allow-root "$@"; }

php -m | grep -qi pdo_sqlite || { echo "PHP's pdo_sqlite extension is required." >&2; exit 1; }

if [ ! -f "$WP_DIR/wp/wp-config.php" ]; then
	echo "Installing WordPress $WP_VERSION into $WP_DIR ..."
	mkdir -p "$WP_DIR"
	git -c advice.detachedHead=false clone -q --depth 1 --branch "$WP_VERSION" https://github.com/WordPress/WordPress.git "$WP_DIR/wp"
	git -c advice.detachedHead=false clone -q --depth 1 --branch "$SQLITE_VERSION" https://github.com/WordPress/sqlite-database-integration.git \
		"$WP_DIR/wp/wp-content/plugins/sqlite-database-integration"
	curl -fsSL -o "$WP_CLI" "https://github.com/wp-cli/wp-cli/releases/download/v$WPCLI_VERSION/wp-cli-$WPCLI_VERSION.phar"

	if [ "$WITH_WOOCOMMERCE" = "1" ]; then
		echo "Downloading WooCommerce $WC_VERSION ..."
		curl -fsSL -o "$WP_DIR/woocommerce.zip" "https://github.com/woocommerce/woocommerce/releases/download/$WC_VERSION/woocommerce.zip"
		unzip -q -o "$WP_DIR/woocommerce.zip" -d "$WP_DIR/wp/wp-content/plugins/"
		rm -f "$WP_DIR/woocommerce.zip"
	fi

	SQLITE_DIR="$WP_DIR/wp/wp-content/plugins/sqlite-database-integration"
	sed -e "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#$SQLITE_DIR#" \
		-e "s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#" \
		"$SQLITE_DIR/db.copy" > "$WP_DIR/wp/wp-content/db.php"

	# The site URL comes from C1791_SITE_URL at runtime, so it can change without a reinstall.
	cat > "$WP_DIR/wp/wp-config.php" <<EOF
<?php
define( 'DB_NAME', 'wp' ); define( 'DB_USER', '' ); define( 'DB_PASSWORD', '' ); define( 'DB_HOST', '' );
define( 'DB_CHARSET', 'utf8' ); define( 'DB_COLLATE', '' );
define( 'DB_DIR', __DIR__ . '/wp-content/database/' ); define( 'DB_FILE', '.ht.sqlite' );
define( 'AUTH_KEY', 'c1791-demo' ); define( 'SECURE_AUTH_KEY', 'c1791-demo' ); define( 'LOGGED_IN_KEY', 'c1791-demo' ); define( 'NONCE_KEY', 'c1791-demo' );
define( 'AUTH_SALT', 'c1791-demo' ); define( 'SECURE_AUTH_SALT', 'c1791-demo' ); define( 'LOGGED_IN_SALT', 'c1791-demo' ); define( 'NONCE_SALT', 'c1791-demo' );
\$table_prefix = 'wp_';
define( 'WP_DEBUG', true ); define( 'WP_DEBUG_LOG', true ); define( 'WP_DEBUG_DISPLAY', false );
\$c1791_url = getenv( 'C1791_SITE_URL' ) ? rtrim( getenv( 'C1791_SITE_URL' ), '/' ) : '$SITE_URL';
define( 'WP_HOME', \$c1791_url ); define( 'WP_SITEURL', \$c1791_url );
define( 'WP_ENVIRONMENT_TYPE', 'local' );
if ( ! defined( 'ABSPATH' ) ) define( 'ABSPATH', __DIR__ . '/' );
require_once ABSPATH . 'wp-settings.php';
EOF

	ln -sfn "$THEME_DIR" "$WP_DIR/wp/wp-content/themes/$THEME_SLUG"

	# WooCommerce's background queue (Action Scheduler) uses MySQL-only SQL that the
	# SQLite layer can't run; keep it from firing on this demo server.
	mkdir -p "$WP_DIR/wp/wp-content/mu-plugins"
	cat > "$WP_DIR/wp/wp-content/mu-plugins/c1791-demo-sqlite.php" <<'EOF'
<?php
/**
 * Plugin Name: Concealed 1791 demo: SQLite tweaks
 * Description: Turns off WooCommerce's async background queue, which doesn't run on SQLite. Demo server only.
 */
add_filter( 'action_scheduler_allow_async_request_runner', '__return_false' );
add_filter( 'action_scheduler_queue_runner_concurrent_batches', '__return_zero' );
EOF

	export C1791_SITE_URL="$SITE_URL"
	wp core install --url="$SITE_URL" --title="A & A Tactical" \
		--admin_user=admin --admin_password=admin --admin_email=admin@example.com --skip-email
	wp option update blogdescription "Concealed carry & defensive training in Broomfield, Colorado"
	wp option update timezone_string "America/Denver"

	if [ "$WITH_WOOCOMMERCE" = "1" ]; then
		wp plugin activate woocommerce
		# New stores start in WooCommerce's "Coming soon" mode; open the demo store.
		wp option update woocommerce_coming_soon no
		wp option update woocommerce_currency USD
		wp option update woocommerce_default_country "US:CO"
	fi

	for plugin in $PLUGINS; do
		if wp plugin install "$plugin" --activate >/dev/null 2>&1; then
			echo "Installed $plugin from wordpress.org."
		else
			echo "Warning: couldn't install $plugin from wordpress.org (no access?). Skipping it." >&2
		fi
	done

	wp theme activate "$THEME_SLUG"
	wp rewrite structure '/%postname%/'
	wp post delete 1 --force >/dev/null 2>&1 || true
	wp eval-file "$THEME_DIR/bin/seed-demo.php"
fi

# Router: serves real files and sends everything else to WordPress.
cat > "$WP_DIR/router.php" <<'EOF'
<?php
$root = __DIR__ . '/wp';
$uri  = urldecode( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
$file = $root . $uri;
if ( is_dir( $file ) ) {
	$uri  = rtrim( $uri, '/' ) . '/index.php';
	$file = $root . $uri;
}
if ( is_file( $file ) ) {
	if ( '.php' !== substr( $file, -4 ) ) {
		return false;
	}
	$_SERVER['SCRIPT_NAME']     = $uri;
	$_SERVER['PHP_SELF']        = $uri;
	$_SERVER['SCRIPT_FILENAME'] = $file;
	chdir( dirname( $file ) );
	require $file;
	return true;
}
$_SERVER['SCRIPT_NAME']     = '/index.php';
$_SERVER['PHP_SELF']        = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';
chdir( $root );
require $root . '/index.php';
EOF

export C1791_SITE_URL="$SITE_URL"
echo "Serving $SITE_URL  (log in at $SITE_URL/wp-admin with admin / admin) — Ctrl+C to stop"
exec php -d memory_limit=512M -S "$HOST:$PORT" -t "$WP_DIR/wp" "$WP_DIR/router.php"
