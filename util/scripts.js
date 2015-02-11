$(document).ready(function() { 
  center_ul();
});

$(window).on('resize', function() {
  center_ul();
});

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

  var too_small = false;
  if (num > $("ul.cat li").length) { 
    var new_num = $("ul.cat li").length;
    too_small = true;
    new_num = new_num * (li_padding + li_width);
  }

  num = num * (li_padding + li_width);

  if (too_small) {
    $("ul.cat").width(new_num);
  } else {
    $("ul.cat").width(num);
  }

}