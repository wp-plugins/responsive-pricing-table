<?php

class Pricing_Package {

    protected static $instance = null;
    
    function __construct() {
        add_action( 'init', array( $this, 'custom_post' ), 0 );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_post' ) );

        add_filter('manage_edit-pricing_packages_columns', array( $this, 'columns_head'));
        add_action('manage_pricing_packages_posts_custom_column', array( $this, 'columns_content'), 10, 2);
    }

    public static function get_instance(){
        if (null == self::$instance) {
            $instance = new self;
        }

        return $instance;
    }

    public static function custom_post() {

        $labels = array(
            'name'                => _x( 'Pricing Packages', 'Post Type General Name', 'pricingtable' ),
            'singular_name'       => _x( 'Pricing Package', 'Post Type Singular Name', 'pricingtable' ),
            'menu_name'           => __( 'Pricing Packages', 'pricingtable' ),
            'parent_item_colon'   => __( 'Parent Pricing Package:', 'pricingtable' ),
            'all_items'           => __( 'Pricing Packages', 'pricingtable' ),
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
            'show_in_menu'        => 'responsive-pricing-table',
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => false,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
        );
        register_post_type( 'pricing_packages', $args );

    }

    public function add_meta_box() {

        add_meta_box(
            "pricing-package-info", 
            __("Pricing Package Info", "pricingtable"), 
            array( $this, 'package_info' ),
            "pricing_packages", 
            "normal", 
            "high"
        );
    }

    public function package_info( $post ) {

        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'pricing_package_box', 'pricing_package_box_nonce' );

        $pricing_packages_info = array(
            'package_price'         => __('Package Price', 'pricingtable'),
            'package_tenure'        => __('Package Tenure', 'pricingtable'),
            'package_buy_link'      => __('Buy Now Link', 'pricingtable'),
            'package_buy_text'      => __('Buy Now Label', 'pricingtable'),
            'add_package_features'  => __('Add Package Features', 'pricingtable'),
            'add_features'          => __('Add Features', 'pricingtable'),
            'delete'                => __('Delete', 'pricingtable'),
        );

        $package_price = get_post_meta($post->ID, "_package_price", true);
        $package_tenure = get_post_meta($post->ID, "_package_tenure", true);
        $package_buy_link = get_post_meta($post->ID, "_package_buy_link", true);
        $package_buy_text = get_post_meta($post->ID, "_package_buy_text", true);

        if ( empty($package_buy_text) ) {
            $package_buy_text = __('Buy Now', 'pricingtable');
        } else {
            $package_buy_text = $package_buy_text;
        }

        $package_features = get_post_meta($post->ID, "_package_features", true);
        $package_features = ($package_features == '') ? array() : json_decode($package_features);


        $html = '<table class="form-table">';
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
        // Buy Now Label
        $html .= "<tr>";
        $html .= "<th style=''><label for='Buy Now'>".$pricing_packages_info['package_buy_text']." *</label></th>";
        $html .= "<td><input name='package_buy_text' id='package_buy_text' type='text' value='$package_buy_text' /></td>";
        $html .= "</tr>";
        // Package Features
        $html .= "<tr>";
        $html .= "<th style=''><label for='Price'>".$pricing_packages_info['add_package_features']."</label></th>";
        $html .= "<td><input name='package_feature' id='package_feature' type='text'  /> <input type='button' id='add_features' value='".$pricing_packages_info['add_features']."' /></td>";
        $html .= "</tr>";

        $html .= "<tr><th></th><td><ul id='package_features_box' name='package_features_box' >";

        foreach ($package_features as $package_feature) {
            $html .= "<li><input type='hidden' name='package_features[]' value='$package_feature' />$package_feature
            <a href='javascript:void(0);'> ".$pricing_packages_info['delete']."</a></li>";
        }
        
        $html .= "</ul></td></tr>";

        $html .= '</table>';

        echo $html;
    }

    public function save_post($post_id) {
        /*
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['pricing_package_box_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['pricing_package_box_nonce'], 'pricing_package_box' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ('pricing_packages' == $_POST['post_type'] && current_user_can('edit_post', $post_id)) {
            $package_price = (isset($_POST['package_price']) ? $_POST['package_price'] : '');
            $package_tenure = (isset($_POST['package_tenure']) ? $_POST['package_tenure'] : '');
            $package_buy_link = (isset($_POST['package_buy_link']) ? $_POST['package_buy_link'] : '');
            $package_buy_text = (isset($_POST['package_buy_text']) ? $_POST['package_buy_text'] : '');

            $package_features = (isset($_POST['package_features']) ? $_POST['package_features'] : array());
            $package_features = json_encode($package_features);

            update_post_meta($post_id, "_package_price", $package_price);
            update_post_meta($post_id, "_package_tenure", $package_tenure);
            update_post_meta($post_id, "_package_buy_link", $package_buy_link);
            update_post_meta($post_id, "_package_buy_text", $package_buy_text);
            update_post_meta($post_id, "_package_features", $package_features);
        } else {
            return $post_id;
        }
    }

    public function columns_head( $defaults ) {
        unset( $defaults['date'] );

        $defaults['package_price'] = __( 'Package Price' );
        $defaults['package_tenure'] = __( 'Package Tenure' );
        $defaults['package_buy_link'] = __( 'Package Buy Link' );
        $defaults['package_features'] = __( 'Package Features' );

        return $defaults;
    }

    public function columns_content( $column_name ) {

        global $post;

        $package_price = get_post_meta($post->ID, "_package_price", true);
        $package_tenure = get_post_meta($post->ID, "_package_tenure", true);
        $package_buy_link = get_post_meta($post->ID, "_package_buy_link", true);

        $package_features = get_post_meta($post->ID, "_package_features", true);
        $package_features = ($package_features == '') ? array() : json_decode($package_features);


        if ( 'package_price' == $column_name ) {

            if (! empty( $package_price )) {
                echo $package_price;
            }
        }
        if ( 'package_tenure' == $column_name ) {

            if (! empty( $package_tenure )) {
                echo $package_tenure;
            }
        }
        if ( 'package_buy_link' == $column_name ) {

            if (! empty( $package_buy_link )) {
                echo $package_buy_link;
            }
        }
        if ( 'package_features' == $column_name ) {

            if (! empty( $package_features )) {

                foreach ($package_features as $package_feature) {
                    echo $package_feature.'<br>';
                }
            }
        }
    }

}
Pricing_Package::get_instance();