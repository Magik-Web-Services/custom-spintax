<?php

/*
**Get Post data in datatable
*/
if (!function_exists('get_post_data')) {
	function get_post_data()
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		if(isset($_POST) && !empty($_POST)){

			// $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}posts WHERE post_type = post"));	
			// $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}posts  LIMIT 0 , 10"));	

			// echo "<pre>";
			// print_r ($data);
			// echo "</pre>";
		}
		die;
	}
}


add_action('wp_ajax_get_post_data', 'get_post_data');	
add_action( 'wp_ajax_nopriv_get_post_data', 'get_post_data' );