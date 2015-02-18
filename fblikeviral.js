var t;

jQuery(function($) {
  var boxHTML = '<div class="vr_popup">';

  boxHTML += '<div class="vr_x_close"><a href="#" onclick="hideFBLikeViral(); return false;">X</a></div>';

  boxHTML += '<div class="vr_actions">';

  if(vconf.fblike) {
    if(vconf.scount) {
      boxHTML += '<div class="vr_action vr_largeBtn"><iframe src="http://www.facebook.com/plugins/like.php?href=' + vconf.url + '&layout=box_count&show_faces=false&width=55&action=like&colorscheme=light&height=61" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:70px; height:61px;" allowTransparency="true"></iframe></div>';
    } else {
      boxHTML += '<div class="vr_action vr_smallBtn"><iframe src="http://www.facebook.com/plugins/like.php?href=' + vconf.url + '&layout=standard&show_faces=false&width=55&action=like&colorscheme=light&height=61" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:70px; height:61px;" allowTransparency="true"></iframe></div>';
    }
  }

  if(vconf.google) {
    if(vconf.scount) {
      boxHTML += '<div class="vr_action vr_largeBtn"><g:plusone size="tall" href="' + vconf.url + '" count="true"></g:plusone></div>';
    } else {
      boxHTML += '<div class="vr_action vr_smallBtn"><g:plusone href="' + vconf.url + '" count="false"></g:plusone></div>';
    }
  }

  if(vconf.twitter) {
    if(vconf.scount) {
      boxHTML += '<div class="vr_action vr_largeBtn"><a href="http://twitter.com/share" class="twitter-share-button" data-url="' + vconf.url + '" data-count="vertical" data-via="' + vconf.tvia + '">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>';
    } else {
      boxHTML += '<div class="vr_action vr_smallBtn"><a href="http://twitter.com/share" class="twitter-share-button" data-url="' + vconf.url + '" data-count="none" data-via="' + vconf.tvia + '">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>';
    }
  }

  if(vconf.fbshare) {
    if(vconf.scount) {
      boxHTML += '<div class="vr_action vr_largeBtn"><a name="fb_share" type="box_count" href="http://www.facebook.com/sharer.php" share_url="' + vconf.url + '">Share</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script></div>';
    } else {
      boxHTML += '<div class="vr_action vr_smallBtn"><a name="fb_share" type="standard" href="http://www.facebook.com/sharer.php" share_url="' + vconf.url + '">Share</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script></div>';
    }
  }

  if(vconf.fbsend) {
    boxHTML += '<div class="vr_action vr_smallBtn"><fb:send href="' + vconf.url + '" font="tahoma"></fb:send></div>';
  }

  boxHTML += '</div>';

  boxHTML += '<div class="vr_msg">' + vconf.message;
  boxHTML += '<div class="vr_close"><a href="#" onclick="hideFBLikeViral(); return false;">No, thanks!</a></div>';

  if(vconf.popcountdown) {
    boxHTML += '<div class="vr_countdown">This popup will close in <span id="fbcnt">10</span> seconds...</div>';
  }

  boxHTML += '</div>';

  if(vconf.powered) {
    if(vconf.afflink != '') {
      boxHTML += '<div class="vr_powered"><a href="' + vconf.afflink + '" target="_blank">Powered By FB Like Viral</a></div>';
    } else {
      boxHTML += '<div class="vr_powered"><a href="http://www.fblikeviral.com/" target="_blank">Powered By FB Like Viral</a></div>';
    }
  }
  
  boxHTML += '</div>';
  
  jQuery('<div class="vr_bg"></div>' + boxHTML).prependTo('body');

  jQuery('.vr_bg').click(function(){
    hideFBLikeViral();
  });

  jQuery('.vr_popup').css({
    'background-color': '#' + vconf.style_bg,
    'margin-left': (jQuery(document).width() - jQuery('.vr_popup').width()) / 2 + 'px',
    'margin-top': jQuery(document).height() / 6 + 'px'
  });

  jQuery('.vr_msg').css({
    'color': '#' + vconf.style_color,
    'font-family': vconf.style_font,
    'font-size': vconf.style_size + 'px'
  });

  jQuery('.vr_bg').css({
    '-ms-filter': '"progid:DXImageTransform.Microsoft.Alpha(Opacity=' + vconf.opacity + ')"',
    'filter': 'alpha(opacity=' + vconf.opacity + ')',
    '-moz-opacity': vconf.opacity / 100,
    '-khtml-opacity': vconf.opacity / 100,
    'opacity': vconf.opacity / 100
  });

  if(vconf.trigger == 'end') {
    jQuery(window).scroll(function() {
      var a = jQuery(document).height();
      var b = jQuery(window).scrollTop();

      if((b*2) > (a*(vconf.perc / 100))) {
        showFBLikeViral();
        vconf.show = 0;
      } else {
        vconf.show = 1;
      }
    });
  } else if (vconf.trigger == 'after') {
    setTimeout('showFBLikeViral()', vconf.trigger_timeout * 1000);
  } else {
    var hits = getCookie('fbviral_hits');

    if(hits === undefined) {
      hits = 1;
    }

    if(hits < vconf.trigger_hits ) {
      setCookie('fbviral_hits', ++hits, 2);
    } else {
      setCookie('fbviral_hits', 1, 2);
      setTimeout('showFBLikeViral()', vconf.trigger_timeout * 1000);
    }
  }
});

function showFBLikeViral() {
  if (jQuery(".vr_bg:visible").length == 1) {
    return false;
  }

  if(vconf.vreturn == 0) {
    if(getCookie('fbviral') != null) {
      return false;
    } else {
      setCookie('fbviral', 'on', 2);
    }
  }

  if(vconf.show == 0) {
    return false;
  }

  if(vconf.timeout > 0) {
    setTimeout('hideFBLikeViral()', vconf.timeout * 1000);
  }

  if(vconf.popcountdown) {
    fblikeCountdown();
  }

  if(vconf.style_color && vconf.powered) {
    jQuery('.vr_powered > a').css({
      'color': '#' + vconf.style_color
    });
  }

  jQuery('.vr_popup').css({'margin-top': '200px'});
  jQuery('#wrapper').css({'margin-top': '0px'});

  if(vconf.effect == 'fade') {
    jQuery('.vr_bg').fadeIn('slow', function() {
      jQuery('.vr_popup').show();
    });
  } else if (vconf.effect == 'slide') {
    jQuery('.vr_bg').fadeIn('slow', function() {
      jQuery('.vr_popup').css({'margin-top': '0px', 'top': '-100px'}).show().animate({
        marginTop: (jQuery(window).height() / 2) + 'px'
      }, 1000);
    });
  } else {
    jQuery('.vr_bg').show();
    jQuery('.vr_popup').show();
  }
}

function hideFBLikeViral() {
  if(vconf.popcountdown) {
    clearTimeout(t);
  }

  jQuery('.vr_popup').fadeOut('slow', function() {
    jQuery('.vr_bg').hide();
    jQuery('#wrapper').removeAttr('style');
  });
}

function getCookie(c_name) {
  var i,x,y,ARRcookies=document.cookie.split(";");

  for (i=0;i<ARRcookies.length;i++) {
    x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
    y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
    x=x.replace(/^\s+|\s+$/g,"");

    if (x==c_name) {
      return unescape(y);
    }
  }
}

function setCookie(c_name,value,exdays) {
  var exdate=new Date();
  exdate.setDate(exdate.getDate() + exdays);
  var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
  document.cookie=c_name + "=" + c_value;
}

function fblikeCountdown() {
  if (vconf.timeout <= 0) {
    clearTimeout(t);
 } else {
    vconf.timeout -= 1;
 }

  if(vconf.timeout >= 0) {
    document.getElementById('fbcnt').innerHTML = vconf.timeout;
    t = setTimeout("fblikeCountdown()", 1000);
  }
}
