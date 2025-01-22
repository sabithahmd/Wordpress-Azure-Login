<?php
/**
 * Plugin Name: Login with Azure
 * Plugin URI: http://wordpress.org/plugins/login-azure/
 * Description: Plugin to integrate azure AD Login with WordPress.
 * Author: Sabith Ahammad
 * Author URI: https://github.com/sabithahmd
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package login-azure
 * Version: 1.0.4
 */

// including the required files.
require_once __DIR__ . '/classes/class-wpazurelogin.php';
require_once __DIR__ . '/classes/class-walazureservice.php';
require_once __DIR__ . '/classes/class-settingspage.php';

define( 'WP_AZURE_LOGIN_DIR', __DIR__ );

$azure_login = WpAzureLogin::get_instance();
