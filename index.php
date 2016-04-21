<?php
/*
Plugin Name: Modulr
Description: Modulessssss
Author: Chris Smith
Version: 0.1
*/

global $modulr_db_version;
$modulr_db_version = '1.0';

function modulr_install() {
	global $wpdb;
	global $modulr_db_version;

	/*$table_name = $wpdb->prefix . 'modulr';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		widget_id text NOT NULL,
		row mediumint(9) DEFAULT '' NOT NULL,
		col mediumint(9) DEFAULT '' NOT NULL,
		sizex mediumint(9) DEFAULT '' NOT NULL,
		sizey mediumint(9) DEFAULT '' NOT NULL,
		last_updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";*/

	$table_name = $wpdb->prefix . 'modulr';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		widget_id text NOT NULL,
		row varchar(12) DEFAULT '' NOT NULL,
		col varchar(12) DEFAULT '' NOT NULL,
		sizex varchar(12) DEFAULT '' NOT NULL,
		sizey varchar(12) DEFAULT '' NOT NULL,
		link_id mediumint(9),
		last_updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'modulr_db_version', $modulr_db_version );
}

function modulr_install_data() {
	global $wpdb;
	
	$id_list = array("1_1_3_1", "1_2_1_1", "2_2_1_1", "3_2_1_2", "1_3_2_1", "1_4_1_1", "2_4_2_1", "1_5_3_1", "1_6_2_1", "3_6_1_1", "1_7_1_1", "2_7_2_1");
	$row_list = array("1", "2", "2", "2", "3", "4", "4", "5", "6", "6", "7", "7");
	$col_list = array("1", "1", "2", "3", "1", "1", "2", "1", "1", "3", "1", "2");
	$x_list = array("3", "1", "1", "1", "2", "1", "2", "3", "2", "1", "1", "2");
	$y_list = array("1", "1", "1", "2", "1", "1", "1", "1", "1", "1", "1", "1");

	$table_name = $wpdb->prefix . 'modulr';
	
	foreach($id_list as $key=>$id) {
		$wpdb->insert( 
			$table_name,
			array( 
				'widget_id' => $id,
				'row' => $row_list[$key],
				'col' => $col_list[$key],
				'sizex' => $x_list[$key],
				'sizey' => $y_list[$key],
				'last_updated' => current_time( 'mysql' ),
			) 
		);
	}
}

register_activation_hook( __FILE__, 'modulr_install' );
register_activation_hook( __FILE__, 'modulr_install_data' );


add_action('admin_menu', 'modulr_setup_menu');
 
function modulr_setup_menu(){
	add_menu_page( 'Modulr', 'Modulr', 'manage_options', 'modulr', 'modulr_init' );
}

add_action('wp_ajax_action_name', 'my_ajax_processing_function');
add_action('wp_ajax_nopriv_action_name', 'my_ajax_processing_function');


function my_ajax_processing_function() {
	//print_r($_GET);
	$id = $_GET['id'];
	//echo "id = ".$id;
	$size = substr($_GET['size'], 0, -2);
	$img = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), $size );
	$title = get_the_title($id);
	//echo $title;
	//$img = wp_get_attachment_image_src( get_post_thumbnail_id($id) );
	//echo $img[0];
	$id_numbers = $img[0].'%'.$title.'%';
	echo $id_numbers;

	die();
}

function modulr_init(){
	$posts = get_posts();
	echo "<h1>Modulr</h1>";
	
	$list = "<h3>Select a Post:</h3>
	<ul id='post_list'>";
	foreach($posts as $post) {
		$list.="<li class='list_items' id='".$post->ID."'>".$post->post_title."</li>";
	}
	$list.="</ul>
	<h5 class='cancel'>Cancel</h5>";

	echo '<section class="demo">
			<div class="gridster">
				<ul>
					<li class="box_list" id="1_1_3_1" data-row="1" data-col="1" data-sizex="3" data-sizey="1"><h1>A 3x1</h1><button id="1_1_3_1_button">Set</button><div class="list" style="display:none;"">'; echo $list; echo '</div></li>

					<li class="box_list" id="1_2_1_1" data-row="2" data-col="1" data-sizex="1" data-sizey="1"><h1>B 1x1</h1><button id="1_2_1_1_button">Set</button><div class="list" style="display:none;"">'; echo $list; echo '</div></li>
					<li class="box_list" id="2_2_1_1" data-row="2" data-col="2" data-sizex="1" data-sizey="1"><h1>C 1x1</h1><button id="2_2_1_1_button">Set</button><div class="list" style="display:none;"">'; echo $list; echo '</div></li>
					<li class="box_list" id="3_2_1_2" data-row="2" data-col="3" data-sizex="1" data-sizey="2"><h1>D 1x2</h1><button id="3_2_1_2_button">Set</button><div class="list" style="display:none;"">'; echo $list; echo '</div></li>
					<li class="box_list" id="1_3_2_1" data-row="3" data-col="1" data-sizex="2" data-sizey="1"><h1>E 2x1</h1><button id="1_3_2_1_button">Set</button><div class="list" style="display:none;"">'; echo $list; echo '</div></li>

					<li class="box_list" id="1_4_1_1" data-row="4" data-col="1" data-sizex="1" data-sizey="1"><h1>F 1x1</h1><button id="1_4_1_1_button">Set</button><div class="list" style="display:none;"">'; echo $list; echo '</div></li>
					<li class="box_list" id="2_4_2_1" data-row="4" data-col="2" data-sizex="2" data-sizey="1"><h1>G 2x1</h1><button id="2_4_2_1_button">Set</button><div class="list" style="display:none;"">'; echo $list; echo '</div></li>
					
					<li class="box_list" id="1_5_3_1" data-row="5" data-col="1" data-sizex="3" data-sizey="1"><h1>H 3x1</h1><button id="1_5_3_1_button">Set</button><div class="list" style="display:none;"">'; echo $list; echo '</div></li>

					<li class="box_list" id="1_6_2_1" data-row="6" data-col="1" data-sizex="2" data-sizey="1"><h1>I 2x1</h1><button id="1_6_2_1_button">Set</button><div class="list" style="display:none;"">'; echo $list; echo '</div></li>
					<li class="box_list" id="3_6_1_1" data-row="6" data-col="3" data-sizex="1" data-sizey="1"><h1>J 1x1</h1><button id="3_6_1_1_button">Set</button><div class="list" style="display:none;"">'; echo $list; echo '</div></li>

					<li class="box_list" id="1_7_1_1" data-row="1" data-col="2" data-sizex="1" data-sizey="1"><h1>K 1x1</h1><button id="1_7_1_1_button">Set</button><div class="list" style="display:none;"">'; echo $list; echo '</div></li>
					<li class="box_list" id="2_7_2_1" data-row="1" data-col="1" data-sizex="2" data-sizey="1"><h1>L 2x1</h1><button id="2_7_2_1_button">Set</button><div class="list" style="display:none;"">'; echo $list; echo '</div></li>
				</ul>
			</div>
		</section><div id="save">Save</div>';
}

function gr_enqueue($hook) {
	/*if('edit.php' != $hook) {
		return;
	}*/
	//wp_enqueue_style( 'gridster_css', plugin_dir_url( __FILE__ ) . 'main.css');
	//wp_enqueue_script('gridster', get_template_directory_url().'/gridster.js');
	//wp_enqueue_script( 'gridster_min', plugin_dir_url( __FILE__ ) . 'gridster/dist/jquery.gridster.min.js');
	//wp_enqueue_script( 'gridster_min', plugin_dir_url( __FILE__ ) . '/libs/qunit/junit.js');
	wp_enqueue_style( 'gridster_js_css', plugin_dir_url( __FILE__ ) . 'assets/css/jquery.gridster.css');
	wp_enqueue_style( 'gridster_css', plugin_dir_url( __FILE__ ) . 'assets/css/styles.css');
	wp_enqueue_script( 'jquery_js', plugin_dir_url( __FILE__ ) . 'assets/jquery.js');
	wp_enqueue_script( 'gridster_js', plugin_dir_url( __FILE__ ) . 'assets/jquery.gridster.js');
	//wp_enqueue_script( 'gridster_test', plugin_dir_url( __FILE__ ) . 'gridster/jquery.gridster_test.js');

  /*<script src="../src/jquery.gridster.js"></script>
  <script src="jquery.gridster_test.js"></script>*/
	wp_enqueue_script( 'gridster', plugin_dir_url( __FILE__ ) . 'gridster.js');
}

add_action('admin_enqueue_scripts', 'gr_enqueue')

?>