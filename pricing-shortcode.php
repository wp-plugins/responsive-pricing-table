<?php
/*
 * Pricing table shortcode
 */
add_shortcode("show_pricing_table", "sis_wp_generate_pricing_table");

function sis_wp_generate_pricing_table($atts) {
    global $post;

    extract(shortcode_atts(array(
		'table_id' => '0',
	), $atts));

    $table_packages = get_post_meta($table_id, "_table_packages", true);
    $table_packages = ($table_packages == '') ? array() : json_decode($table_packages);

    $html = '<ul class="pricing_table">';

    $pricing_index = 0;
    foreach ($table_packages as $table_package) {
        $pricing_index++;

        $plan_title = get_the_title($table_package);

        $package_price = get_post_meta($table_package, "_package_price", true);
        $package_tenure = get_post_meta($table_package, "_package_tenure", true);
        $package_buy_link = get_post_meta($table_package, "_package_buy_link", true);

        $package_features = get_post_meta($table_package, "_package_features", true);
        $package_features = ($package_features == '') ? array() : json_decode($package_features);

        $html .= '<li class="price_block" id="pricing_plan' . $pricing_index . '">';
        $html .= '<h3 class="plan_title">' . $plan_title . '</h3>';
        $html .= '<div class="price"><div class="price_figure">';
        $html .= '<span class="price_number">' . $package_price . '</span>';
        $html .= '<span class="price_tenure">' .$package_tenure. '</span>';
        $html .= '</div></div>';
        $html .= '<ul class="features">';

        foreach ($package_features as $package_feature) {

            $html .= '<li>' . $package_feature . '</li>';
        }

        $html .= '</ul>';
        $html .= '<div class="footer"><a href="' . $package_buy_link . '" class="package_buy_link" rel="nofollow">Buy Now</a></div>';
        $html .= '</li>';
    }
    $html .= '</ul>';

    echo  $html;
}