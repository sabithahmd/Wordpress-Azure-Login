<?php
/**
 * Login Template
 *
 * This file contains login template which redirects to the Azure login page.
 *
 * @package azure-login
 * @author Sabith Ahammad
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// including the required files.
require_once WP_AZURE_LOGIN_DIR . '/wp_azure_login.php';

$azure_login      = WpAzureLogin::get_instance();
$azure_login_link = $azure_login->azure_service->get_auth_url();
wp_safe_redirect( $azure_login_link );
exit;
