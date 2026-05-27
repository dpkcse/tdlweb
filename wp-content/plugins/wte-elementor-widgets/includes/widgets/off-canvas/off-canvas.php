<?php
namespace WPTRAVELENGINEEB;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

/**
 * Off Canvas Widget.
 *
 * All-in-one widget that renders a toggle button and a fixed off-canvas panel.
 * Panel content is either a WordPress navigation menu or an Elementor template
 * selected from the saved Templates library.
 *
 * @since 1.5.2
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class Widget_Off_Canvas.
 *
 * @since 1.5.2
 */
class Widget_Off_Canvas extends Widget {

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'wptravelengine-off-canvas';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Off Canvas', 'wptravelengine-elementor-widgets' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-sidebar';
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
		return array( 'off canvas', 'offcanvas', 'menu', 'mobile', 'sidebar', 'drawer', 'navigation', 'hamburger', 'toggle' );
	}

	/**
	 * Script dependencies.
	 *
	 * wte-offcanvas is enqueued centrally in class-plugin.php register_scripts().
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array();
	}

	/**
	 * Returns published Off Canvas builder posts for the SELECT control.
	 *
	 * @return array  [ post_id => post_title ]
	 */
	private function get_offcanvas_templates() {
		$posts = get_posts(
			array(
				'post_type'      => 'wpte_offcanvas',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		// Use a non-numeric prefix so PHP treats all keys as strings and preserves
		// insertion order (numeric-looking keys get sorted before string keys).
		$options = array( '' => __( 'Select', 'wptravelengine-elementor-widgets' ) );
		foreach ( $posts as $post ) {
			$options[ 'tpl_' . $post->ID ] = $post->post_title;
		}

		return $options;
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// =========================================================
		// CONTENT TAB
		// =========================================================

		// --- Toggle Button ---
		$this->start_controls_section(
			'section_toggle_button',
			array(
				'label' => __( 'Toggle Button', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'toggle_icon',
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
			'toggle_text',
			array(
				'label'       => __( 'Text', 'wptravelengine-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Menu', 'wptravelengine-elementor-widgets' ),
			)
		);

		$this->add_control(
			'toggle_icon_position',
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
				'condition' => array( 'toggle_text!' => '' ),
			)
		);

		$this->end_controls_section();

		// --- Panel Settings ---
		$this->start_controls_section(
			'section_panel_settings',
			array(
				'label' => __( 'Panel Settings', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'panel_side',
			array(
				'label'   => __( 'Panel Side', 'wptravelengine-elementor-widgets' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'  => array(
						'title' => __( 'Left', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
				'toggle'  => false,
			)
		);

		$this->add_control(
			'show_overlay',
			array(
				'label'     => __( 'Show Overlay', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Yes', 'wptravelengine-elementor-widgets' ),
				'label_off' => __( 'No', 'wptravelengine-elementor-widgets' ),
			)
		);

		$this->add_control(
			'close_on_overlay',
			array(
				'label'     => __( 'Close on Overlay Click', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Yes', 'wptravelengine-elementor-widgets' ),
				'label_off' => __( 'No', 'wptravelengine-elementor-widgets' ),
				'condition' => array( 'show_overlay' => 'yes' ),
			)
		);

		$this->add_control(
			'template_id',
			array(
				'label'       => __( 'Off Canvas Content', 'wptravelengine-elementor-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $this->get_offcanvas_templates(),
				'default'     => '',
				'separator'   => 'before',
				'description' => sprintf(
					/* translators: %s: link to Off Canvas builder */
					__( 'Build your panel content in the <a href="%s" target="_blank">Off Canvas Builder</a>.', 'wptravelengine-elementor-widgets' ),
					esc_url( admin_url( 'edit.php?post_type=wpte_offcanvas' ) )
				),
			)
		);

		$this->add_control(
			'panel_preview',
			array(
				'label'     => __( 'Panel Preview', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'label_on'  => __( 'Show', 'wptravelengine-elementor-widgets' ),
				'label_off' => __( 'Hide', 'wptravelengine-elementor-widgets' ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		// --- Close Button ---
		$this->start_controls_section(
			'section_close_button',
			array(
				'label' => __( 'Close Button', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'close_icon',
			array(
				'label'       => __( 'Icon', 'wptravelengine-elementor-widgets' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => array(
					'value'   => 'fas fa-times',
					'library' => 'fa-solid',
				),
				'recommended' => array(
					'fa-solid' => array( 'times', 'times-circle', 'window-close', 'xmark' ),
				),
			)
		);

		$this->add_control(
			'close_text',
			array(
				'label'       => __( 'Label', 'wptravelengine-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Close', 'wptravelengine-elementor-widgets' ),
			)
		);

		$this->end_controls_section();

		// =========================================================
		// STYLE TAB
		// =========================================================

		// --- Toggle Button Style ---
		$this->start_controls_section(
			'section_toggle_style',
			array(
				'label' => __( 'Toggle Button', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'toggle_icon_size',
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
			'toggle_icon_text_gap',
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
				'condition'  => array( 'toggle_text!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'toggle_typography',
				'selector'  => '{{WRAPPER}} .wte-offcanvas-toggle',
				'condition' => array( 'toggle_text!' => '' ),
			)
		);

		$this->start_controls_tabs( 'toggle_style_tabs' );

		$this->start_controls_tab(
			'toggle_tab_normal',
			array( 'label' => __( 'Normal', 'wptravelengine-elementor-widgets' ) )
		);

		$this->add_control(
			'toggle_color',
			array(
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-offcanvas-btn-wrapper' => '--btn-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_bg_color',
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
			'toggle_tab_hover',
			array( 'label' => __( 'Hover', 'wptravelengine-elementor-widgets' ) )
		);

		$this->add_control(
			'toggle_hover_color',
			array(
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-offcanvas-btn-wrapper' => '--btn-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_hover_bg',
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
			'toggle_padding',
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
				'name'     => 'toggle_border',
				'selector' => '{{WRAPPER}} .wte-offcanvas-toggle',
			)
		);

		$this->add_responsive_control(
			'toggle_border_radius',
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
				'name'     => 'toggle_box_shadow',
				'selector' => '{{WRAPPER}} .wte-offcanvas-toggle',
			)
		);

		$this->end_controls_section();

		// --- Panel ---
		$this->start_controls_section(
			'section_panel_style',
			array(
				'label' => __( 'Panel', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'panel_width',
			array(
				'label'      => __( 'Width', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 800,
					),
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
					'vw' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 320,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-offcanvas-panel' => '--offcanvas-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'panel_background_color',
			array(
				'label'     => __( 'Background Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wte-offcanvas-panel' => '--offcanvas-bg: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'panel_padding',
			array(
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => '30',
					'right'  => '30',
					'bottom' => '30',
					'left'   => '30',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-offcanvas-panel' => '--offcanvas-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'panel_box_shadow',
				'selector' => '{{WRAPPER}} .wte-offcanvas-content',
			)
		);

		$this->end_controls_section();

		// --- Overlay ---
		$this->start_controls_section(
			'section_overlay_style',
			array(
				'label'     => __( 'Overlay', 'wptravelengine-elementor-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_overlay' => 'yes' ),
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.5)',
				'selectors' => array(
					'{{WRAPPER}} .wte-offcanvas-panel' => '--offcanvas-overlay-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// --- Close Button Style ---
		$this->start_controls_section(
			'section_close_style',
			array(
				'label' => __( 'Close Button', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'close_icon_size',
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
					'size' => 20,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-offcanvas-close' => '--close-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'close_button_tabs' );

		$this->start_controls_tab(
			'close_tab_normal',
			array( 'label' => __( 'Normal', 'wptravelengine-elementor-widgets' ) )
		);

		$this->add_control(
			'close_color',
			array(
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-offcanvas-close' => '--close-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'close_tab_hover',
			array( 'label' => __( 'Hover', 'wptravelengine-elementor-widgets' ) )
		);

		$this->add_control(
			'close_hover_color',
			array(
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-offcanvas-close' => '--close-hover-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'close_typography',
				'selector'  => '{{WRAPPER}} .wte-offcanvas-close',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'close_padding',
			array(
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .wte-offcanvas-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the panel body content.
	 *
	 * @param array $settings Widget settings.
	 */
	private function render_panel_body( array $settings ) {
		$raw         = $settings['template_id'] ?? '';
		$template_id = absint( str_replace( 'tpl_', '', $raw ) );

		if ( ! $template_id || ! class_exists( '\Elementor\Plugin' ) ) {
			echo '<p class="wte-offcanvas-no-menu">' . esc_html__( 'Please select an Off Canvas from the widget settings.', 'wptravelengine-elementor-widgets' ) . '</p>';
			return;
		}

		$post = get_post( $template_id );
		if ( ! $post || 'publish' !== $post->post_status ) {
			echo '<p class="wte-offcanvas-no-menu">' . esc_html__( 'The selected Off Canvas template is not published.', 'wptravelengine-elementor-widgets' ) . '</p>';
			return;
		}

		$content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id );

		if ( ! empty( $content ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $content;
		} else {
			echo '<p class="wte-offcanvas-no-menu">' . esc_html__( 'No Elementor content found. Please edit this template with Elementor and add some content.', 'wptravelengine-elementor-widgets' ) . '</p>';
		}
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// --- Derive values ---
		$panel_id = 'wte-offcanvas-' . $this->get_id();

		$side = in_array( $settings['panel_side'], array( 'left', 'right' ), true )
			? $settings['panel_side']
			: 'left';

		$has_overlay      = 'yes' === $settings['show_overlay'];
		$close_on_overlay = $has_overlay && 'yes' === $settings['close_on_overlay'];

		// --- Toggle button ---
		$has_icon   = ! empty( $settings['toggle_icon']['value'] );
		$has_text   = ! empty( $settings['toggle_text'] );
		$icon_first = ( 'before' === ( $settings['toggle_icon_position'] ?? 'before' ) ) || ! $has_text;

		$this->add_render_attribute(
			'toggle',
			array(
				'class'         => 'wte-offcanvas-toggle',
				'type'          => 'button',
				'data-target'   => $panel_id,
				'aria-expanded' => 'false',
				'aria-controls' => $panel_id,
				'aria-label'    => esc_attr__( 'Open menu', 'wptravelengine-elementor-widgets' ),
			)
		);

		// --- Panel ---
		$is_editor     = \Elementor\Plugin::$instance->editor->is_edit_mode();
		$panel_classes = array( 'wte-offcanvas-panel', 'wte-offcanvas-' . $side );
		if ( $is_editor ) {
			$panel_classes[] = 'wte-offcanvas-editor-preview';
			if ( 'yes' === ( $settings['panel_preview'] ?? '' ) ) {
				$panel_classes[] = 'is-editor-previewing';
			}
		}

		$this->add_render_attribute(
			'panel',
			array(
				'id'                 => $panel_id,
				'class'              => $panel_classes,
				'role'               => 'dialog',
				'aria-modal'         => 'true',
				'aria-label'         => esc_attr__( 'Off Canvas Menu', 'wptravelengine-elementor-widgets' ),
				'aria-hidden'        => 'true',
				'data-close-overlay' => $close_on_overlay ? 'yes' : 'no',
			)
		);
		?>
		<div class="wte-offcanvas-wrapper">

			<?php /* Toggle Button */ ?>
			<div class="wte-offcanvas-btn-wrapper">
				<button <?php $this->print_render_attribute_string( 'toggle' ); ?>>
					<?php if ( $has_icon && $icon_first ) : ?>
						<?php \Elementor\Icons_Manager::render_icon( $settings['toggle_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					<?php endif; ?>
					<?php if ( $has_text ) : ?>
						<span class="wte-offcanvas-btn-text"><?php echo esc_html( $settings['toggle_text'] ); ?></span>
					<?php endif; ?>
					<?php if ( $has_icon && ! $icon_first ) : ?>
						<?php \Elementor\Icons_Manager::render_icon( $settings['toggle_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					<?php endif; ?>
				</button>
			</div>

			<?php /* Off Canvas Panel */ ?>
			<div <?php $this->print_render_attribute_string( 'panel' ); ?>>

				<?php if ( $has_overlay ) : ?>
					<div class="wte-offcanvas-overlay" aria-hidden="true"></div>
				<?php endif; ?>

				<div class="wte-offcanvas-content">

					<div class="wte-offcanvas-header">
						<button
							class="wte-offcanvas-close"
							type="button"
							aria-label="<?php esc_attr_e( 'Close menu', 'wptravelengine-elementor-widgets' ); ?>"
						>
							<?php \Elementor\Icons_Manager::render_icon( $settings['close_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							<?php if ( ! empty( $settings['close_text'] ) ) : ?>
								<span class="wte-offcanvas-close-text"><?php echo esc_html( $settings['close_text'] ); ?></span>
							<?php endif; ?>
						</button>
					</div>

					<div class="wte-offcanvas-body">
						<?php $this->render_panel_body( $settings ); ?>
					</div>

				</div>
			</div>

		</div>
		<?php
	}

	/**
	 * No JS content template — forces Elementor to use the PHP render() for the editor preview.
	 * This ensures the actual Off Canvas template content is shown in the editor panel.
	 *
	 * @since 1.5.2
	 */
	protected function content_template() {}
}
