<?php
/*
 * Registering and saving pricing packages
 */
function sis_wp_register_pricing_packages() {

    $labels = array(
        'name'                  => _x('Pricing Packages', 'text_domain'),
        'singular_name'         => _x('Pricing Package', 'text_domain'),
        'add_new'               => _x('Add New', 'text_domain'),
        'add_new_item'          => _x('Add New Pricing Package', 'text_domain'),
        'edit_item'             => _x('Edit Pricing Package', 'text_domain'),
        'new_item'              => _x('New Pricing Package', 'text_domain'),
        'view_item'             => _x('View Pricing Package', 'text_domain'),
        'search_items'          => _x('Search Pricing Packages', 'text_domain'),
        'not_found'             => _x('No Pricing Packages found', 'text_domain'),
        'not_found_in_trash'    => _x('No Pricing Packages found in Trash', 'text_domain'),
        'parent_item_colon'     => _x('Parent Pricing Package:', 'text_domain'),
        'menu_name'             => _x('Pricing Packages', 'text_domain'),
    );

    $args = array(
        'labels'                => $labels,
        'hierarchical'          => false,
        'description'           => 'Pricing Packages',
        'supports'              => array('title'),
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'menu_position'         => 5,
        'menu_icon'             => ''.plugins_url( 'img/packages.png' , __FILE__ ).'',
        'publicly_queryable'    => true,
        'exclude_from_search'   => false,
        'has_archive'           => true,
        'query_var'             => true,
        'can_export'            => true,
        'rewrite'               => true,
        'capability_type'       => 'post'
    );

    register_post_type('pricing_packages', $args);
}
add_action('init', 'sis_wp_register_pricing_packages');


function sis_wp_pricing_packages_meta_boxes() {

    add_meta_box("pricing-package-info", "Pricing Package Info", 'sis_wp_generate_pricing_package_info', "pricing_packages", "normal", "high");
    add_meta_box("pricing-features-info", "Pricing Features", 'sis_wp_generate_pricing_features_info', "pricing_packages", "normal", "high");
}
add_action('add_meta_boxes', 'sis_wp_pricing_packages_meta_boxes');

function sis_wp_generate_pricing_package_info() {
    global $post;

    $package_price = get_post_meta($post->ID, "_package_price", true);
    $package_tenure = get_post_meta($post->ID, "_package_tenure", true);
    $package_buy_link = get_post_meta($post->ID, "_package_buy_link", true);

    $html = '<input type="hidden" name="pricing_package_box_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

    $html .= '<table class="form-table">';
    // Pricing Price
    $html .= "<tr>";
    $html .= "<th style=''><label for='Price'>Package Price *</label></th>";
    $html .= "<td><input name='package_price' id='package_price' type='text' value='$package_price' /></td>";
    $html .= "</tr>";
    // Pricing Tenure
    $html .= "<tr>";
    $html .= "<th style=''><label for='Tenure'>Package Tenure</label></th>";
    $html .= "<td><input name='package_tenure' id='package_tenure' type='text' value='$package_tenure' /></td>";
    $html .= "</tr>";
    // Buy Now Link
    $html .= "<tr>";
    $html .= "<th style=''><label for='Buy Now'>Buy Now Link *</label></th>";
    $html .= "<td><input name='package_buy_link' id='package_buy_link' type='text' value='$package_buy_link' /></td>";
    $html .= "</tr>";

    $html .= '</table>';

    echo $html;
}

function sis_wp_generate_pricing_features_info() {

    global $post;

    $package_features = get_post_meta($post->ID, "_package_features", true);
    $package_features = ($package_features == '') ? array() : json_decode($package_features);

    $html .= '<table class="form-table">';

    $html .= "<tr>";
    $html .= "<th style=''><label for='Price'>Add Package Features</label></th>";
    $html .= "<td><input name='package_feature' id='package_feature' type='text'  /> <input type='button' id='add_features' value='Add Features' /></td>";
    $html .= "</tr>";

    $html .= "<tr><td><ul id='package_features_box' name='package_features_box' >";

    foreach ($package_features as $package_feature) {
        $html .= "<li><input type='hidden' name='package_features[]' value='$package_feature' />$package_feature
        <a href='javascript:void(0);'>Delete</a></li>";
    }
    
    $html .= "</ul></td></tr>";

    $html .= '</table>';

    echo $html;
}


function sis_wp_save_pricing_packages($post_id) {

    if (!wp_verify_nonce($_POST['pricing_package_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('pricing_packages' == $_POST['post_type'] && current_user_can('edit_post', $post_id)) {
        $package_price = (isset($_POST['package_price']) ? $_POST['package_price'] : '');
        $package_tenure = (isset($_POST['package_tenure']) ? $_POST['package_tenure'] : '');
        $package_buy_link = (isset($_POST['package_buy_link']) ? $_POST['package_buy_link'] : '');

        $package_features = (isset($_POST['package_features']) ? $_POST['package_features'] : array());
        $package_features = json_encode($package_features);

        update_post_meta($post_id, "_package_price", $package_price);
        update_post_meta($post_id, "_package_tenure", $package_tenure);
        update_post_meta($post_id, "_package_buy_link", $package_buy_link);
        update_post_meta($post_id, "_package_features", $package_features);
    } else {
        return $post_id;
    }
}
add_action('save_post', 'sis_wp_save_pricing_packages');