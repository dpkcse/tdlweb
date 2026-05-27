<?php
/**
 * Trip Cost Widget Demo.
 *
 * @since 1.3.9
 * @package wptravelengine-elementor-widgets
 */

use Elementor\Icons_Manager;

$attributes   = (object) $attributes;
$include_icon = isset( $attributes->{'include_icon'} ) ? $attributes->{'include_icon'} : '';
$exclude_icon = isset( $attributes->{'exclude_icon'} ) ? $attributes->{'exclude_icon'} : '';

// Cost excludes items
$cost_excludes_content = array(
	'International flights',
	'Travel insurance',
	'Nepal entry visa',
	'Personal expenses',
);

// Cost includes items
$cost_includes_content = array(
	'Airport transfers.',
	'Accommodation.',
	'Meals.',
	'Internal flights',
);
?>

<div id="wte-cost" class="post-data cost">
	<div class="content">
		<?php if ( ! empty( $cost_includes_content ) && isset( $attributes->{'showCostInclude'} ) && 'yes' === $attributes->{'showCostInclude'} ) : ?>
			<div class="cost-includes <?php echo ! empty( $include_icon['value'] ) ? 'has-custom-icon' : ''; ?>">
				<h3><?php esc_html_e( 'Cost Includes', 'wptravelengine-elementor-widgets' ); ?></h3>
				<ul id="include-result">
					<?php
					foreach ( $cost_includes_content as $include ) :
						if ( ! empty( trim( $include ) ) ) :
							?>
							<li class="cost-include-item">
								<?php
								if ( ! empty( $include_icon ) && isset( $include_icon['value'] ) && ! empty( $include_icon['value'] ) ) :
									if ( ! is_array( $include_icon['value'] ) ) :
										?>
										<i class="<?php echo esc_attr( $include_icon['value'] ); ?>"></i>
										<?php
									else :
										Icons_Manager::render_icon( $include_icon, array( 'aria-hidden' => 'true' ) );
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

		<?php if ( ! empty( $cost_excludes_content ) && isset( $attributes->{'showCostExclude'} ) && 'yes' === $attributes->{'showCostExclude'} ) : ?>
			<div class="cost-excludes <?php echo ! empty( $exclude_icon['value'] ) ? 'has-custom-icon' : ''; ?>">
				<h3><?php esc_html_e( 'Cost Excludes', 'wptravelengine-elementor-widgets' ); ?></h3>
				<ul id="exclude-result">
					<?php
					foreach ( $cost_excludes_content as $exclude ) :
						if ( ! empty( trim( $exclude ) ) ) :
							?>
							<li class="cost-exclude-item">
								<?php
								if ( ! empty( $exclude_icon ) && isset( $exclude_icon['value'] ) && ! empty( $exclude_icon['value'] ) ) :
									if ( ! is_array( $exclude_icon['value'] ) ) :
										?>
										<i class="<?php echo esc_attr( $exclude_icon['value'] ); ?>"></i>
										<?php
									else :
										Icons_Manager::render_icon( $exclude_icon, array( 'aria-hidden' => 'true' ) );
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
