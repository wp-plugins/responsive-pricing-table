<?php

class Pricing_Table {

    protected static $instance = null;
    
    function __construct() {
        add_action( 'init', array( $this, 'custom_post' ), 0 );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_post' ) );

        add_filter('manage_edit-pricing_tables_columns', array( $this, 'columns_head'));
        add_action('manage_pricing_tables_posts_custom_column', array( $this, 'columns_content'), 10, 2);
        add_filter('manage_edit-pricing_tables_sortable_columns', array( $this, 'sortable_columns'));
    }

    public static function get_instance(){
        if (null == self::$instance) {
            $instance = new self;
        }

        return $instance;
    }

    public static function custom_post() {

        $labels = array(
            'name'                => _x( 'Pricing Tables', 'Post Type General Name', 'pricingtable' ),
            'singular_name'       => _x( 'Pricing Table', 'Post Type Singular Name', 'pricingtable' ),
            'menu_name'           => __( 'Pricing Tables', 'pricingtable' ),
            'parent_item_colon'   => __( 'Parent Pricing Table:', 'pricingtable' ),
            'all_items'           => __( 'Pricing Tables', 'pricingtable' ),
            'view_item'           => __( 'View Pricing Table', 'pricingtable' ),
            'add_new_item'        => __( 'Add New Pricing Table', 'pricingtable' ),
            'add_new'             => __( 'Add New', 'pricingtable' ),
            'edit_item'           => __( 'Edit Pricing Table', 'pricingtable' ),
            'update_item'         => __( 'Update Pricing Table', 'pricingtable' ),
            'search_items'        => __( 'Search Pricing Table', 'pricingtable' ),
            'not_found'           => __( 'Not found', 'pricingtable' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'pricingtable' ),
        );
        $args = array(
            'label'               => __( 'Pricing Tables', 'pricingtable' ),
            'description'         => __( 'Pricing Tables', 'pricingtable' ),
            'labels'              => $labels,
            'supports'            => array( 'title' ),
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
        register_post_type( 'pricing_tables', $args );
    }

    public function add_meta_box() {
     
        add_meta_box( 
            "pricing-table-info", 
            __("Pricing Table Info", "pricingtable"), 
            array( $this, 'table_info'), 
            "pricing_tables", 
            "normal", 
            "high" 
        );
        add_meta_box( 
            "pricing-table-shortcode", 
            __("Usage (Shortcode)", "pricingtable"), 
            array( $this, 'shortcode_info' ), 
            "pricing_tables", 
            "side", 
            "high"
        );
     
    }

    public function shortcode_info(){
        $pricing_id = get_the_ID();

        if ( !empty($pricing_id) ){

            $pricing_id = '[show_pricing_table table_id="'.$pricing_id.'"]';

            echo '<p>Copy the following shortcode and paste in post or page where you want to show.</p>';
            echo '<p>'.$pricing_id.'</p>';
        }
    }

    public function table_info( $post ){

        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'pricing_table_box', 'pricing_table_box_nonce' );

        $pricing_table_info = array(
            'package_status'         => __('Package Status', 'pricingtable'),
            'package_name'         => __('Package Name', 'pricingtable'),
        );
     
        $table_packages = get_post_meta( $post->ID, "_table_packages", true );
        $table_packages = ( $table_packages == '' ) ? array() : json_decode( $table_packages );
     
        $query = new WP_Query( array(
            'post_type' => 'pricing_packages',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'post_date',
            'order' => 'ASC'
        ) );
     
        $html = '<table class="form-table">';
        $html .= '<tr>';
        $html .= '<th>'.$pricing_table_info['package_status'].'</th>';
        $html .= '<td>'.$pricing_table_info['package_name'].'</td></tr>';
        $html .= '</tr>';
     
        while ( $query->have_posts() ) : $query->the_post();

            $checked_status = ( in_array( $query->post->ID, $table_packages ) ) ? "checked" : "";
     
            $html .= '<tr>';
            $html .= '<th><input type="checkbox" name="pricing_table_packages[]" ' . $checked_status . ' value="' . $query->post->ID . '" /></th>';
            $html .= '<td>' . $query->post->post_title . '</td>';
            $html .= '</tr>';
     
        endwhile;
     
        $html .= '</table>';
     
        echo $html;
    }

    public function save_post($post_id) {

        /*
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['pricing_table_box_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['pricing_table_box_nonce'], 'pricing_table_box' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ('pricing_tables' == $_POST['post_type'] && current_user_can('edit_post', $post_id)) {

            $pricing_table_packages = (isset($_POST['pricing_table_packages']) ? $_POST['pricing_table_packages'] : array());
            $pricing_table_packages = json_encode($pricing_table_packages);

            update_post_meta($post_id, "_table_packages", $pricing_table_packages);
        } else {
            return $post_id;
        }
    }

    public function columns_head($columns){
        
        $columns = array(
            'cb'    => '<input type="checkbox">',
            'title' => __('Pricing Table Name', 'pricingtable'),
            'ID'    => __('Pricing Table No', 'pricingtable'),
            'usage' => __('Usage (shortcode)', 'pricingtable')
        );

        return $columns;

    }

    public function columns_content($column, $post_id) {
        global $post;

        switch ($column) {


            case 'ID' :


                $pricing_id = $post_id;


                if (empty($pricing_id))
                    echo __('Unknown');


                else
                    printf($pricing_id);

                break;

            case 'usage':

                $pricing_id = $post_id;

                if ( !empty($pricing_id) ){
                    echo '<pre><code>[show_pricing_table table_id="'.$pricing_id.'"]</code></pre>';
                }

                break;
            default :
                break;
        }
    }

    public function sortable_columns($columns) {

        $columns['ID'] = 'ID';

        return $columns;
    }
}
Pricing_Table::get_instance();