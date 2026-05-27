<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
class Yeekit_El_Repeater_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
	private $fixed_files_indices = false;
	public function get_type()
	{
		return 'repeater';
	}
	public function editor_preview_footer()
	{
		add_action('wp_footer', array($this, "content_template_script"));
	}
	function content_template_script()
	{
?>
		<script>
			jQuery(document).ready(() => {
				elementor.hooks.addFilter(
					'elementor_pro/forms/content_template/field/repeater',
					function(inputField, item, i) {
						return `<hr>`;
					}, 10, 3
				);
			});
		</script>
	<?php
	}
	public function get_name()
	{
		return esc_html__('Repeater End', 'repeater-for-elementor');
	}
	/**
	 * @param Widget_Base $widget
	 */
	public function update_controls($widget)
	{
		$elementor = \ElementorPro\Plugin::elementor();
		$control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
		if (is_wp_error($control_data)) {
			return;
		}
		$field_controls = [
			'add_button' => [
				'name' => 'add_button',
				'label' => esc_html__('Add button text', 'repeater-for-elementor'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Add more...',
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'initial_rows' => [
				'name' => 'initial_rows',
				'label' => esc_html__('Initial Rows', 'repeater-for-elementor'),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'content_classes' => 'pro_disable elementor-panel-alert elementor-panel-alert-info',
				'raw' => esc_html__('The number of rows at start, if empty no rows will be created ( Default = 1 Upgrade to pro to change it )', 'repeater-for-elementor'),
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'initial_rows_map' => [
				'name' => 'initial_rows_map',
				'label' => esc_html__('Map field with Initial Rows', 'repeater-for-elementor'),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'content_classes' => 'pro_disable elementor-panel-alert elementor-panel-alert-info',
				'raw' => esc_html__('The number of rows at the start map with a field ( Upgrade to pro to add it )', 'repeater-for-elementor'),
				'default' => '',
				'description' => 'The number of rows at the start map with a field',
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'limit' => [
				'name' => 'limit',
				'label' => esc_html__('Limit', 'repeater-for-elementor'),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'default' => '10',
				'content_classes' => 'pro_disable elementor-panel-alert elementor-panel-alert-info',
				'raw' => esc_html__('Max number of rows applicable by the user, leave empty for no limit ( Default = 5 Upgrade to pro to change it )', 'repeater-for-elementor'),
				'description' => 'Max number of rows applicable by the user, leave empty for no limit',
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'button_padding' => [
				'name' => 'button_padding',
				'label' => esc_html__('Button Padding', 'repeater-for-elementor'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'rem', 'custom'],
				'default' => array("unit" => "px", "top" => 3, "right" => 24, "bottom" => 3, "left" => 24, 'isLinked' => false),
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'advanced',
				'inner_tab' => 'form_fields_advanced_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'button_background' => [
				'name' => 'button_background',
				'label' => esc_html__('Button Background Color', 'repeater-for-elementor'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#69727d',
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'advanced',
				'inner_tab' => 'form_fields_advanced_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'button_color' => [
				'name' => 'button_color',
				'label' => esc_html__('Button Color', 'repeater-for-elementor'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'advanced',
				'inner_tab' => 'form_fields_advanced_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'button_border_width' => [
				'name' => 'button_border_width',
				'label' => esc_html__('Button Border Width', 'repeater-for-elementor'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'rem', 'custom'],
				'default' => array("unit" => "px", "top" => 0, "right" => 0, "bottom" => 0, "left" => 0, 'isLinked' => false),
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'advanced',
				'inner_tab' => 'form_fields_advanced_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'button_border_color' => [
				'name' => 'button_border_color',
				'label' => esc_html__('Button Border Color', 'repeater-for-elementor'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#69727d',
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'advanced',
				'inner_tab' => 'form_fields_advanced_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'button_border_radius' => [
				'name' => 'button_border_radius',
				'label' => esc_html__('Button Border Radius', 'repeater-for-elementor'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'rem', 'custom'],
				'default' => array("unit" => "px", "top" => 5, "right" => 5, "bottom" => 5, "left" => 5, 'isLinked' => false),
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'advanced',
				'inner_tab' => 'form_fields_advanced_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
		];
		$control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
		$widget->update_control('form_fields', $control_data);
	}
	/**
	 * @param      $item
	 * @param      $item_index
	 * @param Form $form
	 */
	private function cover_css($css = "padding", $datas = array())
	{
		if (!is_array($datas)) {
			$datas = array("unit" => "px", "top" => 0, "right" => 0, "bottom" => 0, "left" => 0);
		}
		return $css . ":" . $datas["top"] . $datas["unit"] . " " . $datas["right"] . $datas["unit"] . " " . $datas["bottom"] . $datas["unit"] . " " . $datas["left"] . $datas["unit"] . "; ";
	}
	public function render($item, $item_index, $form)
	{
		$initial_rows = (isset($item['initial_rows']) ? $item['initial_rows'] : "1");
		$initial_rows_map = (isset($item['initial_rows_map']) ? $item['initial_rows_map'] : "");
		$limit = (isset($item['limit']) ? $item['limit'] : "5");
		$button_padding = (isset($item['button_padding']) ? $item['button_padding'] : array("unit" => "px", "top" => 3, "right" => 24, "bottom" => 3, "left" => 24));
		$button_background = (isset($item['button_background']) ? $item['button_background'] : "#69727d");
		$button_color = (isset($item['button_color']) ? $item['button_color'] : "#fff");
		$button_border_width = (isset($item['button_border_width']) ? $item['button_border_width'] : array("unit" => "px", "top" => 0, "right" => 0, "bottom" => 0, "left" => 0));
		$button_border_color = (isset($item['button_border_color']) ? $item['button_border_color'] : "#69727d");
		$button_border_radius = (isset($item['button_border_radius']) ? $item['button_border_radius'] : array("unit" => "px", "top" => 5, "right" => 5, "bottom" => 5, "left" => 5));
		$form->add_render_attribute('div' . $item_index, 'class', 'elementor-field-repeater-end');
		$form->add_render_attribute('div' . $item_index, 'data-initial_rows', $initial_rows);
		$form->add_render_attribute('div' . $item_index, 'data-initial_rows_map', $initial_rows_map);
		$form->add_render_attribute('div' . $item_index, 'data-map_id', "elementor-field-group-field_" . $item['_id']);
		$form->add_render_attribute('div' . $item_index, 'data-limit', $limit);
		$form->set_render_attribute('input' . $item_index, 'type', 'hidden');
		$form->add_render_attribute('input' . $item_index, 'class', 'elementor-field-repeater-data');
		$button_style = $this->cover_css("padding", $button_padding);
		$button_style .= $this->cover_css("border-width", $button_border_width);
		$button_style .= $this->cover_css("border-radius", $button_border_radius);
		$button_style .= 'color:' . $button_color . "; ";
		$button_style .= 'background:' . $button_background . "; ";
		$button_style .= 'border-color:' . $button_border_color . "; ";
		$button_style .= 'border-style:solid; ';
	?>
		<div <?php $form->print_render_attribute_string('div' . $item_index); ?>>
			<div class="repeater-field-warp-item">
			</div>
			<div class="repeater-field-footer"><a href="#" class="repeater-field-button-add" style="<?php echo wp_kses_post($button_style) ?>"><?php echo esc_html($item['add_button']) ?></a></div>
			<input <?php $form->print_render_attribute_string('input' . $item_index); ?>>
			<textarea class="elementor-field-repeater-data-html hidden"></textarea>
		</div>
<?php
	}
	public function process_field($field, $record, $ajax_handler)
	{
		$id = $field['id'];
		$datas_files = $record->get("files");
		$values = json_decode($field['raw_value'], true);
		$data_ids = $values["id"];
		$fields = $values["fields"];
		//phpcs:disable WordPress.Security.NonceVerification.Missing
		// elementor pro form submit nonce verification
		if (isset($_POST["form_fields"])) {
			$datas = wp_unslash($_POST["form_fields"]); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}
		$html = "<ol class='yeekit-el-repeater-html'>";
		foreach ($data_ids as $field_id) {
			$field_id = sanitize_text_field($field_id);
			$html .= "<li><ul>";
			foreach ($fields as $name) {
				if ($name == "") {
					continue;
				}
				$name = preg_match('/form_fields\[(.*?)\]/m', $name, $matches);
				$name = $matches[1];
				$data_names = $datas[$name];
				$value = $data_names[$field_id];
				if ($value == null) {
					if (isset($_FILES['form_fields']['name'][$field_id])) {
						$files = $_FILES['form_fields'];
						foreach ($files['name'][$field_id] as $index => $name) {
							if (empty($name) || $files['error'][$field_id][$index] !== UPLOAD_ERR_OK) {
								continue;
							}
							$file_to_handle = array(
								'name'     => $files['name'][$field_id][$index],
								'type'     => $files['type'][$field_id][$index],
								'tmp_name' => $files['tmp_name'][$field_id][$index],
								'error'    => $files['error'][$field_id][$index],
								'size'     => $files['size'][$field_id][$index],
							);
							if (!function_exists('wp_handle_upload')) {
								require_once(ABSPATH . 'wp-admin/includes/file.php');
							}
							$upload_overrides = array(
								'test_form' => false,
								// 'mimes'     => array(
								// 	'jpg|jpeg|jpe' => 'image/jpeg',
								// 	'gif'          => 'image/gif',
								// 	'png'          => 'image/png',
								// 	'pdf'          => 'application/pdf',
								// ),
							);
							$move_new_file = wp_handle_upload($file_to_handle, $upload_overrides);
							if ($move_new_file && !isset($move_new_file['error'])) {
								$url = $move_new_file['url'];
								$final_filename = basename($move_new_file['file']); // Lấy tên file thực tế sau khi xử lý unique
								if (!is_array($value)) {
									$value = array();
								}
								$value[] = sprintf(
									'<a href="%1$s" target="_blank" download>%2$s</a>',
									esc_url($url),
									esc_html($final_filename)
								);
							} else {
								$ajax_handler->add_error($id, $move_new_file['error']);
							}
						}
					}
				}
				if ($value == "") {
					if (isset($datas_files[$name])) {
						$value_files = $datas_files[$name]["url"];
						if (isset($value_files[$field_id])) {
							$value = $value_files[$field_id];
						}
					}
				}
				if (is_array($value)) {
					$value = implode(", ", $value);
				}
				$array_remove = array("rednumber_dev_check", "1023-01-01", "1234567892", "rednumber_dev_check@test.com");
				if (!in_array($value, $array_remove)) {
					if ($value != "") {
						$html .= "<li>" . $this->get_label($record, $name) . " : " . $value . "</li>";
					}
				}
			}
			$html .= "</ul></li>";
		}
		$html .= "</ol>";
		$html = apply_filters('yeekit_el_repeater_html', $html, $record, $field);
		$record->update_field($id, 'value', $html);
	}
	private function get_file_url($file_name)
	{
		$wp_upload_dir = wp_upload_dir();
		$url = $wp_upload_dir['baseurl'] . '/elementor/forms/' . $file_name;
		/**
		 * Upload file URL.
		 *
		 * Filters the URL to a file uploaded using Elementor forms.
		 *
		 * @since 1.0.0
		 *
		 * @param string $url       File URL.
		 * @param string $file_name File name.
		 */
		$url = apply_filters('elementor_pro/forms/upload_url', $url, $file_name);
		return $url;
		//phpcs:enable WordPress.Security.NonceVerification.Missing
	}
	private function get_ensure_upload_dir()
	{
		$path = $this->get_upload_dir();
		if (file_exists($path . '/index.php')) {
			return $path;
		}
		wp_mkdir_p($path);
		$files = [
			[
				'file' => 'index.php',
				'content' => [
					'<?php',
					'// Silence is golden.',
				],
			],
			[
				'file' => '.htaccess',
				'content' => [
					'Options -Indexes',
					'<ifModule mod_headers.c>',
					'	<Files *.*>',
					'       Header set Content-Disposition attachment',
					'	</Files>',
					'</IfModule>',
				],
			],
		];
		foreach ($files as $file) {
			if (! file_exists(trailingslashit($path) . $file['file'])) {
				$content = implode(PHP_EOL, $file['content']);
				@file_put_contents(trailingslashit($path) . $file['file'], $content);
			}
		}
		return $path;
	}
	private function get_upload_dir()
	{
		$wp_upload_dir = wp_upload_dir();
		$path = $wp_upload_dir['basedir'] . '/elementor/forms';
		/**
		 * Upload file path.
		 *
		 * Filters the path to a file uploaded using Elementor forms.
		 *
		 * @since 1.0.0
		 *
		 * @param string $url File URL.
		 */
		$path = apply_filters('elementor_pro/forms/upload_path', $path);
		return $path;
	}
	private function get_label($record, $id = "")
	{
		$form_fields = $record->get_form_settings('form_fields');
		foreach ($form_fields as $field) {
			if (isset($field["_id"])) {
				if ($id == $field["custom_id"] || $id == $field["_id"]) {
					$record->remove_field($field["_id"]);
					$record->remove_field($field["custom_id"]);
					return $field["field_label"];
				}
			}
		}
	}
	public function __construct()
	{
		parent::__construct();
		add_action("wp_enqueue_scripts", array($this, "add_lib"), 1000);
		add_action('elementor/preview/init', array($this, 'editor_preview_footer'));
	}
	function add_lib()
	{
		wp_enqueue_script("elementor_repeater", ELEMENTOR_REPEATER_PLUGIN_URL . "libs/elementor_repeater.js", array("jquery"));
		wp_localize_script('elementor_repeater', 'elementor_repeater', array("wp_is_mobile" => wp_is_mobile()));
		wp_enqueue_style("repeater_icon", ELEMENTOR_REPEATER_PLUGIN_URL . "libs/css/repeatericons.css");
		wp_enqueue_style("elementor_repeater", ELEMENTOR_REPEATER_PLUGIN_URL . "libs/elementor_repeater.css");
	}
}
