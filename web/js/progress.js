'use strict';

/* DOCUMENT READY */

$(document).ready(function() {
  if ($('.progress').length > 0) {
    // Scroll progress
    window.onscroll = function(){
      /* height scrolled from top */
      var scrolled_top = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;

      /* total scrollable height */
      var to_scroll = (document.documentElement.scrollHeight || document.body.scrollHeight) - (document.documentElement.clientHeight || document.body.clientHeight || window.innerHeight)

      /* percentage of height scrolled */
      var horizontal_width = (scrolled_top/to_scroll)*100;

      /* assigning calculated width to progress bar */
      document.getElementById('progress').style.width = horizontal_width + '%';
    }
  }
});


/* FUNCTIONS */


