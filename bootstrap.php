<?php

  // *fac.es bootstrap script

  $localname = 'localhost:8888';
  $lanname = '192.168.0.100:8888';
  $localaddr = '::1';

  define( 'webroot', dirname( __FILE__ ));

  // scan the sites folder for installed sites
  // ignores hidden files, so sites can be disabled by
  // prepending a . to their folder
  $sites = array_values( preg_grep('/^([^.])/', scandir(webroot.'/sites')));

  // determine which config to load

  // localhost
  if( $_SERVER['HTTP_HOST'] == $localname or $_SERVER['HTTP_HOST'] == $lanname or $_SERVER['SERVER_ADDR'] == $localaddr ) {

    // determine which site to load

    // site switcher was used
    if( isset( $_POST['_SITE'] )) {
      $_SITE = $_POST['_SITE'];
      setcookie('site', $_POST['_SITE'], time()+60*60*24*30);
    }
    else if( isset( $_COOKIE['site'] )) {
      $_SITE = $_COOKIE['site'];
    }
    else {
      $_SITE = 'squid'; //$sites[0];
    }

    // load site config
    require_once( webroot.'/sites/'.$_SITE.'/config-'.$_SITE.'.php' );

    //$_CONFIG['app']['baseurl'] = 'http://'.$lanname.'/Web/fac.es';
  }

  // not localhost
  else {
    $_SITE = trim( substr($_SERVER['SERVER_NAME'], 0, -6), 'm.' );
    require_once( 'sites/'.$_SITE.'/config-'.$_SITE.'.php' );
  }

  // autoloader
  class Loader {

    static public function init() {
      $loaders = array('autoload_a','autoload_b');
      foreach( $loaders as $l ) {
        self::register( $l );
      }
    }

    static public function register( $loader ) {
      spl_autoload_register( array( 'Loader', $loader ) );
    }

    static private function autoload_a( $class ) {
      if( file_exists( 'classes/'.$class.'.php' )) {
        require_once( 'classes/'.$class.'.php' );
      }
    }

    static private function autoload_b( $class ) {
      if( file_exists( '../classes/'.$class.'.php' )) {
        require_once( '../classes/'.$class.'.php' );
      }
    }
  }

  Loader::init();

  // init some time-related stuff
  date_default_timezone_set('Europe/Berlin');
  define('NOW', time());

  // init db
  $db = @mysql_connect($DBHOST, $DBUSER, $DBPASS); // or die( 'trying' );
  mysql_select_db($DBNAME,$db);
  mysql_query( "SET NAMES 'utf8'", $db );

  // init config from DB
  $conf = new Config();

  if( !defined('NO_DISPATCH')) {

    if( (int)$conf->maintenance === 1 or (int)$conf->protest === 1 ) {
      $p = pathinfo($_SERVER['PHP_SELF'] );
      $d = substr( $p['dirname'], -5 );

      session_start();
      $hasOverride = (isset($_SESSION['admin_override'])) ? $_SESSION['admin_override'] : false;

      if( $d != 'admin' and !$hasOverride ) {
        if( (int)$conf->protest === 1 ) $jumpTo = 'protest';
        if( (int)$conf->maintenance === 1 ) $jumpTo = 'maintenance';
        header('location: '.$_CONFIG['app']['baseurl'].'/'.$jumpTo );
      }
    }
  }

  // some functions

  function getStats() {
    global $db;
    $sql = 'SELECT COUNT(id) AS total_faces, SUM(views) AS total_views FROM faces WHERE enabled=1';
    $rs = mysql_query( $sql, $db );
    $stats = mysql_fetch_assoc( $rs );
    return $stats;
  }

  function sortPopularity( $a, $b ) {
    if( $a->popularity > $b->popularity ) return -1;
    else if( $a->popularity < $b->popularity ) return 1;
    else return 0;
  }

  function sortAdded( $a, $b ) {
    if( $a->added > $b->added ) return -1;
    else if( $a->added < $b->added ) return 1;
    else return 0;
  }

  function detectOS() {
    $ua = $_SERVER["HTTP_USER_AGENT"];
    // Mobile
    if(strpos($ua, 'iPhone')) return array( 'name' => 'iPhone', 'type' => 'Mobile' );
    if(strpos($ua, 'iPad')) return array( 'name' => 'iPad', 'type' => 'Mobile' );
    if(strpos($ua, 'Android')) return array( 'name' => 'Android', 'type' => 'Mobile' );
    if(strpos($ua, 'BlackBerry')) return array( 'name' => 'BlackBerry', 'type' => 'Mobile' );
    // Desktop
    if(strpos($ua, 'Windows')) return array( 'name' => 'Windows', 'type' => 'Desktop' );
    if(strpos($ua, 'Macintosh')) return array( 'name' => 'Macintosh', 'type' => 'Desktop' );
    if(strpos($ua, 'Linux')) return array( 'name' => 'Linux', 'type' => 'Desktop' );
  }

  function getView( $view ) {
    global $_CONFIG;
    $userfile = 'sites/'.$_CONFIG['app']['face'].'/views/'.$view.'.php';
    $worldfile = 'views/'.$view.'.php';
    if( file_exists( $userfile  )) return $userfile;
    else if( file_exists( $worldfile )) return $worldfile;
    else return false;
  }

  // extend config
  $_CONFIG['sites'] = array(
    'pony' => 'http://ponyfac.es',
    'lauer'=> 'http://lauerfac.es',
  );

  // detect OS and select according copy hint text
  $os = detectOS();
  if( $os['name'] == 'Macintosh' ) $_CONFIG['app']['copytext'] = $_CONFIG['app']['copytextMac'];

  // set up ordering
  $_CONFIG['order'] = array(
    'id' => 'Default order',
    'popularity' => 'Most popular',
    'added' => 'Newest',
  );

  // load categories
  $_CONFIG['category'] = array( 0 => new Category( 0, 'All faces', 0 ));
  $sql = 'SELECT `id`, `name`, `weight` FROM categories ORDER BY weight ASC';
  $rs = mysql_query( $sql, $db );
  while( $data = mysql_fetch_assoc( $rs )) {
    $c = new Category( (int)$data['id'], $data['name'], $data['weight'] );
    $_CONFIG['category'][$c->id] = $c;
  }

  // some variables
  $faces    = array();
  $stats    = getStats();
  $root     = '.';
?>