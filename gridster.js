var gridster;
jQuery(function() {
    gridtster = jQuery(".gridster > ul").gridster({
        widget_margins: [10, 10], 
        widget_base_dimensions: [360, 360],
        min_cols: 3,
        max_cols: 3
    }).data('gridster');
});

var media_uploader = null;

function open_media_uploader_image(thisval)
{
    media_uploader = wp.media({
        frame:    "post",
        state:    "insert",
        multiple: false
    });

    media_uploader.on("insert", function(){
        var json = media_uploader.state().get("selection").first().toJSON();
        var main_id = thisval.parent().parent().parent().parent().parent().parent().attr('id');

        var image_url = json.url;
        var image_caption = json.caption;
        var image_title = json.title;
        var image_id = json.id;
        var size = main_id.substring(4,7);

        var postData = {
            action : 'upload_media',
            image_id : image_id,
            image_url : image_url,
            size : size,
        }
        jQuery.ajax({
            method: 'GET',
            url: "/wp-admin/admin-ajax.php",
            data: postData,
            success: function(response) {
                jQuery('#'+main_id).css('background-image', 'url('+response+')');
                var overlay_text = prompt('Image overlay text');
                var media_link = prompt('Add link URL');

                jQuery('#'+main_id+' > div > div > h1').text(overlay_text.toUpperCase());
                jQuery('#'+main_id+' > div > div > h1').show();
                if(media_link) {
                    jQuery('#'+main_id+' > div > .custom_link').text('Link: '+media_link);
                    jQuery('#'+main_id+' > div > .custom_link').show();
                }
                jQuery('#'+main_id+' > div > .list').hide();
                jQuery('#'+main_id+'_button').show();
                jQuery('#'+main_id).attr("post_id", '6666'+image_id);
            }
        });
    });

    media_uploader.open();
}

jQuery(document).ready(function() {

    jQuery('.upload_media').click(function() {
        open_media_uploader_image(jQuery(this));
    });

    jQuery('.apply').click(function() {
        
    });

    jQuery('.apply').click(function() {

    });
    
    jQuery('#save').click(function() {

        var gridster = jQuery('.gridster ul').gridster().data('gridster');
        var widge = gridster.$widgets;
        var id_list = Array();
        var title_list = Array();
        var link_list = Array();
        var row_list = Array();
        var col_list = Array();
        var sizex_list = Array();
        var sizey_list = Array();
        var post_list = Array();
        var x = 0;
        widge.each(function() {
            var item = jQuery(this);
            var atts = item[0].attributes;
            //console.log(atts);
            post_list.push(atts[0].value);
            id_list.push(atts[2].value);
            title_list.push(jQuery('#'+atts[2].value+' > div > div > h1').html());
            link_list.push(jQuery('#custom_link_'+x).html());
            //alert(jQuery('#custom_link_'+x).html());
            row_list.push(atts[3].value);
            col_list.push(atts[4].value);
            sizex_list.push(atts[5].value);
            sizey_list.push(atts[6].value);
            x++;
        });
        gridster.init();

        var postData = {
            action : 'save_all',
            id_list : id_list,
            row_list : row_list,
            col_list : col_list,
            sizex_list : sizex_list,
            sizey_list : sizey_list,
            post_list : post_list,
            title_list : title_list,
            link_list : link_list,
        }
        jQuery.ajax({
          method: 'GET',
          url: "/wp-admin/admin-ajax.php",
          data: postData,
          success: function(response) {
            jQuery("#saved").show().delay(2000).fadeOut();
          }
        });
    })

    jQuery('li.box_list button').click(function() {
        var id = jQuery(this).parent().parent().attr('id');
        jQuery('#'+id+' > div > div > h1').hide();
        jQuery('#'+id+'_button').hide();
        jQuery('#'+id+' > div > .list').show();
    })

    jQuery('.cancel').click(function() {
        var id = jQuery(this).parent().parent().parent().parent().parent().attr('id');
        jQuery('#'+id+' > div > div > h1').show();
        jQuery('#'+id+'_button').show();
        jQuery('#'+id+' > div > .list').hide();
    })
    jQuery('li.list_items').click(function() {
        var id = jQuery(this).attr('id');
        var main_id = jQuery(this).parent().parent().parent().parent().parent().parent().parent().attr('id');
        var size = main_id.substring(4,7);
        var postData = {
            action : 'set_item',
            id : id,
            size : size,
        }
        jQuery.ajax({
          method: 'GET',
          url: "/wp-admin/admin-ajax.php",
          data: postData,
          success: function(response) {
            var arr = response.split("%");
            jQuery('#'+main_id).css('background-image', 'url('+arr[0]+')');
            jQuery('#'+main_id+' > div > .custom_link').text('');
            jQuery('#'+main_id+' > div > .custom_link').hide();
            jQuery('#'+main_id+' > div > div > h1').text(arr[1].toUpperCase());
            jQuery('#'+main_id+' > div > div > h1').show();
            jQuery('#'+main_id+' > div > .list').hide();
            jQuery('#'+main_id+'_button').show();
            jQuery('#'+main_id).attr("post_id", id);
          }
        });
    });
});