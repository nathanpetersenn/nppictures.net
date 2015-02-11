<?php 

define(PICASA_USER, 'nppictures.storage');
$CACHE_ON = false;

$START_TIME = microtime(true);

function urlToBluredBase64($url, $blur) {
  $image = imagecreatefromstring(file_get_contents($url));
  for ($i = 0; $i < $blur; $i++) {
    imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
  }

  ob_start();
    imagejpeg($image);
    $contents = ob_get_contents();
  ob_end_clean();

  return "data:image/jpeg;base64," . base64_encode($contents);
}

require_once 'util/PicasaWebUser.php';
require_once 'util/PicasaWebAlbum.php';
require_once 'util/PicasaWebPhoto.php';

if ($CACHE_ON) {
  require_once 'util/_cache_start.php';
}

$albumId = $_REQUEST['id'];
$albumTitle = $_REQUEST['title'];

// filter out bad requests
if ($albumId != 'recent') {
  if (!is_numeric($albumId) || !PicasaWebAlbum::isAlbum(PICASA_USER, $albumId)) {
    header('Location: ../..');
    die();
  }
}

$user = new PicasaWebUser(PICASA_USER);
$album = new PicasaWebAlbum(PICASA_USER, $albumId);

// make sure name in URL matches name of album with id $albumId
if ($albumId != 'recent') {
  if ($albumTitle != PicasaWebUser::makeLink($album->getTitle())) {
    $url = '/category/' . $albumId . '/' . PicasaWebUser::makeLink($album->getTitle());
    header('Location: ' . $url);
    die();
  }
}


$BACKGROUND_ART_SRC = urlToBluredBase64($album->getAlbumCover(400), 200);
//$BACKGROUND_ART_SRC = '';

?>

<!doctype html>

<html lang="en">

<head>

  <title><?= $album->getTitle() . ' | NPPictures' ?></title>

  <meta name="description" content="A collection of pictures taken by Nathan Petersen">
  <meta name="author" content="Nathan Petersen">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <link rel="stylesheet" type="text/css" media="screen, print, projection" href="/util/styles.fonts.css">
  <link rel="stylesheet" type="text/css" media="screen, print, projection" href="/util/album.css" />
  <link rel="stylesheet" type="text/css" media="screen, print, projection" href="/util/font-awesome/css/font-awesome.min.css">

  <script src="/util/jquery.min.js"></script>
  <script src="/util/jquery.pictowall.js"></script>

  <script>

    $(document).ready(function() {
      $("img.small").each(function() {
        var lrgsrc = $(this).data("lrgsrc");
        $(this).attr("src", lrgsrc);
      });

      $("span.close").click(function() {
        $("body").css({opacity: '0.5'});
        $("html").prepend('<div style="font-size: 100px; position: fixed; top: calc(50% - 50px); left: calc(50% - 50px); color: #000; z-index: 10;"><i class="fa fa-spinner fa-spin"></i></div>');
        window.location.href = 'http://nppictures.net';
      });

      var BACKGROUND_ART_SRC = $('div.background-art').css('background-image');
      $('footer').css('background-image', BACKGROUND_ART_SRC);
    });

    $(document).keydown(function(e) {
      switch(e.which) {
        case 37: // left
          var url = $("li.left a").attr("href");
          if (url) window.location = url;
          break;

        case 39: // right
          var url = $("li.right a").attr("href");
          if (url) window.location = url;
          break;

        case 27: // esc
          $("span.close").click();
          break;

        default: return;
      }
      e.preventDefault();
    });

    // uses Google Picasa's downloader!
    // appending &imgdl=1 downloads the original image
    function download(obj) {
      var src = $(obj).data("src");
      src = src.replace("imgmax=2000", "imgdl=1");

      window.location.href = src;
    }

  /* Google Analytics code */

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-47404422-1', 'nppictures.net');
    ga('send', 'pageview');
    
  // pictowall code
  $(document).ready(function() {
    $("div.gallery").pictowall({
        avgHeightLastRow : true,
        maxImgHeight     : 500,
        margin           : 5
      });

    window.addEventListener('resize', function() {
      $("div.gallery").pictowall({
        avgHeightLastRow : true,
        maxImgHeight     : 500,
        margin           : 5
      });
    });
  });
  </script>

</head>

<body>

<header>
  <h1><?= $album->getTitle() ?></h1>

  <ul>
    <?php
      if ($album->getPrev() != '') {
        $prev = new PicasaWebAlbum(PICASA_USER, $album->getPrev());
        
        if ($album->getPrev() == 'recent') {
          $prev_url = '/recent';
        } else {
          $prev_url = '/category/' . $prev->getAlbumId() . '/' . PicasaWebUser::makeLink($prev->getTitle());
        }

        $prev_title = $prev->getTitle();
      }

      if ($album->getNext() != '') {
        $next = new PicasaWebAlbum(PICASA_USER, $album->getNext()); 
        $next_url = '/category/' . $next->getAlbumId() . '/' . PicasaWebUser::makeLink($next->getTitle());
        $next_title = $next->getTitle();
      }
    ?>

    <li class="middle">
      <i class="fa fa-photo"></i>
      <br/>
      <?= count($album->getPhotos()) ?> photos
    </li>

    <li class="left"<?php if ($album->getPrev() == '') { echo ' style="visibility:hidden"'; } ?>>
      <a href="<?= $prev_url ?>">
        <i class="fa fa-chevron-left"></i>
        <br/>
        <?= $prev_title ?>
      </a>
    </li>

    <li class="right"<?php if ($album->getNext() == '') { echo ' style="visibility:hidden"'; } ?>>
      <a href="<?= $next_url ?>">
        <i class="fa fa-chevron-right"></i>
        <br/>
        <?= $next_title  ?>
      </a>
    </li>
  </ul>

  <div class="background-dark"></div>
  <div class="background-art" style="background-image: url(<?= $BACKGROUND_ART_SRC ?>);"></div>

  <span class="close">
    <i class="fa fa-close"></i>
  </span>
</header>

<?php

$TARGET_HEIGHT = 500; // px

$photos = $album->getPhotos();
  /* RETURNS:
   * title
   * caption
   * id
   * src
   * height
   * width
   * timestamp
   */

echo '<div class="gallery" style="visibility:hidden">' . PHP_EOL;

foreach($photos as $p) {
  $crop_factor = $p['height'] / $TARGET_HEIGHT;
  $new_width = $p['width'] / $crop_factor;
  $new_width = round($new_width);

  $a_src = $p['src'] . '?imgmax=2000';

  $photo = new PicasaWebPhoto($p['userId'], $p['albumId'], $p['photoId']);
  $e = $photo->getExif();

  $caption = '<button onClick=\'download(this)\' data-src=\'' . $a_src . '\' class=\'dl\'>Download</button>';

  if ($e['time'] || $e['model'] || $e['iso']) { $caption .= '<b>EXIF Information:</b><br/>'; } else { $caption .= ''; }
  if ($e['time']) { $caption .= 'Date: ' . date('F j, Y \a\t g:i A', $e['time']) . '<br/>'; }
  if ($e['model']) { $caption .= 'Camera: ' . $e['model'] . ' at ' . $e['focallength'] . 'mm<br/>'; }
  if ($e['iso']) { $caption .= $e['exposure'] . ' at f/' . $e['fstop'] . ', ISO ' . $e['iso'] . '<br/>'; }

  $img_tag_content = array(
    'class' => "small",
    //'src' => $p['src'] . '?imgmax=50',
    'src' => 'data:image/jpg;base64,' . base64_encode(file_get_contents($p['src'] . '?imgmax=50')),
    'data-lrgsrc' => $p['src'] . '?imgmax=1000',
    'data-height' => $TARGET_HEIGHT,
    'height' => $TARGET_HEIGHT,
    'data-width' => $new_width,
    'width' => $new_width,
    'alt' => $p['title']
    );

  $img_tag = '<img ';
  $i = 0;
  foreach ($img_tag_content as $attr => $value) {
    $img_tag .= $attr . '="' . $value . '"';
    if (++$i < sizeof($img_tag_content)) {
      $img_tag .= ' ';
    }
  }
  $img_tag .= ' />';


  echo '<a href="' . $a_src . '" target="_blank" caption="' . $caption . '">';
  echo $img_tag;
  echo '</a>';

}

echo '</div>' . PHP_EOL . PHP_EOL;

?>


<?php 
require 'util/_footer.php';

if ($CACHE_ON) {
  require_once 'util/_cache_end.php';
}
?>