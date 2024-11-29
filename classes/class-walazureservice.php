<?php
/**
 * WalAzureService class file
 *
 * File containing the WalAzureService class.
 *
 * @package    azure-login
 * @author     Sabith Ahammad <sa.codinglife@gmail.com>
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * WalAzureService class
 *
 * This class contains functionalities for
 * connecting with azure services for the login.
 *
 * @author Sabith Ahammad
 */
class WalAzureService {
	/**
	 * Static instance of the WalAzureService class
	 *
	 * @var WalAzureService
	 */
	private static $instance;

	/**
	 * Azure AD Application client ID
	 *
	 * @var string
	 */
	public $client_id;

	/**
	 * Azure AD Application redirect URI
	 *
	 * @var string
	 */
	public $redirect_uri;

	/**
	 * Azure AD Application client secret
	 *
	 * @var string
	 */
	public $client_secret;

	/**
	 * Azure AD Tenant ID
	 *
	 * @var string
	 */
	public $tenant_id;

	/**
	 * Get the static instance of the WalAzureService class
	 *
	 * @param string $client_id     Azure AD Application client ID.
	 * @param string $redirect_uri  Azure AD Application redirect URI.
	 * @param string $client_secret Azure AD Application client secret.
	 * @param string $tenant_id     Azure AD Tenant ID.
	 *
	 * @return WalAzureService
	 */
	public static function get_instance( $client_id, $redirect_uri, $client_secret, $tenant_id ) {
		if ( null === self::$instance ) {
			self::$instance = new self( $client_id, $redirect_uri, $client_secret, $tenant_id );
		}
		return self::$instance;
	}

	/**
	 * Private constructor to prevent instantiation of the class
	 *
	 * @param string $client_id     Azure AD Application client ID.
	 * @param string $redirect_uri  Azure AD Application redirect URI.
	 * @param string $client_secret Azure AD Application client secret.
	 * @param string $tenant_id     Azure AD Tenant ID.
	 */
	private function __construct( $client_id, $redirect_uri, $client_secret, $tenant_id ) {
		$this->client_id     = $client_id;
		$this->redirect_uri  = $redirect_uri;
		$this->client_secret = $client_secret;
		$this->tenant_id     = $tenant_id;
	}

	/**
	 * Get the user data from Microsoft Graph
	 *
	 * @param array $auth_data Authentication data returned from Azure AD
	 *                          authorization code flow.
	 *
	 * @return array|WP_Error User data or WP_Error if something goes wrong.
	 */
	public function get_user_data( $auth_data ) {
		$response = wp_remote_get(
			'https://graph.microsoft.com/v1.0/me',
			array(
				'headers' => array(
					'Accept'        => 'application/json',
					'Authorization' => 'Bearer ' . $auth_data['access_token'],
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$json = wp_remote_retrieve_body( $response );
		return json_decode( $json, true );
	}

	/**
	 * Get the authentication data from Azure AD
	 *
	 * @param string $code_verifier The random code verifier string.
	 * @param string $code          The authorization code returned from Azure AD.
	 *
	 * @return array|WP_Error Authentication data or WP_Error if something goes wrong.
	 */
	public function get_auth_data( $code_verifier, $code ) {
		$post_data = array(
			'grant_type'    => 'authorization_code',
			'client_id'     => $this->client_id,
			'redirect_uri'  => $this->redirect_uri,
			'code'          => $code,
			'code_verifier' => $code_verifier,
			'client_secret' => $this->client_secret,
		);
		$response  = wp_remote_post(
			'https://login.microsoftonline.com/' . $this->tenant_id . '/oauth2/v2.0/token',
			array(
				'body'    => $post_data,
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$json = wp_remote_retrieve_body( $response );
		return json_decode( $json, true );
	}

	/**
	 * Construct azure auth url based on the credentials
	 *
	 * @return string
	 */
	public function get_auth_url() {
		$code_verifier             = $this->generate_code_verifier();
		$code_challenge            = $this->generate_code_challenge( $code_verifier );
		$_SESSION['code_verifier'] = $code_verifier;
		$params                    = http_build_query(
			array(
				'state'                 => session_id(),
				'scope'                 => 'User.Read',
				'response_type'         => 'code',
				'approval_prompt'       => 'auto',
				'client_id'             => $this->client_id,
				'redirect_uri'          => $this->redirect_uri,
				'code_challenge'        => $code_challenge,
				'code_challenge_method' => 'S256',
			)
		);
		$url                       = 'https://login.microsoftonline.com/' . $this->tenant_id . '/oauth2/v2.0/authorize?' . $params;
		return $url;
	}

	/**
	 * Generate a random code verifier using secure random bytes
	 *
	 * @return string
	 */
	public function generate_code_verifier() {
		$length = 128;
		$bytes  = random_bytes( $length );
		return rtrim( strtr( base64_encode( $bytes ), '+/', '-_' ), '=' ); //phpcs:ignore
	}

	/**
	 * Generate a code challenge from the code verifier
	 *
	 * @param string $code_verifier The random code verifier string.
	 * @return string
	 */
	public function generate_code_challenge( $code_verifier ) {
		return rtrim( strtr( base64_encode( hash( 'sha256', $code_verifier, true ) ), '+/', '-_' ), '=' ); //phpcs:ignore
	}
}
