<?php
/**
 * General configuration
 */

final class Config extends ConfigAbstract {

	// Absolute URL of the website
	const URL_ABSOLUTE	= 'http://localhost/';
	// Absolute URL of the storage dir
	const URL_STORAGE	= '/www/data/storage/';
	// Absolute path of the website on the domain
	const URL_ROOT		= '/www/app/';
	// Absolute path for static files
	const URL_STATIC	= '/www/app/static/';

	// Timezone
	const TIMEZONE	= 'Europe/Paris';

	// DB connection
	public static $DB	= array(
		'driver'	=> 'mysql',
		'dsn'		=> 'host=localhost;dbname=iseplive',
		'username'	=> 'root',
		'password'	=> ''
	);

	// LDAP
	public static $LDAP = array (
		'host'		=> 'ldap.isep.fr',
		'port'		=> 636,
		'basedn'	=> 'ou=People,dc=isep.fr'
	);

	// Authentication mode: 'ldap' (ISEP's LDAP) or 'form' (using https://gcma.isep.fr/ form over https)
	const AUTHENTICATION_MODE	= 'ldap';

	// Encryption secret key (for Encryption class)
	const ENCRYPTION_KEY	= 'XrDy2H8Ob8';

	// Directories
	// relative to "app" dir
	const DIR_APP_STATIC	= 'static/';		// Fichiers statics
	// relative to "data" dir
	const DIR_DATA_LOGS		= 'logs/';		// Logs²
	const DIR_DATA_STORAGE	= 'storage/';	// Storage
	const DIR_DATA_TMP		= 'tmp/';		// Temporary files
	const DIR_DATA_ADMIN	= 'admin/';		// Admin functions files

	// Name of the session
	const SESS_ID		= 'PHPSESSID';

	// Cache
	public static $CACHE	= array(
		'driver'	=> 'memcache',
		'prefix'	=> 'iseplive-'
	);

	// ElasticSearch
	public static $ELASTICSEARCH	= array(
		'host'	=> 'localhost',
		'port'	=> 80,
		'index'	=> 'iseplive'
	);

	// Contact name and mail
	const CONTACT_NAME	= 'ISEPLive';
	const CONTACT_MAIL	= 'contact@iseplive.fr';

	// SMTP server
	const SMTP_HOST		= 'smtp.iseplive.fr';

	// Google Analytics tracker ID
	const GA_TRACKER	= 'UA-2659605-1';

	// Languages
	public static $LOCALES = array("fr_FR");

	// Thumbs sizes
	public static $THUMBS_SIZES = array(100, 100);
	
	// Mobiles sizes
	public static $MOBILE_SIZES = array(500, 500);
        

	// Avatars' thumbs sizes
	public static $AVATARS_THUMBS_SIZES = array(70, 70);
	
			        // Number of displayed like
        const DISLIKE_DISPLAYED = 2;

	// Max uploaded files sizes
	const UPLOAD_MAX_SIZE_PHOTO = 2097152;
	const UPLOAD_MAX_SIZE_VIDEO = 629145600;
	const UPLOAD_MAX_SIZE_AUDIO = 20971520;
	const UPLOAD_MAX_SIZE_FILE = 10485760;

	// Max width for videos
	const VIDEO_MAX_WIDTH = 480;
	const VIDEO_SAMPLING_RATE = 44100;
	const VIDEO_AUDIO_BIT_RATE = 64;

	// Number of displayed posts
	const POST_DISPLAYED = 10;
        
        // Number of displayed like
        const LIKE_DISPLAYED = 2;

	// Number of displayed photos per post in the timeline
	const PHOTOS_PER_POST = 3;

	// Max number of comments displayed by default
	const COMMENTS_PER_POST = 5;

	// Galleries
	const GALLERY_COLS = 5;
	const GALLERY_ROWS = 8;

	// Debug mode
	const DEBUG			= true;

}


// PHPVideoToolkit constants
define('PHPVIDEOTOOLKIT_TEMP_DIRECTORY', 'D:\wamp\tmp\\');
define('PHPVIDEOTOOLKIT_FFMPEG_BINARY', '%FFMPEG_HOME%\ffmpeg');
define('PHPVIDEOTOOLKIT_FFPROBE_BINARY', '%FFMPEG_HOME%\ffprobe');
define('PHPVIDEOTOOLKIT_FLVTOOLS_BINARY', '/usr/bin/flvtool2');