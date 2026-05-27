<?php
/**
 * Trip Cost Excludes Widget Demo.
 *
 * @since 1.3.0
 * @package wptravelengine-elementor-widgets
 */

use Elementor\Icons_Manager;

$attributes    = (object) $attributes;
$icon          = isset( $attributes->{'icon'} ) ? $attributes->{'icon'} : '';
$cost_excludes = array(
	'International flights',
	'Travel insurance',
	'Nepal entry visa',
	'Personal expenses',
);
?>

<div id="wte-costexcludes" class="post-data cost">
	<div class="content">
		<?php if ( ! empty( $cost_excludes ) ) : ?>
			<ul <?php echo ( empty( $icon ) || empty( $icon['value'] ) ) ? 'id="exclude-result"' : 'class="custom-icon"'; ?>>
				<?php
				foreach ( $cost_excludes as $exclude ) :
					if ( ! empty( trim( $exclude ) ) ) :
						?>
						<li class="cost-exclude-item">
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
							<span><?php echo esc_html( trim( $exclude ) ); ?></span>
						</li>
						<?php
					endif;
				endforeach;
				?>
			</ul>
		<?php endif; ?>
	</div>
</div>
