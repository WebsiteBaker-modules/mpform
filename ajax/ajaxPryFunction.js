var CLUETIP_DIR = AJAX_PLUGINS +"/jquery.cluetip"; //plugins directory
$.insert(CLUETIP_DIR +"/jquery.cluetip.pryFunction.css"); 
$.insert(CLUETIP_DIR +"/jquery.cluetip.min.js");

//$.insert(WB_URL +"/include/jquery/jquery-ui.css"); // uncomment if you want to use jquery-ui.css

(function($) {
  // Back-compat file for clueTip 1.2
  // This modifies the $.fn.cluetip object to make the plugin work the way it did before clueTip version 1.2
  $.extend(true, $.fn.cluetip, {
    backCompat: true,
    template: ['<div id="cluetip">',
      '<div id="cluetip-outer" class="ui-cluetip-outer">',
          '<h3 id="cluetip-title" class="ui-widget-header ui-cluetip-header"></h3>',
          '<div id="cluetip-inner" class="ui-widget-content ui-cluetip-content"></div>',
        '</div>',
        '<div id="cluetip-extra"></div>',
        '<div id="cluetip-arrows" class="cluetip-arrows"></div>',
      '</div>'].join('')
  });
})(jQuery);

$('.pry').cluetip({
                width: 800,
                //height: 500, 
                cluezIndex: 9999,
                sticky: true, 
                leftOffset: 20,
                dropShadow: true,  
                dropShadowSteps: 4,  
                closePosition: 'title', 
                waitImage: true,  
                arrows: true,
                closeText: '<img src="'+CLUETIP_DIR+'/cluetip_images/close_button.gif" title="'+ LANG.CLOSE +'" alt ="[x]" width="16" height="16"/>',
                ajaxCache: false // don't cache 
});
$(".hideCluetip").click(function () { 
        $(this).trigger('hideCluetip');                        
});
