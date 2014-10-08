<?php

class WP_Auth0_Admin{
    public static function init(){
        add_action( 'admin_menu', array(__CLASS__, 'init_menu') );
        add_action( 'admin_init', array(__CLASS__, 'init_admin'));
        add_action( 'admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue'));
    }

    public static function admin_enqueue(){
        if(!isset($_REQUEST['page']) || $_REQUEST['page'] != 'wpa0')
            return;

        wp_enqueue_media();
        wp_enqueue_script( 'wpa0_admin', WPA0_PLUGIN_URL . 'assets/js/admin.js', array('jquery'));
        wp_enqueue_style('media');

        wp_localize_script( 'wpa0_admin', 'wpa0', array(
            'media_title' => __('Choose your icon', WPA0_LANG),
            'media_button' => __('Choose icon', WPA0_LANG)
        ));
    }

    protected static function init_option_section($sectionName, $settings)
    {
        $lowerName = strtolower($sectionName);
        add_settings_section(
            "wp_auth0_{$lowerName}_settings_section",
            __($sectionName, WPA0_LANG),
            array(__CLASS__, "render_{$lowerName}_description"),
            WP_Auth0_Options::OPTIONS_NAME
        );

        foreach ($settings as $setting)
        {
            add_settings_field(
                $setting['id'],
                __($setting['name'], WPA0_LANG),
                array(__CLASS__, $setting['function']),
                WP_Auth0_Options::OPTIONS_NAME,
                "wp_auth0_{$lowerName}_settings_section",
                array('label_for' => $setting['id'])
            );
        }
    }

    public static function init_admin(){

/* ------------------------- BASIC ------------------------- */

        self::init_option_section('Basic', array(

            array('id' => 'wpa0_domain', 'name' => 'Domain', 'function' => 'render_domain'),
            array('id' => 'wpa0_client_id', 'name' => 'Client ID', 'function' => 'render_client_id'),
            array('id' => 'wpa0_client_secret', 'name' => 'Client Secret', 'function' => 'render_client_secret'),
            array('id' => 'wpa0_login_enabled', 'name' => 'WordPress login enabled', 'function' => 'render_allow_wordpress_login'),

        ));

/* ------------------------- Appearance ------------------------- */

        self::init_option_section('Appearance', array(

            array('id' => 'wpa0_form_title', 'name' => 'Form Title', 'function' => 'render_form_title'),
            array('id' => 'wpa0_social_big_buttons', 'name' => 'Show big social buttons', 'function' => 'render_social_big_buttons'),
            array('id' => 'wpa0_icon_url', 'name' => 'Icon URL', 'function' => 'render_icon_url'),
            array('id' => 'wpa0_gravatar', 'name' => 'Enable Gravatar integration', 'function' => 'render_gravatar'),

        ));

/* ------------------------- ADVANCED ------------------------- */

        self::init_option_section('Advanced', array(

            array('id' => 'wpa0_dict', 'name' => 'Translation', 'function' => 'render_dict'),
            array('id' => 'wpa0_username_style', 'name' => 'Username style', 'function' => 'render_username_style'),
            array('id' => 'wpa0_remember_last_login', 'name' => 'Remember last login', 'function' => 'render_remember_last_login'),
            array('id' => 'wpa0_verified_email', 'name' => 'Requires verified email', 'function' => 'render_verified_email'),
            array('id' => 'wpa0_allow_signup', 'name' => 'Allow signup', 'function' => 'render_allow_signup'),
            array('id' => 'wpa0_auto_login', 'name' => 'Auto Login (no widget)', 'function' => 'render_auto_login'),
            array('id' => 'wpa0_auto_login_method', 'name' => 'Auto Login Method', 'function' => 'render_auto_login_method'),
            array('id' => 'wpa0_ip_range_check', 'name' => 'Enable on IP Ranges', 'function' => 'render_ip_range_check'),
            array('id' => 'wpa0_ip_ranges', 'name' => 'IP Ranges', 'function' => 'render_ip_ranges'),
            array('id' => 'wpa0_extra_conf', 'name' => 'Extra settings', 'function' => 'render_extra_conf'),
            array('id' => 'wpa0_cdn_url', 'name' => 'Widget URL', 'function' => 'render_cdn_url'),

        ));

        register_setting(WP_Auth0_Options::OPTIONS_NAME, WP_Auth0_Options::OPTIONS_NAME, array(__CLASS__, 'input_validator'));
    }

    public static function render_extra_conf(){
        $v = WP_Auth0_Options::get( 'extra_conf' );
        echo '<textarea name="' . WP_Auth0_Options::OPTIONS_NAME . '[extra_conf]" id="wpa0_extra_conf">' . esc_attr( $v ) . '</textarea>';
        echo '<br/><span class="description">' . __('This field should be a valid json with any setting that is not editable here. Other options will override this settings.', WPA0_LANG)
            . '<br/>' . __('(IE: {"disableResetAction": true }) ', WPA0_LANG)
            . '<a href="https://github.com/auth0/lock/wiki/Auth0Lock-customization">' . __('More info', WPA0_LANG) . '</a></span>';
    }
    public static function render_remember_last_login () {
        $v = absint(WP_Auth0_Options::get( 'remember_last_login' ));
        echo '<input type="checkbox" name="' . WP_Auth0_Options::OPTIONS_NAME . '[remember_last_login]" id="wpa0_remember_last_login" value="1" ' . checked( $v, 1, false ) . '/>';
        echo '<br/><span class="description">' . __('Request for SSO data and enable Last time you signed in with[...] message.', WPA0_LANG) . '<a href="https://github.com/auth0/lock/wiki/Auth0Lock-customization#rememberlastlogin-boolean">' . __('More info', WPA0_LANG) . '</a></span>';
    }

    public static function render_client_id(){
        $v = WP_Auth0_Options::get( 'client_id' );
        echo '<input type="text" name="' . WP_Auth0_Options::OPTIONS_NAME . '[client_id]" id="wpa0_client_id" value="' . esc_attr( $v ) . '"/>';
        echo '<br/><span class="description">' . __('Application id, copy from the auth0 dashboard', WPA0_LANG) . '</span>';
    }
    public static function render_client_secret(){
        $v = WP_Auth0_Options::get( 'client_secret' );
        echo '<input type="text" name="' . WP_Auth0_Options::OPTIONS_NAME . '[client_secret]" id="wpa0_client_secret" value="' . esc_attr( $v ) . '"/>';
        echo '<br/><span class="description">' . __('Application secret, copy from the auth0 dashboard', WPA0_LANG) . '</span>';
    }
    public static function render_domain(){
        $v = WP_Auth0_Options::get( 'domain' );
        echo '<input type="text" name="' . WP_Auth0_Options::OPTIONS_NAME . '[domain]" id="wpa0_domain" value="' . esc_attr( $v ) . '"/>';
        echo '<br/><span class="description">' . __('Your Auth0 domain, you can see it in the auth0 dashboard', WPA0_LANG) . '</span>';
    }

    public static function render_form_title(){
        $v = WP_Auth0_Options::get( 'form_title' );
        echo '<input type="text" name="' . WP_Auth0_Options::OPTIONS_NAME . '[form_title]" id="wpa0_form_title" value="' . esc_attr( $v ) . '"/>';
        echo '<br/><span class="description">' . __('This is the title for the login widget', WPA0_LANG) . '</span>';
    }

    public static function render_dict(){
        $v = WP_Auth0_Options::get( 'dict' );
        echo '<textarea name="' . WP_Auth0_Options::OPTIONS_NAME . '[dict]" id="wpa0_dict">' . esc_attr( $v ) . '</textarea>';
        echo '<br/><span class="description">' . __('This is the widget\'s dict param.', WPA0_LANG) . '<a target="_blank" href="https://github.com/auth0/lock/wiki/Auth0Lock-customization#dict-stringobject">' . __('More info', WPA0_LANG) . '</a></span>';
    }

    public static function render_username_style(){
        $v = WP_Auth0_Options::get( 'username_style' );
        echo '<input type="radio" name="' . WP_Auth0_Options::OPTIONS_NAME . '[username_style]" id="wpa0_username_style_email" value="email" ' . (esc_attr( $v ) == 'email' ? 'checked="true"' : '') . '"/>';
        echo '<label for="wpa0_username_style_email">' . __('Email', WPA0_LANG) . '</label>';
        echo ' ';
        echo '<input type="radio" name="' . WP_Auth0_Options::OPTIONS_NAME . '[username_style]" id="wpa0_username_style_username" value="username" ' . (esc_attr( $v ) == 'username' ? 'checked="true"' : '') . '"/>';
        echo '<label for="wpa0_username_style_username">' . __('Username', WPA0_LANG) . '</label>';

        echo '<br/><span class="description">' . __('If you don\'t want to validate that the user enters an email, just set this to username.', WPA0_LANG) . '<a target="_blank" href="https://github.com/auth0/lock/wiki/Auth0Lock-customization#usernamestyle-string">' . __('More info', WPA0_LANG) . '</a></span>';
    }

    public static function render_auto_login(){
        $v = absint(WP_Auth0_Options::get( 'auto_login' ));
        echo '<input type="checkbox" name="' . WP_Auth0_Options::OPTIONS_NAME . '[auto_login]" id="wpa0_auto_login" value="1" ' . checked( $v, 1, false ) . '/>';
        echo '<br/><span class="description">' . __('Mark this to avoid the login page (you will have to select a single login provider)', WPA0_LANG) . '</span>';
    }
    public static function render_auto_login_method(){
        $v = WP_Auth0_Options::get( 'auto_login_method' );
        echo '<input type="text" name="' . WP_Auth0_Options::OPTIONS_NAME . '[auto_login_method]" id="wpa0_auto_login_method" value="' . esc_attr( $v ) . '"/>';
        echo '<br/><span class="description">' . __('To find the method name, log into Auth0 Dashboard, and navigate to: Connection -> [Connection Type] (eg. Social or Enterprise). Click the "down arrow" to expand the wanted method, and use the value in the "Name"-field. Example: google-oauth2', WPA0_LANG) . '</span>';
    }
    public static function render_ip_range_check(){
        $v = absint(WP_Auth0_Options::get( 'ip_range_check' ));
        echo '<input type="checkbox" name="' . WP_Auth0_Options::OPTIONS_NAME . '[ip_range_check]" id="wpa0_ip_range_check" value="1" ' . checked( $v, 1, false ) . '/>';
    }
    public static function render_ip_ranges(){
        $v = WP_Auth0_Options::get( 'ip_ranges' );
        echo '<textarea cols="25" name="' . WP_Auth0_Options::OPTIONS_NAME . '[ip_ranges]" id="wpa0_ip_ranges">' . esc_textarea( $v ) . '</textarea>';
        echo '<br/><span class="description">' . __('Only one range per line! Range format should be as: <code>xx.xx.xx.xx - yy.yy.yy.yy</code> (spaces will be trimmed)', WPA0_LANG) . '</span>';
    }

    public static function render_social_big_buttons(){
        $v = absint(WP_Auth0_Options::get( 'social_big_buttons' ));
        echo '<input type="checkbox" name="' . WP_Auth0_Options::OPTIONS_NAME . '[social_big_buttons]" id="wpa0_social_big_buttons" value="1" ' . checked( $v, 1, false ) . '/>';
    }

    public static function render_gravatar(){
        $v = absint(WP_Auth0_Options::get( 'gravatar' ));
        echo '<input type="checkbox" name="' . WP_Auth0_Options::OPTIONS_NAME . '[gravatar]" id="wpa0_gravatar" value="1" ' . checked( $v, 1, false ) . '/>';
    }

    public static function render_icon_url(){
        $v = WP_Auth0_Options::get( 'icon_url' );
        echo '<input type="text" name="' . WP_Auth0_Options::OPTIONS_NAME . '[icon_url]" id="wpa0_icon_url" value="' . esc_attr( $v ) . '"/>';
        echo ' <a href="javascript:void(0);" id="wpa0_choose_icon" class="button-secondary">' . __( 'Choose Icon', WPA0_LANG ) . '</a>';
        echo '<br/><span class="description">' . __('The icon should be 32x32 pixels!', WPA0_LANG) . '</span>';
    }

    public static function render_cdn_url () {
        $v = WP_Auth0_Options::get( 'cdn_url' );
        echo '<input type="text" name="' . WP_Auth0_Options::OPTIONS_NAME . '[cdn_url]" id="wpa0_cdn_url" value="' . esc_attr( $v ) . '"/>';
        echo '<br/><span class="description">' . __('Point this to the latest widget available in the CDN', WPA0_LANG) . '</span>';
    }

    public static function render_verified_email () {
        $v = absint(WP_Auth0_Options::get( 'requires_verified_email' ));
        echo '<input type="checkbox" name="' . WP_Auth0_Options::OPTIONS_NAME . '[requires_verified_email]" id="wpa0_verified_email" value="1" ' . checked( $v, 1, false ) . '/>';
        echo '<br/><span class="description">' . __('Mark this if you require the user to have a verified email to login', WPA0_LANG) . '</span>';
    }

    public static function render_allow_signup () {
        $v = absint(WP_Auth0_Options::get( 'allow_signup' ));
        echo '<input type="checkbox" name="' . WP_Auth0_Options::OPTIONS_NAME . '[allow_signup]" id="wpa0_allow_signup" value="1" ' . checked( $v, 1, false ) . '/>';
        echo '<br/><span class="description">' . __('If you have database connection you can allow users to signup in the widget', WPA0_LANG) . '</span>';
    }

    public static function render_allow_wordpress_login () {
        $v = absint(WP_Auth0_Options::get( 'wordpress_login_enabled' ));
        echo '<input type="checkbox" name="' . WP_Auth0_Options::OPTIONS_NAME . '[wordpress_login_enabled]" id="wpa0_wp_login_enabled" value="1" ' . checked( $v, 1, false ) . '/>';
        echo '<br/><span class="description">' . __('Mark this if you want to enable the regular WordPress login', WPA0_LANG) . '</span>';
    }


    public static function render_basic_description(){

    }

    public static function render_appearance_description(){

    }

    public static function render_advanced_description(){

    }


    public static function init_menu(){
        add_options_page( __('Auth0 Settings', WPA0_LANG), __('Auth0 Settings', WPA0_LANG), 'manage_options', 'wpa0', array(__CLASS__, 'render_settings_page') );
    }

    public static function render_settings_page(){
        include WPA0_PLUGIN_DIR . 'templates/settings.php';
    }

    public static function input_validator( $input ){
        $input['client_id'] = sanitize_text_field( $input['client_id'] );
        $input['form_title'] = sanitize_text_field( $input['form_title'] );
        $input['icon_url'] = esc_url( $input['icon_url'], array(
            'http',
            'https'
        ));

        $input['requires_verified_email'] = (isset($input['requires_verified_email']) ? 1 : 0);
        $input['wordpress_login_enabled'] = (isset($input['wordpress_login_enabled']) ? 1 : 0);
        $input['allow_signup'] = (isset($input['allow_signup']) ? 1 : 0);

        $input['social_big_buttons'] = (isset($input['social_big_buttons']) ? 1 : 0);
        $input['gravatar'] = (isset($input['gravatar']) ? 1 : 0);

        $input['remember_last_login'] = (isset($input['remember_last_login']) ? 1 : 0);

        $error = "";
        if (empty($input["domain"]) ) {
            $error = __("You need to specify domain", WPA0_LANG);
        }
        if (empty($input["client_id"])) {
            $error = __("You need to specify a client id", WPA0_LANG);
        }
        if (empty($input["client_secret"])) {
            $error = __("You need to specify a client secret", WPA0_LANG);
        }

        if (trim($input["dict"]) != '')
        {
            if (strpos($input["dict"], '{') !== false && json_decode($input["dict"]) === null)
            {
                $error = __("The Translation parameter should be a valid json object", WPA0_LANG);
            }
        }

        if (trim($input["extra_conf"]) != '')
        {
            if (json_decode($input["extra_conf"]) === null)
            {
                $error = __("The Extra settings parameter should be a valid json object", WPA0_LANG);
            }
        }

        if ($error != "") {
            add_settings_error(
                WP_Auth0_Options::OPTIONS_NAME,
                WP_Auth0_Options::OPTIONS_NAME,
                $error,
                'error'
            );

        }

        // $input['endpoint'] = esc_url( $input['endpoint'], array('https', 'http') );
        // if(!empty($input['endpoint']))
        //  $input['endpoint'] = trailingslashit($input['endpoint']);

        return $input;
    }
}