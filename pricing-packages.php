<?php
// Register Custom Post Type
function sis_wp_register_pricing_packages() {

	$labels = array(
		'name'                => _x( 'Pricing Packages', 'Post Type General Name', 'pricingtable' ),
		'singular_name'       => _x( 'Pricing Package', 'Post Type Singular Name', 'pricingtable' ),
		'menu_name'           => __( 'Pricing Packages', 'pricingtable' ),
		'parent_item_colon'   => __( 'Parent Pricing Package:', 'pricingtable' ),
		'all_items'           => __( 'All Pricing Packages', 'pricingtable' ),
		'view_item'           => __( 'View Pricing Package', 'pricingtable' ),
		'add_new_item'        => __( 'Add New Pricing Package', 'pricingtable' ),
		'add_new'             => __( 'Add New', 'pricingtable' ),
		'edit_item'           => __( 'Edit Pricing Package', 'pricingtable' ),
		'update_item'         => __( 'Update Pricing Package', 'pricingtable' ),
		'search_items'        => __( 'Search Pricing Package', 'pricingtable' ),
		'not_found'           => __( 'Not found', 'pricingtable' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'pricingtable' ),
	);
	$args = array(
		'label'               => __( 'pricing_packages', 'pricingtable' ),
		'description'         => __( 'Pricing Packages', 'pricingtable' ),
		'labels'              => $labels,
		'supports'            => array( 'title', ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => ''.plugins_url( 'img/packages.png' , __FILE__ ).'',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type( 'pricing_packages', $args );

}

// Hook into the 'init' action
add_action( 'init', 'sis_wp_register_pricing_packages', 0 );


function sis_wp_pricing_packages_meta_boxes() {

    add_meta_box("pricing-package-info", __("Pricing Package Info", "pricingtable"), 'sis_wp_generate_pricing_package_info', "pricing_packages", "normal", "high");
    add_meta_box("pricing-features-info", __("Pricing Features", "pricingtable"), 'sis_wp_generate_pricing_features_info', "pricing_packages", "normal", "high");
}
add_action('add_meta_boxes', 'sis_wp_pricing_packages_meta_boxes');


function sis_wp_generate_pricing_package_info() {
    global $post;

	$pricing_packages_info = array(
	    'package_price'         => __('Package Price', 'pricingtable'),
	    'package_tenure'        => __('Package Tenure', 'pricingtable'),
	    'package_buy_link'      => __('Buy Now Link', 'pricingtable'),
	);

    $package_price = get_post_meta($post->ID, "_package_price", true);
    $package_tenure = get_post_meta($post->ID, "_package_tenure", true);
    $package_buy_link = get_post_meta($post->ID, "_package_buy_link", true);

    $html = '<input type="hidden" name="pricing_package_box_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

    $html .= '<table class="form-table">';
    // Pricing Price
    $html .= "<tr>";
    $html .= "<th style=''><label for='Price'>".$pricing_packages_info['package_price']." *</label></th>";
    $html .= "<td><input name='package_price' id='package_price' type='text' value='$package_price' /></td>";
    $html .= "</tr>";
    // Pricing Tenure
    $html .= "<tr>";
    $html .= "<th style=''><label for='Tenure'>".$pricing_packages_info['package_tenure']."</label></th>";
    $html .= "<td><input name='package_tenure' id='package_tenure' type='text' value='$package_tenure' /></td>";
    $html .= "</tr>";
    // Buy Now Link
    $html .= "<tr>";
    $html .= "<th style=''><label for='Buy Now'>".$pricing_packages_info['package_buy_link']." *</label></th>";
    $html .= "<td><input name='package_buy_link' id='package_buy_link' type='text' value='$package_buy_link' /></td>";
    $html .= "</tr>";

    $html .= '</table>';

    echo $html;
}

function sis_wp_generate_pricing_features_info() {

    global $post;

	$pricing_features_info = array(
	    'add_package_features'  => __('Add Package Features', 'pricingtable'),
	    'add_features'          => __('Add Features', 'pricingtable'),
	    'delete'                => __('Delete', 'pricingtable'),
	);

    $package_features = get_post_meta($post->ID, "_package_features", true);
    $package_features = ($package_features == '') ? array() : json_decode($package_features);

    $html .= '<table class="form-table">';

    $html .= "<tr>";
    $html .= "<th style=''><label for='Price'>".$pricing_features_info['add_package_features']."</label></th>";
    $html .= "<td><input name='package_feature' id='package_feature' type='text'  /> <input type='button' id='add_features' value='".$pricing_features_info['add_features']."' /></td>";
    $html .= "</tr>";

    $html .= "<tr><td><ul id='package_features_box' name='package_features_box' >";

    foreach ($package_features as $package_feature) {
        $html .= "<li><input type='hidden' name='package_features[]' value='$package_feature' />$package_feature
        <a href='javascript:void(0);'> ".$pricing_features_info['delete']."</a></li>";
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