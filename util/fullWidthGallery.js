$(document).ready(function () {

  HEIGHTS = [];

  function getheight(images, width) {
    width -= images.length * 4;
    var h = 0;
    for (var i = 0; i < images.length; ++i) {
      h += $(images[i]).data('width') / $(images[i]).data('height');
    }
    return width / h;
  }

  function setheight(images, height) {
    HEIGHTS.push(height);
    for (var i = 0; i < images.length; ++i) {
      $(images[i]).css({
        width: height * $(images[i]).data('width') / $(images[i]).data('height'),
        height: height
      });
    }
  }

  function resize(images, width) {
    setheight(images, getheight(images, width));
  }

  function run(max_height) {
    var size = $("div.gallery").width() - 1;

    var n = 0;
    var images = $("div.gallery img");
    w: while (images.length > 0) {
      for (var i = 1; i < images.length + 1; ++i) {
        var slice = images.slice(0, i);
        var h = getheight(slice, size);
        if (h < max_height) {
          setheight(slice, h);
          n++;
          images = images.slice(i);
          continue w;
        }
      }
      setheight(slice, Math.min(max_height, h));
      n++;
      break;
    }
    console.log(n);
  }

  window.addEventListener('resize', function () {
    run(205);
  });
  $(document).ready(function () {
    run(205);
  });

});
