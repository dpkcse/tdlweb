<?php
namespace WPTRAVELENGINEEB;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Off Canvas Button Widget.
 *
 * Renders a toggle button that opens an Off Canvas panel widget.
 * Place this widget anywhere in the header builder — typically on mobile viewports.
 *
 * @since 1.5.2
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class Widget_Off_Canvas_Button.
 *
 * @since 1.5.2
 */
class Widget_Off_Canvas_Button extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'wptravelengine-off-canvas-button';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Off Canvas Button', 'wptravelengine-elementor-widgets' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-menu-bar';
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
	 * Get widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'off canvas', 'offcanvas', 'hamburger', 'mobile menu', 'toggle', 'button', 'navigation' );
	}

	/**
	 * Script dependencies.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'wte-offcanvas' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// =========================================================
		// CONTENT TAB
		// =========================================================

		$this->start_controls_section(
			'section_button_content',
			array(
				'label' => __( 'Button', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label'       => __( 'Icon', 'wptravelengine-elementor-widgets' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'default'     => array(
					'value'   => 'fas fa-bars',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => __( 'Text', 'wptravelengine-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Menu', 'wptravelengine-elementor-widgets' ),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'     => __( 'Icon Position', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'before' => array(
						'title' => __( 'Before', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-h-align-left',
					),
					'after'  => array(
						'title' => __( 'After', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'before',
				'toggle'    => false,
				'condition' => array( 'button_text!' => '' ),
			)
		);

		$this->add_control(
			'target_id',
			array(
				'label'       => __( 'Target Panel ID', 'wptravelengine-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'e.g. my-mobile-menu', 'wptravelengine-elementor-widgets' ),
				'description' => __( 'Enter the Panel ID set in the Off Canvas widget. Leave empty if there is only one Off Canvas panel on the page.', 'wptravelengine-elementor-widgets' ),
				'separator'   => 'before',
			)
		);

		$this->end_controls_section();

		// =========================================================
		// STYLE TAB
		// =========================================================

		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => __( 'Button', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 60,
					),
				),
				'default'    => array(
					'size' => 24,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-offcanvas-btn-wrapper' => '--btn-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_text_gap',
			array(
				'label'      => __( 'Icon & Text Gap', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-offcanvas-toggle' => 'gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'button_text!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'button_typography',
				'selector'  => '{{WRAPPER}} .wte-offcanvas-toggle',
				'condition' => array( 'button_text!' => '' ),
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab(
			'btn_tab_normal',
			array( 'label' => __( 'Normal', 'wptravelengine-elementor-widgets' ) )
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-offcanvas-btn-wrapper' => '--btn-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_color',
			array(
				'label'     => __( 'Background', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-offcanvas-btn-wrapper' => '--btn-bg: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'btn_tab_hover',
			array( 'label' => __( 'Hover', 'wptravelengine-elementor-widgets' ) )
		);

		$this->add_control(
			'button_hover_color',
			array(
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-offcanvas-btn-wrapper' => '--btn-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_bg',
			array(
				'label'     => __( 'Background', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-offcanvas-btn-wrapper' => '--btn-hover-bg: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .wte-offcanvas-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .wte-offcanvas-toggle',
			)
		);

		$this->add_responsive_control(
			'button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .wte-offcanvas-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .wte-offcanvas-toggle',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$settings   = $this->get_settings_for_display();
		$target_id  = ! empty( $settings['target_id'] ) ? sanitize_html_class( $settings['target_id'] ) : '';
		$has_icon   = ! empty( $settings['button_icon']['value'] );
		$has_text   = ! empty( $settings['button_text'] );
		$icon_first = ( 'before' === $settings['icon_position'] ) || ! $has_text;

		$this->add_render_attribute(
			'toggle',
			array(
				'class'         => 'wte-offcanvas-toggle',
				'type'          => 'button',
				'aria-expanded' => 'false',
				'aria-label'    => esc_attr__( 'Open menu', 'wptravelengine-elementor-widgets' ),
			)
		);

		if ( $target_id ) {
			$this->add_render_attribute( 'toggle', 'data-target', $target_id );
		}
		?>
		<div class="wte-offcanvas-btn-wrapper">
			<button <?php $this->print_render_attribute_string( 'toggle' ); ?>>
				<?php if ( $has_icon && $icon_first ) : ?>
					<?php \Elementor\Icons_Manager::render_icon( $settings['button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				<?php endif; ?>
				<?php if ( $has_text ) : ?>
					<span class="wte-offcanvas-btn-text"><?php echo esc_html( $settings['button_text'] ); ?></span>
				<?php endif; ?>
				<?php if ( $has_icon && ! $icon_first ) : ?>
					<?php \Elementor\Icons_Manager::render_icon( $settings['button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				<?php endif; ?>
			</button>
		</div>
		<?php
	}
}
