<?php

$CACHE_ON = true;

// generates string for cache filename
$cachefile = 'cache/' . basename($_SERVER['PHP_SELF'], '.php');
if ($_SERVER['QUERY_STRING'] != '') {
  $cachefile .= '?' . $_SERVER['QUERY_STRING'] . '.cache';
} else {
  $cachefile .= '.cache';
}

clearstatcache();

if (file_exists($cachefile) && $CACHE_ON) {
  // good to serve!
  require $cachefile;
  die();
}

ob_start();

?>