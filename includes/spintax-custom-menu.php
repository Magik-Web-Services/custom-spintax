<?php

/**
 * Menu for plugin setting options
 */
add_action('admin_menu', 'spintax_setting_menu');
function spintax_setting_menu()
{
    add_menu_page(__('SpinTax Setting Options', 'spintax-text'), __('SpinTax', 'spintax-text'), 'manage_options', 'spintax', '', 'dashicons-media-code', 4);
    add_submenu_page('spintax', __('SpinTax List', 'spintax-text'), __('Imported List', 'spintax-text'), 'manage_options', 'spintax', 'spintax_item_lists_html');
    add_submenu_page('spintax', __('SpainTax Import', 'spintax-text'), __('Import', 'spintax-text'), 'manage_options', 'spintax-import', 'spintax_import_html');
    add_submenu_page('spintax', __('SpainTax Settings', 'spintax-text'), __('Settings', 'spintax-text'), 'manage_options', 'spintax-setting', 'spintax_setting_html');
    add_submenu_page('spintax', __('Bulk Edit of Posts', 'spintax-text'), __('Bulk Edit of Posts', 'spintax-text'), 'manage_options', 'spintax-bulk-edit-post', 'spintax_bulk_edit_post_setting_html');
    add_submenu_page('spintax', __('Bulk Edit of Pages', 'spintax-text'), __('Bulk Edit of Pages', 'spintax-text'), 'manage_options', 'spintax-bulk-edit-page', 'spintax_bulk_edit_page_setting_html');
}

/**
 * Bulk Edit of Posts
 */
function spintax_bulk_edit_post_setting_html()
{

// Get Posts
?>
    <div class="container-fluid clear">
        <div class="row">
            <div class="col-md-12">
                <div class="p-3 w-100 border mt-4">
                    <table id="bulkedit" class="table table-hover my-0 dt-responsive nowrap" style="width:100%;margin-top: 15px !important;margin-bottom: 15px !important;">
                        <thead>
                            <tr>
                            <!-- <th><?php // echo __('#', 'spintax-text'); ?></th> -->
                            <th><?php  echo __('Item ID', 'spintax-text'); ?></th>
                            <th><?php  echo __('Title ', 'spintax-text'); ?></th>
                            <th><?php  echo __('Category', 'spintax-text'); ?></th>
                            <th><?php  echo __('Tag', 'spintax-text'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
}

/**
 * Bulk Edit of Pages
 */
function spintax_bulk_edit_page_setting_html()
{
?>
    <div class="container-fluid clear">
        <div class="row">
            <div class="col-md-12">
                <div class="p-3 w-100 border mt-4">
                    <table id="bulkedit" class="table table-hover my-0 dt-responsive nowrap" style="width:100%;margin-top: 15px !important;margin-bottom: 15px !important;">
                        <thead>
                            <tr>
                                <th><?php echo __('#', 'spintax-text'); ?></th>
                                <th><?php echo __('Item ID', 'spintax-text'); ?></th>
                                <th><?php echo __('Title', 'spintax-text'); ?></th>
                                <th><?php echo __('Type', 'spintax-text'); ?></th>
                                <th><?php echo __('Category', 'spintax-text'); ?></th>
                                <th><?php echo __('Tag', 'spintax-text'); ?></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
}




/**
 * CSV import html page function
 */
function spintax_import_html()
{
    ?>
        <div class="container-fluid clear">
            <div class="row">
                <div class="col-md-12">
                    <div class="card text-dark bg-light p-0 w-25">
                        <div class="card-header"><?php echo __('CSV Import', 'spintax-text'); ?></div>
                        <div class="card-body">
                            <form class="form-horizontal" action="" method="post" name="spintax_form_csv_import" enctype="multipart/form-data" id="spintax_form_csv_import">
                                <?php wp_nonce_field('security_nonce', 'security_nonce'); ?>
                                <div class="input-row">
                                    <div id="list">fff</div>
                                    <input class="form-control mt-3" type="button" id="pick" value="Upload">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * List of sheets and item lists
     */
    function spintax_item_lists_html()
    {
        global $wpdb;
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $filePath = $upload_dir . '/spintax/';
        $files = scandir($filePath);
        $filenames = array_slice($files, 2);
        $table_name = $wpdb->prefix . 'spintax';
        // Delete Single Product
        if (isset($_GET['del']) && !empty($_GET['del'])) {
            $sheetresults = $wpdb->delete($table_name, array('item_id' => $_GET['del']));
            $url = get_admin_url() . 'admin.php?page=spintax&view=spintax-plugin-db.csv';
            wp_redirect($url);
        }

        if (isset($_GET['view']) && !empty($_GET['view'])) {

            $filename = $_GET['view'];
            $sheetresults = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `sheet_name` LIKE '%$filename%' ORDER BY product ASC");
        ?>
            <div class="container-fluid clear">
                <!--Start Dashboard Content-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="p-3 w-100 border mt-4">
                            <table id="listofitems" class="table table-hover my-0 dt-responsive nowrap" style="width:100%;margin-top: 15px !important;margin-bottom: 15px !important;">
                                <thead>
                                    <tr>
                                        <th><?php echo __('Item ID', 'spintax-text'); ?></th>
                                        <th><?php echo __('Product', 'spintax-text'); ?></th>
                                        <th><?php echo __('Main Cat', 'spintax-text'); ?></th>
                                        <th><?php echo __('Subcat', 'spintax-text'); ?></th>
                                        <th><?php echo __('Tag', 'spintax-text'); ?></th>
                                        <th><?php echo __('Manufacturer', 'spintax-text'); ?></th>
                                        <th><?php echo __('Model Name', 'spintax-text'); ?></th>
                                        <th><?php echo __('mpn', 'spintax-text'); ?></th>
                                        <th><?php echo __('Price', 'spintax-text'); ?></th>
                                        <th><?php echo __('Output Voltage 1 (volts)', 'spintax-text'); ?></th>
                                        <th><?php echo __('Output Current 1 (amps)', 'spintax-text'); ?></th>
                                        <th><?php echo __('Output Power 1 (watts)', 'spintax-text'); ?></th>
                                        <th><?php echo __('Rated Total Battery Voltage [V]', 'spintax-text'); ?></th>
                                        <th><?php echo __('Rated Total Battery Capacity [Ahr]', 'spintax-text'); ?></th>
                                        <th><?php echo __('Rated Total Battery Energy [Whr]', 'spintax-text'); ?></th>
                                        <th><?php echo __('Battery Model Number', 'spintax-text'); ?></th>
                                        <th><?php echo __('Battery Price', 'spintax-text'); ?></th>
                                        <th><?php echo __('Created Date', 'spintax-text'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($sheetresults as $sheetresult) {
                                        echo '<tr>';
                                        echo '<td>' . $sheetresult->item_id . '<a href="?page=spintax&view=' . $filename . '&del=' . $sheetresult->item_id . '"><span style="color: red; font-size: 12px;">trash</span></a></td>';
                                        echo '<td >' . $sheetresult->product . '</td>';
                                        echo '<td>' . $sheetresult->main_cat . '</td>';
                                        echo '<td>' . $sheetresult->subcat . '</td>';
                                        echo '<td>' . $sheetresult->tag . '</td>';
                                        echo '<td>' . $sheetresult->manufacturer . '</td>';
                                        echo '<td>' . $sheetresult->model_name . '</td>';
                                        echo '<td>' . $sheetresult->mpn . '</td>';
                                        echo '<td>' . $sheetresult->price . '</td>';
                                        echo '<td>' . $sheetresult->outputvoltage1 . '</td>';
                                        echo '<td>' . $sheetresult->outputcurrent1 . '</td>';
                                        echo '<td>' . $sheetresult->outputpower1 . '</td>';
                                        echo '<td>' . $sheetresult->ratedtotalbatteryvoltage . '</td>';
                                        echo '<td>' . $sheetresult->ratedtotalbatterycapacity . '</td>';
                                        echo '<td>' . $sheetresult->ratedtotalbatteryenergy . '</td>';
                                        echo '<td>' . $sheetresult->batterymodelnumber . '</td>';
                                        echo '<td>' . $sheetresult->batteryprice . '</td>';
                                        echo '<td>' . $sheetresult->created_date . '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        <?php
        } elseif (isset($_GET['delete']) && !empty($_GET['delete'])) {
            if (file_exists($filePath . $_GET['delete'])) {
                unlink($filePath . $_GET['delete']);
                $sheetresults = $wpdb->delete($table_name, array('sheet_name' => $_GET['delete']));
                $url = get_admin_url() . 'admin.php?page=spintax';
                wp_redirect($url);
            } else {
                $url = get_admin_url() . 'admin.php?page=spintax';
                wp_redirect($url);
                exit;
            }
        } else {
        ?>
            <div class="container-fluid clear">
                <!--Start Dashboard Content-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="p-0 w-75 border mt-4">
                            <table id="lstofsheets" class="table table-hover my-0" style="width:100%;margin-top: 0px !important;margin-bottom: 0px !important;">
                                <thead>
                                    <tr>
                                        <th><?php echo __('S. No.', 'spintax-text'); ?></th>
                                        <th><?php echo __('File Name', 'spintax-text'); ?></th>
                                        <th><?php echo __('Action', 'spintax-text'); ?></th>
                                        <th><?php echo __('Spintax Shortcode', 'spintax-text'); ?></th>
                                        <th><?php echo __('Table Shortcode', 'spintax-text'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $x = 0;
                                    foreach ($filenames as $filename) {
                                        $x++;
                                        echo "<tr>";
                                        echo '<td>' . $x . '</td>';
                                        echo '<td>' . $filename . '</td>';
                                        echo '<td><a href="?page=spintax&view=' . $filename . '" class="text-decoration-none btn btn-primary">View</a> <a href="?page=spintax&delete=' . $filename . '" class="text-decoration-none btn btn-danger">Delete</a></td>';
                                        echo '<td>[wp-spintax type=text refresh="off"]</td>';
                                        echo '<td>[wp-spintax type=table]</td>';
                                        echo "</tr>";
                                    }
                                    ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
    }

    /**
     * Spintax setting page html
     */
    function spintax_setting_html()
    {


        if (isset($_POST) && !empty($_POST) && !wp_verify_nonce(sanitize_text_field($_POST['spintax-nonce']), 'spintax-nonce')) {
            $spintax_settings = $_POST['setting'];
            foreach ($spintax_settings as $key => $value) {
                // echo "<pre>";
                // echo $key;
                // echo "</pre>";

                if ($key == 'exclude_ids') {
                    $setting[sanitize_key($key)] = $value;
                } else {
                    $setting[sanitize_key($key)] = sanitize_text_field($value);
                }
            }

            $newsetting['setting'] = $setting;
            $result  = update_option('spintax_all_settings', $newsetting);
        } else {
            $result = '';
        }

        $get_all_setting  = get_option('spintax_all_settings', true);
        $get_all_setting  = $get_all_setting['setting'];
        ?>
        <form action="" method="post">
            <div class="container-fluid clear">
                <div class="row">
                    <div class="col-md-12 mt-4">
                        <?php
                        if (isset($result) && !empty($result) && $result == true) {
                        ?>
                            <div class="alert alert-success" role="alert">
                                Setting save successfully.
                            </div>
                        <?php
                        } elseif (isset($result) && !empty($result) && $result == false) {
                        ?>
                            <div class="alert alert-danger" role="alert">
                                Failed to save your setting. Please try again.
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>

                <?php
                $numOfCols = 3;
                $rowCount = 0;
                $bootstrapColWidth = 12 / $numOfCols;
                $rows = ['post', 'page', 'product'];
                ?>
                <div class="row">
                    <?php
                    foreach ($rows as $row) {
                    ?>
                        <div class="col-md-<?php echo $bootstrapColWidth; ?>">
                            <div class="thumbnail">
                                <div class="card text-dark bg-light p-0">
                                    <div class="card-header"><?php echo __('Spintax for ' . ucfirst($row), 'spintax-text'); ?></div>
                                    <div class="card-body">
                                        <?php
                                        $setting_options  = ['stnt' => 'spintax at the top, no table', 'sttb' => 'spintax at the top, table at the bottom', 'sbnt' => 'spintax at the bottom, no table', 'sbtb' => 'spintax at the bottom, table at the bottom', 'otb' => 'only table at the bottom', 'oms' => 'only manual shortcode', 'none' => 'none'];
                                        $x = 0;
                                        foreach ($setting_options as $setting_option_key => $setting_option) {
                                            $active = (!empty($get_all_setting[$row]) && $get_all_setting[$row] == $setting_option_key) ? 'checked="checked"' : '';
                                        ?>
                                            <div class="form-group">
                                                <input class="form-control" type="radio" id="po-spintax-top-nt-<?php echo $row . $x; ?>" name="setting[<?php echo $row; ?>]" value="<?php echo esc_html($setting_option_key); ?>" <?php echo  $active; ?> />
                                                <label for="po-spintax-top-nt-<?php echo $row . $x; ?>"><?php echo ucfirst(esc_html($setting_option)); ?></label>
                                            </div>
                                        <?php
                                            $x++;
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="card text-dark bg-light p-0">
                                    <div class="card-header"><?php echo __('Exclude Specific ' . ucfirst($row) . 's', 'spintax-text'); ?></div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="" class="form-check-label">Select ids : </label>
                                            <?php
                                            $optionnew = '';
                                            if (isset($get_all_setting['exclude_ids']) && !empty($get_all_setting['exclude_ids']) && is_array($get_all_setting['exclude_ids'])) {
                                                foreach ($get_all_setting['exclude_ids'][$row] as $id_value) {
                                                    $optionnew .= '<option value="' . $id_value . '">' . get_the_title($id_value) . '</opiton>';
                                                }
                                            }
                                            ?>
                                            <select class="filtered_posts_ids form-control" name="setting[exclude_ids][<?php echo $row; ?>][]" data-select-type="<?php echo $row; ?>" id="exclude-<?php echo $row; ?>"><?php echo $optionnew; ?></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                        $rowCount++;
                        if ($rowCount % $numOfCols == 0) echo '</div><div class="row">';
                    }
                    ?>
                </div>
                <div class="row">
                    <div class="col-md-12 my-3">
                        <?php wp_nonce_field(basename(__FILE__), 'spintax-nonce'); ?>
                        <button class="btn btn-info text-white" name="submmit" type="submit" value="submit">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    <?php
    }
