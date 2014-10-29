<?php

class WP_Auth0_Settings_Section {

    public static function init(){
        add_action( 'admin_menu', array(__CLASS__, 'init_menu') );
    }

    public static function init_menu(){
        add_menu_page( __('Auth0', WPA0_LANG), __('Auth0', WPA0_LANG), 'manage_options', 'wpa0',
            array('WP_Auth0_Admin', 'render_settings_page'),
            WP_Auth0::getPluginDirUrl() . 'assets/img/a0icon.png',
            81 );

        add_submenu_page('wpa0', __('Auth0 Settings', WPA0_LANG), __('Settings', WPA0_LANG), 'manage_options', 'wpa0', array('WP_Auth0_Admin', 'render_settings_page') );
        add_submenu_page('wpa0', __('Auth0 Error Log', WPA0_LANG), __('Error Log', WPA0_LANG), 'manage_options', 'wpa0-errors', array('WP_Auth0_ErrorLog', 'render_settings_page') );
    }
}
