<?php
/**
 * Plugin Name: SECDASH
 * Plugin URI: http://secdash.de/
 * Description: A plugin which provides the SECDASH service with all information it needs.
 * Version: 1.5.1
 * Author: Baseplus DIGITAL MEDIA GmbH <secdash@baseplus.de>
 * Author URI: https://secdash.de/
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: secdash
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    die();
}

define('SECDASH_PLUGIN_VERSION', '1.5.1');

if (false === class_exists('Baseplus\Secdash\ApiEndpoint')) {
    include_once __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'ApiEndpoint.php';
}

if (false === class_exists('Baseplus\Secdash\Mailer')) {
    include_once __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Mailer.php';
}

class Secdash
{
    static public function getOption($option, $constant = null)
    {
        if (null !== $constant && defined($constant)) {
            return constant($constant);
        }

        $options = wp_parse_args(get_option('secdash'), [
            'access_token' => null,
            'mailer_token' => null,
        ]);
        if (isset($options[$option])) {
            return $options[$option];
        }

        return null;
    }
}

add_action('wp_loaded', ['Baseplus\Secdash\ApiEndpoint', 'exec']);
add_action('wp_loaded', ['Baseplus\Secdash\Mailer', 'init']);

//register_activation_hook(__FILE__, ['Baseplus\Secdash\ApiEndpoint', 'onActivation']);
//register_deactivation_hook(__FILE__, ['Baseplus\Secdash\ApiEndpoint', 'onDeactivation']);
register_uninstall_hook(__FILE__, ['Baseplus\Secdash\ApiEndpoint', 'onUninstall']);


add_action('admin_init', function() {
    register_setting('secdash', 'secdash', function($values) {
        $values['access_token'] = trim($values['access_token']);
        $values['mailer_token'] = trim($values['mailer_token']);
        return $values;
    });
    add_settings_section('secdash_settings', __('Settings', 'secdash'), function() {
        //echo '';
    }, 'secdash_plugin');

    add_settings_field( 'secdash_access_token', __('Access Token', 'secdash'), function() {
        $options = get_option('secdash');
        echo '<input id="secdash_access_token" name="secdash[access_token]" type="text" value="' . $options['access_token'] .'" >';
    }, 'secdash_plugin', 'secdash_settings');

    add_settings_field( 'secdash_mailer_token', __('Mailer Token', 'secdash'), function() {
        $options = get_option('secdash');
        echo '<input id="secdash_mailer_token" name="secdash[mailer_token]" type="text" value="' . $options['mailer_token'] .'" >';
    }, 'secdash_plugin', 'secdash_settings');
});

add_action('admin_menu', function() {
    add_options_page(__('SECDASH', 'secdash'), __('SECDASH', 'secdash'),'manage_options','secdash', function() {

        // todo - add action handling the correct wordpress way.
        if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_GET['action']) && 'backend' === $_GET['action']) {
            \Baseplus\Secdash\ApiEndpoint::execBackendRegistration();
        }
        ?>
        <h2>SECDASH Settings</h2>
        <form action="<?php echo admin_url('options.php'); ?>" method="post">
            <?php
            settings_fields('secdash');
            do_settings_sections('secdash_plugin'); ?>
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save'); ?>">
        </form>
        <?php /*
        <form action="<?php echo add_query_arg(['page' => 'secdash', 'action' => 'backend'], admin_url('options-general.php')); ?>" method="post">
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Register'); ?>">
        </form>
        */ ?>
    <?php });
});

add_filter('plugin_action_links_' . plugin_basename( __FILE__), function($links) {
    $link = [sprintf(
        '<a href="%s">%s</a>', add_query_arg(['page' => 'secdash'], admin_url('options-general.php')), __('Settings', 'secdash')
    )];
    return array_merge($links, $link);
});

add_action('admin_notices', function() {
    $screen = get_current_screen();
    if ('settings_page_secdash' !== $screen->id) {
        return;
    }

    if (false === function_exists('json_encode')) {
        echo '<div class="notice notice-error"><p>' .
            __('Your PHP does not include json support. Please enable ext-json if possible otherwise the plugin will not work.', 'secdash') .
        '</p></div>';
    }
});
