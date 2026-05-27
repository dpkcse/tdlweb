<?php
/**
 * Trip Cost Includes Widget.
 *
 * @since 1.3.0
 * @package wptravelengine-elementor-widgets
 */

use Elementor\Icons_Manager;

$attributes = (object) $attributes;
$icon       = isset( $attributes->{'icon'} ) ? $attributes->{'icon'} : '';

global $post;

$post_meta             = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
$cost_includes_content = isset( $post_meta['cost']['cost_includes'] ) ? trim( $post_meta['cost']['cost_includes'] ) : '';
$cost_includes         = ! empty( $cost_includes_content ) ? preg_split( '/\r\n|[\r\n]/', $cost_includes_content ) : array();
?>

<div id="wte-costincludes" class="post-data cost">
	<div class="content">
		<?php if ( ! empty( $cost_includes ) ) : ?>
			<ul <?php echo ( empty( $icon ) || empty( $icon['value'] ) ) ? 'id="include-result"' : 'class="custom-icon"'; ?>>
				<?php
				foreach ( $cost_includes as $include ) :
					if ( ! empty( trim( $include ) ) ) :
						?>
						<li class="cost-include-item">
							<?php
							if ( ! empty( $icon ) && isset( $icon['value'] ) && ! empty( $icon['value'] ) ) :
								if ( ! is_array( $icon['value'] ) ) :
									?>
									<i class="<?php echo esc_attr( $icon['value'] ); ?>"></i>
									<?php
								else :
									Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
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
		<?php endif; ?>
	</div>
</div>
