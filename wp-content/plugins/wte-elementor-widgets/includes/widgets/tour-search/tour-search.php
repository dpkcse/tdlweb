<?php
namespace WPTRAVELENGINEEB;

use WPTRAVELENGINEEB\Widget;

/**
 * Trip Search Widget.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Widgets.
 *
 * @since 1.4.1
 */
class Widget_Tour_Search extends Widget {

	/**
	 *
	 * @var $widget_name
	 */
	public $widget_name = 'wptravelengine-tour-search';

	/**
	 * Widget keywords.
	 *
	 * @since 1.4.1
	 *
	 * @var array
	 */
	protected $keywords = array( 'search', 'wp travel engine', 'wte', 'tour-search' );

	/**
	 * Style dependencies.
	 */
	public function get_style_depends() {
		wp_register_style( 'wpte-tour-search', plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/css/wpte-tour-search.css' );

		return array( 'wpte-tour-search' );
	}

	/**
	 * Widget categories.
	 */
	public function get_categories() {
		return array( 'wptravelengine' );
	}
	/**
	 * Widget Settings.
	 */
	protected function register_controls() {
		wp_enqueue_style( 'wte-fonts-style' );
		$settings = Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name, 'controls' );
		$controls = isset( $settings['controls'] ) && is_array( $settings['controls'] ) ? $settings['controls'] : array();
		$this->_wte_add_controls( $settings );

		$controls = include WPTRAVELENGINEEB_PATH . 'includes/widgets/tour-search/controls.php';

		$this->_wte_add_controls( $controls );
	}

	protected function render() {
		$attributes   = $this->get_settings_for_display();
		$button_label = isset( $attributes['searchBtnLabel'] ) ? $attributes['searchBtnLabel'] : esc_html__( 'View All Posts', 'wptravelengine-elementor-widgets' );

		if ( isset( $attributes['listby'] ) ) {
			if ( 'byids' !== $attributes['listby'] ) {

				$items = get_terms(
					array(
						'taxonomy'  => $attributes['selectTax'],
						'childless' => true,
						'number'    => isset( $attributes['itemsCount'] ) ? $attributes['itemsCount'] : 4,
						'fields'    => 'ids',
					)
				);

				if ( is_array( $items ) ) {
					$attributes['listItems'] = $items;
				} else {
					$attributes['listItems'] = array();
				}
			} else {
				$tax_selected = $attributes['selectTax'] === 'destination' ? $attributes['listItemsDestination'] : ( $attributes['selectTax'] === 'trip_types' ? $attributes['listItemsTripTypes'] : $attributes['listItemsActivities'] );

				if ( isset( $tax_selected ) && is_array( $tax_selected ) ) {
					$attributes['listItems'] = $tax_selected;
				} else {
					$attributes['listItems'] = array();
				}
			}
		}

		$results = array();

		if ( ! empty( $attributes['listItems'] ) ) {
			$results = wte_get_terms_by_id(
				$attributes['selectTax'],
				array(
					'taxonomy'   => $attributes['selectTax'],
					'number'     => 300,
					'hide_empty' => true,
				)
			);

			if ( ! is_array( $results ) ) {
				return;
			}
		}?>
		<div class="wpte-widget">
			<div class="wpte-tour-search">
				<?php
				echo '<div class="form-section">';
				?>
					<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search Trips...', 'placeholder', 'wptravelengine-elementor-widgets' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
						<input type="hidden" name="post_type" value="trip" />
						<button type="submit" class="search-submit">
							<span class="screen-reader-text"><?php echo _x( 'Search', 'submit button', 'wptravelengine-elementor-widgets' ); ?></span>
							<i class="fas fa-search"></i>
						</button>
					</form>
					<?php
					echo '</div>';
					if ( $attributes['showCategory'] && is_array( $results ) ) {
						echo '<div class="search-keyword">';
						foreach ( $attributes['listItems'] as $term_id ) {
							if ( ! isset( $results[ $term_id ] ) ) {
								continue;
							}

							if ( $results[ $term_id ] ) {
								echo '<a class="cat-title" href="' . esc_url( $results[ $term_id ]->link ) . '">' . esc_html( $results[ $term_id ]->name ) . '</a>';
							}
						}
						echo '</div>';
					}

					if ( ! empty( $attributes['searchBtnLink'] ) && ! empty( $attributes['searchBtnLink']['url'] ) ) {
						$target = $attributes['searchBtnLink']['is_external'] ? ' target=_blank' : '';
						?>
						<a class="wpte-tour-search__btn" href="<?php echo esc_url( $attributes['searchBtnLink']['url'] ); ?>" <?php echo esc_attr( $target ); ?>>
								<?php echo esc_html( $button_label ); ?>
								<?php if ( ! empty( $attributes['search_btn_arrow'] ) && is_array( $attributes['search_btn_arrow'] ) && ! empty( $attributes['search_btn_arrow']['value'] ) && ! is_array( $attributes['search_btn_arrow']['value'] ) ) : ?>
								<i class="<?php echo esc_attr( $attributes['search_btn_arrow']['value'] ); ?>"></i>
									<?php
								elseif ( is_array( $attributes['search_btn_arrow']['value'] ) && ! empty( $attributes['search_btn_arrow']['value']['url'] ) ) :
									\Elementor\Icons_Manager::render_icon( $attributes['search_btn_arrow'] );
								endif;
								?>
						</a>
					<?php } ?>
			</div>
		</div>
		<?php
	}
}
