<?php

/**
 * Register Meta Box
 * Post,Page and Product
 */
add_action('add_meta_boxes', 'spintax_register_meta_box');
function spintax_register_meta_box()
{
	add_meta_box('spintax-meta-box-1', __('Spintax', 'spintax-text'), 'spintax_callback_textarea_html', ['post', 'page', 'product'], 'advanced', 'high');
	add_meta_box('spintax-meta-box-2', __('Spintax Variables', 'spintax-text'), 'spintax_variable_html', ['post', 'page', 'product'], 'advanced', 'high');
	add_meta_box('spintax-meta-box-3', __('Spintax by Category', 'spintax-text'), 'spintax_select_category_html', ['post', 'page', 'product'], 'advanced', 'high');
	add_meta_box('spintax-meta-box-4', __('Spintax by Tag', 'spintax-text'), 'spintax_select_tag_html', ['post', 'page', 'product'], 'advanced', 'high');
	add_meta_box('spintax-meta-box-6', __('Search and Map', 'spintax-text'), 'spintax_search_map_html', ['post', 'page', 'product'], 'advanced', 'high');
	add_meta_box('spintax-meta-box-5', __('Custom Field Maping', 'spintax-text'), 'spintax_field_map_html', ['post', 'page', 'product'], 'advanced', 'high');
}

/**
 * Meta Box 1
 * Spintax textarea html
 */
function spintax_callback_textarea_html($meta_id)
{
	wp_nonce_field(basename(__FILE__), 'spintax-nonce');
	$outline = '';
	$spintax_textarea = get_post_meta($meta_id->ID, 'spintax_textarea', true);
	$spintax_refresh = get_post_meta($meta_id->ID, 'spintax_refresh', true);
	$checked = (!empty($spintax_refresh) && $spintax_refresh == 'on') ? 'checked="checked"' : '';
	$outline .= '<h4 style="margin-bottom: 10px">Refresh Spintax</h4>';
	$outline .= '<label class="switch"><input type="checkbox" name="spintax_refresh" ' . $checked . '><span class="slider round"></span></label>';
	$outline .= '<br><br>';
	$outline .= '<textarea class="widefat" cols="50" rows="5" name="spintax_textarea">' . esc_html($spintax_textarea) . '</textarea>';
	echo $outline;
}

/**
 * Meta Box 2
 * Spintax variable html
 */
function spintax_variable_html($meta_id)
{
	$spintax_variable_key = get_post_meta($meta_id->ID, 'spintax_variable_key', true);
	$spintax_variable_value = get_post_meta($meta_id->ID, 'spintax_variable_value', true);
	$variable_count = get_post_meta($meta_id->ID, 'spintax_variable_count', true);
	if (isset($spintax_variable_key) && !empty($spintax_variable_key) && sizeof($spintax_variable_key) == 0 && isset($spintax_variable_value) && !empty($spintax_variable_value)) { ?>
<table id="variable" class="widefat">
	<thead>
		<tr>
			<th class="left"><label for="spintax_variable_key"><?php echo __('Name', 'spintax-text'); ?></label></th>
			<th><label for="spintax_variable_value"><?php echo __('Value', 'spintax-text'); ?></label></th>
			<th><label for="action"><?php echo __('Action', 'spintax-text'); ?> <span class="dashicons dashicons-plus-alt addmetafield"></span></label></th>
		</tr>
	</thead>
	<tbody>
		<?php
                // $x = 0;
                $active = (count($spintax_variable_key) > 1) ? 'active' : '';
                for ($y = 0; $y <= $variable_count; $y++) {
		?>
		<tr>
			<td><input type="text" id="spintax_variable_key" name="spintax_variable_key[]" class="widefat" value="<?php echo (isset($spintax_variable_key[$y]) && !empty($spintax_variable_key[$y])) ? esc_html($spintax_variable_key[$y]) : ''; ?>" /></td>
			<td><input type="text" id="spintax_variable_value" name="spintax_variable_value[]" class="widefat" value="<?php echo (isset($spintax_variable_value[$y]) && !empty($spintax_variable_value[$y])) ? esc_html($spintax_variable_value[$y]) : ''; ?>" /></td>
			<td class="actionbtn <?php echo $active; ?>"><a href="javascript:void(0)" class="button delete_row"><?php echo __('Delete', 'spintax-text'); ?></a></td>
		</tr>
		<?php
				} ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3" style="padding:0"><input type="hidden" name="variable_count" value="<?php echo $variable_count; ?>"></td>
		</tr>
	</tfoot>
</table>
<?php
    } else {
		global  $wpdb;
		$table_name = $wpdb->prefix . 'spintax';
		$spintax_search_map_html = get_post_meta($meta_id->ID, 'spintax_search_map_html', true);
		$custom_field_map_post = get_post_meta($meta_id->ID, 'custom_field_map_post', true);


		if (isset($spintax_search_map_html) && !empty($spintax_search_map_html)) {
			$spintax_search_map_html = implode(",", $spintax_search_map_html);
			$sheetresults = $wpdb->get_row("SELECT * FROM `$table_name` WHERE `spintax_id` IN ($spintax_search_map_html)", ARRAY_A);
		} else {
			if (isset($custom_field_map_post) && !empty($custom_field_map_post)) {
				$sheetresults = $wpdb->get_row("SELECT * FROM `$table_name` WHERE `item_id` IN ($custom_field_map_post)", ARRAY_A);
			}
		} ?>
<table id="variable" class="widefat">
	<thead>
		<tr>
			<th class="left"><label for="spintax_variable_key"><?php echo __('Name', 'spintax-text'); ?></label></th>
			<th><label for="spintax_variable_value"><?php echo __('Value', 'spintax-text'); ?></label></th>
			<th><label for="action"><?php echo __('Action', 'spintax-text'); ?> <span class="dashicons dashicons-plus-alt addmetafield"></span></label></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if (isset($sheetresults) && !empty($sheetresults)) {
			unset($sheetresults['spintax_id']);
			unset($sheetresults['item_id']);
			unset($sheetresults['author_id']);
			unset($sheetresults['sheet_name']);
			unset($sheetresults['created_date']);
			unset($sheetresults['updated_date']);
			unset($sheetresults['product']);
// 			unset($sheetresults['manufacturer']);
			unset($sheetresults['main_cat']);
			unset($sheetresults['subcat']);
			unset($sheetresults['tag']);
			unset($sheetresults['mpn']);
			$sheetresults['$manufacturer'] = $sheetresults['manufacturer'];
			unset($sheetresults['manufacturer']);
			$sheetresults['$price'] = $sheetresults['price'];
			unset($sheetresults['price']);
			$sheetresults['$modelname'] = $sheetresults['model_name'];
			unset($sheetresults['model_name']);
			$sheetresults['$outputvoltage'] = $sheetresults['outputvoltage1'];
			unset($sheetresults['outputvoltage1']);
			$sheetresults['$outputcurrent'] = $sheetresults['outputcurrent1'];
			unset($sheetresults['outputcurrent1']);
			$sheetresults['$outputpower'] = $sheetresults['outputpower1'];
			unset($sheetresults['outputpower1']);
			$sheetresults['$ratedtotalbatteryvoltage'] = $sheetresults['ratedtotalbatteryvoltage'];
			unset($sheetresults['ratedtotalbatteryvoltage']);
			$sheetresults['$ratedtotalbatterycapacity'] = $sheetresults['ratedtotalbatterycapacity'];
			unset($sheetresults['ratedtotalbatterycapacity']);
			$sheetresults['$ratedtotalbatteryenergy'] = $sheetresults['ratedtotalbatteryenergy'];
			unset($sheetresults['ratedtotalbatteryenergy']);
			$sheetresults['$batterymodelnumber'] = $sheetresults['batterymodelnumber'];
			unset($sheetresults['batterymodelnumber']);
			$sheetresults['$batteryprice'] = $sheetresults['batteryprice'];
			unset($sheetresults['batteryprice']);

			$active = (count($sheetresults) > 1) ? 'active' : '';
			foreach ($sheetresults as $key => $value) {
		?>
		<tr>
			<td><input type="text" id="spintax_variable_key" name="spintax_variable_key[]" class="widefat" value="<?php echo $key; ?>" /></td>
			<td><input type="text" id="spintax_variable_value" name="spintax_variable_value[]" class="widefat" value="<?php echo $value; ?>" /></td>
			<td class="actionbtn <?php echo $active; ?>"><a href="javascript:void(0)" class="button delete_row"><?php echo __('Delete', 'spintax-text'); ?></a></td>
		</tr>
		<?php
			}
		} else {
		?>
		<tr>
			<td><input type="text" id="spintax_variable_key" name="spintax_variable_key[]" class="widefat" value="" /></td>
			<td><input type="text" id="spintax_variable_value" name="spintax_variable_value[]" class="widefat" value="" /></td>
			<td class="actionbtn"><a href="javascript:void(0)" class="button delete_row"><?php echo __('Delete', 'spintax-text'); ?></a></td>
		</tr>
		<?php
		}
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3" style="padding:0"><input type="hidden" name="variable_count" value="<?php echo (!empty($sheetresults)) ? count($sheetresults) : '1'; ?>"></td>
		</tr>
	</tfoot>
</table>
<?php
	}
}

/**
 * Meta Box 3
 * Spintax Select category html
 */
function spintax_select_category_html($meta_id)
{
	global $wpdb;
	$outline = '';
	$spintax_select_category_html = get_post_meta($meta_id->ID, 'spintax_select_category_html', true);
	$spintax_selected_category = get_post_meta($meta_id->ID, 'spintax_selected_category', true);

	$terms_category = get_the_terms($meta_id->ID, 'product_cat');
	if (isset($terms_category) && !empty($terms_category)) {
		$categoryss = get_the_terms($meta_id->ID, 'product_cat');
	} else {
		$categoryss = get_the_category($meta_id->ID);
	}
	if (isset($categoryss) && !empty($categoryss) && count($categoryss) > 1) {
		$cate_option = '<option>--' . __('Select Category', 'spintax-text') . '--</option>';
		foreach ($categoryss as $getmain_cat) {
			$selected = ($spintax_selected_category == $getmain_cat->term_id) ? 'selected="selected"' : '';
			$cate_option .= '<option value="' . $getmain_cat->term_id . '" ' . $selected . '>' . $getmain_cat->name . '</option>';
		}
		$outline .= '<select name="spintax_selected_category">' . $cate_option . '</select> ';
	}

	$checked = (!empty($spintax_select_category_html) && $spintax_select_category_html == 'on') ? 'checked="checked"' : '';
	$cate_option = '<label class="switch"><input type="checkbox" name="spintax_select_category_html" ' . $checked . '><span class="slider round"></span></label>';

	$outline .= $cate_option;
	echo $outline;
}
/**
 * Meta Box 4
 * Spintax Select tag html
 */
function spintax_select_tag_html($meta_id)
{
	global $wpdb;
	$spintax_select_tag_html = get_post_meta($meta_id->ID, 'spintax_select_tag_html', true);
	$spintax_selected_tag = get_post_meta($meta_id->ID, 'spintax_selected_tag', true);

	$terms_tag = get_the_terms($meta_id->ID, 'product_tag');
	if (isset($terms_tag) && !empty($terms_tag)) {
		$tagss = get_the_terms($meta_id->ID, 'product_tag');
	} else {
		$tagss = get_the_tags();
	}

	$outline = '';
	if (isset($tagss) && !empty($tagss) && count($tagss) > 1) {
		$tag_option = '<option>--' . __('Select Tag', 'spintax-text') . '--</option>';
		foreach ($tagss as $tag) {

			$selected = ($spintax_selected_tag == $tag->term_id) ? 'selected="selected"' : '';
			$tag_option .= '<option value="' . $tag->term_id . '" ' . $selected . '>' . $tag->name . '</option>';
		}
		$outline .= '<select name="spintax_selected_tag">' . $tag_option . '</select> ';
	}

	$checked = (!empty($spintax_select_tag_html) && $spintax_select_tag_html == 'on') ? 'checked="checked"' : '';
	$tag_option = '<label class="switch"><input type="checkbox" name="spintax_select_tag_html" ' . $checked . '><span class="slider round"></span></label>';
	$outline .= $tag_option;

	echo $outline;
}
/**
 * Meta Box 5
 * Spintax search map html
 */
function spintax_search_map_html($meta_id)
{
	global $wpdb;
	$spintax_search_map_html = get_post_meta($meta_id->ID, 'spintax_search_map_html', true);
	$option = '';
	if (isset($spintax_search_map_html) && !empty($spintax_search_map_html)) {
		$spintax_search_map_html = implode(",", $spintax_search_map_html);
		$table_name = $wpdb->prefix . 'spintax';
		$sheetresults = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `spintax_id` IN ($spintax_search_map_html)");
		foreach ($sheetresults as $sheetresult) {
			$option .= '<option value="' . $sheetresult->spintax_id . '" selected="selected">' . $sheetresult->product . '</option>';
		}
	}
	$outline = '';
	$outline .= '<select class="js-data-example-ajax form-control widefat" name="spintax_search_map_html[]">' . $option . '</select>';
	echo $outline;
}

/**
 * Meta Box 6
 * Custom Field Map Spintax to csv sheet
 */
function spintax_field_map_html($meta_id)
{
	global $wpdb;
	$custom_field_map_post = get_post_meta($meta_id->ID, 'custom_field_map_post', true);
	$outline = '';
	$outline .= '<input type="text" name="custom_field_map_post" value="' . $custom_field_map_post . '"/>';
	echo $outline;
}

/**
 * Save Meta field value
 */
add_action('save_post', 'spintax_save_postdata');
function spintax_save_postdata($post_id)
{
	if (!isset($_POST['spintax-nonce']) || !wp_verify_nonce($_POST['spintax-nonce'], basename(__FILE__))) {
		return $post_id;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}

	if (isset($_POST) && !empty($_POST['action']) && $_POST['action'] == 'editpost') {
		update_post_meta($post_id, 'spintax_refresh', $_POST['spintax_refresh']);
		update_post_meta($post_id, 'spintax_textarea', sanitize_text_field($_POST['spintax_textarea']));
		foreach ($_POST['spintax_variable_key'] as $spintax_variable_key => $spintax_varival) {
			$spintax_varikey[] = sanitize_text_field($spintax_varival);
		}
		update_post_meta($post_id, 'spintax_variable_key', $spintax_varikey);
		foreach ($_POST['spintax_variable_value'] as $spintax_variable_value => $spintax_variablevalue) {
			$spintax_variableval[] = sanitize_text_field($spintax_variablevalue);
		}
		update_post_meta($post_id, 'spintax_variable_value', $spintax_variableval);
		update_post_meta($post_id, 'spintax_variable_count', sanitize_text_field($_POST['variable_count']));

		update_post_meta($post_id, 'spintax_select_category_html', sanitize_text_field($_POST['spintax_select_category_html']));
		update_post_meta($post_id, 'spintax_selected_category', sanitize_text_field($_POST['spintax_selected_category']));

		update_post_meta($post_id, 'spintax_select_tag_html', sanitize_text_field($_POST['spintax_select_tag_html']));
		update_post_meta($post_id, 'spintax_selected_tag', sanitize_text_field($_POST['spintax_selected_tag']));

		update_post_meta($post_id, 'spintax_search_map_html', $_POST['spintax_search_map_html']);
		update_post_meta($post_id, 'custom_field_map_post', $_POST['custom_field_map_post']);
	}
}


/**
 * Meta Box 2
 * Spintax Category variable html
 */
add_action('category_add_form_fields', 'spintax_cat_variable_html', 10, 2);
add_action('category_edit_form', 'spintax_cat_variable_html', 10, 2);
add_action('product_cat_add_form_fields', 'spintax_cat_variable_html', 10, 2);
add_action('product_cat_edit_form', 'spintax_cat_variable_html', 10, 2);
function spintax_cat_variable_html($term_id)
{
	if (isset($term_id->term_id) && !empty($term_id->term_id)) {
		$spintax_refresh = get_term_meta($term_id->term_id, 'spintax_refresh', true);
		$spintax_variable_key = get_term_meta($term_id->term_id, 'spintax_variable_key', true);
		$spintax_variable_value = get_term_meta($term_id->term_id, 'spintax_variable_value', true);
		$category_spintax = get_term_meta($term_id->term_id, 'category_spintax', true);
		$variable_count = get_term_meta($term_id->term_id, 'spintax_variable_count', true);
	} else {
		$spintax_variable_key =  '';
		$spintax_variable_value = '';
		$category_spintax = '';
		$variable_count = '';
	}
	if (isset($_GET['tag_ID']) && !empty($_GET['tag_ID'])) {


?>
<table class="form-table">
	<tbody>
		<tr class="form-field form-required term-name-wrap">
			<th valign="top" scope="row"><label for="spintax_refresh"><?php _e('Refresh Spintax'); ?></label></th>
			<td>
				<?php $checked = (!empty($spintax_refresh) && $spintax_refresh == 'on') ? 'checked="checked"' : ''; ?>
				<label class="switch">
					<input type="checkbox" id="spintax_refresh" name="spintax_refresh" <?php echo $checked; ?> />
					<span class="slider round"></span>
				</label>
			</td>
		</tr>
		<tr class="form-field form-required term-name-wrap">
			<th valign="top" scope="row"><label for="category_spintax"><?php _e('Category Spintax'); ?></label></th>
			<td>
				<textarea class="large-text" cols="50" rows="5" id="category_spintax" name="category_spintax"><?php echo esc_textarea($category_spintax); ?></textarea><br />
				<span class="description"><?php _e('Please enter spintax for category'); ?></span>
			</td>
		</tr>
	</tbody>
</table>
<?php
														   } else {
?>
<tr class="form-field">
	<th valign="top" scope="row"><label for="category_spintax">
		<h4><?php _e('Category Spintax'); ?></h4>
		</label></th>
	<td>
		<textarea class="large-text" cols="50" rows="5" id="category_spintax" name="category_spintax"></textarea><br />
		<span class="description"><?php _e('Please enter spintax for category'); ?></span>
	</td>
</tr>
<?php
																  }
	if (isset($spintax_variable_key)  && !empty($spintax_variable_key)  && sizeof($spintax_variable_key) > 0 && isset($spintax_variable_value) && !empty($spintax_variable_value)) {
?>

<table id="variable" class="widefat">
	<thead>
		<tr>
			<th class="left"><label for="spintax_variable_key"><?php echo __('Name', 'spintax-text'); ?></label></th>
			<th><label for="spintax_variable_value"><?php echo __('Value', 'spintax-text'); ?></label></th>
			<th><label for="action"><?php echo __('Action', 'spintax-text'); ?><span class="dashicons dashicons-plus-alt addmetafield"></span></label></th>
		</tr>
	</thead>
	<tbody>
		<?php
                $x = 0;
                $active = (count($spintax_variable_key) > 1) ? 'active' : '';
                foreach ($spintax_variable_key as $spintax_cat_varival) { ?>
		<tr>
			<td><input type="text" id="spintax_variable_key" name="spintax_variable_key[]" class="widefat" value="<?php echo esc_html($spintax_cat_varival); ?>" /></td>
			<td><input type="text" id="spintax_variable_value" name="spintax_variable_value[]" class="widefat" value="<?php echo esc_html($spintax_variable_value[$x]); ?>" /></td>
			<td class="actionbtn <?php echo $active; ?>"><a href="javascript:void(0)" class="button delete_row"><?php echo __('Delete', 'spintax-text'); ?></a></td>
		</tr>
		<?php
																		 $x++;
																		} ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3" style="padding:0"><input type="hidden" name="variable_count" value="<?php echo (!empty($variable_count)) ? $variable_count : '1'; ?>"></td>
		</tr>
	</tfoot>
</table>
<?php
    } else {

		$spin_variables = [
			'$price',
			'$modelname',
			'$outputvoltage',
			'$outputcurrent',
			'$outputpower',
			'$ratedtotalbatteryvoltage',
			'$ratedtotalbatterycapacity',
			'$ratedtotalbatteryenergy',
			'$batterymodelnumber',
			'$batteryprice',
			'$manufacturer'
		];
?>
<tr class="form-field">
	<th valign="top" scope="row"><label for="category_spintax">
		<h4><?php _e('Category Spintax Variable'); ?></h4>
		</label></th>
</tr>
<table id="variable" class="widefat">
	<thead>
		<tr>
			<th class="left"><label for="spintax_variable_key"><?php echo __('Name', 'spintax-text'); ?></label></th>
			<th><label for="spintax_variable_value"><?php echo __('Value', 'spintax-text'); ?></label></th>
			<th><label for="action"><?php echo __('Action', 'spintax-text'); ?> <span class="dashicons dashicons-plus-alt addmetafield"></span></label></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$active = (count($spin_variables) > 1) ? 'active' : '';
		foreach ($spin_variables as $spin_variable) {
		?>
		<tr>
			<td><input type="text" id="spintax_variable_key" name="spintax_variable_key[]" class="widefat" value="<?php echo $spin_variable; ?>" /></td>
			<td><input type="text" id="spintax_variable_value" name="spintax_variable_value[]" class="widefat" value="" /></td>
			<td class="actionbtn <?php echo $active; ?>"><a href="javascript:void(0)" class="button delete_row"><?php echo __('Delete', 'spintax-text'); ?></a></td>
		</tr>
		<?php
		}
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3" style="padding:0"><input type="hidden" name="variable_count" value="<?php echo count($spin_variables); ?>"></td>
		</tr>
	</tfoot>
</table>
<?php
	}
}

add_action('edited_category', 'spintax_save_category_fields', 10, 2);
add_action('create_category', 'spintax_save_category_fields', 10, 2);
add_action('edited_product_cat', 'spintax_save_category_fields', 10, 2);
add_action('create_product_cat', 'spintax_save_category_fields', 10, 2);
function spintax_save_category_fields($term_id)
{
	$category_spintax = (isset($_POST['category_spintax']) && !empty($_POST['category_spintax'])) ? sanitize_text_field($_POST['category_spintax']) : '';
	update_term_meta($term_id, 'category_spintax', $category_spintax);

	$spintax_refresh = (isset($_POST['spintax_refresh']) && !empty($_POST['spintax_refresh'])) ? $_POST['spintax_refresh'] : '';
	update_term_meta($term_id, 'spintax_refresh', $spintax_refresh);

	if (isset($_POST['spintax_variable_key']) && !empty($_POST['spintax_variable_key'])) {
		foreach ($_POST['spintax_variable_key'] as $spintax_variable_key => $spintax_varival) {
			$spintax_varikey[] = sanitize_text_field($spintax_varival);
		}
		update_term_meta($term_id, 'spintax_variable_key', $spintax_varikey);
	}

	if (isset($_POST['spintax_variable_value']) && !empty($_POST['spintax_variable_value'])) {
		foreach ($_POST['spintax_variable_value'] as $spintax_variable_value => $spintax_variablevalue) {
			$spintax_variableval[] = sanitize_text_field($spintax_variablevalue);
		}
		update_term_meta($term_id, 'spintax_variable_value', $spintax_variableval);
	}
	update_term_meta($term_id, 'spintax_variable_count', sanitize_text_field($_POST['variable_count']));
}

/* End for category */

/**
 * Meta Box 2
 * Spintax Tag variable html
 */

add_action('post_tag_edit_form', 'spintax_tag_variable_html', 10, 2);
add_action('product_tag_edit_form', 'spintax_tag_variable_html', 10, 2);
function spintax_tag_variable_html($term_id)
{
	$spintax_refresh = get_term_meta($term_id->term_id, 'spintax_refresh', true);
	$spintax_variable_key = get_term_meta($term_id->term_id, 'spintax_variable_key', true);
	$tag_spintax = get_term_meta($term_id->term_id, 'tag_spintax', true);
	$spintax_variable_value = get_term_meta($term_id->term_id, 'spintax_variable_value', true);
	$variable_count = get_term_meta($term_id->term_id, 'spintax_variable_count', true);

?>
<table class="form-table">
	<tbody>
		<tr class="form-field form-required term-name-wrap">
			<th valign="top" scope="row"><label for="spintax_refresh"><?php _e('Refresh Spintax'); ?></label></th>
			<td>
				<?php $checked = (!empty($spintax_refresh) && $spintax_refresh == 'on') ? 'checked="checked"' : ''; ?>
				<label class="switch">
					<input type="checkbox" id="spintax_refresh" name="spintax_refresh" <?php echo $checked; ?> />
					<span class="slider round"></span>
				</label>
			</td>
		</tr>
		<tr class="form-field form-required term-name-wrap">
			<th valign="top" scope="row"><label for="category_spintax"><?php _e('Tag Spintax'); ?></label></th>
			<td>
				<textarea class="large-text" cols="50" rows="5" id="tag_spintax" name="tag_spintax"><?php echo esc_textarea($tag_spintax); ?></textarea><br />
				<span class="description"><?php _e('Please enter spintax for tag'); ?></span>
			</td>
		</tr>
	</tbody>
</table>
<?php
	if (isset($spintax_variable_key) && !empty($spintax_variable_key) && sizeof($spintax_variable_key) == 0 && isset($spintax_variable_value) && !empty($spintax_variable_value)) {
?>
<table id="variable" class="widefat">
	<thead>
		<tr>
			<th class="left"><label for="spintax_variable_key"><?php echo __('Name', 'spintax-text'); ?></label></th>
			<th><label for="spintax_variable_value"><?php echo __('Value', 'spintax-text'); ?></label></th>
			<th><label for="action"><?php echo __('Action', 'spintax-text'); ?><span class="dashicons dashicons-plus-alt addmetafield"></span></label> </th>
		</tr>
	</thead>
	<tbody>
		<?php
                $x = 0;
                $active = (count($spintax_variable_key) > 1) ? 'active' : '';
                foreach ($spintax_variable_key as $spintax_tag_varival) { ?>
		<tr>
			<td><input type="text" id="spintax_variable_key" name="spintax_variable_key[]" class="widefat" value="<?php echo esc_html($spintax_tag_varival); ?>" /></td>
			<td><input type="text" id="spintax_variable_value" name="spintax_variable_value[]" class="widefat" value="<?php echo esc_html($spintax_variable_value[$x]); ?>" /></td>
			<td class="actionbtn <?php echo $active; ?>"><a href="javascript:void(0)" class="button delete_row"><?php echo __('Delete', 'spintax-text'); ?></a></td>
		</tr>
		<?php
																		 $x++;
																		} ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3" style="padding:0"><input type="hidden" name="variable_count" value="<?php echo (!empty($variable_count) && $variable_count > 0) ? $variable_count : '1'; ?>"></td>
		</tr>
	</tfoot>
</table>
<?php
    } else {

		$spin_variables = [
			'$manufacturer',
			'$price',
			'$modelname',
			'$outputvoltage',
			'$outputcurrent',
			'$outputpower',
			'$ratedtotalbatteryvoltage',
			'$ratedtotalbatterycapacity',
			'$ratedtotalbatteryenergy',
			'$batterymodelnumber',
			'$batteryprice',
		];
?>
<table id="variable" class="widefat">
	<thead>
		<tr>
			<th class="left"><label for="spintax_variable_key"><?php echo __('Name', 'spintax-text'); ?></label></th>
			<th><label for="spintax_variable_value"><?php echo __('Value', 'spintax-text'); ?></label></th>
			<th><label for="action"><?php echo __('Action', 'spintax-text'); ?> <span class="dashicons dashicons-plus-alt addmetafield"></span></label></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$active = (count($spin_variables) > 1) ? 'active' : '';
		foreach ($spin_variables as $spin_variable) {
		?>
		<tr>
			<td><input type="text" id="spintax_variable_key" name="spintax_variable_key[]" class="widefat" value="<?php echo $spin_variable; ?>" /></td>
			<td><input type="text" id="spintax_variable_value" name="spintax_variable_value[]" class="widefat" value="" /></td>
			<td class="actionbtn <?php echo $active; ?>"><a href="javascript:void(0)" class="button delete_row"><?php echo __('Delete', 'spintax-text'); ?></a></td>
		</tr>
		<?php
		}
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3" style="padding:0"><input type="hidden" name="variable_count" value="<?php echo count($spin_variables); ?>"></td>
		</tr>
	</tfoot>
</table>
<?php
	}
}

add_action('add_tag_form_fields', 'spintax_add_tag_variable_html', 10, 2);
function spintax_add_tag_variable_html($term_id)
{
	$spin_variables = [
		'$manufacturer',
		'$price',
		'$modelname',
		'$outputvoltage',
		'$outputcurrent',
		'$outputpower',
		'$ratedtotalbatteryvoltage',
		'$ratedtotalbatterycapacity',
		'$ratedtotalbatteryenergy',
		'$batterymodelnumber',
		'$batteryprice',
	];
?>
<tr class="form-field form-required term-name-wrap">
	<th valign="top" scope="row"><label for="tag_spintax"><?php _e('Tag Spintax'); ?></label></th>
	<td>
		<textarea class="large-text" cols="50" rows="5" id="tag_spintax" name="tag_spintax"></textarea><br />
		<span class="description"><?php _e('Please enter spintax for tag'); ?></span>
	</td>
</tr>
<table id="variable" class="widefat">
	<thead>
		<tr>
			<th class="left"><label for="spintax_variable_key"><?php echo __('Name', 'spintax-text'); ?></label></th>
			<th><label for="spintax_variable_value"><?php echo __('Value', 'spintax-text'); ?></label></th>
			<th><label for="action"><?php echo __('Action', 'spintax-text'); ?> <span class="dashicons dashicons-plus-alt addmetafield"></span></label></th>
		</tr>
	</thead>
	<tbody>
		<?php
	$active = (count($spin_variables) > 1) ? 'active' : '';
	foreach ($spin_variables as $spin_variable) {
		?>
		<tr>
			<td><input type="text" id="spintax_variable_key" name="spintax_variable_key[]" class="widefat" value="<?php echo $spin_variable; ?>" /></td>
			<td><input type="text" id="spintax_variable_value" name="spintax_variable_value[]" class="widefat" value="" /></td>
			<td class="actionbtn <?php echo $active; ?>"><a href="javascript:void(0)" class="button delete_row"><?php echo __('Delete', 'spintax-text'); ?></a></td>
		</tr>
		<?php
	}
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3" style="padding:0"><input type="hidden" name="variable_count" value="<?php echo count($spin_variables); ?>"></td>
		</tr>
	</tfoot>
</table>
<?php
}


/**
 * Save Tag Custom field data
 */
add_action('edited_post_tag', 'spintax_save_tag_fields', 10, 2);
add_action('create_post_tag', 'spintax_save_tag_fields', 10, 2);
add_action('edited_product_tag', 'spintax_save_tag_fields', 10, 2);
add_action('create_product_tag', 'spintax_save_tag_fields', 10, 2);
function spintax_save_tag_fields($term_id)
{

	$tag_spintax = (isset($_POST['tag_spintax']) && !empty($_POST['tag_spintax'])) ? sanitize_text_field($_POST['tag_spintax']) : '';
	update_term_meta($term_id, 'tag_spintax', $tag_spintax);

	$spintax_refresh = (isset($_POST['spintax_refresh']) && !empty($_POST['spintax_refresh'])) ? $_POST['spintax_refresh'] : '';
	update_term_meta($term_id, 'spintax_refresh', $spintax_refresh);

	if (isset($_POST['spintax_variable_key']) && !empty($_POST['spintax_variable_key'])) {
		foreach ($_POST['spintax_variable_key'] as $spintax_variable_key => $spintax_varival) {
			$spintax_varikey[] = sanitize_text_field($spintax_varival);
		}
		update_term_meta($term_id, 'spintax_variable_key', $spintax_varikey);
	}

	if (isset($_POST['spintax_variable_value']) && !empty($_POST['spintax_variable_value'])) {
		foreach ($_POST['spintax_variable_value'] as $spintax_variable_value => $spintax_variablevalue) {
			$spintax_variableval[] = sanitize_text_field($spintax_variablevalue);
		}
		update_term_meta($term_id, 'spintax_variable_value', $spintax_variableval);
	}
	update_term_meta($term_id, 'spintax_variable_count', sanitize_text_field($_POST['variable_count']));
}
