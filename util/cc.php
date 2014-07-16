<?php

$files = glob('../cache/*');

foreach($files as $file) {
  if(is_file($file))
    unlink($file);
}









/******** REGENERATES CACHE FILES BY CRAWLING SITE ROOT *******/


require '_simple_html_dom.php';

$html = file_get_html('http://nppictures.net/');

$hrefs = array();
foreach($html->find('a') as $e) {
  $href = $e->href;
  if (!isIn($href, 'mailto:') && $href != '/') {
    array_push($hrefs, $href);
  }
}


foreach ($hrefs as $url) {
  $file = 'http://nppictures.net/' . $url;
  $contents = file_get_contents($file);
}

header('Location: /');




function isIn($string, $findme) {
  $pos = strpos($string, $findme);
  if ($pos !== false) {
    return true;
  } else {
    return false;
  }
}

?>