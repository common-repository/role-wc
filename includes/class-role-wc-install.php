<?php
/**
 * Installation related functions and actions.
 *
 * @package Role_WC\Classes
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit;

/**
 * Role_WC_Install Class.
 */
class Role_WC_Install
{

    /**
     * Hook in tabs.
     */
    public static function init(){
        add_action( 'init', array( __CLASS__, 'install' ), 5 );
    }

    /**
     * Install WC.
     */
    public static function install(){
        if (!is_blog_installed()) {
            return;
        }
        self::remove_roles();
        self::create_roles();

    }

    /**
     * Create roles and capabilities.
     */
    public static function create_roles(){
        global $wp_roles;

        if (!class_exists('WP_Roles')) {
            return;
        }

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
        }

        // Dummy gettext calls to get strings in the catalog.
        /* translators: user role */
        _x( 'Creator', 'User role', 'role-wc' );

        // Add Shop Manager role.
        // Creator role.
        add_role(
            'shop_creator',
            'Creator',
            array(
                'read'                   => true,
                'read_private_pages'     => true,
                'read_private_posts'     => true,
                'edit_posts'             => true,
                'edit_pages'             => true,
                'edit_published_posts'   => true,
                'edit_published_pages'   => true,
                'edit_private_pages'     => true,
                'edit_private_posts'     => true,
                'edit_others_posts'      => true,
                'edit_others_pages'      => true,
                'publish_posts'          => true,
                'publish_pages'          => true,
                'delete_posts'           => true,
                'delete_pages'           => true,
                'delete_private_pages'   => true,
                'delete_private_posts'   => true,
                'delete_published_pages' => true,
                'delete_published_posts' => true,
                'delete_others_posts'    => true,
                'delete_others_pages'    => true,
                'manage_categories'      => true,
                'manage_links'           => true,
                'upload_files'           => true,
                'edit_theme_options'     => true,
            )
        );
        $capabilities = self::get_core_capabilities();

        foreach ( $capabilities as $cap_group ) {
            foreach ( $cap_group as $cap ) {
                $wp_roles->add_cap( 'shop_creator', $cap );
            }
        }
    }

    /**
     * Get capabilities for WooCommerce - these are assigned to creator manager during installation or reset.
     *
     * @return array
     */
    public static function get_core_capabilities() {
        $capabilities = array();

        $capabilities['core'] = array(
            'manage_woocommerce',
            'view_woocommerce_reports',
        );

        $capability_types = array( 'product', 'shop_coupon' );

        foreach ( $capability_types as $capability_type ) {

            $capabilities[ $capability_type ] = array(
                // Post type.
                "edit_{$capability_type}",
                "read_{$capability_type}",
                "delete_{$capability_type}",
                "edit_{$capability_type}s",
                "edit_others_{$capability_type}s",
                "publish_{$capability_type}s",
                "read_private_{$capability_type}s",
                "delete_{$capability_type}s",
                "delete_private_{$capability_type}s",
                "delete_published_{$capability_type}s",
                "delete_others_{$capability_type}s",
                "edit_private_{$capability_type}s",
                "edit_published_{$capability_type}s",

                // Terms.
                "manage_{$capability_type}_terms",
                "edit_{$capability_type}_terms",
                "delete_{$capability_type}_terms",
                "assign_{$capability_type}_terms",
            );
        }

        return $capabilities;
    }

    /**
     * Remove Creator roles.
     */
    public static function remove_roles() {
        global $wp_roles;

        if ( ! class_exists( 'WP_Roles' ) ) {
            return;
        }

        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
        }

        $capabilities = self::get_core_capabilities();

        foreach ( $capabilities as $cap_group ) {
            foreach ( $cap_group as $cap ) {
                $wp_roles->remove_cap( 'shop_creator', $cap );
            }
        }

        remove_role( 'shop_creator' );
    }
}

Role_WC_Install::init();
