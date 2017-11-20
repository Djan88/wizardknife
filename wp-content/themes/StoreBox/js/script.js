jQuery(document).ready(function() {

    jQuery('.panel-body').find('iframe').css('height', '400px');
    var wideoHeight = function(){
        var video_w = parseFloat(jQuery('.panel-body').find('iframe').css('width'));
        jQuery('.panel-body').find('iframe').css('height', video_w/1.5+'px');
    }
    wideoHeight();
    jQuery(window).resize(function() {
      console.log(parseFloat(jQuery('.panel-body').find('iframe').css('width')));
      wideoHeight();
    });

});
