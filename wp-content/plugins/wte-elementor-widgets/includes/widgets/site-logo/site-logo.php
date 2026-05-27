<?php
namespace WPTRAVELENGINEEB;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;

/**
 * Site Logo Widget.
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class Widget_Site_Logo.
 *
 * @since 1.4.7
 */
class Widget_Site_Logo extends Widget_Base {

	/**
	 * Widget name.
	 *
	 * @var string
	 */
	public $widget_name = 'wptravelengine-site-logo';

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
		return __( 'Site Logo', 'wptravelengine-elementor-widgets' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'icon-wte-logo';
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
			'section_layout',
			array(
				'label' => __( 'Layout', 'wptravelengine-elementor-widgets' ),
			)
		);

		$this->add_control(
			'logo_type',
			array(
				'label'   => __( 'Logo Type', 'wptravelengine-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'wptravelengine-elementor-widgets' ),
					'custom'  => __( 'Custom', 'wptravelengine-elementor-widgets' ),
				),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'     => __( 'Choose logo', 'wptravelengine-elementor-widgets' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => '',
				),
				'condition' => array(
					'logo_type' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'logo_size',
				'include'   => array(),
				'default'   => 'thumbnail',
				'condition' => array(
					'logo_type' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => __( 'Image Width', 'wptravelengine-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 250,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wte-site-logo.custom' => '--logo-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'logo_type' => 'custom',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'wrapper',
			'class',
			array(
				'wte-site-logo',
				esc_attr( $settings['logo_type'] ),
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( 'custom' === $settings['logo_type'] ) : ?>
				<?php
				// Check if transparent header is enabled and transparent logo exists
				$ed_transparent_header = get_theme_mod( 'ed_transparent_header', false );
				$transparent_logo      = get_theme_mod( 'transparent_logo_upload', '' );

				// Use transparent logo if transparent header is enabled and transparent logo is set
				if ( $ed_transparent_header && ! empty( $transparent_logo ) ) :
					?>
					<a class="site-branding" href="<?php echo esc_url( home_url() ); ?>">
						<img src="<?php echo esc_url( $transparent_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="transparent-logo">
					</a>
				<?php elseif ( ! empty( $settings['image']['url'] ) ) : ?>
					<a class="site-branding" href="<?php echo esc_url( home_url() ); ?>">
						<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'logo_size', 'image' ); ?>
					</a>
				<?php endif; ?>
			<?php elseif ( function_exists( 'travel_monster_site_branding' ) ) : ?>
				<?php travel_monster_site_branding(); ?>
			<?php else : ?>
				<?php
				// Fallback: Default logo from customizer
				$custom_logo_id = get_theme_mod( 'custom_logo' );
				if ( $custom_logo_id ) :
					?>
					<a class="site-branding" href="<?php echo esc_url( home_url() ); ?>">
						<?php echo wp_get_attachment_image( $custom_logo_id, 'full', false, array( 'class' => 'custom-logo' ) ); ?>
					</a>
				<?php else : ?>
					<a class="site-branding site-title" href="<?php echo esc_url( home_url() ); ?>">
						<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
					</a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
