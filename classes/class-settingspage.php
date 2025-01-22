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
				<script type="text/javascript">
				document.addEventListener('DOMContentLoaded', function() {
					function toggleConditionalFields( radioFieldName, fields) {
						const selectedValue = document.querySelector('input[name="'+radioFieldName+'"]:checked').value;
						fields.forEach((fieldData) => {
							const field = document.querySelector('input[name="'+fieldData[0]+'"]').closest('tr');
							if (selectedValue === fieldData[1]) {
								field.style.display = '';
							}
							else {
								field.style.display = 'none';
							}
						});
					}
					<?php
					foreach ( $this->display_if_data as $radio => $fields ) {
						?>
						document.querySelectorAll('input[name="<?php echo esc_attr( $radio ); ?>"]').forEach((radio) => {
							radio.addEventListener('change', function(){ toggleConditionalFields(radio.name, JSON.parse('<?php echo wp_json_encode( $fields ); ?>')); } );
						});
						<?php
					}
					?>
				});
				</script>
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
					register_setting( $this->page_data['slug'], $field['name'] );
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
				echo '<p>' . $section['description'] . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<table class="form-table" role="presentation"><tbody>';
				foreach ( $section['fields'] as $field ) {
					echo '<tr style="' . ( esc_attr( $this->display_if_callback( $field ) ) ) . '"><th scope="row">' . esc_html( $field['label'] ) . '</th><td>';
					$this->settings_callback( $field );
					echo '</td></tr>';
				}
				echo '</tbody></table>';
			}
		}
	}
endif;