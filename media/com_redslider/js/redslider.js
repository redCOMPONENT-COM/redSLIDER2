// Fix youtube iframe overlay issue
jQuery(document).ready(function($) {
    $("#redslider2 iframe").each(function(){
        var ifr_source = $(this).attr('src');
        var wmode = "wmode=transparent";
        if(ifr_source.indexOf('?') != -1) {
            var getQString = ifr_source.split('?');
            var oldString = getQString[1];
            var newString = getQString[0];
            $(this).attr('src',newString+'?'+wmode+'&'+oldString);
        }
        else $(this).attr('src',ifr_source+'?'+wmode);
    });
    $('#redslider2 ul.slides > li.redshop_slide').each(function(index, el) {
        $(this).css('background-image', 'url('+$(this).find('.slide-img').find('img').attr('src')+')');
    });
    $('div.attribute_wrapper').find('select').each(function(index, el) {
        $(this).wrapAll('<div class="select-wrapper"></div>');
    });
});