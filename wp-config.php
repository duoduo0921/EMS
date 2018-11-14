<?php
# Database Configuration
define( 'DB_NAME', 'wp_webstaffgsd' );
define( 'DB_USER', 'webstaffgsd' );
define( 'DB_PASSWORD', '79kPkd3iiBF6gM426h1S' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_HOST_SLAVE', '127.0.0.1' );

define( 'DB_CHARSET', 'utf8' );
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wp_';

# Security Salts, Keys, Etc
define('AUTH_KEY',         'VdS;7D<NM;9YdFo.0uJODfAIz`ZVKBuUn:U!.l{YF`v+f5kr-KU]+FPv)iw[,c*o');
define('SECURE_AUTH_KEY',  'aSzS^fr_N*91g*gi9((ByY7Oe_,^$1mK+>jCTs+tbF^ aevWps|*9_A31_3n m,?');
define('LOGGED_IN_KEY',    'IM93e;A_{EmK`?uB8SQTrj&r;[QK%&+^|YgXO^G`{7yI|kuEj-BDe$vzxx7>:4p{');
define('NONCE_KEY',        'V/xi5BT$m~uZv*-}n9e6zhB|fMM|T#@pot+CS/X$@O/!RU7`{G6BuRxIsAkK1sS!');
define('AUTH_SALT',        'B-zJ)&/WF-en`?A%Z8N:(ovd<nP~,1angG3#a#N}4v<J(WSOVgZYXtBkSIn%L.<5');
define('SECURE_AUTH_SALT', 'Z+bJ(y?k?!RNus:ms{XPH6V|jt-&_Z+;3f&M3?!w4<e6o`.PfZkvU]W&RoN;p7P_');
define('LOGGED_IN_SALT',   ':e<.}KI ;(^`&:A3WdSuCu{Usp.x%rC&r#ysxB7IcCJivoFD-B;95yj%J^?b6x2&');
define('NONCE_SALT',       '-qv#jU.*lb|/=Xe23,uSV|}8OAniSdKfQ+QMw07}S``8^37T`VkQX=E!ya-+w5r+');


# Localized Language Stuff

define( 'WP_CACHE', TRUE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'PWP_NAME', 'webstaffgsd' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

define( 'PWP_ROOT_DIR', '/nas/wp' );

define( 'WPE_APIKEY', '0692b6e4718456b217f762b6f9f50e0c56f16056' );

define( 'WPE_CLUSTER_ID', '100005' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_SFTP_PORT', 2222 );

define( 'WPE_LBMASTER_IP', '' );

define( 'WPE_CDN_DISABLE_ALLOWED', false );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISALLOW_FILE_EDIT', FALSE );

	
define('DB_COLLATE', '');

define('DISABLE_WP_CRON', true);

define( 'WPE_FORCE_SSL_LOGIN', true );

define( 'FORCE_SSL_LOGIN', true );

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', 3 );

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

//turn on cron in wp
//define('ALTERNATE_WP_CRON', true);

umask(0002);

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'webstaff.gsd.harvard.edu', 1 => 'webstaffgsd.wpengine.com', );

$wpe_varnish_servers=array ( 0 => 'pod-100005', );

$wpe_special_ips=array ( 0 => '104.197.167.140', );

$wpe_ec_servers=array ( );

$wpe_largefs=array ( );

$wpe_netdna_domains=array ( );

$wpe_netdna_domains_secure=array ( );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( );


# WP Engine ID


# WP Engine Settings






define('WP_DEBUG', false);
//define('WP_CONTENT_DIR', '/nas/content/live/alainacrgblog/crg/wp-content');
//define('WP_CONTENT_URL', 'http://alainacrgblog.wpengine.com/crg/wp-content');

# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');

$_wpe_preamble_path = null; if(false){}
