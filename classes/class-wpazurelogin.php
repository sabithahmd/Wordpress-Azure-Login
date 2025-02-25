<?php
/**
 * WpAzureLogin class file
 *
 * File containing the WpAzureLogin class.
 *
 * @package    login-azure
 * @author     Sabith Ahammad <sa.codinglife@gmail.com>
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * WpAzureLogin class
 *
 * This class contains functionalities for
 * Azure login integration with WordPress.
 *
 * @package WpAzureLogin
 * @author Sabith Ahammad
 */
class WpAzureLogin {

	/**
	 * Static instance of the WpAzureLogin class
	 *
	 * @var WpAzureLogin
	 */
	private static $instance;

	/**
	 * Instance of the WalAzureService class
	 *
	 * @var WalAzureService
	 */
	public $azure_service;

	/**
	 * Whether to disable password login or not
	 *
	 * @var bool
	 */
	private $disable_password_login = false;


	/**
	 * Constructor of the WpAzureLogin class
	 */
	private function __construct() {
		$this->wal_session_start();

		add_action( 'login_form', array( $this, 'azure_add_login_fields' ) );
		add_action( 'plugins_loaded', array( $this, 'load_wp_azure_login' ) );

		add_filter( 'template_include', array( $this, 'wal_load_login_template' ) );
		add_filter( 'theme_page_templates', array( $this, 'wal_login_template' ) );

		$value_selctor          = get_option( 'azure_config_option_selector' );
		$diabled_password_login = get_option( 'wal_disable_password_login' );
		$redirect_uri           = get_option( 'wal_redirect_url_value' );
		if ( 'yes' === $diabled_password_login ) {
			add_action( 'login_init', array( $this, 'wal_disable_password_login' ) );
			$this->disable_password_login = true;
		}
		if ( 'database' === $value_selctor ) {
			$client_id     = get_option( 'wal_client_id_value' );
			$client_secret = get_option( 'wal_client_secret_value' );
			$tenant_id     = get_option( 'wal_tenant_id_value' );
		} else {
			$client_id     = getenv( 'WAL_CLIENT_ID' );
			$client_secret = getenv( 'WAL_CLIENT_SECRET' );
			$tenant_id     = getenv( 'WAL_TENANT_ID' );
		}
		$this->azure_service = WalAzureService::get_instance( $client_id, $redirect_uri, $client_secret, $tenant_id );
		$this->init_settings_page();
	}


	/**
	 * Get the static instance of the WpAzureLogin class
	 *
	 * @return WpAzureLogin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the settings page for Azure Configuration.
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
	private function init_settings_page() {

		$settings_page_data = array(
			'parent_slug' => 'options-general.php',
			'page_title'  => 'Configuration',
			'menu_title'  => 'Azure Configuration',
			'capability'  => 'manage_options',
			'slug'        => 'wal_configuration',
			'sections'    => array(
				array(
					'slug'        => 'wal_config_settings_section',
					'title'       => 'Azure Configuration',
					'description' => 'Enter your azure app credentials here.',
					'fields'      => array(
						array(
							'name'              => 'azure_config_option_selector',
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
							'content'    => 'Use env variables WAL_CLIENT_ID, WAL_CLIENT_SECRET, WAL_TENANT_ID for Client ID, Client Secret, Tenant ID respectively.',
							'display_if' => array( 'azure_config_option_selector', 'environment', true ),
						),
						array(
							'name'              => 'wal_client_id_value',
							'label'             => 'Client ID',
							'type'              => 'text',
							'display_if'        => array( 'azure_config_option_selector', 'database', true ),
							'value_type'        => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						array(
							'name'              => 'wal_client_secret_value',
							'label'             => 'Client Secret',
							'type'              => 'text',
							'display_if'        => array( 'azure_config_option_selector', 'database', true ),
							'value_type'        => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						array(
							'name'              => 'wal_tenant_id_value',
							'label'             => 'Tenant ID',
							'type'              => 'text',
							'display_if'        => array( 'azure_config_option_selector', 'database', true ),
							'value_type'        => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						array(
							'name'              => 'wal_redirect_url_value',
							'label'             => 'Redirect URL',
							'type'              => 'text',
							'value_type'        => 'string',
							'sanitize_callback' => 'sanitize_url',
						),
						array(
							'name'              => 'wal_disable_password_login',
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
	private function get_current_url_without_arguments() {
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
	private function wal_session_start() {
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
	private function wal_log_user_by_email_in( $email ) {
		$user = get_user_by( 'email', $email );
		if ( $user ) {
			wp_clear_auth_cookie();
			wp_set_current_user( $user->ID );
			wp_set_auth_cookie( $user->ID );
			$redirect_to = user_admin_url();
			wp_safe_redirect( $redirect_to );
			exit();
		} else {
			$this->wal_handle_error( 'User not found' );
		}
	}

	/**
	 * Function to capture the return request coming from Azure after login
	 *
	 * @return void
	 */
	private function catch_return_journey() {
		if ( isset( $_GET['code'] ) ) { // phpcs:ignore
			$current_url = $this->get_current_url_without_arguments();
			if ( hash_equals( $current_url, $this->azure_service->redirect_uri ) ) {
				if ( hash_equals( session_id(), isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : '' ) ) { // phpcs:ignore
					if ( ! isset( $_SESSION['code_verifier'] ) ) {
						$this->wal_handle_error( 'Code verifier not found in session' );
					}
					$code_verifier = sanitize_text_field( $_SESSION['code_verifier'] );
					$code          = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : ''; // phpcs:ignore
					$auth_data     = $this->azure_service->get_auth_data( $code_verifier, $code );
					if ( is_wp_error( $auth_data ) ) {
						$this->wal_handle_error( $auth_data->get_error_message() );
					}
					if ( isset( $auth_data['error'] ) ) {
						$this->wal_handle_error( $auth_data['error_description'] );
					}
					$user_data = $this->azure_service->get_user_data( $auth_data );
					if ( is_wp_error( $user_data ) ) {
						$this->wal_handle_error( $user_data->get_error_message() );
					}
					if ( isset( $user_data['error'] ) ) {
						$this->wal_handle_error( $user_data['error_description'] );
					}
					$email = $user_data['mail'];
					$this->wal_log_user_by_email_in( $email );
				} else {
					$this->wal_handle_error( 'State mismatch' );
				}
			} else {
				$this->wal_handle_error( 'Redirect URI mismatch' );
			}
		}
	}

	/**
	 * Display error and stop further execution
	 *
	 * @param string $error error message.
	 * @return void
	 */
	private function wal_handle_error( $error ) {
		wp_die( esc_html( $error ) );
	}

	/**
	 * Callback to execute when plugin is loaded
	 *
	 * @return void
	 */
	public function load_wp_azure_login() {
		$this->catch_return_journey();
		add_shortcode( 'wal_login_button', array( $this, 'wal_login_button_callback' ) );
	}

	/**
	 * Callback to display the login button when shortcode is called
	 *
	 * @return string
	 */
	public function wal_login_button_callback() {
		wp_enqueue_style( 'login-button-style', WP_AZURE_LOGIN_URL . 'assets/css/shortcode-style.css', array(), WP_AZURE_LOGIN_VERSION );
		$url = $this->azure_service->get_auth_url();
		ob_start();
		include WP_AZURE_LOGIN_DIR . '/templates/azure-login-button.php';
		return ob_get_clean();
	}

	/**
	 * Adds Azure login fields to the login form.
	 *
	 * This function outputs the Azure login button using a shortcode.
	 * If password login is disabled, it hides the default WordPress login fields.
	 *
	 * @return void
	 */
	public function azure_add_login_fields() {
		echo do_shortcode( '[wal_login_button]' );
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
	public function wal_disable_password_login() {
		// phpcs:ignore
		if ( isset( $_POST['log'] ) || isset( $_POST['user_login'] ) ) {
			wp_die( 'Password login is disabled.' );
		}
	}

	/**
	 * Load the custom login template if the page is configured to use it
	 *
	 * @param string $template The current template to be used.
	 * @return string The template to be used.
	 */
	public function wal_load_login_template( $template ) {
		global $post;
		if ( $post && 'page' === $post->post_type ) {
			$custom_template = get_post_meta( $post->ID, '_wp_page_template', true );
			if ( 'azure-login-template.php' === $custom_template ) {
				$template = plugin_dir_path( __FILE__ ) . 'templates/azure-login-template.php';
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
	public function wal_login_template( $templates ) {
		$templates['azure-login-template.php'] = 'Azure Login';
		return $templates;
	}
}
