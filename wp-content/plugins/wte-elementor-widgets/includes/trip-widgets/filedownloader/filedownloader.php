<?php

// Get the shortcode from the settings
$attributes = (object) $attributes;
$shortcode  = isset( $attributes->{'shortcode'} ) ? $attributes->{'shortcode'} : '[trip_file_downloads]';

// Render the shortcode on the front-end
echo do_shortcode( $shortcode );
