<?php
/*
Plugin Name: Modulr
Description: Modulessssss
Author: Chris Smith
Version: 0.1
*/

/*** Database setup ***/

global $modulr_db_version;
$modulr_db_version = '1.0';

function modulr_install() {
	global $wpdb;
	global $modulr_db_version;

	$table_name = $wpdb->prefix . 'modulr';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		widget_id text NOT NULL,
		row varchar(12) DEFAULT '' NOT NULL,
		col varchar(12) DEFAULT '' NOT NULL,
		sizex varchar(12) DEFAULT '' NOT NULL,
		sizey varchar(12) DEFAULT '' NOT NULL,
		link_id varchar(12),
		free_text varchar(255) NULL,
		free_link varchar(255) NULL,
		last_updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'modulr_db_version', $modulr_db_version );
}

function enqueue_media_uploader()
{
    wp_enqueue_media();
}

add_action("admin_enqueue_scripts", "enqueue_media_uploader");

/*** Database initial population ***/

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
				'link_id' => '0',
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

/*** Ajax return functions ***/
/* Set widget post function */

add_action('wp_ajax_set_item', 'set_item_ajax_processing_function');
add_action('wp_ajax_nopriv_set_item', 'set_item_ajax_processing_function');

function set_item_ajax_processing_function() {
	$id = $_GET['id'];
	$size = $_GET['size'];
	$img = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), $size );
	$title = get_the_title($id);
	$id_numbers = $img[0].'%'.$title.'%';
	echo $id_numbers;

	die();
}

/* Save widget state and items function */

add_action('wp_ajax_save_all', 'save_all_ajax_processing_function');
add_action('wp_ajax_nopriv_save_all', 'save_all_ajax_processing_function');

function save_all_ajax_processing_function() {
	$id_list = $_GET['id_list'];
	$row_list = $_GET['row_list'];
	$col_list = $_GET['col_list'];
	$sizex_list = $_GET['sizex_list'];
	$sizey_list = $_GET['sizey_list'];
	$post_list = $_GET['post_list'];
	$title_list = $_GET['title_list'];
	$link_list = $_GET['link_list'];

	global $wpdb;
	$table_name = $wpdb->prefix . 'modulr';

	for($v=0;$v<count($id_list);$v++) {
		$data = array(
		    'row' => $row_list[$v],
		    'col' => $col_list[$v],
		    'sizex' => $sizex_list[$v],
		    'sizey' => $sizey_list[$v],
		    'link_id' => $post_list[$v],
		    'free_text' => $title_list[$v],
		    'free_link' => substr($link_list[$v], 6),
		);
		$where = array( 'widget_id' => $id_list[$v] );
		$wpdb->update( $table_name, $data, $where);
	}
	die();
}

add_action('wp_ajax_upload_media', 'upload_media_ajax_processing_function');
add_action('wp_ajax_nopriv_upload_media', 'upload_media_ajax_processing_function');

function upload_media_ajax_processing_function() {
	$image_id = $_GET['image_id'];
	$image_url = $_GET['image_url'];
	$size = $_GET['size'];

	$img = wp_get_attachment_image_src($image_id, $size);
	echo $img[0];

	die();
}

/*** Admin page setup ***/

function modulr_init(){
	global $wpdb;

	add_image_size('1_1', 360, 360, true);
	add_image_size('2_1', 740, 360, true);
	add_image_size('1_2', 360, 740, true);
	add_image_size('3_1', 1120, 360, true);

	$table_name = $wpdb->prefix . 'modulr';
	$res = $wpdb->get_results("SELECT * FROM ".$table_name);

	$args = array('posts_per_page' => -1);
	$posts = get_posts($args);
	$type = 'products';
	$args=array(
		'post_type' => 'project',
		'posts_per_page' => -1
	);
	$projects = new WP_Query($args);
	$projects = $projects->get_posts();

	echo "<h1>Modulr</h1><div class='modulr_container'>";

	/*** Projects ***/
	$projects_list="<h3>Select a Project:</h3>
		<ul id='post_list'>";
	foreach($projects as $project) {
		$projects_list.="<li class='list_items' id='".$project->ID."'>".$project->post_title."</li>";
	}
	$projects_list.="</ul>";

	/*** Posts ***/
	$posts_list="<h3>Select a Post:</h3>
		<ul id='post_list'>";
	foreach($posts as $post) {
		$posts_list.="<li class='list_items' id='".$post->ID."'>".$post->post_title."</li>";
	}
	$posts_list.="</ul>";

	$media_list="<h3>Upload Media:</h3>";

	echo '<div id="saved">Saved</div><section class="demo"><button id="save">Save</button>
			<div class="gridster">
				<ul id="box_ul">';

	$title_list = array("Large Horizontal 1", "Small 1", "Small 2", "Medium Vertical 1", "Medium Horizontal 1", "Small 3", "Medium Horizontal 2", "Large Horizontal 2", "Medium Horizontal 3", "Small 4", "Small 5", "Medium Horizontal 4");

	$post_id = '0';

	foreach ($res as $key=>$rs) {
		$link = '';
		if($rs->link_id!='0') {
			$post_id = $rs->link_id;
			$sub = $post_id;
			$img = '';
			$size = $rs->sizex."_".$rs->sizey;
			if(strlen($post_id)>4) {
				if(substr($post_id, 0, 4)=='6666') {
					$sub = substr($post_id, 4);
					$img = wp_get_attachment_image_src($sub, $size);
					$title = $rs->free_text;
					if($rs->free_link)
						$link = "Link: ".$rs->free_link;
				}
			}

			if($img=='') {
				$img = wp_get_attachment_image_src( get_post_thumbnail_id( $sub ), $size );
				$title = get_the_title($post_id);
			}

			$css = "style='background-image:url(".$img[0].");'";
			
		} else {
			$post_id = '0';
			$css = "style='background:rgb(23, 98, 161);'";
			$title = $title_list[$key];
		}

		echo '<li post_id="'.$post_id.'" class="box_list" id="'.$rs->widget_id.'" data-row="'.$rs->row.'" data-col="'.$rs->col.'" data-sizex="'.$rs->sizex.'" data-sizey="'.$rs->sizey.'"" '.$css.'>
				<div class="rel_container_'.$rs->sizex.'_'.$rs->sizey.'">
					<button class="inner_button" id="'.$rs->widget_id.'_button">Set</button>
					<div class="custom_link" id="custom_link_'.$key.'">'.$link.'</div>
					<div class="fixed_container">
						<h1>'.$title.'</h1>
					</div>
					<div class="list" style="display:none;"">
						<div id="tabs_'.$key.'" class="c-tabs no-js">
							<div class="c-tabs-nav">
								<a href="#" class="c-tabs-nav__link_'.$key.' c-tabs-nav__link is-active">Projects</a>
								<a href="#" class="c-tabs-nav__link_'.$key.' c-tabs-nav__link">Posts</a>
								<a href="#" class="c-tabs-nav__link_'.$key.' c-tabs-nav__link">Media</a>
							</div>
							<div class="c-tab_'.$key.' c-tab is-active">
								<div class="c-tab__content">'
									.$projects_list.
								'</div>
								<h5 class="cancel">Cancel</h5>
							</div>
							<div class="c-tab_'.$key.' c-tab">
								<div class="c-tab__content">'
									.$posts_list.
								'</div>
								<h5 class="cancel">Cancel</h5>
							</div>
							<div class="c-tab_'.$key.' c-tab">
								<div class="c-tab__content">'
									.$media_list.
									'<button id="upload_media_'.$key.'" class="upload_media">Upload Media</button>
								</div>
								<h5 class="cancel">Cancel</h5>
							</div>
						</div>
					</div>
				</div>
			</li>';
	}
	echo "<script>";
			for($i=0;$i<count($res);$i++) {
		        echo "var myTabs_".$i." = tabs({
		            el: '#tabs_".$i."',
		            tabNavigationLinks: '.c-tabs-nav__link_".$i."',
		            tabContentContainers: '.c-tab_".$i."'
		        });
		        myTabs_".$i.".init();";
		    }
			echo "</script>";
		echo '</ul>
			</div>
		</section></div>';
}

/*** Script enqueueing ***/

function gr_enqueue($hook) {
	wp_enqueue_style( 'gridster_js_css', plugin_dir_url( __FILE__ ) . 'assets/css/jquery.gridster.css');
	wp_enqueue_style( 'gridster_css', plugin_dir_url( __FILE__ ) . 'assets/css/styles.css');
	wp_enqueue_script( 'gridster_js', plugin_dir_url( __FILE__ ) . 'assets/jquery.gridster.js');
	wp_enqueue_script( 'gridster', plugin_dir_url( __FILE__ ) . 'gridster.js');
	wp_enqueue_script(' tabs', plugin_dir_url(__FILE__) . 'tabs.js');
}

add_action('admin_enqueue_scripts', 'gr_enqueue');

function register_plugin_styles() {
	wp_register_style('modulr_frontend', plugin_dir_url( __FILE__ ) .'assets/css/modulr_front.css');
	wp_enqueue_style('modulr_frontend');
}

add_action( 'wp_enqueue_scripts', 'register_plugin_styles' );


/*** Shortcode generation ***/

function modulr_output() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'modulr';
	$res = $wpdb->get_results("SELECT * FROM ".$table_name);

	echo "<div style='width:100%;'>";
	foreach ($res as $key=>$rs) {
		$post_id = $rs->link_id;
		$size = $rs->sizex."_".$rs->sizey;
		$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
		$css = "background-image:url(".$img[0].");";
		$title = get_the_title($post_id);

		$extra = 0;
		if($rs->sizex>1)
			$extra = ($rs->sizex-1)*20;

		echo "<a href=".get_the_permalink($post_id)."><div class='modulr_blocks' style='".$css."float:left;height:".($rs->sizey*360)."px; width:".(($rs->sizex*360)+$extra)."px;'>".$title."</div></a>";
	}
	echo "</div>";
}

add_shortcode('modulr', 'modulr_output');


/*
Todo:
	Add in more media option
	Add in text option
	UI overhaul
	Load content, disable, enable regens starting content
	Media page save
	Save li bug

Further On:
	Box resizer
	Box adder
	Box removal
	Template loading
	Add to any page
*/

?>