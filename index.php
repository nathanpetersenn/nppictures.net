<?php
require 'util/_cache_start.php';
require 'util/_functions.php';

$title = 'NPPictures';
$show_about = true;
require 'util/_header.php';
?>

<h4>Categories</h4>

<ul class="cat">

<?

$cats = get_categories_array();
/* OUTPUT: $cats contains the following fields:
 * name
 * numphotos
 * timestamp
 * thumb
 * id
 */

foreach ($cats as $c) {
  echo '<li>' . PHP_EOL;

  $thumb = urlencode($c['thumb'] . '?imgmax=1000');
  $main_src = '/util/timthumb/timthumb.php?src=' . $thumb . '&amp;w=600&amp;h=600&amp;q=90';


  if ($c['id'] == 'recent') {

    echo '<a href="recent">';
    echo '<img class="main" src="' . $main_src . '" alt="' . $c['name'] . '" />' . PHP_EOL; // main category cover

  } else {

    echo '<a href="category/' . $c['id'] . '/';
    echo make_link($c['name']);
    echo '">' . PHP_EOL;

    echo '<img class="main" src="' . $main_src . '" alt="' . $c['name'] . '" />' . PHP_EOL; // main category cover


    // renders preview of photos in category

    $imgs = get_pictures_in_album_by_id($c['id']);
    shuffle($imgs);

    // chooses 4 random pictures to preview
    // ensures preview is not cover image
    $i = 0;
    while($i++ < 4) {
      $thumb = urlencode($imgs[$i] . '?imgmax=1000');
      $src = '/util/timthumb/timthumb.php?src=' . $thumb . '&amp;w=600&amp;h=600&amp;q=90';

      $md5_1 = md5(file_get_contents('http://nppictures.net' . $main_src));
      $md5_2 = md5(file_get_contents('http://nppictures.net' . $src));
      if ($md5_1 == $md5_2) { continue; } // prevents album cover from appearing as preview

      echo '<img class="preview" src="' . $src . '" alt="' . $c['name'] . ' preview" />' . PHP_EOL; // sub category image for preview

    }

  }

  echo '<div>';
  echo $c['name'];
  if ($c['numphotos'] == 1) { echo '<span> - ' . $c['numphotos'] . ' photo</span>'; } else { echo '<span> - ' . $c['numphotos'] . ' photos</span>'; }
  echo '</div>' . PHP_EOL;
  echo '</a>' . PHP_EOL;
  echo '</li>' . PHP_EOL . PHP_EOL;

}

?>

</ul>

<script>

/*

Script to cycle through category previews... not implemented yet


$(document).ready(function() {

  $('ul.cat li a img').mouseenter(function() {
    var img = $(this);

    img.fadeTo(400, 0, function() {
      img.next().css('visibility', 'hidden');

      img.next().fadeTo(400, 1, function() {
        img.next().css('visibility', 'visible');
        img.insertAfter(img.last().prev()); // moves current preview to last in line
      });

    });
    
  });

});

*/

</script>


<?php 
require 'util/_footer.php';
require 'util/_cache_end.php';
?>