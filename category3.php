<?php 

define(PICASA_USER, 'nppictures.storage');

require_once 'util/PicasaWebUser.php';
require_once 'util/PicasaWebAlbum.php';
require_once 'util/PicasaWebPhoto.php';

//require 'util/_cache_start.php';

$album_id = $_REQUEST['id'];
$album_name = $_REQUEST['name'];

$user = new PicasaWebUser(PICASA_USER);
$album = new PicasaWebAlbum(PICASA_USER, $album_id);


$title = $album->getTitle() . ' | NPPictures';
$show_about = false;
require 'util/_header.php';

// generate infomation used to make right arrow on category page
$next_id = $album->getNext();
if ($next_id != '') {
  $next_album = new PicasaWebAlbum(PICASA_USER, $next_id);

  $next_title = $next_album->getTitle();
  $next_title_link = PicasaWebUser::makeLink($next_title);
  $next_url = '/category/' . $next_id . '/' . $next_title_link;
}

// generate infomation used to make left arrow on category page
$prev_id = $album->getPrev();
if ($prev_id != '' && $prev_id != 'recent') {
  $prev_album = new PicasaWebAlbum(PICASA_USER, $prev_id);

  $prev_title = $prev_album->getTitle();
  $prev_title_link = PicasaWebUser::makeLink($prev_title);
  $prev_url = '/category/' . $prev_id . '/' . $prev_title_link;
} else if ($prev_id == 'recent') {
  $prev_url = '/recent';
}

?>

<h4><?= $album->getTitle() ?></h4>

<div class="cat-nav">
<?php
if ($prev_url) {
  echo '<a class="prev" href="' . $prev_url . '"><img class="arrow" src="/util/left-arrow.gif" alt="left-arrow" /></a>';
} else {
  echo '<img class="arrow hidden" src="/util/left-arrow.gif" alt="left-arrow" />';
}
if ($next_url) {
  echo '<a class="next" href="' . $next_url . '"><img class="arrow" src="/util/right-arrow.gif" alt="right-arrow" /></a>';
} else {
  echo '<img class="arrow hidden" src="/util/right-arrow.gif" alt="right-arrow" />';
}
?>
</div>

<script>

  $(document).ready(function() {
    $("img.small").each(function() {
      var lrgsrc = $(this).data("lrgsrc");
      $(this).attr("src", lrgsrc);
    });

    $("div.gallery").pictowall();

  });

  $(document).resize(function() {
    $("div.gallery").pictowall();
  })

  // uses Google Picasa's downloader!
  // appending &imgdl=1 downloads the original image
  function download(obj) {
    var src = $(obj).data("src");
    src = src.replace("imgmax=2000", "imgdl=1");

    window.location.href = src;
  }

</script>


<?php

$TARGET_HEIGHT = 200; // px

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

echo '<div class="gallery">' . PHP_EOL;

foreach($photos as $p) {
  $crop_factor = $p['height'] / $TARGET_HEIGHT;
  $new_width = $p['width'] / $crop_factor;
  $new_width = round($new_width);

  $a_src = $p['src'] . '?imgmax=2000';

  $photo = new PicasaWebPhoto($user->getUserId(), $album->getAlbumId(), $p['photoId']);
  $e = $photo->getExif();

  $caption = '<button onClick=\'download(this)\' data-src=\'' . $a_src . '\' class=\'dl\'>Download</button>';

  if ($e['time'] || $e['model'] || $e['iso']) { $caption .= '<b>EXIF Information:</b><br/>'; } else { $caption .= ''; }
  if ($e['time']) { $caption .= 'Date: ' . date('F j, Y \a\t g:i A', $e['time']) . '<br/>'; }
  if ($e['model']) { $caption .= 'Camera: ' . $e['model'] . ' at ' . $e['focallength'] . 'mm<br/>'; }
  if ($e['iso']) { $caption .= $e['exposure'] . ' at f/' . $e['fstop'] . ', ISO ' . $e['iso'] . '<br/>'; }

  $img_tag_content = array(
    'class' => "small",
    //'src' => $p['src'] . '?imgmax=50',
    'src' => 'data:image/jpg;base64,' . base64_encode(file_get_contents($p['src'] . '?imgmax=5')),
    'data-lrgsrc' => $p['src'] . '?imgmax=500',
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


  echo '<a href="' . $a_src . '" caption="' . $caption . '">';
  echo $img_tag;
  echo '</a>';

}

echo '</div>' . PHP_EOL . PHP_EOL;

?>

<?php 
require 'util/_footer.php';
//require 'util/_cache_end.php';
?>