<?php

/**
 * Include admin area style and sctipt
 */
add_action('admin_enqueue_scripts', 'spntax_admin_styles_and_scripts');
function spntax_admin_styles_and_scripts()
{

    if (isset($_REQUEST['page']) && !empty($_REQUEST['page']) && (sanitize_text_field($_REQUEST['page']) == 'spintax' || sanitize_text_field($_REQUEST['page']) == 'spintax-import' || sanitize_text_field($_REQUEST['page']) == 'spintax-setting' ||  sanitize_text_field($_REQUEST['page']) == 'spintax-bulk-edit-post' ||  sanitize_text_field($_REQUEST['page']) == 'spintax-bulk-edit-page')) {
        wp_enqueue_style(SPINTAX_NAME . '-btsp', SPINTAX_LIBS . 'libs/bootstrap/css/bootstrap.min.css', array(), SPINTAX_VERSON);
        wp_enqueue_style(SPINTAX_NAME . '-datatable', SPINTAX_LIBS . 'libs/datatable/css/dataTables.bootstrap5.min.css', array(), SPINTAX_VERSON);
        wp_enqueue_style(SPINTAX_NAME . '-datatable-responsive', SPINTAX_LIBS . 'libs/datatable/css/responsive.bootstrap5.min.css', array(), SPINTAX_VERSON);
    }
    wp_enqueue_style(SPINTAX_NAME . '-select2', SPINTAX_LIBS . 'libs/select2/css/select2.min.css', array(), SPINTAX_VERSON);
    wp_enqueue_style(SPINTAX_NAME . '-fontawesome', SPINTAX_LIBS . 'libs/fontawesome/css/all.min.css', array(), SPINTAX_VERSON);
    wp_enqueue_style(SPINTAX_NAME . '-administrator', SPINTAX_CSS . 'admin.css', array(), SPINTAX_VERSON);

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-tooltip');
    wp_enqueue_script(SPINTAX_NAME . '-bundle', SPINTAX_LIBS . 'libs/bootstrap/js/bootstrap.bundle.min.js', array('jquery'), false, SPINTAX_VERSON);
    wp_enqueue_script(SPINTAX_NAME . '-btsp', SPINTAX_LIBS . 'libs/bootstrap/js/bootstrap.min.js', array('jquery'), false, SPINTAX_VERSON);
    wp_enqueue_script(SPINTAX_NAME . '-plupload', SPINTAX_LIBS . 'libs/plupload/plupload.full.min.js', array('jquery'), false, SPINTAX_VERSON);
    wp_enqueue_script(SPINTAX_NAME . '-datatable1', SPINTAX_LIBS . 'libs/datatable/js/jquery.dataTables.min.js', array('jquery'), false, SPINTAX_VERSON);
    wp_enqueue_script(SPINTAX_NAME . '-datatable-bootstrap51', SPINTAX_LIBS . 'libs/datatable/js/dataTables.bootstrap5.min.js', array('jquery'), false, SPINTAX_VERSON);
    wp_enqueue_script(SPINTAX_NAME . '-datatable-bootstrap5-responsive1', SPINTAX_LIBS . 'libs/datatable/js/dataTables.responsive.min.js', array('jquery'), false, SPINTAX_VERSON);
    wp_enqueue_script(SPINTAX_NAME . '-datatable-responsive1', SPINTAX_LIBS . 'libs/datatable/js/responsive.bootstrap5.min.js', array('jquery'), false, SPINTAX_VERSON);
    wp_enqueue_script(SPINTAX_NAME . '-select21', SPINTAX_LIBS . 'libs/select2/js/select2.full.min.js', array('jquery'), false, SPINTAX_VERSON);
    wp_enqueue_script(SPINTAX_NAME . '-adminnew', SPINTAX_JS . 'customspin.js', array(), false, SPINTAX_VERSON);
    // Add
    wp_enqueue_script(SPINTAX_NAME . '-postajax', SPINTAX_JS . 'postajax.js', array(), false, SPINTAX_VERSON);


    $admin_url = strtok(admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')), '?');
    wp_localize_script(
        SPINTAX_NAME . '-adminnew',
        'MyAjax',
        array(
            'ajaxurl' => $admin_url,
            'pluginurl' => SPINTAX_PLUGIN_URL,
            'security_nonce' => wp_create_nonce('security_nonce'),
        )
    );
}
/**
 * Include user area style and sctipt
 */
add_action('wp_enqueue_scripts', 'spntax_user_styles_and_scripts');
function spntax_user_styles_and_scripts()
{
    wp_enqueue_style(SPINTAX_NAME . '-user', SPINTAX_CSS . 'user.css', array(), SPINTAX_VERSON);
}
