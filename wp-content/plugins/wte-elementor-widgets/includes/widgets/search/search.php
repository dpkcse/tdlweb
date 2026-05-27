<?php
namespace WPTRAVELENGINEEB;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Search Widget.
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class Widget_Search.
 *
 * @since 1.4.7
 */
class Widget_Search extends Widget_Base {

	/**
	 * Widget name.
	 *
	 * @var string
	 */
	public $widget_name = 'wptravelengine-search';

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->widget_name;
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Search', 'wptravelengine-elementor-widgets' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'icon-wte-search';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'travel-monster' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'search_style',
			array(
				'label' => __( 'Style', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'enable_search_box',
			array(
				'label'     => __( 'Enable Search Box', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
				'separator' => 'after',
			)
		);

		$this->add_responsive_control(
			'search_input_padding',
			array(
				'label'      => __( 'Input Padding', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .wte-search-widget' => '--input-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'enable_search_box' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'input_typography',
				'label'     => __( 'Typography', 'wptravelengine-elementor-widgets' ),
				'selector'  => '{{WRAPPER}} .wte-search-widget.search-box-active .wte-search-field',
				'condition' => array(
					'enable_search_box' => 'yes',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-search-widget' => '--search-text-color: {{VALUE}}',
				),
				'condition' => array(
					'enable_search_box' => 'yes',
				),
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label'     => __( 'Border Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-search-widget' => '--border-color: {{VALUE}}',
				),
				'condition' => array(
					'enable_search_box' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'search_border_radius',
			array(
				'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .wte-search-widget' => '--search-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'enable_search_box' => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-search-widget' => '--search-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => __( 'Icon Hover Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-search-widget' => '--search-color-hover: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-search-widget' => '--icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'background_color',
			array(
				'label'     => __( 'Background Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-search-widget' => '--search-bg: {{VALUE}};',
				),
				'condition' => array(
					'enable_search_box' => 'yes',
				),
			)
		);

		$this->add_control(
			'search_width',
			array(
				'label'      => __( 'Width', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1024,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-search-widget' => '--search-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'enable_search_box' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$settings          = $this->get_settings_for_display();
		$enable_search_box = 'yes' === $settings['enable_search_box'];

		$this->add_render_attribute(
			'search-widget-wrap',
			'class',
			array(
				'wte-search-widget',
				$enable_search_box ? 'search-box-active' : '',
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'search-widget-wrap' ); ?>>
			<?php
			// Render search toggle button (icon only mode)
			if ( ! $enable_search_box ) {
				$this->render_search_toggle();
			}

			// Render search form if search box is enabled
			if ( $enable_search_box ) {
				$this->render_search_form();
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render search toggle button.
	 */
	private function render_search_toggle() {
		?>
		<button type="button" class="wte-search-toggle toggle" data-toggle-target=".search-modal" data-toggle-body-class="showing-search-modal" data-set-focus=".search-modal .search-field" aria-expanded="false"aria-label="<?php esc_attr_e( 'Search', 'wptravelengine-elementor-widgets' ); ?>">
			<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M8.7838 0.666016C13.2588 0.666016 16.8988 4.30602 16.8988 8.78102C16.8988 10.8923 16.0886 12.8179 14.7627 14.2631L17.3716 16.8666C17.6158 17.1108 17.6166 17.5058 17.3725 17.7499C17.2508 17.8733 17.09 17.9341 16.93 17.9341C16.7708 17.9341 16.6108 17.8733 16.4883 17.7516L13.8479 15.1185C12.4589 16.2309 10.6977 16.8968 8.7838 16.8968C4.3088 16.8968 0.667969 13.256 0.667969 8.78102C0.667969 4.30602 4.3088 0.666016 8.7838 0.666016ZM8.7838 1.91602C4.99797 1.91602 1.91797 4.99518 1.91797 8.78102C1.91797 12.5668 4.99797 15.6468 8.7838 15.6468C12.5688 15.6468 15.6488 12.5668 15.6488 8.78102C15.6488 4.99518 12.5688 1.91602 8.7838 1.91602Z" fill="currentColor">	
				</path>
			</svg>
		</button>
		<?php
	}

	/**
	 * Render search form.
	 */
	private function render_search_form() {
		?>
		<form role="search" method="get" class="wte-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="wte-search-field-<?php echo esc_attr( $this->get_id() ); ?>"><?php esc_html_e( 'Search for:', 'wptravelengine-elementor-widgets' ); ?></label>
			<input type="search" id="wte-search-field-<?php echo esc_attr( $this->get_id() ); ?>" class="wte-search-field" placeholder="<?php esc_attr_e( 'Search...', 'wptravelengine-elementor-widgets' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
			<button type="submit" class="wte-search-submit">
				<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M8.7838 0.666016C13.2588 0.666016 16.8988 4.30602 16.8988 8.78102C16.8988 10.8923 16.0886 12.8179 14.7627 14.2631L17.3716 16.8666C17.6158 17.1108 17.6166 17.5058 17.3725 17.7499C17.2508 17.8733 17.09 17.9341 16.93 17.9341C16.7708 17.9341 16.6108 17.8733 16.4883 17.7516L13.8479 15.1185C12.4589 16.2309 10.6977 16.8968 8.7838 16.8968C4.3088 16.8968 0.667969 13.256 0.667969 8.78102C0.667969 4.30602 4.3088 0.666016 8.7838 0.666016ZM8.7838 1.91602C4.99797 1.91602 1.91797 4.99518 1.91797 8.78102C1.91797 12.5668 4.99797 15.6468 8.7838 15.6468C12.5688 15.6468 15.6488 12.5668 15.6488 8.78102C15.6488 4.99518 12.5688 1.91602 8.7838 1.91602Z" fill="currentColor">	
					</path>
				</svg>
				<span class="screen-reader-text"><?php esc_html_e( 'Search', 'wptravelengine-elementor-widgets' ); ?></span>
			</button>
		</form>
		<?php
	}
}
