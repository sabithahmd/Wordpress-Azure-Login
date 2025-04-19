<?php
/**
 * Plugin Name: Login with Microsoft Entra ID
 * Description: Plugin to integrate Microsoft Entra ID Login with WordPress.
 * Author: Sabith Ahammad
 * Author URI: https://github.com/sabithahmd
 * Requires at least: 6.3
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package login-azure
 * Version: 1.0.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// including the required files.
require_once __DIR__ . '/classes/class-loginwithazure.php';
require_once __DIR__ . '/classes/class-loginwiazazureservice.php';
require_once __DIR__ . '/classes/class-settingspage.php';

$plugin_data    = get_file_data( __FILE__, array( 'Version' => 'Version' ) );
$plugin_version = $plugin_data['Version'];

define( 'LOGIN_WITH_AZURE_DIR', __DIR__ );
define( 'LOGIN_WITH_AZURE_URL', plugin_dir_url( __FILE__ ) );
define( 'LOGIN_WITH_AZURE_VERSION', $plugin_version );

$login_with_azure = LoginWithAzure::get_instance();
