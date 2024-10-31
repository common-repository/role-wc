<?php
/**
 * Plugin Name: Role Management for WooCommerce
 * Framework Name: Artisan Workshop FrameWork for WooCommerce
 * Framework Version : 2.0.11
 * Author: Artisan Workshop
 * Author URI: https://wc.artws.info/
 * Text Domain: role-wc
 *
 * @category JP4WC_Framework
 * @author Artisan Workshop
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return apply_filters(
    'role_wc_framework_config',
    array(
        'description_check_pattern' => __( 'Please check it if you want to use %s.', 'role-wc' ),
        'description_payment_pattern' => __( 'Please check it if you want to use the payment method of %s.', 'role-wc' ),
        'description_input_pattern' => __( 'Please input %s.', 'role-wc' ),
        'description_select_pattern' => __( 'Please select one from these as %s.', 'role-wc' ),
        'support_notice_01' => __( 'Need support?', 'role-wc' ),
        'support_notice_02' => __( 'If you are having problems with this plugin, talk about them in the <a href="%s" target="_blank" title="Pro Version">Support forum</a>.', 'role-wc' ),
        'support_notice_03' => __( 'If you need professional support, please consider about <a href="%1$s" target="_blank" title="Site Construction Support service">Site Construction Support service</a> or <a href="%2$s" target="_blank" title="Maintenance Support service">Maintenance Support service</a>.', 'role-wc' ),
        'pro_notice_01' => __( 'Pro version', 'role-wc' ),
        'pro_notice_02' => __( 'The pro version is available <a href="%s" target="_blank" title="Support forum">here</a>.', 'role-wc' ),
        'pro_notice_03' => __( 'The pro version includes support for bulletin boards. Please consider purchasing the pro version.', 'role-wc' ),
        'update_notice_01' => __( 'Finished Latest Update, WordPress and WooCommerce?', 'role-wc' ),
        'update_notice_02' => __( 'One the security, latest update is the most important thing. If you need site maintenance support, please consider about <a href="%s" target="_blank" title="Support forum">Site Maintenance Support service</a>.', 'role-wc' ),
        'community_info_01' => __( 'Where is the study group of Woocommerce in Japan?', 'role-wc' ),
        'community_info_02' => __( '<a href="%s" target="_blank" title="Tokyo WooCommerce Meetup">Tokyo WooCommerce Meetup</a>.', 'role-wc' ),
        'community_info_03' => __( '<a href="%s" target="_blank" title="Kansai WooCommerce Meetup">Kansai WooCommerce Meetup</a>.', 'role-wc' ),
        'community_info_04' => __('Join Us!', 'role-wc' ),
        'author_info_01' => __( 'Created by', 'role-wc' ),
        'author_info_02' => __( 'WooCommerce Doc in Japanese', 'role-wc' ),
        'framework_version' => ROLE_WC_FRAMEWORK_VERSION,
    )
);
