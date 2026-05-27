<?php
namespace WPTRAVELENGINEEB;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Icons_Manager;

/**
 * Contact Widget.
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class Widget_Contact.
 *
 * @since 1.4.7
 */
class Widget_Contact extends Widget_Base {

	/**
	 * Widget name.
	 *
	 * @var string
	 */
	public $widget_name = 'wptravelengine-contact';

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
		return __( 'Contact', 'wptravelengine-elementor-widgets' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'icon-wte-contact';
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
			'header_info',
			array(
				'label' => __( 'Contact Settings', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'contact_label',
			array(
				'label'       => __( 'Title', 'wptravelengine-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Email', 'wptravelengine-elementor-widgets' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'contact_icon',
			array(
				'label'       => __( 'Icon', 'wptravelengine-elementor-widgets' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'default'     => array(
					'value'   => 'fas fa-envelope',
					'library' => 'fa-solid',
				),
			)
		);

		$repeater->add_control(
			'contact_text',
			array(
				'label'       => __( 'Text', 'wptravelengine-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'user@example.com', 'wptravelengine-elementor-widgets' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'contact_url',
			array(
				'label'       => __( 'Link', 'wptravelengine-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'mailto:user@example.com', 'wptravelengine-elementor-widgets' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'contact_list',
			array(
				'label'       => __( 'Contact list', 'wptravelengine-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'contact_label' => __( 'Email', 'wptravelengine-elementor-widgets' ),
						'contact_icon'  => array(
							'value'   => 'fas fa-envelope',
							'library' => 'fa-solid',
						),
						'contact_text'  => __( 'user@example.com', 'wptravelengine-elementor-widgets' ),
						'contact_url'   => __( 'mailto:user@example.com', 'wptravelengine-elementor-widgets' ),
					),
				),
				'title_field' => '{{{ contact_label }}}',
			)
		);

		$this->add_control(
			'contact_direction',
			array(
				'label'   => __( 'Contact Direction', 'wptravelengine-elementor-widgets' ),
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
			'content_direction',
			array(
				'label'   => __( 'Content Direction', 'wptravelengine-elementor-widgets' ),
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

		$this->end_controls_section();

		$this->start_controls_section(
			'contact_style',
			array(
				'label' => __( 'Style', 'wptravelengine-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// Title Styling Controls
		$this->add_control(
			'title_styling_heading',
			array(
				'label' => __( 'Title Styling', 'wptravelengine-elementor-widgets' ),
				'type'  => Controls_Manager::HEADING,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .wte-header-contact-item .wte-header-contact-label',
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-header-contact-item' => '--label-color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wte-header-contact-item' => '--label-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->add_control(
			'contact_styling_heading',
			array(
				'label' => __( 'Contact Styling', 'wptravelengine-elementor-widgets' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'textypography',
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .wte-header-contact',
			)
		);

		$this->add_control(
			'item_spacing',
			array(
				'label'      => esc_html__( 'Item Spacing', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-header-contact ' => '--item-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'contact_style_tabs'
		);

		// Normal Tab
		$this->start_controls_tab(
			'contact_style_normal',
			array(
				'label' => __( 'Normal', 'wptravelengine-elementor-widgets' ),
			)
		);

		$this->add_control(
			'contact_color',
			array(
				'label'     => __( 'Text Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-header-contact' => '--text-color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_tab();

		// Hover Tab
		$this->start_controls_tab(
			'contact_style_hover',
			array(
				'label' => __( 'Hover', 'wptravelengine-elementor-widgets' ),
			)
		);

		$this->add_control(
			'contact_hover_color',
			array(
				'label'     => __( 'Text Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  .wte-header-contact' => '--text-color-hover: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'icon_styling_heading',
			array(
				'label'     => __( 'Icon Styling', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wte-header-contact' => '--icon-color: {{VALUE}} !important',
				),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-header-contact' => '--icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'icon_spacing',
			array(
				'label'      => esc_html__( 'Icon Spacing', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-header-contact' => '--icon-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	protected function render() {

		$settings      = $this->get_settings_for_display();
		$infolink_list = $settings['contact_list'];
		$custom_class  = ! empty( $settings['contact_icon']['value'] ) ? ' custom-contact-icon' : '';
		$this->add_render_attribute(
			'wte-header-contact',
			'class',
			array(
				'wte-header-contact',
				$custom_class,
				esc_attr( $settings['contact_direction'] ),
			)
		);
		?>
			<?php if ( ! empty( $infolink_list ) ) : ?>
				<div <?php $this->print_render_attribute_string( 'wte-header-contact' ); ?>>
					<?php foreach ( $infolink_list as $info ) : ?>
					<div class="wte-header-contact-item">
						<?php
						if ( isset( $info['contact_icon']['value'] ) && ! is_array( $info['contact_icon']['value'] ) ) :
							?>
							<i class="<?php echo esc_attr( $info['contact_icon']['value'] ); ?>"></i>
							<?php
						elseif ( is_array( $info['contact_icon']['value'] ) && ! empty( $info['contact_icon']['value']['url'] ) ) :
							Icons_Manager::render_icon( $info['contact_icon'], array( 'aria-hidden' => 'true' ) );
						endif;
						echo '<div class="wte-header-contact-content ' . esc_attr( $settings['content_direction'] ) . '">';
						if ( ! empty( $info['contact_label'] ) ) {
							echo '<span class="wte-header-contact-label">' . esc_html( $info['contact_label'] ) . '</span> ';
						}
						if ( ! empty( $info['contact_text'] ) && ! empty( $info['contact_url'] ) ) {
							echo '<a href="' . esc_url( $info['contact_url'] ) . '">' . esc_html( $info['contact_text'] ) . '</a>';
						}
						echo '</div>';
						?>
					</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		<?php
	}
}
