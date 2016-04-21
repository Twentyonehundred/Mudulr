var gridster;
jQuery(function() {
    gridtster = jQuery(".gridster > ul").gridster({
        widget_margins: [10, 10], 
        widget_base_dimensions: [240, 240],
        min_cols: 3,
        max_cols: 3
    }).data('gridster');


    /*var grid_canvas = $(".gridster > ul").gridster( 
    {

        widget_margins: [10, 10],

        widget_base_dimensions: [240, 240],
        min_cols: 3,
        max_cols: 3,

         serialize_params: function($w, wgd)
          {
           return {
            id: $($w).attr('id'),
            col: wgd.col,
            row: wgd.row,
            size_x: wgd.size_x,
            size_y: wgd.size_y,
           };
          },

        draggable: 
      {
       stop: function(event, ui) {    
        var positions = JSON.stringify(this.serialize());
        localStorage.setItem('positions', positions);
      
        $.post(
        "index.php",
        {"positions": positions},
        function(data)
         {
          if(data==200)
           console.log("Data successfully sent to the server");
          else
           console.log("Error: Data cannot be sent to the server")
         }
        );}
      } 
    }).data('gridster');*/

    /*var localData = JSON.parse(localStorage.getItem('positions'));
 
 if(localData!=null)
  {
            $.each(localData, function(i,value){
 
            var id_name;

             id_name="#";
            id_name = id_name + value.id;
   
            $(id_name).attr({"data-col":value.col, "data-row":value.row, "data-sizex":value.size_x, "data-sizey":value.size_y});
   
             });
  }
else{
           console.log('No data stored in the local storage. Continue with the default values'); 
  }*/

});

jQuery(document).ready(function() {

    //var gridster = $('.gridster ul').gridster().data('gridster');    
    
    jQuery('#save').click(function() {

        var gridster = $('.gridster ul').gridster().data('gridster');
        var widge = gridster.$widgets;
        widge.each(function() {
            var item = $(this);
            var atts = item[0].attributes;
            //console.log(item[0]);
            //var it = $('div.widget[data-id="' + widgets[i].data_id + '"');
            var it = $('#'+item[0].id);
            console.log(it);
            it.attr("data-row", atts[2]++);
            //gridster.move_widget_to(item, atts[2].value++);
            //gridster.move_widget(item, atts[2])

            console.log(item[0].id + ' is at ' + atts[2].value + ', ' + atts[3].value);
        });
        gridster.init();


        /*var widgets = ["1_1_3_1", "1_2_1_1", "2_2_1_1", "3_2_1_2", "1_3_2_1", "1_4_1_1", "2_4_2_1", "1_5_3_1", "1_6_2_1", "3_6_1_1", "1_7_1_1", "2_7_2_1",];
        for (var i = 0; i < widgets.length; i++) {
          console.log(widgets[i]);
        }*/

        /*$.each(widge, function(key, value) {
            console.log(widge[value]);
            console.log(value.attributes);
        });*/


        /*$('.gridster > ul > li').each(function (){
        //$.each('.gridster > ul > li.gs_w', function(key, value) {
            //alert('key = ' + key);
            var $item = $(this);
            //alert($item.data);

            var col  = $item.data('col');
            var row  = $item.data('row');
            var sizex = $item.data('sizex');
            var sizey = $item.data('sizey');
            alert('item = ' + $item + ' col = ' + col + '_' + row + '_' + sizex + '_' + sizey);
            return;
        });*/
    })

    jQuery('li.box_list button').click(function() {
        var id = jQuery(this).parent().attr('id');
        jQuery('#'+id+' > h1').hide();
        jQuery('#'+id+'_button').hide();
        jQuery('#'+id+' > .list').show();
        jQuery('#'+id).css({ 'background-color': '#1762a1' });
        jQuery('#'+id).css('background-image', 'none');
    })

    jQuery('.cancel').click(function() {
        var id = jQuery(this).parent().parent().attr('id');
        jQuery('#'+id+' > h1').show();
        jQuery('#'+id+'_button').show();
        jQuery('#'+id+' > .list').hide();
        jQuery('#'+id).css({ 'background-color': '#87a6c9' });
        jQuery('#'+id).css('background-image', 'none');
    })
    jQuery('li.list_items').click(function() {
        var id = jQuery(this).attr('id');
        var main_id = jQuery(this).parent().parent().parent().attr('id');
        var postData = {
            action : 'action_name',
            id : id,
            size : main_id,
        }
        jQuery.ajax({
          method: 'GET',
          url: "/wp-admin/admin-ajax.php",
          data: postData,
          success: function(response) {
            var arr = response.split("%");
            jQuery('#'+main_id).css('background-image', 'url('+arr[0]+')');
            jQuery('#'+main_id+' > h1').text(arr[1].toUpperCase());
            jQuery('#'+main_id+' > h1').show();
            jQuery('#'+main_id+' > .list').hide();
            jQuery('#'+main_id+'_button').show();
          }
        });
    });
});