<?php
/**
 * Role for WooCommerce
 *
 * Admin Page control
 *
 * @class 		ROLE_WC_Admin_Screen
 * @version		1.0.0
 * @author		Artisan Workshop
 */
use ArtisanWorkshop\WooCommerce\PluginFramework\v2_0_11 as Framework;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $_SESSION;

class ROLE_WC_Admin_Screen {

	public $jp4wc_plugin;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'role_wc_admin_menu' ) ,55 );
		add_action( 'admin_init', array( $this, 'role_wc_setting_init') );

		$this->jp4wc_plugin = new Framework\JP4WC_Plugin();
	}
	/**
	 * Admin Menu
	 */
	public function role_wc_admin_menu() {
        $user = wp_get_current_user();
        $roles = ( array )$user->roles;
        if( in_array('administrator', $roles ) ) {
            add_submenu_page('woocommerce', __('Role Setting', 'role-wc'), __('Role Setting', 'role-wc'), 'manage_woocommerce', 'role-wc-setting', array($this, 'role_wc_output'));
        }
	}

	/**
	 * Admin Screen output
	 */
	public function role_wc_output() {
        include_once( 'views/html-admin-screen.php' );
	}

	function role_wc_setting_init(){
        if ( ! isset($active_plugins) ) {
            $active_plugins = (array) get_option( 'active_plugins', array() );

            if ( is_multisite() )
                $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }
		register_setting(
			'role_wc',// Option Group
			'role_wc_settings',// Option Name
			array( 'validate_options' )// Sanitize callback
		);
		// Basic Setting
		add_settings_section(
			'role_wc_settings',// id
			__( 'Hide by role', 'role-wc' ),// title
            array( $this, 'role_wc_setting_explain'),// callback
			'role_wc'// page
		);

		// All in One WP Migration
		if(in_array( 'all-in-one-wp-migration/all-in-one-wp-migration.php', $active_plugins )){
            add_settings_field(
                'role_wc_aiowpm_export',// id
                __( 'All in One WP Migration (Export)', 'role-wc' ),// title
                array( $this, 'role_wc_aiowpm_export' ),// callback
                'role_wc',// page
                'role_wc_settings'// section
            );
            add_settings_field(
                'role_wc_aiowpm_import',// id
                __( 'All in One WP Migration (Import)', 'role-wc' ),// title
                array( $this, 'role_wc_aiowpm_import' ),// callback
                'role_wc',// page
                'role_wc_settings'// section
            );
            add_settings_field(
                'role_wc_aiowpm_backup',// id
                __( 'All in One WP Migration (Backup)', 'role-wc' ),// title
                array( $this, 'role_wc_aiowpm_backup' ),// callback
                'role_wc',// page
                'role_wc_settings'// section
            );
        }else{
            add_settings_field(
                'role_wc_aiowpm_no_activation',// id
                __( 'All in One WP Migration', 'role-wc' ),// title
                array( $this, 'role_wc_no_activation' ),// callback
                'role_wc',// page
                'role_wc_settings'// section
            );
        }
		do_action( 'role_wc_add_plugins' );
		$this->role_wc_setting_save_post( $_POST );
	}
    /**
     * Description of settings
     *
     * @return mixed
     */
    public function role_wc_setting_explain(){
        echo __( 'Check the roles you want to hide.', 'role-wc' ).'<br />';
    }

	/**
	 * All in One WP Migration (Export) setting
	 * 
	 * @return mixed
	 */
	public function role_wc_aiowpm_export(){
        $role_wc_settings = get_option('role_wc_settings');
	    $args = array(
            'id' => 'aiowpm-export',
            'checked' => array(
                'shop_manager' => $role_wc_settings['manager-aiowpm-export'],
            )
        );
	    $this->role_wc_manager_roles_checkbox( $args );
	}

    /**
     * All in One WP Migration (Export) setting
     *
     * @return mixed
     */
    public function role_wc_aiowpm_import(){
        $role_wc_settings = get_option('role_wc_settings');
        $args = array(
            'id' => 'aiowpm-import',
            'checked' => array(
                'shop_manager' => $role_wc_settings['manager-aiowpm-import'],
            )
        );
        $this->role_wc_manager_roles_checkbox( $args );
    }

    /**
     * All in One WP Migration (Export) setting
     */
    public function role_wc_aiowpm_backup(){
        $role_wc_settings = get_option('role_wc_settings');
        $args = array(
            'id' => 'aiowpm-backup',
            'checked' => array(
                'shop_manager' => $role_wc_settings['manager-aiowpm-backup'],
            )
        );
        $this->role_wc_manager_roles_checkbox( $args );
    }

    /**
     * No activation notice
     */
    public function role_wc_no_activation(){
        echo __( 'The target plugin is not activated.', 'role-wc' );
    }

	/**
	 * Checkboxes for shop managers and creators
	 *
     * @param array
	 */
	public function role_wc_manager_roles_checkbox( $args ){
	    $manager_checked = '';
        if($args['checked']['shop_manager'] == 'on'){$manager_checked = 'checked';}
        echo '<label>'.__( 'Shop Manager', 'role-wc' ).' <input type="checkbox" id="manager-'.esc_attr( $args['id'] ).'" name="manager-'.esc_attr($args['id']).'" '.$manager_checked.'></label> ';
	}

    /**
     * Checkboxes for shop managers and creators
     *
     * @param array POST DATA
     * @param array GET DATA
     * @return mixed
     */
    public function role_wc_setting_save_post( $post ){
        if( isset( $post['_wpnonce']) and isset($post['option_page']) and $post['option_page'] == 'role_wc' ){
            $post_data = array();
            unset($post['_wpnonce'], $post['option_page'], $post['action'], $post['_wp_http_referer'], $post['save_role_wc']);
            foreach($post as $key => $value){
                if(empty($value))$value = '';
                $post_data[$key] = sanitize_text_field($value);
            }
            update_option('role_wc_settings', $post_data);
        }
    }
}

new ROLE_WC_Admin_Screen();