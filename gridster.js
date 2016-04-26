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

function open_media_uploader_image()
{
    media_uploader = wp.media({
        frame:    "post", 
        state:    "insert", 
        multiple: false
    });

    media_uploader.on("insert", function(){
        var json = media_uploader.state().get("selection").first().toJSON();
        var main_id = jQuery('#upload_media').parent().parent().parent().parent().parent().parent().attr('id');
        alert(main_id);

        var image_url = json.url;
        var image_caption = json.caption;
        var image_title = json.title;
        var image_id = json.id;
        alert(image_id);
        var postData = {
            action : 'upload_media',
            image_id : image_id,
            image_url : image_url,
        }
        jQuery.ajax({
            method: 'GET',
            url: "/wp-admin/admin-ajax.php",
            data: postData,
            success: function(response) {
                alert(response);
                jQuery('#'+main_id).css('background-image', 'url('+response+')');
                jQuery('#'+main_id+' > div > div > h1').show();
                jQuery('#'+main_id+' > div > .list').hide();
                jQuery('#'+main_id+'_button').show();
                jQuery('#'+main_id).attr("post_id", 0);
            }
        });
    });

    media_uploader.open();
}

jQuery(document).ready(function() {

    jQuery('#upload_media').click(function() {
        open_media_uploader_image();
    })
    
    jQuery('#save').click(function() {

        var gridster = $('.gridster ul').gridster().data('gridster');
        var widge = gridster.$widgets;
        var id_list = Array();
        var row_list = Array();
        var col_list = Array();
        var sizex_list = Array();
        var sizey_list = Array();
        var post_list = Array();
        widge.each(function() {
            var item = $(this);
            var atts = item[0].attributes;
            console.log(atts);
            post_list.push(atts[0].value);
            id_list.push(atts[2].value);
            row_list.push(atts[3].value);
            col_list.push(atts[4].value);
            sizex_list.push(atts[5].value);
            sizey_list.push(atts[6].value);
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
        }
        jQuery.ajax({
          method: 'GET',
          url: "/wp-admin/admin-ajax.php",
          data: postData,
          success: function(response) {
            $("#saved").show().delay(2000).fadeOut();
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
            //alert(response);
            var arr = response.split("%");
            jQuery('#'+main_id).css('background-image', 'url('+arr[0]+')');
            jQuery('#'+main_id+' > div > div > h1').text(arr[1].toUpperCase());
            jQuery('#'+main_id+' > div > div > h1').show();
            jQuery('#'+main_id+' > div > .list').hide();
            jQuery('#'+main_id+'_button').show();
            jQuery('#'+main_id).attr("post_id", id);
          }
        });
    });
});