<?php

function my_plugin_menu() {
    add_options_page(
        'تنظیمات پلاگین پیامکی',
        'تنظیمات پیامکی',
        'manage_options',
        'my-plugin-settings',
        'my_plugin_settings_page'
    );
}
add_action('admin_menu', 'my_plugin_menu');

function my_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>تنظیمات سرویس پیامکی- فقط ملی پیامک</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('my_plugin_options_group');
            do_settings_sections('my-plugin-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function my_plugin_settings_init() {
    add_settings_section(
        'my_plugin_settings_section',
        'اطلاعات سرویس پیامکی',
        null,
        'my-plugin-settings'
    );

    add_settings_field(
        'sms_service_username',
        'یوزرنیم سرویس پیامکی',
        'my_plugin_username_render',
        'my-plugin-settings',
        'my_plugin_settings_section'
    );

    add_settings_field(
        'sms_service_password',
        'پسورد سرویس پیامکی',
        'my_plugin_password_render',
        'my-plugin-settings',
        'my_plugin_settings_section'
    );
    add_settings_field(
        'sms_service_patern',
        'کلید پترن سرویس پیامکی',
        'my_plugin_patern_render',
        'my-plugin-settings',
        'my_plugin_settings_section'
    );

    register_setting('my_plugin_options_group', 'sms_service_username');
    register_setting('my_plugin_options_group', 'sms_service_password');
    register_setting('my_plugin_options_group', 'sms_service_patern');
}
add_action('admin_init', 'my_plugin_settings_init');

function my_plugin_username_render() {
    $username = get_option('sms_service_username');
    ?>
    <input type="text" name="sms_service_username" value="<?php echo esc_attr($username); ?>" />
    <?php
}

function my_plugin_password_render() {
    $password = get_option('sms_service_password');
    ?>
    <input type="password" name="sms_service_password" value="<?php echo esc_attr($password); ?>" />
    <?php
}function my_plugin_patern_render() {
    $patern = get_option('sms_service_patern');
    ?>
    <input type="password" name="sms_service_patern" value="<?php echo esc_attr($patern); ?>" />
    <?php
}
