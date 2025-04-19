<?php
/**
 * Login Template
 *
 * This file contains login template which redirects to the Azure login page.
 *
 * @package login-azure
 * @author Sabith Ahammad
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$login_with_azure = LoginWithAzure::get_instance();
$azure_login_link = $login_with_azure->azure_service->get_auth_url();
wp_safe_redirect( $azure_login_link );
exit;
