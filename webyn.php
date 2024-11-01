<?php
/*
 * The Webyn plugin file
 *
 * @link              https://www.webyn.ai
 * @since             1.1.0
 * @package           Webyn
 *
 * @wordpress-plugin
 * Plugin Name:       Webyn
 * Plugin URI:        https://www.webyn.ai/wordpress-plugin
 * Description:       Turn your visitor into customers with Webyn.ai CRO plugin
 * Version:           1.1.0
 * Author:            Webyn
 * Author URI:        https://www.webyn.ai/
 * Text Domain:       webyn
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'WEBYN_VERSION', '1.1.0' );

/***
 * Webyn Functions
 *
 * When logged in as a super admin, these functions will run to provide
 * debugging information when specific super admin menu items are selected.
 *
 * They are not used when a regular user is logged in.
 */
class Webyn {

	function __construct() {
        // do nothing
	}

	function init() {
        if ( is_admin() ) {
            // Add setting page
            add_action( 'admin_menu', [ $this, 'webyn_add_settings_page' ] );
            add_action( 'admin_init', [ $this, 'webyn_register_settings' ] );
        } else {
            add_action( 'wp_enqueue_scripts', [ $this, 'webyn_add_header_script' ] );
        }
    }

    function webyn_add_header_script() {
        $options = get_option( 'webyn_plugin_options' );
        if (is_array($options) && isset($options['client_id'])) {
            global $wp_version;
            $webynJsFile = 'https://files.webyn.ai/webyn.min.js?apiKey=' . $options['client_id'];
            $args = [
                'in_footer' => false,
                'strategy'  => 'async',
                ''
            ];
            if ( version_compare( $wp_version,'6.3', '>=' ) ) {
                wp_enqueue_script('webyn_front', $webynJsFile, array(), false, $args);
            } else {
                wp_enqueue_script('webyn_front', $webynJsFile, array(), false, false);
            }
        }
    }

    function webyn_add_settings_page() {
        add_options_page( 'Webyn Settings', 'Webyn Settings', 'manage_options', 'webyn-settings', [ $this, 'webyn_render_settings_page' ] );
    }

    function webyn_render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Webyn Settings</h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'webyn_plugin_options' );
                do_settings_sections( 'webyn_plugin' ); ?>
                <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
            </form>
            </div>
        <?php
    }

    function webyn_register_settings() {
        register_setting( 'webyn_plugin_options', 'webyn_plugin_options' );
        add_settings_section( 'api_settings', 'Welcome to Webyn!', [ $this, 'webyn_plugin_section_text' ], 'webyn_plugin' );

        add_settings_field( 'webyn_setting_client_id', 'Client Id', [ $this, 'webyn_setting_client_id' ], 'webyn_plugin', 'api_settings' );
    }

    function webyn_plugin_section_text() {
        echo '<p>';
        echo '<ul>';
        echo '<li>To complete the setup of  Webyn, please visit the <a href="https://app.webyn.ai/signup/integration#wordpress" target="_blank">integration page</a> and copy your <em>Client Id</em> below.</li>';
        echo '<li>If you haven\'t created a Webyn account yet, you can create one <a href="https://app.webyn.ai/signup" target="_blank">here</a>.</li>';
        echo '<li>For any questions or assistance, don\'t hesitate to reach out to us at <a href="mailto:clients@webyn.ai" target="_blank">clients@webyn.ai</a>.</li>';
        echo '</ul>';
        echo '</p>';
    }

    function webyn_setting_client_id() {
        $options = get_option( 'webyn_plugin_options' );
        echo "<input id='webyn_plugin_setting_client_id' name='webyn_plugin_options[client_id]' type='text' value='" . esc_attr( $options['client_id'] ) . "' class='regular-text' />";
    }
}

$GLOBALS['webyn'] = new Webyn();
$GLOBALS['webyn']->init();