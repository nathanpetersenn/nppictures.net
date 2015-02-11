<?php

phpinfo();
die();


function urlToBluredBase64($url, $blur) {
  $image = imagecreatefromjpeg($url);
  for ($i = 0; $i < $blur; $i++) {
    imagefilter($image, IMG_FILTER_SMOOTH, 2);
  }

  ob_start();
    imagejpeg($image);
    $contents = ob_get_contents();
  ob_end_clean();

  return "data:image/jpeg;base64," . base64_encode($contents);
}


?>

<!doctype html>

<html lang="en">
<head>
  <title>Play - NPPictures</title>

  <meta name="description" content="NPPictures Play">
  <meta name="author" content="Nathan Petersen">
  <meta charset="utf-8">

  <link rel="stylesheet" type="text/css" href="/util/pictova.css">

  <script src="/util/jquery.min.js"></script>
  <script>
  
  $(document).ready(function() {
    $("img").mouseover(function() {
      var altsrc = $(this).data("alt-src");
      var src = $(this).attr("src");

      $(this).attr("src", altsrc);
      $(this).data("alt-src", src);
    });

    $("img").mouseout(function() {
      var altsrc = $(this).data("alt-src");
      var src = $(this).attr("src");

      $(this).attr("src", altsrc);
      $(this).data("alt-src", src);
    });

  });

  </script>


  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>

<body>


<?php

$URL = 'https://lh4.googleusercontent.com/-HwGb7aWb7UY/UsmvXrNYs2I/AAAAAAAAA4g/op2jTMscOMg/IMG_0112.jpg?imgmax=1000';
$BLUR_AMOUNT = 100;


$blurred = urlToBluredBase64($URL, $BLUR_AMOUNT);


echo '<img src="' . $blurred . '" data-alt-src="' . $URL . '" />';


?>

</body>

</html>