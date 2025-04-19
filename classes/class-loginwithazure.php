<?php
/**
 * LoginWithAzure class file
 *
 * File containing the LoginWithAzure class.
 *
 * @package    login-azure
 * @author     Sabith Ahammad <sa.codinglife@gmail.com>
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * LoginWithAzure class
 *
 * This class contains functionalities for
 * Azure login integration with WordPress.
 *
 * @package LoginWithAzure
 * @author Sabith Ahammad
 */
class LoginWithAzure {

	/**
	 * Static instance of the LoginWithAzure class
	 *
	 * @var LoginWithAzure
	 */
	private static $instance;

	/**
	 * Instance of the LoginWiAzAzureService class
	 *
	 * @var LoginWiAzAzureService
	 */
	public $azure_service;

	/**
	 * Whether to disable password login or not
	 *
	 * @var bool
	 */
	private $disable_password_login = false;


	/**
	 * Constructor of the LoginWithAzure class
	 */
	private function __construct() {
		$this->loginwiaz_session_start();

		add_action( 'login_form', array( $this, 'loginwiaz_add_login_button' ) );
		add_action( 'plugins_loaded', array( $this, 'loginwiaz_on_loaded' ) );

		add_filter( 'allowed_redirect_hosts', array( $this, 'loginwiaz_add_redirect_hosts' ) );
		add_filter( 'template_include', array( $this, 'loginwiaz_load_login_template' ) );
		add_filter( 'theme_page_templates', array( $this, 'loginwiaz_login_template' ) );

		$cred_storage           = get_option( 'loginwiaz_cred_storage' );
		$disable_password_login = get_option( 'loginwiaz_disable_password_login' );
		$redirect_uri           = get_option( 'loginwiaz_redirect_url_value' );
		if ( 'yes' === $disable_password_login ) {
			add_action( 'login_init', array( $this, 'loginwiaz_disable_password_login' ) );
			$this->disable_password_login = true;
		}
		if ( 'database' === $cred_storage ) {
			$client_id     = get_option( 'loginwiaz_client_id_value' );
			$client_secret = get_option( 'loginwiaz_client_secret_value' );
			$tenant_id     = get_option( 'loginwiaz_tenant_id_value' );
		} else {
			$client_id     = getenv( 'LOGINWIAZ_CLIENT_ID' );
			$client_secret = getenv( 'LOGINWIAZ_CLIENT_SECRET' );
			$tenant_id     = getenv( 'LOGINWIAZ_TENANT_ID' );
		}
		$this->azure_service = LoginWiAzAzureService::get_instance( $client_id, $redirect_uri, $client_secret, $tenant_id );
		$this->loginwiaz_init_settings_page();
	}


	/**
	 * Get the static instance of the LoginWithAzure class
	 *
	 * @return LoginWithAzure
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the settings page for Login with Microsoft Entra ID Configuration.
	 *
	 * This function sets up the configuration page in the WordPress admin
	 * area under the 'Settings' menu. It defines the page's metadata, such
	 * as the title, menu label, and required capability. It also specifies
	 * the sections and fields for entering Azure login credentials.
	 *
	 * The settings page allows selecting where to store credentials (Database
	 * or Environment) and entering specific details like Client ID, Client
	 * Secret, Tenant ID, and Redirect URL if 'Database' is chosen.
	 */
	private function loginwiaz_init_settings_page() {

		$settings_page_data = array(
			'parent_slug' => 'options-general.php',
			'page_title'  => 'Configuration',
			'menu_title'  => 'Microsoft Entra ID Configuration',
			'capability'  => 'manage_options',
			'slug'        => 'loginwiaz_configuration',
			'sections'    => array(
				array(
					'slug'        => 'loginwiaz_config_settings_section',
					'title'       => 'Microsoft Entra ID Configuration',
					'description' => 'Enter your app credentials here.',
					'fields'      => array(
						array(
							'name'              => 'loginwiaz_cred_storage',
							'label'             => 'Credential Storage',
							'type'              => 'radio',
							'choices'           => array(
								'database'    => 'Database',
								'environment' => 'Environment',
							),
							'default'           => 'database',
							'value_type'        => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						array(
							'name'       => 'env_instruction',
							'label'      => '',
							'type'       => 'p',
							'content'    => 'Use env variables LOGINWIAZ_CLIENT_ID, LOGINWIAZ_CLIENT_SECRET, LOGINWIAZ_TENANT_ID for Client ID, Client Secret, Tenant ID respectively.',
							'display_if' => array( 'loginwiaz_cred_storage', 'environment', true ),
						),
						array(
							'name'              => 'loginwiaz_client_id_value',
							'label'             => 'Client ID',
							'type'              => 'text',
							'display_if'        => array( 'loginwiaz_cred_storage', 'database', true ),
							'value_type'        => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						array(
							'name'              => 'loginwiaz_client_secret_value',
							'label'             => 'Client Secret',
							'type'              => 'text',
							'display_if'        => array( 'loginwiaz_cred_storage', 'database', true ),
							'value_type'        => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						array(
							'name'              => 'loginwiaz_tenant_id_value',
							'label'             => 'Tenant ID',
							'type'              => 'text',
							'display_if'        => array( 'loginwiaz_cred_storage', 'database', true ),
							'value_type'        => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						array(
							'name'              => 'loginwiaz_redirect_url_value',
							'label'             => 'Redirect URL',
							'type'              => 'text',
							'value_type'        => 'string',
							'sanitize_callback' => 'sanitize_url',
						),
						array(
							'name'              => 'loginwiaz_disable_password_login',
							'label'             => 'Disable Password Login',
							'type'              => 'radio',
							'choices'           => array(
								'yes' => 'Yes',
								'no'  => 'No',
							),
							'default'           => 'no',
							'value_type'        => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			),
		);
		new SettingsPage( $settings_page_data );
	}

	/**
	 * Get current url without arguments
	 *
	 * @return string
	 */
	private function loginwiaz_get_current_url_without_arguments() {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$uri_parts   = explode( '?', $request_uri );
		$full_url    = get_site_url( null, $uri_parts[0] );
		return $full_url;
	}

	/**
	 * Function to start session if not started
	 *
	 * @return void
	 */
	private function loginwiaz_session_start() {
		if ( ! session_id() ) {
			session_start();
		}
	}

	/**
	 * Log user in by their email
	 *
	 * @param string $email user email.
	 * @return void
	 */
	private function loginwiaz_log_user_by_email_in( $email ) {
		$user = get_user_by( 'email', $email );
		if ( $user ) {
			wp_clear_auth_cookie();
			wp_set_current_user( $user->ID );
			wp_set_auth_cookie( $user->ID );
			$redirect_to = user_admin_url();
			wp_safe_redirect( $redirect_to );
			exit();
		} else {
			$this->loginwiaz_handle_error( 'User not found' );
		}
	}

	/**
	 * Function to capture the return request coming from Azure after login
	 *
	 * @return void
	 */
	private function loginwiaz_catch_return_journey() {
		if ( isset( $_GET['code'] ) ) { // phpcs:ignore
			$current_url = $this->loginwiaz_get_current_url_without_arguments();
			if ( hash_equals( $current_url, $this->azure_service->redirect_uri ) ) {
				if ( hash_equals( session_id(), isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : '' ) ) { // phpcs:ignore
					if ( ! isset( $_SESSION['code_verifier'] ) ) {
						$this->loginwiaz_handle_error( 'Code verifier not found in session' );
					}
					$code_verifier = sanitize_text_field( $_SESSION['code_verifier'] );
					$code          = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : ''; // phpcs:ignore
					$auth_data     = $this->azure_service->get_auth_data( $code_verifier, $code );
					if ( is_wp_error( $auth_data ) ) {
						$this->loginwiaz_handle_error( $auth_data->get_error_message() );
					}
					if ( isset( $auth_data['error'] ) ) {
						$this->loginwiaz_handle_error( $auth_data['error_description'] );
					}
					$user_data = $this->azure_service->get_user_data( $auth_data );
					if ( is_wp_error( $user_data ) ) {
						$this->loginwiaz_handle_error( $user_data->get_error_message() );
					}
					if ( isset( $user_data['error'] ) ) {
						$this->loginwiaz_handle_error( $user_data['error_description'] );
					}
					$email = $user_data['mail'];
					$this->loginwiaz_log_user_by_email_in( $email );
				} else {
					$this->loginwiaz_handle_error( 'State mismatch' );
				}
			} else {
				$this->loginwiaz_handle_error( 'Redirect URI mismatch' );
			}
		}
	}

	/**
	 * Display error and stop further execution
	 *
	 * @param string $error error message.
	 * @return void
	 */
	private function loginwiaz_handle_error( $error ) {
		wp_die( esc_html( $error ) );
	}

	/**
	 * Callback to execute when plugin is loaded
	 *
	 * @return void
	 */
	public function loginwiaz_on_loaded() {
		$this->loginwiaz_catch_return_journey();
		add_shortcode( 'loginwiaz_login_button', array( $this, 'loginwiaz_login_button_callback' ) );
	}

	/**
	 * Callback to display the login button when shortcode is called
	 *
	 * @return string
	 */
	public function loginwiaz_login_button_callback() {
		wp_enqueue_style( 'login-button-style', LOGIN_WITH_AZURE_URL . 'assets/css/shortcode-style.css', array(), LOGIN_WITH_AZURE_VERSION );
		$url = $this->azure_service->get_auth_url();
		ob_start();
		include LOGIN_WITH_AZURE_DIR . '/templates/azure-login-button.php';
		return ob_get_clean();
	}

	/**
	 * Adds Azure login button to the login form.
	 *
	 * This function outputs the Azure login button using a shortcode.
	 * If password login is disabled, it hides the default WordPress login fields.
	 *
	 * @return void
	 */
	public function loginwiaz_add_login_button() {
		echo do_shortcode( '[loginwiaz_login_button]' );
		if ( $this->disable_password_login ) {
			$this->hide_login_fields();
		}
	}

	/**
	 * Hide the default WordPress login fields (username, password, etc) to disable password login.
	 *
	 * @since 1.0.0
	 */
	public function hide_login_fields() {
		$css = '#loginform #user_login,
				#loginform #user_pass, #loginform p, .user-pass-wrap, p#nav  {
						display: none;
				}';
		wp_add_inline_style( 'login-button-style', $css );
	}

	/**
	 * Disable password login by stopping the script execution
	 * when a user attempts to login with a username and password.
	 *
	 * @since 1.0.0
	 */
	public function loginwiaz_disable_password_login() {
		// phpcs:ignore
		if ( isset( $_POST['log'] ) || isset( $_POST['user_login'] ) ) {
			wp_die( 'Password login is disabled.' );
		}
	}


	/**
	 * Add Microsoft Azure's login URL to the list of allowed redirect hosts.
	 * This is necessary when wp_safe_redirect() is used.
	 *
	 * @param array $hosts The list of allowed hosts.
	 *
	 * @return array The updated list of allowed hosts.
	 */
	public function loginwiaz_add_redirect_hosts( $hosts ) {
		$hosts[] = 'login.microsoftonline.com';
		return $hosts;
	}

	/**
	 * Load the custom login template if the page is configured to use it
	 *
	 * @param string $template The current template to be used.
	 * @return string The template to be used.
	 */
	public function loginwiaz_load_login_template( $template ) {
		global $post;
		if ( $post && 'page' === $post->post_type ) {
			$custom_template = get_post_meta( $post->ID, '_wp_page_template', true );
			if ( 'azure-login-template.php' === $custom_template ) {
				$template = LOGIN_WITH_AZURE_DIR . '/templates/azure-login-template.php';
			}
		}
		return $template;
	}

	/**
	 * Filter to add the Azure Login template to the page template options
	 *
	 * @param array $templates Array of page templates.
	 * @return array
	 */
	public function loginwiaz_login_template( $templates ) {
		$templates['azure-login-template.php'] = 'Azure Login';
		return $templates;
	}
}
