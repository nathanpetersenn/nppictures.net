(function($) {
 
  $.fn.pictowall = function(options) {

    // defaults
    var defaults = {
      avgHeightLastRow : true,
      maxImgHeight     : 200,
      margin           : 5
    };

    var settings = $.extend(defaults, options);
  
    function getheight(images, width) {
      width -= images.length * (settings.margin * 2);
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
    };


    // START

    var n = 0;
    var images = this.find("img");

    images.each(function() {
      $(this).css({margin : settings.margin});
    });

    this.css({padding: settings.margin});

    var HEIGHTS = [];
    var size = this.width() - 1;

    w: while (images.length > 0) {
      for (var i = 1; i < images.length + 1; ++i) {
        var slice = images.slice(0, i);
        var h = getheight(slice, size);
        if (h < settings.maxImgHeight) {
          setheight(slice, h);
          n++;
          images = images.slice(i);
          continue w;
        }
      }

      if (settings.avgHeightLastRow == true) {
        var sum = 0;
        for (var i = 0; i < HEIGHTS.length; i++) {
          sum += parseInt(HEIGHTS[i], 10);
        }
        var avg = sum / HEIGHTS.length;
        setheight(slice, avg);
      } else {
        setheight(slice, Math.min(settings.maxImgHeight, h));
      }

      n++;
      break;
    }

    //console.log(n);

    this.css({"visibility" : "visible"});

    return this;
  };
 
})(window.jQuery);