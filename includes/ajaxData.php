<?php

/*
**Get Post data in datatable
*/
if (!function_exists('get_post_data')) {
	function get_post_data()
	{

		$params = $_REQUEST;


		global $wpdb;

		// get Data by datatable
		$tsql = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post'";
		$total_posts = $wpdb->get_var($tsql);

		// get Data by datatable
		$sql = "SELECT * FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post' ";
		$sql .=  "LIMIT ". $params['start']." , ".$params['length'];
		$posts = $wpdb->get_results($sql);

		$data = array();

		foreach ($posts as $post) {
			$data[] = array(
				'ID' => $post->ID,
				'post_title' => $post->post_title,
				'post_content' => $post->post_content,
				// Add more fields as needed
			);
		}
		$output = array(
			"recordsFiltered" 	=> 	$total_posts,
			"recordsTotal"  	=>  $total_posts,
			"data"				=>	$data,
		);

		wp_send_json($output);
		die;
	}
}


add_action('wp_ajax_get_post_data', 'get_post_data');
add_action('wp_ajax_nopriv_get_post_data', 'get_post_data');
