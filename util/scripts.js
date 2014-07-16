$(document).ready(function() { 
  center_ul();

    $("div.gallery a").attr('rel', 'gallery').fancybox({
      beforeLoad: function () {
        this.title = $(this.element).attr('caption');
      },
      prevEffect: 'none',
      nextEffect: 'none',
      padding: 10,
      loop: false,
      helpers: {
        title: {
          type: 'inside'
        }
      }
    });

});


$(window).on('resize', function() { center_ul(); });

function center_ul() {
  var width = $(window).width();

  if (width < 700) {
    $("ul.cat").width("100%");
    return;
  }

  var li_width = $("ul.cat li").width();
  var li_padding = 2 * parseInt($("ul.cat li").css("margin-left"));

  var num = width / (li_width + li_padding);
  num = Math.floor(num);

  if (num == 0) { num = 1; }

  var too_small = 0;
  if (num > $("ul.cat li").length) { 
    var new_num = $("ul.cat li").length;
    too_small = 1;
    new_num = new_num * (li_padding + li_width);
  }

  num = num * (li_padding + li_width);

  if (too_small) {
    $("ul.cat").width(new_num);
  } else {
    $("ul.cat").width(num);
  }

}

/*
if ($("div.fancybox-overlay").length == 0) {
  $(document).keydown(function(e) {

    switch(e.which) {
        case 37: // left
          var url = $("a.prev").attr("href");
          if (url) window.location = url;
        break;

        case 39: // right
          var url = $("a.next").attr("href");
          if (url) window.location = url;
        break;

        default: return;
    }
    e.preventDefault();
  });
};

*/