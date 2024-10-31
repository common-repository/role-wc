<?php
/**
 * Remove submenu for some Roles
 *
 * @package Role_WC\Classes
 * @version 1.0.0
 */

use WPFront\Scroll_Top\WPFront_Scroll_Top;
use WPFront\Scroll_Top\WPFront_Scroll_Top_Options_View;

defined( 'ABSPATH' ) || exit;

/**
 * Remove Sub Menu Class.
 */
class Role_WC_Remove_Menu{

    /**
     * Hook in tabs.
     */
    public static function init(){
        add_action( 'admin_menu', array( __CLASS__, 'custom_remove_menu' ), 90 );
        add_action( 'admin_menu', array( __CLASS__, 'remove_menu' ), 99 );
        add_action( 'admin_menu', array( __CLASS__, 'seo_simple_pack_add_menu' ), 10 );
        add_filter( 'mailpoet_permission_access_plugin_admin', array( __CLASS__, 'role_wc_mailpoet_permission' ), 10 );
        add_filter( 'mailpoet_permission_manage_settings', array( __CLASS__, 'role_wc_mailpoet_permission' ), 10 );
        add_filter( 'mailpoet_permission_manage_features', array( __CLASS__, 'role_wc_mailpoet_permission' ), 10 );
        add_filter( 'mailpoet_permission_manage_emails', array( __CLASS__, 'role_wc_mailpoet_permission' ), 10 );
        add_filter( 'mailpoet_permission_manage_subscribers', array( __CLASS__, 'role_wc_mailpoet_permission' ), 10 );
        add_filter( 'mailpoet_permission_manage_forms', array( __CLASS__, 'role_wc_mailpoet_permission' ), 10 );
        add_filter( 'mailpoet_permission_manage_segments', array( __CLASS__, 'role_wc_mailpoet_permission' ), 10 );
    }

    /**
     * Install WC.
     */
    public static function remove_menu(){
        if (!is_blog_installed()) {
            return;
        }
        if( is_user_logged_in() ) {
            $user = wp_get_current_user();
            $roles = ( array )$user->roles;
            if( in_array('shop_creator', $roles ) ){
                $default_remove_submenu_list_creator = array(
                    0 => array( 'menu_slug'=>'woocommerce', 'submenu_slug'=>'wc-status' ),
                    1 => array( 'menu_slug'=>'woocommerce', 'submenu_slug'=>'wc-addons' )
                );
                $default_remove_menu_list_creator = array(
                    'edit-comments.php',
                    'wc-reports'
                );
                $remove_submenu_list_creator = apply_filters( 'role_wc_creator_remove_submenu_list', $default_remove_submenu_list_creator );
                $remove_menu_list_creator = apply_filters( 'role_wc_creator_remove_menu_list', $default_remove_menu_list_creator );
                // Remove submenu at admin page
                foreach ( $remove_submenu_list_creator as $key => $list ){
                    remove_submenu_page( $list['menu_slug'], $list['submenu_slug'] );
                }
                // Remove menu at admin page
                foreach ( $remove_menu_list_creator as $menu_slug ){
                    remove_menu_page( $menu_slug );
                }
            }elseif( in_array('shop_manager', $roles ) ){
                $default_remove_submenu_list_manager = array();
                $default_remove_menu_list_manager = array();
                $remove_submenu_list_manager = apply_filters( 'role_wc_manager_remove_submenu_list', $default_remove_submenu_list_manager );
                $remove_menu_list_manager = apply_filters( 'role_wc_manager_remove_menu_list', $default_remove_menu_list_manager );
                // Remove submenu at admin page
                if(isset($remove_submenu_list_manager)){
                    foreach ( $remove_submenu_list_manager as $key => $list ){
                        remove_submenu_page( $list['menu_slug'], $list['submenu_slug'] );
                    }
                }
                // Remove menu at admin page
                foreach ( $remove_menu_list_manager as $menu_slug ){
                    remove_menu_page( $menu_slug );
                }
            }
        }
    }

    /**
     * Additional remove menus.
     */
    public static function custom_remove_menu(){
        $wc_role_setting = get_option('role_wc_settings');
        // All in One WP Migration for shop manager
        if($wc_role_setting['manager-aiowpm-export'] == 'on'){
            add_filter('role_wc_manager_remove_submenu_list',
                function( $list ) { return array_merge($list, array(array( 'menu_slug'=>'ai1wm_export', 'submenu_slug'=>'ai1wm_export' ))); }
            );
        }
        if($wc_role_setting['manager-aiowpm-import'] == 'on'){
            add_filter('role_wc_manager_remove_submenu_list',
                function( $list ) { return array_merge($list, array(array( 'menu_slug'=>'ai1wm_export', 'submenu_slug'=>'ai1wm_import' ))); }
            );
        }
        if($wc_role_setting['manager-aiowpm-backup'] == 'on'){
            add_filter('role_wc_manager_remove_submenu_list',
                function( $list ) { return array_merge($list, array(array( 'menu_slug'=>'ai1wm_export', 'submenu_slug'=>'ai1wm_backups' ))); }
            );
        }
        if($wc_role_setting['manager-aiowpm-export'] == 'on' && $wc_role_setting['manager-aiowpm-import'] == 'on' && $wc_role_setting['manager-aiowpm-backup'] == 'on'){
            add_filter('role_wc_manager_remove_menu_list',
                function( $list ) { return array_merge($list, array('ai1wm_export' )); }
            );
        }
        // All in One WP Migration for creator
        if($wc_role_setting['creator-aiowpm-export'] == 'on'){
            add_filter('role_wc_creator_remove_submenu_list',
                function( $list ) { return array_merge($list, array(array( 'menu_slug'=>'ai1wm_export', 'submenu_slug'=>'ai1wm_export' ))); }
            );
        }
        if($wc_role_setting['creator-aiowpm-import'] == 'on'){
            add_filter('role_wc_creator_remove_submenu_list',
                function( $list ) { return array_merge($list, array(array( 'menu_slug'=>'ai1wm_export', 'submenu_slug'=>'ai1wm_import' ))); }
            );
        }
        if($wc_role_setting['creator-aiowpm-backup'] == 'on'){
            add_filter('role_wc_creator_remove_submenu_list',
                function( $list ) { return array_merge($list, array(array( 'menu_slug'=>'ai1wm_export', 'submenu_slug'=>'ai1wm_backups' ))); }
            );
        }
    }

    /**
     *
     * Add SEO SIMPLE PACK menu at shop shop manager
     */
    function seo_simple_pack_add_menu(){
        if( class_exists('SEO_SIMPLE_PACK') ) {
            if (is_user_logged_in()) {
                $user = wp_get_current_user();
                $roles = ( array )$user->roles;
                if( in_array('shop_manager', $roles ) ){
                    // トップレベルメニュー
                    $top_menu_title = 'SEO SIMPLE PACK'; // ページのタイトルタグに表示されるテキスト
                    $remove_top_menu_slug = 'ssp_main_setting'; // このメニューを参照するスラッグ名
                    $top_menu_slug = 'wc_ssp_main_setting'; // このメニューを参照するスラッグ名
                    $top_menu_cb = ['SSP_Menu', 'ssp_top_menu']; // 呼び出す関数名

                    remove_menu_page( $remove_top_menu_slug );
                    add_menu_page(
                        $top_menu_title,
                        'SEO PACK',
                        'edit_theme_options', // 必要な権限
                        $top_menu_slug,
                        $top_menu_cb,
                        'dashicons-list-view',
                        81 // 位置
                    );
                    remove_submenu_page( $remove_top_menu_slug, $top_menu_slug);
                    remove_submenu_page( $remove_top_menu_slug, 'ssp_ogp_setting');
                    remove_submenu_page( $remove_top_menu_slug, 'ssp_main_setting');
                    add_submenu_page(
                        $top_menu_slug,
                        $top_menu_title,
                        __('General settings', 'loos-ssp'), // サブ側の名前
                        'shop_manager',       // 権限
                        $top_menu_slug,
                        $top_menu_cb
                    );

                    // サブメニュー:OGP設定
                    add_submenu_page(
                        'wc_ssp_main_setting',
                        __('OGP settings', 'loos-ssp'), // 'OGP設定',
                        __('OGP settings', 'loos-ssp'), // 'OGP設定',
                        'shop_manager',
                        'wc_ssp_ogp_setting',
                        ['SSP_Menu', 'ssp_ogp_menu']
                    );

                    // サブメニュー:HELP
                    add_submenu_page(
                        'wc_ssp_main_setting',
                        'HELP',
                        'HELP',
                        'shop_manager',
                        'wc_ssp_help',
                        ['SSP_Menu', 'ssp_help_menu']
                    );
                }
            }
        }
    }

    /**
     * Set Mailpoet setting permission in the shop manager.
     *
     * @param array Permission array
     * @return array
     */
    function role_wc_mailpoet_permission( $permission_array ){
        array_push($permission_array,'shop_manager');
        if(in_array('editor', $permission_array)){
            array_push($permission_array, 'shop_creator');
        }
        return $permission_array;
    }
}

Role_WC_Remove_Menu::init();
