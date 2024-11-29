<?php
/**
 * Login Template
 *
 * This file contains login template which redirects to the Azure login page.
 *
 * @package wp-azure-login
 * @author Sabith Ahammad
 */

// including the required files.
require_once WP_AZURE_LOGIN_DIR . '/wp_azure_login.php';

$azure_login      = WpAzureLogin::get_instance();
$azure_login_link = $azure_login->get_auth_url();
wp_safe_redirect( $azure_login_link );
exit;
