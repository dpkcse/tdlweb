<?php
/**
 * Fixed Starting Date Widget Template Demo.
 *
 * @since 1.3.8
 * @package wptravelengine-elementor-widgets
 */

// Get the attributes from the widget.
$show_trip_title         = isset( $attributes['show_trip_title'] ) ? $attributes['show_trip_title'] : 'yes';
$show_start_date         = isset( $attributes['show_start_date'] ) ? $attributes['show_start_date'] : 'yes';
$show_end_date           = isset( $attributes['show_end_date'] ) ? $attributes['show_end_date'] : 'yes';
$show_price_column       = isset( $attributes['show_price_column'] ) ? $attributes['show_price_column'] : 'yes';
$show_space_left_column  = isset( $attributes['show_space_left_column'] ) ? $attributes['show_space_left_column'] : 'yes';
$time_slots_label        = isset( $attributes['time_slots_label'] ) ? $attributes['time_slots_label'] : '';
$group_discount_label    = isset( $attributes['group_discount_label'] ) ? $attributes['group_discount_label'] : '';
$book_now_btn_txt        = isset( $attributes['book_now_btn_txt'] ) ? $attributes['book_now_btn_txt'] : '';
$sold_out_btn_txt        = isset( $attributes['sold_out_btn_txt'] ) ? $attributes['sold_out_btn_txt'] : '';
$show_more_btn_txt       = isset( $attributes['show_more_btn_txt'] ) ? $attributes['show_more_btn_txt'] : '';
$show_less_btn_txt       = isset( $attributes['show_less_btn_txt'] ) ? $attributes['show_less_btn_txt'] : '';
$date_format             = isset( $attributes['date_format'] ) ? $attributes['date_format'] : '';
$days_format             = isset( $attributes['days_format'] ) ? $attributes['days_format'] : '';
$fsd_not_available_label = isset( $attributes['not_available_text'] ) ? $attributes['not_available_text'] : '';

// Dummy Data For Demo
$fsd_entries = array(
	array(
		'time_slots_label'     => $time_slots_label,
		'group_discount_label' => $group_discount_label,
		'start_date'           => '2025-01-01',
		'end_date'             => '2025-01-05',
		'regular_price'        => '$500',
		'sale_price'           => '$200',
		'discount'             => '60% OFF',
		'seats_left'           => 5,
		'book_now_btn_txt'     => $book_now_btn_txt,
	),
	array(
		'time_slots_label'     => $time_slots_label,
		'group_discount_label' => $group_discount_label,
		'start_date'           => '2025-01-06',
		'end_date'             => '2025-01-10',
		'regular_price'        => '$500',
		'sale_price'           => '$200',
		'discount'             => '60% OFF',
		'seats_left'           => 8,
		'book_now_btn_txt'     => $book_now_btn_txt,
	),
	array(
		'time_slots_label'     => $time_slots_label,
		'group_discount_label' => $group_discount_label,
		'start_date'           => '2025-02-01',
		'end_date'             => '2025-02-05',
		'regular_price'        => '$500',
		'sale_price'           => '$200',
		'discount'             => '60% OFF',
		'seats_left'           => 0,
		'sold_out_btn_txt'     => $sold_out_btn_txt,
	),
);
?>

<div class="wte-fsd__container">
	<!-- Filter for desktop -->
	<div class="wte-fsd__filter-buttons">
		<button type="button" class="wte-fsd__button is-active">All</button>
		<button type="button" class="wte-fsd__button">Jan 2025</button>
		<button type="button" class="wte-fsd__button">Feb 2025</button>
	</div>

	<!-- Filter for mobile -->
		<div data-tab-of="group" class="wte-fsd__filter-select">
			<label for="select-date"><?php esc_html_e( 'Select Date', 'wptravelengine-elementor-widgets' ); ?></label>
			<select class="wte-fsd__date-select" name="date-select">
				<option value=" "><?php esc_html_e( 'Choose a date&hellip;', 'wptravelengine-elementor-widgets' ); ?></option>
				<option value="2025-01-01">January, 2025</option>
				<option value="2025-02-01">February, 2025</option>
			</select>
		</div>

	<div class="wte-fsd__availability-list-wrap">
		<div class="wte-fsd__availability-list-wrapper wte-fsd__availability-from-shortcode">
			<?php if ( $show_trip_title ) : ?>
				<div class="wte-fsd__availability-trip-name">
					<a href="#"><?php echo $post->post_title; ?></a>
				</div>
			<?php endif; ?>
			<ul class="wte-fsd__availability-list">
				<?php
				foreach ( $fsd_entries as $entry ) {
					?>
					<li class="wte-fsd__availability">
						<div class="wte-fsd__tag-wrap">
							<span class="wte-fsd__tag wte-fsd__time-slots"><?php echo $entry['time_slots_label']; ?></span>
							<span class="wte-fsd__tag wte-fsd__group-discount"><?php echo $entry['group_discount_label']; ?></span>
							<span class="wte-fsd__tag wte-fsd__availability-label">Guaranteed</span>
						</div>
						<?php if ( $show_start_date ) : ?>
							<div class="wte-fsd__availability-start-date">
								<span class="wte-fsd__availability-title-text"><?php echo date( $days_format, strtotime( $entry['start_date'] ) ); ?></span>
								<div class="wte-fsd__availability-bold-text"><?php echo date( $date_format, strtotime( $entry['start_date'] ) ); ?></div>
							</div>
							<?php
						endif;
						if ( $show_end_date && $show_start_date ) :
							?>
							<div class="wte-fsd__availability-arrow-wrap">
								<span class="wte-fsd__availability-arrow"></span>
							</div>
							<?php
						endif;
						if ( $show_end_date ) :
							?>
							<div class="wte-fsd__availability-end-date">
								<span class="wte-fsd__availability-title-text"><?php echo date( $days_format, strtotime( $entry['end_date'] ) ); ?></span>
								<div class="wte-fsd__availability-bold-text"><?php echo date( $date_format, strtotime( $entry['end_date'] ) ); ?></div>
							</div>
							<?php
						endif;
						if ( $show_price_column ) :
							?>
							<div class="wte-fsd__availability-price-wrap">
								<div class="wte-fsd__availability-price-inner">
									<span class="wte-fsd__availability-title-text wte-fsd__availability-regular-price"><?php echo $entry['regular_price']; ?></span>
								<div class="wte-fsd__availability-sale-price-wrap">
									<div class="wte-fsd__availability-bold-text"><?php echo $entry['sale_price']; ?></div>
										<span class="wte-fsd__availability-discount"><?php echo $entry['discount']; ?></span>
									</div>
								</div>
							</div>
							<?php
						endif;
						if ( $show_space_left_column ) :
							?>
							<div class="wte-fsd__availability-remaining-seats <?php echo ( $entry['seats_left'] <= 0 ) ? 'wte-fsd__sold-out' : ''; ?>">
								<?php if ( $entry['seats_left'] > 0 ) : ?>
									<div class="wte-fsd__availability-seats-count"><?php echo $entry['seats_left']; ?></div>
									<span class="wte-fsd__availability-title-text">Available</span>
								<?php else : ?>
									<span class="wte-fsd__availability-title-text">Not Available</span>
								<?php endif; ?>
							</div>
							<?php
						endif;
						if ( isset( $entry['book_now_btn_txt'] ) && $entry['book_now_btn_txt'] ) :
							?>
							<div class="wte-fsd__availability-cta-wrap">
								<button class="book-btn wte-fsd__booknow-btn"><?php echo $entry['book_now_btn_txt']; ?></button>
							</div>
							<?php
						endif;
						if ( isset( $entry['sold_out_btn_txt'] ) && $entry['sold_out_btn_txt'] ) :
							?>
							<div class="wte-fsd__availability-cta-wrap">
								<span class="wte-fsd__sold-out"><?php echo $entry['sold_out_btn_txt']; ?></span>
							</div>
						<?php endif; ?>
					</li>       
				<?php } ?>
			</ul>
		</div>
		<div class="wte-fsd__availability-show-more-wrap">
			<button class="wte-fsd__availability-show-more"><?php echo $show_more_btn_txt; ?></button>
			<button class="wte-fsd__availability-show-less" style="display: flex;"> <?php echo $show_less_btn_txt; ?> </button>
		</div>
	</div>
</div>
