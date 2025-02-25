<?php
/**
 * SettingsPage class file
 *
 * File containing the SettingsPage class.
 *
 * @package    login-azure
 * @author     Sabith Ahammad <sa.codinglife@gmail.com>
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! class_exists( 'settingspage' ) ) :
	/**
	 * SettingsPage class
	 *
	 * This class provides functionalities for
	 * adding a settings page with WordPress.
	 *
	 * @author Sabith Ahammad
	 */
	class SettingsPage {

		/**
		 * Static instance of the SettingsPage class
		 *
		 * @var SettingsPage
		 */
		private static $instance;

		/**
		 * Data for the settings page
		 *
		 * @var array
		 */
		private $page_data;

		/**
		 * Data for hide or show fields based on other field values
		 *
		 * @var array
		 */
		private $display_if_data = array();

		/**
		 * Retrieves the singleton instance of the SettingsPage class.
		 *
		 * @param array $page_data Data for the settings page.
		 * @return SettingsPage The singleton instance.
		 */
		public static function get_instance( $page_data ) {
			if ( null === self::$instance ) {
				self::$instance = new self( $page_data );
			}
			return self::$instance;
		}

		/**
		 * Constructor for the SettingsPage class.
		 *
		 * Initializes the settings page with the provided page data and sets
		 * up the necessary WordPress actions for the settings page.
		 *
		 * @param array $page_data Data for the settings page.
		 */
		public function __construct( $page_data ) {
			$this->page_data = $page_data;
			$this->init();
		}

		/**
		 * Initializes the settings page.
		 *
		 * Hooks into the 'admin_menu' action to add the settings page to the menu,
		 * and into the 'admin_init' action to register the settings.
		 */
		private function init() {
			add_action( 'admin_menu', array( $this, 'add_menu' ), 20 );
			add_action( 'admin_init', array( $this, 'settings_init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_script' ) );
		}

		/**
		 * Adds the settings page to the menu.
		 *
		 * @see add_submenu_page()
		 */
		public function add_menu() {
			add_submenu_page( $this->page_data['parent_slug'], $this->page_data['page_title'], $this->page_data['menu_title'], $this->page_data['capability'], $this->page_data['slug'], array( $this, 'settings_page_view' ) );
		}


		/**
		 * Enqueues the admin script for the settings page.
		 *
		 * This script is responsible for handling the settings page JavaScript functionality.
		 * It is enqueued in the footer of the admin page for the plugin.
		 */
		public function enqueue_admin_script() {
			wp_enqueue_script( 'wal-settings', WP_AZURE_LOGIN_URL . '/assets/js/settings.js', array(), WP_AZURE_LOGIN_VERSION, true );
		}

		/**
		 * Adds an inline script to the page to toggle the conditional fields based on the value of the radio buttons.
		 *
		 * This function is called by the init method. It loops through the $display_if_data property and adds an event listener to each radio button.
		 * When a radio button is changed, it calls the toggleConditionalFields function to show or hide the conditional fields based on the radio button's value.
		 *
		 * @return void
		 */
		private function add_inline_script() {
			if ( count( $this->display_if_data ) > 0 ) {
				$script = "document.addEventListener('DOMContentLoaded', function() {";
				foreach ( $this->display_if_data as $radio => $fields ) {
					$script .= "document.querySelectorAll('input[name=\"{$radio}\"]').forEach((radio) => {";
					$script .= "radio.addEventListener('change', function(){ toggleConditionalFields(radio.name, JSON.parse('" . wp_json_encode( $fields ) . "')); });";
					$script .= '});';
				}
				$script .= '});';
				wp_add_inline_script( 'wal-settings', $script );
			}
		}

		/**
		 * Renders the settings page.
		 *
		 * This function is called when the settings page is accessed. It renders
		 * the page with a form to save the settings and displays any error or
		 * success messages.
		 */
		public function settings_page_view() {
			?>
				<div class="wrap">
					<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
					<?php
					if ( 'options-general.php' !== $this->page_data['parent_slug'] ) {
						settings_errors();
					}
					?>
					<form action="options.php" method="post">
						<?php
						settings_fields( $this->page_data['slug'] );
						$this->render_settings_fields();
						submit_button( 'Save Settings' );
						?>
					</form>
				</div>
			<?php
		}

		/**
		 * Initializes and registers the settings and fields for the settings page.
		 *
		 * Iterates over the sections and fields provided in the page data, adding
		 * each section and its fields to the settings page. Each section is added
		 * using `add_settings_section`, and each field is registered using
		 * `register_setting` and added with `add_settings_field`.
		 */
		public function settings_init() {
			foreach ( $this->page_data['sections'] as $section ) {
				add_settings_section(
					$section['slug'],
					$section['title'],
					function () use ( $section ) {
						echo '<p>' . esc_html( $section['description'] ) . '</p>';
					},
					$this->page_data['slug']
				);
				foreach ( $section['fields'] as $field ) {
					if ( 'p' !== $field['type'] ) {
						register_setting(
							$this->page_data['slug'],
							$field['name'],
							array(
								'type'              => $field['value_type'],
								'sanitize_callback' => $field['sanitize_callback'],
							)
						);
					}
				}
			}
		}

		/**
		 * Renders the appropriate HTML input field based on the field type.
		 *
		 * @param array $field An associative array containing field properties such as 'type' and 'name'.
		 */
		private function settings_callback( $field ) {
			switch ( $field['type'] ) {
				case 'text':
					echo '<input type="text" name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( get_option( $field['name'] ) ) . '" class="regular-text" />';
					break;

				case 'radio':
					foreach ( $field['choices'] as $value => $label ) {
						echo '<input type="radio" name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $value ) . '" ' . checked( get_option( $field['name'], isset( $field['default'] ) && $field['default'] === $value ? $value : '' ), $value, false ) . '>';
						echo '<label for="' . esc_attr( $field['name'] ) . '">' . esc_html( $label ) . '</label><br>';
					}
					break;

				case 'p':
					echo '<p name="' . esc_attr( $field['name'] ) . '">' . esc_html( $field['content'] ) . '</p>';
					break;
			}
			if ( isset( $field['display_if'] ) ) {
				$this->display_if_data[ $field['display_if'][0] ][] = array( $field['name'], $field['display_if'][1] );
			}
		}

		/**
		 * Determines if a settings field should be hidden based on the value of another setting.
		 *
		 * @param array $field Associative array containing field properties such as 'type' and 'name'.
		 *
		 * @return string CSS style attribute for hiding the element.
		 */
		private function display_if_callback( $field ) {
			if ( isset( $field['display_if'] ) ) {
				list( $field, $value, $default ) = $field['display_if'];
				$current_value                   = get_option( $field );
				if ( $current_value ) {
					if ( $current_value !== $value ) {
						return 'display: none;';
					}
				} elseif ( false === $default ) {
					return 'display: none;';
				}
			}
		}

		/**
		 * Renders the settings fields for each section.
		 *
		 * Iterates over each section defined in the page data, and renders
		 * a heading and description for the section. Then, it iterates over
		 * each field in the section, and renders a table row for the field.
		 * The table row contains a label and a field element, which is
		 * generated by the settings_callback method.
		 */
		private function render_settings_fields() {
			foreach ( $this->page_data['sections'] as $section ) {
				echo '<h2>' . esc_html( $section['title'] ) . '</h2>';
				echo '<p>' . esc_html( $section['description'] ) . '</p>';
				echo '<table class="form-table" role="presentation"><tbody>';
				foreach ( $section['fields'] as $field ) {
					echo '<tr style="' . ( esc_attr( $this->display_if_callback( $field ) ) ) . '"><th scope="row">' . esc_html( $field['label'] ) . '</th><td>';
					$this->settings_callback( $field );
					echo '</td></tr>';
				}
				echo '</tbody></table>';
			}
			$this->add_inline_script();
		}
	}
endif;