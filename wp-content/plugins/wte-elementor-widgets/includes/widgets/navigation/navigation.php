<?php
namespace WPTRAVELENGINEEB;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

/**
 * Navigation Widget.
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class Widget_Navigation.
 *
 * @since 1.4.7
 */
class Widget_Navigation extends Widget_Base {

	/**
	 * Widget name.
	 *
	 * @var string
	 */
	public $widget_name = 'wptravelengine-navigation';

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
		return __( 'Navigation Menu', 'wptravelengine-elementor-widgets' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'icon-wte-menu';
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
	 * Get script dependencies.
	 *
	 * @since 1.5.1
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'wptravelengineeeb-trips' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// General Settings Section
		$this->start_controls_section(
			'header_section',
			array(
				'label' => __( 'General Settings', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		// Select Menu
		$menus                = wp_get_nav_menus();
		$menu_options         = array();
		$menu_options['none'] = __( 'Choose a menu', 'wptravelengine-elementor-widgets' );
		foreach ( $menus as $menu ) {
			$menu_options[ $menu->slug ] = $menu->name;
		}

		$this->add_control(
			'menu_select',
			array(
				'label'   => __( 'Select a Menu', 'wptravelengine-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $menu_options,
				'default' => 'none',
			)
		);

		$this->add_control(
			'menu_direction',
			array(
				'label'   => __( 'Menu Direction', 'wptravelengine-elementor-widgets' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'horizontal' => array(
						'title' => esc_html__( 'Row - horizontal', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-arrow-right',
					),
					'vertical'   => array(
						'title' => esc_html__( 'Column - vertical', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default' => 'horizontal',
			)
		);

		$this->add_control(
			'menu_stretch',
			array(
				'label'     => __( 'Stretch Menu', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
				'label_on'  => __( 'Yes', 'wptravelengine-elementor-widgets' ),
				'label_off' => __( 'No', 'wptravelengine-elementor-widgets' ),
			)
		);

		$this->end_controls_section();

		// Menu Items Styling
		$this->start_controls_section(
			'menu_items_style',
			array(
				'label' => __( 'Menu Items', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'menu_items_spacing',
			array(
				'label'      => __( 'Items Spacing', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-nav-menu' => '--nav-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'menu_items_typography',
				'selector' => '{{WRAPPER}} .wte-nav-menu .menu-item a',
			)
		);
		$this->start_controls_tabs( 'menu_items_states' );

		// Normal State
		$this->start_controls_tab(
			'menu_items_normal',
			array(
				'label' => __( 'Normal', 'wptravelengine-elementor-widgets' ),
			)
		);

		$this->add_control(
			'menu_items_color',
			array(
				'label'     => __( 'Text Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-nav-menu' => '--nav-item-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		// Hover State
		$this->start_controls_tab(
			'menu_items_hover',
			array(
				'label' => __( 'Hover/Active', 'wptravelengine-elementor-widgets' ),
			)
		);

		$this->add_control(
			'menu_items_hover_color',
			array(
				'label'     => __( 'Text Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-nav-menu' => '--nav-item-hover-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// Submenu Styling
		$this->start_controls_section(
			'submenu_style',
			array(
				'label' => __( 'Submenu', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'submenu_width',
			array(
				'label'      => __( 'Width', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min'  => 100,
						'max'  => 1000,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
					),
					'vw' => array(
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-nav-menu' => '--submenu-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'submenu_background',
			array(
				'label'     => __( 'Background Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-nav-menu' => '--submenu-background: {{VALUE}};',
				),
			)
		);

		// border radius
		$this->add_control(
			'submenu_border_radius',
			array(
				'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wte-nav-menu' => '--submenu-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'submenu_title',
			array(
				'label'     => __( 'Item Styling', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_responsive_control(
			'submenu_padding',
			array(
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .wte-nav-menu' => '--submenu-item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'submenu_items_states' );

		// Normal State
		$this->start_controls_tab(
			'submenu_items_normal',
			array(
				'label' => __( 'Normal', 'wptravelengine-elementor-widgets' ),
			)
		);

		$this->add_control(
			'submenu_items_color',
			array(
				'label'     => __( 'Text Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-nav-menu' => '--submenu-item-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		// Hover State
		$this->start_controls_tab(
			'submenu_items_hover',
			array(
				'label' => __( 'Hover/Active', 'wptravelengine-elementor-widgets' ),
			)
		);

		$this->add_control(
			'submenu_items_hover_color',
			array(
				'label'     => __( 'Text Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-nav-menu' => '--submenu-item-hover-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$settings     = $this->get_settings_for_display();
		$menu_stretch = $settings['menu_stretch'] === 'yes' ? true : false;
		$data_stretch = $menu_stretch ? 'data-stretch=yes' : 'data-stretch=no';

		$this->add_render_attribute(
			'outer-wrapper',
			'class',
			array(
				'wte-nav-menu',
				esc_attr( $settings['menu_direction'] ),
			)
		);

		?>
		<div <?php $this->print_render_attribute_string( 'outer-wrapper' ); ?>>
			<?php
			if ( ! empty( $settings['menu_select'] ) && 'none' !== $settings['menu_select'] ) :
				$random = wp_rand( 1, 100 );
				?>
				<nav id="wte-site-navigation-<?php echo esc_attr( $random ); ?>" class="primary-navigation" <?php echo esc_attr( $data_stretch ); ?>>
					<?php
						wp_nav_menu(
							array(
								'menu'            => $settings['menu_select'],
								'menu_class'      => 'primary-menu-wrapper',
								'container_class' => 'wte-primary-menu-container',
								'fallback_cb'     => false,
								'walker'          => new \Walker_Nav_Menu(),
							)
						);
					?>
				</nav>
			<?php else : ?>
				<p class="wte-no-menu-notice"><?php esc_html_e( 'Please select a menu from the widget settings.', 'wptravelengine-elementor-widgets' ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
}
