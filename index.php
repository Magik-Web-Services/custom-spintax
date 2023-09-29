<?php

/**
 * Plugin Name: Spintax wordpress plugin
 * Description: Add custom spintax to your existing posts, page or product based on the csv file that you import.
 * Plugin URI: 
 * Version: 1.4
 * Author: Arvid
 * Author URI: 
 * Text Domain:spintax-text
 * Domain Path:/languages
 * */

if (!defined('ABSPATH')) {
	exit;
}

// Define plugin url path
define('SPINTAX_VERSON', rand());
define('SPINTAX_NAME', "spintax-text");
define('SPINTAX_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SPINTAX_PLUGIN_DIR', dirname(__FILE__));
define('SPINTAX_JS', SPINTAX_PLUGIN_URL . 'assets/js/');
define('SPINTAX_CSS', SPINTAX_PLUGIN_URL . 'assets/css/');
define('SPINTAX_LIBS', SPINTAX_PLUGIN_URL . 'assets/');
define('SPINTAX_IMG', SPINTAX_PLUGIN_URL . 'assets/js/icons/');
define('SPINTAX_IMAGE', SPINTAX_PLUGIN_URL . 'assets/images/');
define('SPINTAX_INC', SPINTAX_PLUGIN_DIR . '/includes/');


/**
 * Add languages files
 * Translate language
 */
add_action('init', 'spintax_language_translate');
function spintax_language_translate()
{
	$locale = determine_locale();
	$locale = apply_filters('plugin_locale', $locale, 'spintax-text');
	unload_textdomain('spintax-text');
	load_textdomain('spintax-text', SPINTAX_PLUGIN_DIR . '/languages/spintax-text-' . $locale . '.mo');
	load_plugin_textdomain('spintax-text', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

/**
 * Include all css and script files
 */
include(SPINTAX_INC . 'spintax-scripts.php');

/**
 * includes functions.php files
 */
include(SPINTAX_INC . 'functions.php');

/**
 * includes functions.php files
 */
include(SPINTAX_INC . 'spintax-custom-menu.php');

/*
**Create Database table for plugin activation hook
*/
if (!function_exists('spintax_db_install')) {
	function spintax_db_install()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'spintax';
		$sql1 = "CREATE TABLE  $table_name (
		`spintax_id` int NOT NULL AUTO_INCREMENT,
		`item_id` BIGINT DEFAULT NULL,
		`product` varchar(255) DEFAULT NULL,
		`main_cat` varchar(255) DEFAULT NULL,
		`subcat` varchar(255) DEFAULT NULL,
		`tag` varchar(255) DEFAULT NULL,
		`manufacturer` varchar(255) DEFAULT NULL,
		`model_name` varchar(255) DEFAULT NULL,
		`mpn` varchar(255) DEFAULT NULL,
		`price` varchar(255) DEFAULT NULL,
		`outputvoltage1` varchar(255) DEFAULT NULL,
		`outputcurrent1` varchar(255) DEFAULT NULL,
		`outputpower1` varchar(255) DEFAULT NULL,
		`ratedtotalbatteryvoltage` varchar(255) DEFAULT NULL,
		`ratedtotalbatterycapacity` varchar(255) DEFAULT NULL,
		`ratedtotalbatteryenergy` varchar(255) DEFAULT NULL,
		`batterymodelnumber` varchar(255) DEFAULT NULL,
		`batteryprice` varchar(255) DEFAULT NULL,
		`author_id` bigint NOT NULL,
		`sheet_name` varchar(255) DEFAULT NULL,
		`created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`updated_date` datetime DEFAULT NULL,
		PRIMARY KEY (`spintax_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql1);
	}
	register_activation_hook(__FILE__, 'spintax_db_install');
}

/**
 * Setting link to plugin
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'spintax_add_plugin_page_settings_link');
function spintax_add_plugin_page_settings_link($links)
{
	$links[] = '<a href="' . admin_url('admin.php?page=spintax') . '">' . __('Settings', 'spintax-text') . '</a>';
	return $links;
}

/**
 * Spintax metabox for post,page and product
 */
include(SPINTAX_INC . 'spintax-metabox.php');

/**
 * Spintax metabox for post,page and product
 */
include(SPINTAX_INC . 'class-spintax.php');
/**
 * Spintax ajax for post,page datatables
 */
include(SPINTAX_INC . 'ajaxData.php');

/**
 * Use for front to show spintax before and after
 */
function spintax_text_show_after_before_content($content)
{
	global $post, $wpdb;
	$get_all_setting  = get_option('spintax_all_settings', true);
	$get_all_setting  = $get_all_setting['setting'];
	if ((get_post_type() == 'post' || get_post_type() == 'product' || get_post_type() == 'page')) {
		$postId = $post->ID;
		if ($postId == get_the_ID() && isset($get_all_setting['exclude_ids']) && !empty($get_all_setting['exclude_ids']) && is_array($get_all_setting['exclude_ids']) &&!in_array(get_the_ID(),$get_all_setting['exclude_ids'][get_post_type()])) {
			$spintax_category_select = get_post_meta($postId, 'spintax_select_category_html', true);
			$spintax_select_tag_html = get_post_meta($postId, 'spintax_select_tag_html', true);
			$spintax_selected_tag = get_post_meta($postId, 'spintax_selected_tag', true);
			$spintax_selected_category = get_post_meta($postId, 'spintax_selected_category', true);
			$spintax_taxt = get_post_meta($postId, 'spintax_textarea', true);
			$spintax_variable_key = get_post_meta($postId, 'spintax_variable_key', true);
			$spintax_variable_value = get_post_meta($postId, 'spintax_variable_value', true);
			$spintax_refresh = get_post_meta($postId, 'spintax_refresh', true);


			if ($spintax_category_select == 'on' || $spintax_select_tag_html == 'on') {
				if ($spintax_select_tag_html == 'on') {
					$tag_detail = get_the_tags($postId);
					if (isset($tag_detail) && empty($tag_detail)) {
						$termID = $spintax_selected_tag;
					} else {
						$termID = $tag_detail[0]->term_id;
					}
					$spintax_taxt = get_term_meta($termID, 'tag_spintax', true);
					$spintax_variable_key = get_term_meta($termID, 'spintax_variable_key', true);
					$spintax_variable_value = get_term_meta($termID, 'spintax_variable_value', true);
					$spintax_refresh = get_term_meta($termID, 'spintax_refresh', true);
				} else {					
					$category_detail = get_the_category($postId);	
					if(isset($spintax_selected_category) && !empty($spintax_selected_category)){
						$categoryID = $spintax_selected_category;
					}else{
						$categoryID = $category_detail[0]->term_id;
					}	
					if (isset($categoryID) && !empty($categoryID)) {
						$spintax_taxt = get_term_meta($categoryID, 'category_spintax', true);
						$spintax_variable_key = get_term_meta($categoryID, 'spintax_variable_key', true);
						$spintax_variable_value = get_term_meta($categoryID, 'spintax_variable_value', true);
						$spintax_refresh = get_term_meta($categoryID, 'spintax_refresh', true);
					}
				}
			}

			$spintax_search_map_html = get_post_meta($postId, 'spintax_search_map_html', true);
			$custom_field_map_post = get_post_meta($postId, 'custom_field_map_post', true);

			$table_name = $wpdb->prefix . 'spintax';
			if (isset($spintax_search_map_html) && !empty($spintax_search_map_html)) {
				
				$spintax_search_map_html = implode(",", $spintax_search_map_html);
				$sheetresults = $wpdb->get_row("SELECT * FROM `$table_name` WHERE `spintax_id` IN ($spintax_search_map_html)", ARRAY_A);
			} else {	
				if (isset($custom_field_map_post) && !empty($custom_field_map_post)) {
					$sheetresults = $wpdb->get_row("SELECT * FROM `$table_name` WHERE `item_id` IN ($custom_field_map_post)", ARRAY_A);
				}
			}
			if (isset($sheetresults) && !empty($sheetresults)) {
				$table = '<table border="1" cellspadding="0" cellspacing="0" class="infodatatable">';
				$table .= '<tbody>';
				unset($sheetresults['spintax_id']);
				unset($sheetresults['item_id']);
				unset($sheetresults['author_id']);
				unset($sheetresults['sheet_name']);
				unset($sheetresults['created_date']);
				unset($sheetresults['updated_date']);
				$sheetresults['Manufacturer'] = $sheetresults['manufacturer'];
				unset($sheetresults['manufacturer']);
				$sheetresults['Main cat'] = $sheetresults['main_cat'];
				unset($sheetresults['main_cat']);
				$sheetresults['Subcat'] = $sheetresults['subcat'];
				unset($sheetresults['subcat']);
				$sheetresults['Tag'] = $sheetresults['tag'];
				unset($sheetresults['tag']);
				$sheetresults['Mpn'] = $sheetresults['mpn'];
				unset($sheetresults['mpn']);
				$sheetresults['Price'] = $sheetresults['price'];
				unset($sheetresults['price']);
				$sheetresults['Model name'] = $sheetresults['model_name'];
				unset($sheetresults['model_name']);
				$sheetresults['output voltage'] = $sheetresults['outputvoltage1'];
				unset($sheetresults['outputvoltage1']);
				$sheetresults['output current'] = $sheetresults['outputcurrent1'];
				unset($sheetresults['outputcurrent1']);
				$sheetresults['output power'] = $sheetresults['outputpower1'];
				unset($sheetresults['outputpower1']);
				$sheetresults['rated total battery voltage[V]'] = $sheetresults['ratedtotalbatteryvoltage'];
				unset($sheetresults['ratedtotalbatteryvoltage']);
				$sheetresults['rated total battery capacity[Ahr]'] = $sheetresults['ratedtotalbatterycapacity'];
				unset($sheetresults['ratedtotalbatterycapacity']);
				$sheetresults['rated total battery energy[Whr]'] = $sheetresults['ratedtotalbatteryenergy'];
				unset($sheetresults['ratedtotalbatteryenergy']);
				$sheetresults['battery model number'] = $sheetresults['batterymodelnumber'];
				unset($sheetresults['batterymodelnumber']);
				$sheetresults['battery price'] = $sheetresults['batteryprice'];
				unset($sheetresults['batteryprice']);
				foreach ($sheetresults as $sheetkey => $sheetvalue) {
					if (!empty($sheetvalue)) {
						$table .= '<tr>';
						$table .= '<th align="left">' . ucwords($sheetkey) . '</th>';
						$table .= '<td>' . $sheetvalue . '</td>';
						$table .= '</tr>';
					}
				}
				$table .= '</tbody>';
				$table .= '</table>';
			} else {
				$table = '';
			}

			if (isset($spintax_taxt) && !empty($spintax_taxt)) {
				$spintax = new Spintax();
				$string = $spintax_taxt;
				$sp_string =  $spintax->process($string, $spintax_refresh);
				$sp_string = str_replace($spintax_variable_key, $spintax_variable_value, $sp_string);
				$sp_string = str_replace('[', '', $sp_string);
				$sp_string = str_replace(']', '', $sp_string);
			} else {
				$sp_string = '';
			}
			$view_spintax = $get_all_setting[get_post_type()];
			$html =  '<p>' . $sp_string . '</p>';
			switch ($view_spintax) {
				case 'stnt':
					$new_html = $html . $content;
					break;
				case 'sttb':
					$new_html = $html . $content . $table;
					break;
				case 'sbnt':
					$new_html =  $content . $html;
					break;
				case 'sbtb':
					$new_html =  $content . $html . $table;
					break;
				case 'otb':
					$new_html =  $content . $table;
					break;
				default:
					$new_html =  $content;
			}
		}else{
			$new_html = $content;
		}
		return $new_html;
	} else {
		return $content;
	}
}
add_filter('the_content', 'spintax_text_show_after_before_content');

add_shortcode('wp-spintax', 'handler_function_name');
function handler_function_name($atts){
	ob_start();
	if (!isset($atts['type'])) {
		return;
	}
	$get_all_setting  = get_option('spintax_all_settings', true);
	$get_all_setting  = $get_all_setting['setting'];
	global $post, $wpdb;
	$postId = $post->ID;
	if (isset($get_all_setting[get_post_type()]) && !empty($get_all_setting[get_post_type()]) && $get_all_setting[get_post_type()] == 'oms') {
		if (!empty($atts['type']) && $atts['type'] == 'text') {
			if ((get_post_type() == 'post' || get_post_type() == 'product' || get_post_type() == 'page')) {
				if ($postId == get_the_ID() && !in_array(get_the_ID(),$get_all_setting['exclude_ids'][get_post_type()])) {
					$spintax_category_select = get_post_meta($postId, 'spintax_select_category_html', true);
					$spintax_select_tag_html = get_post_meta($postId, 'spintax_select_tag_html', true);
					$spintax_selected_tag = get_post_meta($postId, 'spintax_selected_tag', true);
					$spintax_selected_category = get_post_meta($postId, 'spintax_selected_category', true);
					$spintax_taxt = get_post_meta($postId, 'spintax_textarea', true);
					$spintax_variable_key = get_post_meta($postId, 'spintax_variable_key', true);
					$spintax_variable_value = get_post_meta($postId, 'spintax_variable_value', true);
					if ($spintax_category_select == 'on' || $spintax_select_tag_html == 'on') {
						if ($spintax_select_tag_html == 'on') {
							$tag_detail = get_the_tags($postId);
							if (isset($tag_detail) && !empty($tag_detail) && count($tag_detail) > 1) {
								$termID = $spintax_selected_tag;
							} else {
								$termID = $tag_detail[0]->term_id;
							}
							if (isset($tag_detail) && !empty($tag_detail)) {
								$spintax_taxt = get_term_meta($termID, 'tag_spintax', true);
								$spintax_variable_key = get_term_meta($termID, 'spintax_variable_key', true);
								$spintax_variable_value = get_term_meta($termID, 'spintax_variable_value', true);
							}
						} else {
							$category_detail = get_the_category($postId);
							if (isset($category_detail) && !empty($category_detail) && count($category_detail) > 1) {
								$categoryID = $spintax_selected_category;
							} else {
								$categoryID = $category_detail[0]->term_id;
							}
							if (isset($category_detail) && !empty($category_detail) && $category_detail[0]->slug != 'uncategorized') {
								$spintax_taxt = get_term_meta($categoryID, 'category_spintax', true);
								$spintax_variable_key = get_term_meta($categoryID, 'spintax_variable_key', true);
								$spintax_variable_value = get_term_meta($categoryID, 'spintax_variable_value', true);
							}
						}
					}
					$get_all_setting  = get_option('spintax_all_settings', true);
					$refreshing = (isset($atts['refresh']) && !empty($atts['refresh'])) ? $atts['refresh'] : '';
					$get_all_setting  = $get_all_setting['setting'];
					if (isset($spintax_taxt) && !empty($spintax_taxt)) {
						$spintax = new Spintax();
						$string = $spintax_taxt;
						$sp_string =  $spintax->process($string, $refreshing);
						$sp_string = str_replace($spintax_variable_key, $spintax_variable_value, $sp_string);
						$sp_string = str_replace('[', '', $sp_string);
						$sp_string = str_replace(']', '', $sp_string);
					} else {
						$sp_string = '';
					}
					$html =  '<p>' . $sp_string . '</p>';
					echo $html;
				}
			}
		} elseif (!empty($atts['type']) && $atts['type'] == 'table') {
			if ((get_post_type() == 'post' || get_post_type() == 'product' || get_post_type() == 'page')) {
				$spintax_search_map_html = get_post_meta($postId, 'spintax_search_map_html', true);
				$custom_field_map_post = get_post_meta($postId, 'custom_field_map_post', true);
				$sheetresults = '';
				$table_name = $wpdb->prefix . 'spintax';
				if (isset($spintax_search_map_html) && !empty($spintax_search_map_html)) {
					$spintax_search_map_html = implode(",", $spintax_search_map_html);
					$sheetresults = $wpdb->get_row("SELECT * FROM `$table_name` WHERE `spintax_id` IN ($spintax_search_map_html)", ARRAY_A);
				} else {
					if (isset($custom_field_map_post) && !empty($custom_field_map_post)) {
						$sheetresults = $wpdb->get_row("SELECT * FROM `$table_name` WHERE `item_id` IN ($custom_field_map_post)", ARRAY_A);
					}
				}
				if (isset($sheetresults) && !empty($sheetresults)) {
					$table = '<table border="1" cellspadding="0" cellspacing="0" class="infodatatable">';
					$table .= '<tbody>';
					unset($sheetresults['spintax_id']);
					unset($sheetresults['item_id']);
					unset($sheetresults['author_id']);
					unset($sheetresults['sheet_name']);
					unset($sheetresults['created_date']);
					unset($sheetresults['updated_date']);
					$sheetresults['Manufacturer'] = $sheetresults['manufacturer'];
					unset($sheetresults['manufacturer']);
					$sheetresults['Main cat'] = $sheetresults['main_cat'];
					unset($sheetresults['main_cat']);
					$sheetresults['Subcat'] = $sheetresults['subcat'];
					unset($sheetresults['subcat']);
					$sheetresults['Tag'] = $sheetresults['tag'];
					unset($sheetresults['tag']);
					$sheetresults['Mpn'] = $sheetresults['mpn'];
					unset($sheetresults['mpn']);
					$sheetresults['Price'] = $sheetresults['price'];
					unset($sheetresults['price']);
					$sheetresults['Model name'] = $sheetresults['model_name'];
					unset($sheetresults['model_name']);
					$sheetresults['output voltage'] = $sheetresults['outputvoltage1'];
					unset($sheetresults['outputvoltage1']);
					$sheetresults['output current'] = $sheetresults['outputcurrent1'];
					unset($sheetresults['outputcurrent1']);
					$sheetresults['output power'] = $sheetresults['outputpower1'];
					unset($sheetresults['outputpower1']);
					$sheetresults['rated total battery voltage[V]'] = $sheetresults['ratedtotalbatteryvoltage'];
					unset($sheetresults['ratedtotalbatteryvoltage']);
					$sheetresults['rated total battery capacity[Ahr]'] = $sheetresults['ratedtotalbatterycapacity'];
					unset($sheetresults['ratedtotalbatterycapacity']);
					$sheetresults['rated total battery energy[Whr]'] = $sheetresults['ratedtotalbatteryenergy'];
					unset($sheetresults['ratedtotalbatteryenergy']);
					$sheetresults['battery model number'] = $sheetresults['batterymodelnumber'];
					unset($sheetresults['batterymodelnumber']);
					$sheetresults['battery price'] = $sheetresults['batteryprice'];
					unset($sheetresults['batteryprice']);
					foreach ($sheetresults as $sheetkey => $sheetvalue) {
						if (!empty($sheetvalue)) {
							$table .= '<tr>';
							$table .= '<th align="left">' . ucwords($sheetkey) . '</th>';
							$table .= '<td>' . $sheetvalue . '</td>';
							$table .= '</tr>';
						}
					}
					$table .= '</tbody>';
					$table .= '</table>';
					echo $table;
				}
			}
		}
	} else {
		echo '';
	}

	return ob_get_clean();
}