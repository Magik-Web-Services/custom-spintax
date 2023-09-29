<?php

/*
**Get Post data in datatable
*/
if (!function_exists('get_post_data')) {
	function get_post_data()
	{
		global $wpdb;
		$sql = "SELECT * FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post' LIMIT 0 , 10";
		$posts = $wpdb->get_results($sql);

		// echo "<pre>";
		// print_r($posts);
		// echo "</pre>";
		// die()

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
			"recordsFiltered" 	=> 	1,
			"recordsTotal"  	=>  1,
			"data"				=>	$data
		);

		wp_send_json($output);
		die;
	}
}


add_action('wp_ajax_get_post_data', 'get_post_data');
add_action('wp_ajax_nopriv_get_post_data', 'get_post_data');
