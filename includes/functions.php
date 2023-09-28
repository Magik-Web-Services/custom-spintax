<?php

/**
 * Create Csv upload Directory
 */
add_action('init', 'spintax_create_new_upload_table');
function spintax_create_new_upload_table()
{
	$upload = wp_upload_dir();
	$upload_dir = $upload['basedir'];
	$filePath = $upload_dir . '/spintax/';
	if (!is_dir($filePath)) {
		mkdir($filePath, 0700, true);
	}
}

function spintax_csv_file_import_to_database($item_id, $product, $main_cat, $subcat, $tag, $manufacturer, $model_name, $mpn, $price, $outputvoltage1, $outputcurrent1, $outputpower1, $ratedtotalbatteryvoltage, $ratedtotalbatterycapacity, $ratedtotalbatteryenergy, $batterymodelnumber, $batteryprice, $fileName){
	global $wpdb;
	$prefix = $wpdb->prefix;
	$table_name = $prefix . "spintax";

	$saved_id = $wpdb->get_results("SELECT item_id FROM $table_name WHERE item_id = " . $item_id, ARRAY_A);
	if ($fileName == $saved_id[0]['sheet_name']) {
		$final_rslt    =    $wpdb->update(
			$table_name,
			array(
				'item_id'     => $item_id,
				'product'     => $product,
				'main_cat' => $main_cat,
				'subcat'     => $subcat,
				'tag'            => $tag,
				'manufacturer'           => $manufacturer,
				'model_name'     => $model_name,
				'mpn'     => $mpn,
				'price' => $price,
				'outputvoltage1'     => $outputvoltage1,
				'outputcurrent1'            => $outputcurrent1,
				'outputpower1'           => $outputpower1,
				'ratedtotalbatteryvoltage'     => $ratedtotalbatteryvoltage,
				'ratedtotalbatterycapacity'     => $ratedtotalbatterycapacity,
				'ratedtotalbatteryenergy' => $ratedtotalbatteryenergy,
				'batterymodelnumber'     => $batterymodelnumber,
				'batteryprice'            => $batteryprice,
				'author_id'           => get_current_user_id(),
				'sheet_name'     => $fileName,
				'created_date'     => date("Y-m-d h:i:sa"),
				'updated_date' => date("Y-m-d h:i:sa"),
			),
			array('item_id' => $item_id),
			array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
			array('%d')
		);
	} else {
		$final_rslt    =    $wpdb->insert(
			$table_name,
			array(
				'item_id'     => $item_id,
				'product'     => $product,
				'main_cat' => $main_cat,
				'subcat'     => $subcat,
				'tag'            => $tag,
				'manufacturer'           => $manufacturer,
				'model_name'     => $model_name,
				'mpn'     => $mpn,
				'price' => $price,
				'outputvoltage1'     => $outputvoltage1,
				'outputcurrent1'            => $outputcurrent1,
				'outputpower1'           => $outputpower1,
				'ratedtotalbatteryvoltage'     => $ratedtotalbatteryvoltage,
				'ratedtotalbatterycapacity'     => $ratedtotalbatterycapacity,
				'ratedtotalbatteryenergy' => $ratedtotalbatteryenergy,
				'batterymodelnumber'     => $batterymodelnumber,
				'batteryprice'            => $batteryprice,
				'author_id'           => get_current_user_id(),
				'sheet_name'     => $fileName,
				'created_date'     => date("Y-m-d h:i:sa"),
				'updated_date' => '',
			),
			array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
		);
	}
	return $final_rslt;
}


/**
 * Spintax csv imported list for product,post,page
 */
add_action('wp_ajax_spintax_get_list_items', 'spintax_get_list_items');
function spintax_get_list_items()
{
	global $wpdb;
	if (isset($_GET['term']) && !empty($_GET['term']) && wp_verify_nonce(sanitize_text_field($_GET['security']), 'security_nonce')) {
		$search = $_GET['term'];
		$table_name = $wpdb->prefix . 'spintax';
		$sheetresults = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `product` LIKE '%$search%'");

		$json = [];
		foreach ($sheetresults as $sheetresult) {
			$json[] = ['id' => $sheetresult->spintax_id, 'text' => $sheetresult->product];
		}
		echo json_encode($json);
	}
	die();
}

/**
 * Spintax csv imported list for product,post,page
 */
add_action('wp_ajax_spintax_get_list_items_for_exclude_id', 'spintax_get_list_items_for_exclude_id');
function spintax_get_list_items_for_exclude_id()
{
	global $wpdb;
	if (isset($_GET['term']) && !empty($_GET['term']) && wp_verify_nonce(sanitize_text_field($_GET['security']), 'security_nonce')) {
		$search = $_GET['term'];
		//         $table_name = $wpdb->prefix . 'spintax';
		//         $sheetresults = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `product` LIKE '%$search%'");

		$args = array(
			'post_type'  => $_GET['data_type'],
			'post_status'  => 'publish',
			's' => $search
		);
		$sheetresults = get_posts( $args );

		$json = [];
		foreach ($sheetresults as $sheetresult) {
			$json[] = ['id' => $sheetresult->ID, 'text' => $sheetresult->post_title];
		}
		echo json_encode($json);
	}
	die();
}

/**
 * Selected post type spinttax category
 */
add_action('wp_ajax_spintax_get_post_type_category', 'spintax_get_post_type_category');
function spintax_get_post_type_category()
{
	global $wpdb;
	if (isset($_GET['post_type']) && !empty($_GET['post_type']) && wp_verify_nonce(sanitize_text_field($_GET['security']), 'security_nonce')) {
		$search = $_GET['post_type'];
		$terms = get_terms([
			'taxonomy' => $search,
			'hide_empty' => false,
		]);
		$json = [];
		foreach ($terms as $term) {
			$json[] = ['id' => $term->term_id, 'text' => $term->name];
		}
		echo json_encode($json);
	}
	die();
}