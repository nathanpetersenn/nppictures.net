<?php
function draw($url, $heightsrc, $widthsrc, $heightout, $widthout, $cache, $debug) {
    if ($debug == '1') {echo 'source width: ' . $widthsrc . '<br/>';}
    if ($debug == '1') {echo 'source height: ' .  $heightsrc . '<br/><br/>';}

    $url = 'http://nppictures.net' . $url;
    $image = imagecreatefromjpeg($url);

    $light	= imagecolorallocatealpha($image, 255, 255, 255, 75);
    $dark	= imagecolorallocatealpha($image, 0, 0, 0, 75);

    // Circles
    $center = array($widthout/2, $heightout/2); // Center point (x, y)
    $radius_larg = (100/600) * $widthout;
    $radius_medi = (90/600) * $widthout;
    $radius_smal = (80/600) * $widthout;


    // Playbutton
    $values = array(
      ($widthout/2)-($radius_smal/2),  ($heightout/2)-($radius_larg/2),  // Point 1 (x, y)
      ($widthout/2)-($radius_smal/2),  ($heightout/2)+($radius_larg/2),  // Point 2 (x, y)
      (360/600)*$widthout,  (150/300)*$heightout,  // Point 3 (x, y)
    );

    imagefilledellipse($image, $center[0], $center[1], 2 * $radius_larg, 2 * $radius_larg, $dark);
    imagefilledellipse($image, $center[0], $center[1], 2 * $radius_medi, 2 * $radius_medi, $light);
    imagefilledellipse($image, $center[0], $center[1], 2 * $radius_smal, 2 * $radius_smal, $dark);
    imagefilledpolygon($image, $values, count($values)/2, $light);

    $filename = 'imgcache/' . $cache . '.jpg';

    imagejpeg($image, $filename);
    imagedestroy($image);
    return 'http://nppictures.net/util/php/drawplay/' . $filename;
}


function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            self::deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}


function getvidsrc($url, $crop, $height, $width, $debug) {


  if (!file_exists('imgcache/')) {if ($debug == '1') {echo 'created folder' . '<br/>';} mkdir('imgcache/');} // If cache folder doesn't exist, create it.
  if (!file_exists('imgcache/created.txt')) {$fp = fopen('imgcache/created.txt', 'w'); fwrite($fp, '0'); fclose($fp); if ($debug == '1') {echo 'created created.txt' . '<br/>';}} // If created.txt doesn't exist, create it.

  $created = file_get_contents('imgcache/created.txt');

  if (time() -  (60 * 60 * 24) > $created || $created == '') {
    deleteDir('imgcache/');
    if ($debug == '1') {echo 'deleted all cache folder' . '<br/>';}
    if (!file_exists('imgcache/')) {if ($debug == '1') {echo 'created folder' . '<br/>';} mkdir('imgcache/');} // If cache folder doesn't exist, create it.
    if (!file_exists('imgcache/created.txt')) {$fp = fopen('imgcache/created.txt', 'w'); fwrite($fp, '0'); fclose($fp); if ($debug == '1') {echo 'created created.txt' . '<br/>';}} // If created.txt doesn't exist, create it.
    file_put_contents('imgcache/created.txt', time());
    if ($debug == '1') {echo 'wrote time' . '<br/>';}
  }

  $cache = substr($url, 0, -6);
  $cache = substr($cache, -11);
  $cache = preg_replace("/[^a-zA-Z 0-9]+/", "", $cache);
  if ($debug == '1') {echo 'image name: ' . $cache . '.jpg<br/>';}

  list($widthimg, $heightimg, $typeimg, $attrimg) = getimagesize($url);

  $url = '/util/php/timthumb/timthumb.php?src='.$url.'&w=' . $width . '&h=' . $height . '&q=90';

  if (file_exists('imgcache/'. $cache . '.jpg')) {
    $newimgsrc = 'http://nppictures.net/util/php/drawplay/imgcache/'. $cache . '.jpg';
    if ($debug == '1') {echo 'CACHED:' . '<br/>';}
  } else {
    $newimgsrc = draw($url, $heightimg, $widthimg, $height, $width, $cache, $debug);
    if ($debug == '1') {echo 'created:' . '<br/>';}
  }

  return $newimgsrc;
}


$url = $_GET['url'];
$crop = $_GET['crop'];
$heightin = $_GET['h'];
$widthin = $_GET['w'];
$debug = '0';

if ($url == '' || @getimagesize($url) == false) {die('<b>FATAL ERROR:</b> Please pass in an real image source to the page by typing http://nppictures.net/projects/image/drawplay/?crop=1&url=<b>YOUR_IMAGE_URL</b>');}
list($widthimg, $heightimg, $typeimg, $attrimg) = getimagesize($url);

if ($crop == '') {$crop = '0';}
if ($crop == '0') {$heightin = $heightimg; $widthin = $widthimg;}



if ($crop == '1' && $heightin == '') {$heightin = '300';}
if ($crop == '1' && $widthin == '') {$widthin = $heightin;}


$src = getvidsrc($url, $crop, $heightin, $widthin, $debug); // (video thumbnail src url, debug) 1 for debug, 0 for not

list($widthout, $heightout) = getimagesize($src);

if ($debug == '1') {echo 'output width: ' . $widthin . '<br/>';}
if ($debug == '1') {echo 'output height: ' .  $heightin . '<br/>';}

if ($debug == '1') {
  echo '<img src="' . $src . '" />';
} else {
  header('Content-Type:image/jpeg');
  readfile($src);
}

?>