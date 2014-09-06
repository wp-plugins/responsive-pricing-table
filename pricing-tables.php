<?php
// Register Custom Post Type
function sis_wp_pricing_tables() {

    $labels = array(
        'name'                => _x( 'Pricing Tables', 'Post Type General Name', 'pricingtable' ),
        'singular_name'       => _x( 'Pricing Table', 'Post Type Singular Name', 'pricingtable' ),
        'menu_name'           => __( 'Pricing Tables', 'pricingtable' ),
        'parent_item_colon'   => __( 'Parent Pricing Table:', 'pricingtable' ),
        'all_items'           => __( 'All Pricing Tables', 'pricingtable' ),
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
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'menu_icon'           => ''.plugins_url( 'img/table.png' , __FILE__ ).'',
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
    );
    register_post_type( 'pricing_tables', $args );

}

// Hook into the 'init' action
add_action( 'init', 'sis_wp_pricing_tables', 0 );

// Using Custom Fields for Table Information
function sis_wp_pricing_tables_meta_boxes() {
 
    add_meta_box( "pricing-table-info", __("Pricing Table Info", "pricingtable"), 'sis_wp_generate_pricing_table_info', "pricing_tables", "normal", "high" );
 
}
add_action( 'add_meta_boxes', 'sis_wp_pricing_tables_meta_boxes' );

// Add meta box content for Pricing Table Info
function sis_wp_generate_pricing_table_info(){
    global $post;

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
 
    $html = '<input type="hidden" name="pricing_table_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';
 
    $html .= '<table class="form-table">';
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

// Save Priceng Table
function sis_wp_save_pricing_tables($post_id) {

    if (!wp_verify_nonce($_POST['pricing_table_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('pricing_tables' == $_POST['post_type'] && current_user_can('edit_post', $post_id)) {

        $pricing_table_packages = (isset($_POST['pricing_table_packages']) ? $_POST['pricing_table_packages'] : array());
        $pricing_table_packages = json_encode($pricing_table_packages);

        update_post_meta($post_id, "_table_packages", $pricing_table_packages);
    } else {
        return $post_id;
    }
}
add_action('save_post', 'sis_wp_save_pricing_tables');

// Generating Pricing Table ID
function sis_wp_edit_pricing_tables_columns($columns){
    
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'ID' => __('Pricing Table No', 'pricingtable'),
        'title' => __('Pricing Table Name', 'pricingtable'),
        'date' => __('Date', 'pricingtable')
    );

    return $columns;

}
add_filter('manage_edit-pricing_tables_columns', 'sis_wp_edit_pricing_tables_columns');

function sis_wp_manage_pricing_tables_columns($column, $post_id) {
    global $post;

    switch ($column) {


        case 'ID' :


            $pricing_id = $post_id;


            if (empty($pricing_id))
                echo __('Unknown');


            else
                printf($pricing_id);

            break;


        default :
            break;
    }
}
add_action('manage_pricing_tables_posts_custom_column', 'sis_wp_manage_pricing_tables_columns', 10, 2);


function sis_wp_pricing_tables_sortable_columns($columns) {

    $columns['ID'] = 'ID';

    return $columns;
}
add_filter('manage_edit-pricing_tables_sortable_columns', 'sis_wp_pricing_tables_sortable_columns');