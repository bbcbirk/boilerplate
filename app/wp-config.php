<?php

/** Docs: https://codex.wordpress.org/Editing_wp-config.php & https://developer.wordpress.org/cli/commands/config/set/ */

/*******************************
 *
 * Set vars
 *
 ******************************/
$config_version = 1;
$app_name       = '__APPNAME';
$app_production = false;
$env            = 'dev';
$https          = true;
$table_prefix   = '__APP_PREFIX_'; // Variable used by WP - so don't rename

/** Multisite */
$is_multisite        = false;
$multisite_subdomain = false;

/** Domains */
$protocol     = $https && $env != 'dev' ? 'https://' : 'http://';
$dev_domain   = "{$app_name}.test";
$live_domain  = $app_name;
$prod_domain  = "{$app_name}.wp.prod.domain.dk";
$stage_domain = "{$app_name}.wp.stage.domain.dk";

/*******************************
 *
 * Set domain depending on environment
 *
 ******************************/
switch ( $env ) {
	case 'prod':
		$domain = $app_production ? $live_domain : $prod_domain;
		break;
	case 'stage':
		$domain = $stage_domain;
		break;
	case 'dev':
		$domain = $dev_domain;
		break;
}

/*******************************
 *
 * Stage and production
 *
 ******************************/

if ( $env == 'stage' || $env == 'prod' ) {
	if ( $https ) {
		$_SERVER['HTTPS'] = 'on';
	}

	/** DB master */
	define( 'DB_NAME', '' );
	define( 'DB_USER', '' );
	define( 'DB_PASSWORD', '' );
	define( 'DB_HOST', '' );

	/** WP config */
	define( 'WP_DEBUG', false );

	// Allow WP CLI file edit
	if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
		define( 'DISALLOW_FILE_MODS', true );
	}
}

/*******************************
 *
 * Local
 *
 ******************************/

if ( $env == 'dev' ) {
	/** DB */
	define( 'DB_NAME', '__APPNAME' );
	define( 'DB_USER', '__USER' );
	define( 'DB_PASSWORD', '__PASS' );
	define( 'DB_HOST', 'mysql' );

	/** Debugging */
	define( 'WP_DEBUG', true );
	define( 'SCRIPT_DEBUG', false );
	define( 'WP_DEBUG_LOG', false );
	define( 'SAVEQUERIES', true );
}

/*******************************
 *
 * Shared
 *
 ******************************/

/** Backwards compat */
define( 'IS_MULTISITE', $is_multisite );

/** Multisite */
if ( $is_multisite ) {
	define( 'WP_ALLOW_MULTISITE', false );
	define( 'MULTISITE', true );
	define( 'SUBDOMAIN_INSTALL', $multisite_subdomain );
	define( 'PATH_CURRENT_SITE', '/' );
	define( 'SITE_ID_CURRENT_SITE', 1 );
	define( 'BLOG_ID_CURRENT_SITE', 1 );
	define( 'DOMAIN_CURRENT_SITE', $domain );
}

/** Cookie domain */
define( 'COOKIE_DOMAIN', false );

/** Custom wp-content dir */
define( 'WP_CONTENT_DIR', __DIR__ . '/wp-content' );

if ( $is_multisite ) {
	define( 'WP_CONTENT_URL', $protocol . $_SERVER['HTTP_HOST'] . '/wp-content' );
} else {
	define( 'WP_CONTENT_URL', $protocol . $domain . '/wp-content' );
}

/** DB */
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

/** Revisions Limit */
define( 'WP_POST_REVISIONS', 100 );

/** Salts */
define('AUTH_KEY',         'Iq>YY2)j`9c:hX-5S_+m>!2M(43|$Hm{miL~H7%Q&yuHcFS)PK=ko*L&k}WIx9|+');
define('SECURE_AUTH_KEY',  '&qIXQ8)>&ktOo$,?z|~e=n4sS@[tlmU|:AN]qa,pK2Ib<B?b;eDi;Y-Z{0pN-U8$');
define('LOGGED_IN_KEY',    'G3-3U@F+1/Bo8|,Ft$+OegAW)x&1zkopdBu~K9uz{<1*EX~rzNcHvm`.Hu>1jt:|');
define('NONCE_KEY',        'W=#-/UUIPotfabch1.=,^>[%*lNKCFvtvw/^P1j@g>ZUB[o[u8K4o)?0+eTv!;bW');
define('AUTH_SALT',        'my`I(O>7sS4R3BA8YOf5y K<i*d~Q:_|LQs[S$@2[W4$hZ;$YEepX-hKx?-uC#3N');
define('SECURE_AUTH_SALT', 'FeV >jYs*W-r`858O:=cUf-.c1J-{43_VF!m(|Tg@?|NN?+Sf=Oflbe,RQN{fjF)');
define('LOGGED_IN_SALT',   'Er9/V)G~,(M8-}pxd&j.IYASj#oPj_Kbz@6?%Q7.vTt/*g{8pPZ#&-+9LC=&wtzw');
define('NONCE_SALT',       'o-W]X*:D|LT)7YnqG];JK2X-V+4n )gec[b+1e#]|[j//.Q>VB5,=OFzBX|#w0c#');

/** Default theme */
define( 'WP_DEFAULT_THEME', $app_name );

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/wp/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
