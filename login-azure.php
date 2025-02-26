<?php
/**
 * Plugin Name: Login with Azure
 * Description: Plugin to integrate azure AD Login with WordPress.
 * Author: Sabith Ahammad
 * Author URI: https://github.com/sabithahmd
 * Requires at least: 6.3
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package login-azure
 * Version: 1.0.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// including the required files.
require_once __DIR__ . '/classes/class-wpazurelogin.php';
require_once __DIR__ . '/classes/class-walazureservice.php';
require_once __DIR__ . '/classes/class-settingspage.php';

$plugin_data    = get_file_data( __FILE__, array( 'Version' => 'Version' ) );
$plugin_version = $plugin_data['Version'];

define( 'WP_AZURE_LOGIN_DIR', __DIR__ );
define( 'WP_AZURE_LOGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_AZURE_LOGIN_VERSION', $plugin_version );

$azure_login = WpAzureLogin::get_instance();
