<?php
/**
 * Trip Cost Widget.
 *
 * @since 1.3.9
 * @package wptravelengine-elementor-widgets
 */

use Elementor\Icons_Manager;

global $post;
$attributes     = (object) $attributes;
$trip_settings  = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
$tab_title      = ! empty( $trip_settings['cost_tab_sec_title'] ) ? $trip_settings['cost_tab_sec_title'] : '';
$includes_title = ! empty( $trip_settings['cost']['includes_title'] ) ? $trip_settings['cost']['includes_title'] : '';
$include_icon   = ! empty( $attributes->{'include_icon'}['value'] ) ? 'has-custom-icon' : '';
$excludes_title = ! empty( $trip_settings['cost']['excludes_title'] ) ? $trip_settings['cost']['excludes_title'] : '';
$exclude_icon   = ! empty( $attributes->{'exclude_icon'}['value'] ) ? 'has-custom-icon' : '';

$show_title = isset( $attributes->{'show_title'} ) ? $attributes->{'show_title'} : 'yes';
$html_tag   = wptravelengineeb_normalize_html_tag( $attributes->{'html_tag'} ?? 'h3' );

$post_meta = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );

// Get and sanitize cost includes
$cost_includes_content = isset( $post_meta['cost']['cost_includes'] ) ? trim( $post_meta['cost']['cost_includes'] ) : '';
$cost_includes         = ! empty( $cost_includes_content ) ? preg_split( '/\r\n|[\r\n]/', $cost_includes_content ) : array();

// Get and sanitize cost excludes
$cost_excludes_content = isset( $post_meta['cost']['cost_excludes'] ) ? trim( $post_meta['cost']['cost_excludes'] ) : '';
$cost_excludes         = ! empty( $cost_excludes_content ) ? preg_split( '/\r\n|[\r\n]/', $cost_excludes_content ) : array();
?>

<div id="wte-cost" class="post-data cost">
	<?php if ( $show_title === 'yes' && $tab_title ) : ?>
		<<?php echo esc_html( $html_tag ); ?> class="wpte-cost-tab-title"><?php echo esc_html( $tab_title ); ?></<?php echo esc_html( $html_tag ); ?>>
	<?php endif; ?>

	<div class="content">
		<?php if ( ! empty( $cost_includes ) && isset( $attributes->{'showCostInclude'} ) && 'yes' === $attributes->{'showCostInclude'} ) : ?>
			<div class="cost-includes <?php echo esc_attr( $include_icon ); ?>">
				<h3><?php echo esc_html( $includes_title ); ?></h3>
				<ul id="include-result">
					<?php
					foreach ( $cost_includes as $include ) :
						if ( ! empty( trim( $include ) ) ) :
							?>
							<li class="cost-include-item">
								<?php
								if ( ! empty( $attributes->{'include_icon'}['value'] ) ) :
									if ( ! is_array( $attributes->{'include_icon'}['value'] ) ) :
										?>
										<i class="<?php echo esc_attr( $attributes->{'include_icon'}['value'] ); ?>"></i>
										<?php
									else :
										Icons_Manager::render_icon( $attributes->{'include_icon'}, array( 'aria-hidden' => 'true' ) );
									endif;
								endif;
								?>
								<span><?php echo esc_html( trim( $include ) ); ?></span>
							</li>
							<?php
						endif;
					endforeach;
					?>
				</ul>
			</div>
		<?php endif; ?>
	</div>

	<div class="content">
		<?php if ( ! empty( $cost_excludes ) && isset( $attributes->{'showCostExclude'} ) && 'yes' === $attributes->{'showCostExclude'} ) : ?>
			<div class="cost-excludes <?php echo esc_attr( $exclude_icon ); ?>">
				<h3><?php echo esc_html( $excludes_title ); ?></h3>
				<ul id="exclude-result">
					<?php
					foreach ( $cost_excludes as $exclude ) :
						if ( ! empty( trim( $exclude ) ) ) :
							?>
							<li class="cost-exclude-item">
								<?php
								if ( ! empty( $attributes->{'exclude_icon'}['value'] ) ) :
									if ( ! is_array( $attributes->{'exclude_icon'}['value'] ) ) :
										?>
										<i class="<?php echo esc_attr( $attributes->{'exclude_icon'}['value'] ); ?>"></i>
										<?php
									else :
										Icons_Manager::render_icon( $attributes->{'exclude_icon'}, array( 'aria-hidden' => 'true' ) );
									endif;
								endif;
								?>
								<span><?php echo esc_html( trim( $exclude ) ); ?></span>
							</li>
							<?php
						endif;
					endforeach;
					?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</div>
