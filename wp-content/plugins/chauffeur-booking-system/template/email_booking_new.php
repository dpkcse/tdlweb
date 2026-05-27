<?php
/**
 * Full email voucher template (editable)
 * Save as: email-voucher-template-full.php
 *
 * Notes:
 * - Provide $this->data like in your system (or adapt variables manually).
 * - Configure $config below (logo_url or embed settings).
 * - To embed logo as CID in WP, set 'embed_logo' => true and ensure the phpmailer_init
 *   hook is registered BEFORE wp_mail() runs (hook added below when embed_logo=true).
 */

/* --------------------------
   CONFIG / DEFAULTS (editable)
   -------------------------- */
$config = [
    // Option A (recommended): absolute HTTPS URL to logo (works for most clients)
    'logo_url' => CHBSOption::getOption('logo'),

    // Option B: embed logo as CID. If true, set 'logo_file_path' to server path.
    'embed_logo'   => false,
    'logo_file_path' => WP_CONTENT_DIR . '/uploads/TDL-LOGO-512x512.png', // example server path

    // Web voucher link - where user can view & print (recommended)
    'web_voucher_url' => 'https://tourdelisboa.com/voucher/?id=' . rawurlencode($this->data['booking']['ID'] ?? ''),

    // Editable content blocks
    'meeting_point' => 'After clearing customs, please turn right. We will be waiting for you at <strong>Meeting Point 4</strong>. Our assistants will wait for you there. They will help to call the driver and drop off at the car park. For Emergency: <strong>+351 910 910 122</strong><br>If you can’t find your driver, call the number on your voucher before leaving the airport; otherwise, it will be considered a no-show with no refund.',
    'cancellation'   => 'Free cancellation up to 24 hours before pickup. Changes to pickup time or location must be requested at least 12 hours before the scheduled departure by emailing info@tourdelisboa.com or calling <strong>+351 910 910 122</strong>.',
    'disclaimer'     => 'Please note that flight departure times are not monitored for departure transfers. The driver\'s arrival will be based on the pickup time specified during booking. After the complimentary waiting time has expired, the reservation will be subject to additional waiting fees at the driver/provider\'s discretion. If waiting is not possible due to overcrowding, the driver will mark the reservation as a "no-show" and no refund will be issued. If the guest is carrying more luggage than the reserved vehicle\'s luggage capacity, the driver will mark the reservation as a "no-show" and no refund will be issued. Please verify the vehicle\'s luggage capacity before confirming the booking. If the driver cannot be located, please ensure you reach out to the supplier at the emergency number mentioned on the voucher before leaving the pick-up location. If the customer has left the airport without informing the supplier of the inability to locate the driver, no refund will be given for the unused service. No refund will be applicable in case an incorrect guest contact number is shared with us. In case child seats are required, please notify us before the service date to avoid any guest no shows. In some destinations child seats are mandatory and the driver can refuse the ride if prior information around needing a child seat is not shared with you. Please ensure to select a time that allows ample opportunity to arrive at the airport, complete all necessary checks, and board the aircraft. Please note that the driver\'s waiting time will commence from the flight arrival time. Post the free waiting time specified, additional waiting time is chargeable at the driver\'s discretion and is payable by the traveler. For bookings related to ports and train stations, the arrival and departure timings are not monitored by the supplier. The driver will strictly follow the pickup time specified during booking creation. Any amendment request of transfers within 24 hours of service is subject to availability and at supplier discretion. The refund won\'t be applicable in case of denial of these amendments.',
];

/* --------------------------
   Optional: embed logo via PHPMailer (WP)
   If embed_logo = true, attach the image so we can reference it as cid:tdl_logo_cid.
   Make sure this code runs (and registers the hook) BEFORE wp_mail() is called.
   -------------------------- */
if (!empty($config['embed_logo']) && !empty($config['logo_file_path']) && file_exists($config['logo_file_path'])) {
    add_action('phpmailer_init', function($phpmailer) use ($config) {
        try {
            $phpmailer->addEmbeddedImage($config['logo_file_path'], 'tdl_logo_cid', basename($config['logo_file_path']));
        } catch (\Exception $e) {
            // Fallback silently; URL logo will be used if present.
        }
    }, 10);
}

/* --------------------------
   DATA EXTRACTION & SANITIZE
   -------------------------- */
$booking    = $this->data['booking'] ?? [];
$meta       = $booking['meta'] ?? [];
$billing    = $booking['billing']['summary'] ?? [];

$Date        = $Date        ?? new CHBSDate();
$Validation  = $Validation  ?? new CHBSValidation();

$booking_title = $booking['booking_title'] ?? ($booking['ID'] ?? '—');
$pickup_date   = $Date->formatDateToDisplay($meta['pickup_date'] ?? '') ?: ($meta['pickup_date'] ?? '');
$pickup_time   = ($Validation->isNotEmpty($meta['pickup_time_range'] ?? '') ? ($meta['pickup_time_range']) : $Date->formatTimeToDisplay($meta['pickup_time'] ?? '')) ?: '';
$vehicle_name  = $meta['vehicle_name'] ?? '—';
$flight_number = $meta['flight_number'] ?? '—';
$bag_count     = $booking['vehicle_bag_count'] ?? '';
$pax_count     = $booking['vehicle_passenger_count'] ?? '';

$client_first = trim($meta['client_contact_detail_first_name'] ?? '');
$client_last  = trim($meta['client_contact_detail_last_name'] ?? '');
$client_name  = trim(($client_first . ' ' . $client_last));
$client_phone = $meta['client_contact_detail_phone_number'] ?? '';
$client_email = $meta['client_contact_detail_email_address'] ?? '';
$client_email_safe = function_exists('antispambot') && $client_email ? antispambot($client_email) : $client_email;

/* coords => from/to pins */
$coords = $meta['coordinate'] ?? [];
function chbs_plain_address($v) {
    $a = (class_exists('CHBSHelper') && method_exists('CHBSHelper', 'getAddress')) ? CHBSHelper::getAddress($v) : '';
    return $a ? trim($a) : '';
}
if (count($coords) > 0) {
    $from_v = $coords[0];
    $to_v   = $coords[count($coords)-1];
    $from_address = chbs_plain_address($from_v) ?: 'Pickup address not provided';
    $to_address   = chbs_plain_address($to_v) ?: 'Dropoff address not provided';
    $from_q = (isset($from_v['lat'],$from_v['lng'])) ? ((float)$from_v['lat'].','.(float)$from_v['lng']) : $from_address;
    $to_q   = (isset($to_v['lat'],$to_v['lng'])) ? ((float)$to_v['lat'].','.(float)$to_v['lng']) : $to_address;
    $from_pin = esc_url(add_query_arg(['q'=>$from_q], 'https://www.google.com/maps/'));
    $to_pin   = esc_url(add_query_arg(['q'=>$to_q],   'https://www.google.com/maps/'));
    // directions
    $dirs_url = '';
    if (count($coords) >= 2) {
        $origin      = $from_q;
        $destination = $to_q;
        $wps = [];
        if (count($coords) > 2) {
            for ($i=1; $i<count($coords)-1; $i++) {
                $v = $coords[$i];
                $addr = chbs_plain_address($v);
                $wps[] = (isset($v['lat'],$v['lng'])) ? ((float)$v['lat'].','.(float)$v['lng']) : $addr;
            }
        }
        $args = ['api'=>'1','origin'=>$origin,'destination'=>$destination];
        if (!empty($wps)) $args['waypoints'] = implode('|', $wps);
        $dirs_url = esc_url(add_query_arg($args, 'https://www.google.com/maps/dir/'));
    }
} else {
    $from_address = 'Pickup address not provided';
    $to_address   = 'Dropoff address not provided';
    $from_pin = $to_pin = '#';
    $dirs_url = '';
}

/* --------------------------
   Price logic
   -------------------------- */
$currency_id = $meta['currency_id'] ?? '';
$summary     = $billing;

$gross      = isset($summary['value_gross']) ? (float)$summary['value_gross'] : 0.0;
$net        = isset($summary['value_net'])   ? (float)$summary['value_net']   : null;
$tax_amount = isset($summary['value_tax'])   ? (float)$summary['value_tax']   : null;

if ($net === null && $tax_amount !== null) {
    $net = max(0, $gross - $tax_amount);
} elseif ($tax_amount === null && $net !== null) {
    $tax_amount = max(0, $gross - $net);
} elseif ($net === null && $tax_amount === null) {
    $net = $gross;
    $tax_amount = 0.0;
}
$tax_base = isset($summary['tax_base']) ? (float)$summary['tax_base'] : $gross;
$tax_rate = isset($summary['tax_rate']) ? (float)$summary['tax_rate'] : null;
if ($tax_rate === null && $tax_base > 0) {
    $tax_rate = $tax_amount > 0 ? ($tax_amount / $tax_base) * 100 : 0.0;
}

/* Safe price formatter (class/method may not exist in all contexts) */
$can_format_price = class_exists('CHBSPrice') && method_exists('CHBSPrice', 'format');
$fmt = function($amount) use ($currency_id, $can_format_price) {
    if ($can_format_price) {
        return CHBSPrice::format((float)$amount, $currency_id);
    }
    if (function_exists('number_format_i18n')) {
        return number_format_i18n((float)$amount, 2);
    }
    return number_format((float)$amount, 2, '.', ',');
};

$net_formatted        = $fmt($net);
$tax_base_formatted   = $fmt($tax_base);
$tax_amount_formatted = $fmt($tax_amount);
$gross_formatted      = $fmt($gross);

/* --------------------------
   final logo decision
   -------------------------- */
$logo_url = !empty($config['logo_url']) ? trim($config['logo_url']) : '';
// If relative, make absolute
if (!empty($logo_url) && !preg_match('#^https?://#i', $logo_url)) {
    $base = trailingslashit(site_url());
    $logo_url = esc_url($base . ltrim($logo_url, '/'));
} else {
    $logo_url = esc_url($logo_url);
}
$use_cid  = !empty($config['embed_logo']) && file_exists($config['logo_file_path'] ?? '');
$logo_src = $use_cid ? 'cid:tdl_logo_cid' : $logo_url;

/* --------------------------
   OUTPUT HTML (email-safe)
   -------------------------- */
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo esc_html__('Tour de Lisboa — Voucher', 'chauffeur-booking-system'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    @media only screen and (max-width:95%) {
      .container { width:100% !important; padding:12px !important; }
      .two-col { display:block !important; width:100% !important; }
      .logo img { width:96px !important; height:auto !important; }
    }
  </style>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f3f4f6; padding:24px 0;">
  <tr><td align="center">
    <table role="presentation" width="100%" class="container" style="width:100%; max-width:600px; background:#ffffff; border-radius:12px; overflow:hidden;">
      <tr>
        <td style="padding:18px;">
          <table role="presentation" width="100%">
            <tr>
              <td style="vertical-align:middle;">
                <table role="presentation">
                  <tr>
                    <td class="logo" style="padding-right:12px;">
                      <?php if ($logo_src): ?>
                        <img src="<?php echo esc_attr($logo_src); ?>" alt="Tour de Lisboa" width="112" height="auto" style="display:block; border-radius:12px; border:0;">
                      <?php else: ?>
                        <div style="width:112px; height:40px; border-radius:12px; background:#eef2f7; display:flex; align-items:center; justify-content:center; color:#6b7280;">Logo</div>
                      <?php endif; ?>
                    </td>
                    <td style="vertical-align:middle;">
                      <h1 style="margin:0; font-size:18px;">Tour de Lisboa — Transfer Services</h1>
                      <p style="margin:4px 0 0 0; color:#6b7280; font-size:13px;">Safe, on-time travel</p>
                    </td>
                  </tr>
                </table>
              </td>
              <td align="right" style="vertical-align:middle;">
                <div style="display:inline-block; background:#f8fafc; border:1px solid #eef2f7; padding:8px 12px; border-radius:8px; font-size:13px;">
                  Booking: <strong><?php echo esc_html($booking_title); ?></strong>
                </div>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td style="padding:0 18px 18px 18px;">
          <table role="presentation" width="100%">
            <tr>
              <td style="vertical-align:top; width:65%; padding-right:12px;">
                <table role="presentation" width="100%" style="background:#fbfdff; border:1px solid #eef6ff; padding:14px; border-radius:10px;">
                  <tr><td>
                    <h2 style="margin:0 0 8px 0; font-size:14px;">Itinerary Voucher</h2>
                    <p style="margin:6px 0;"><strong>Ride number:</strong> <?php echo esc_html($booking_title); ?></p>

                    <table role="presentation" width="100%">
                      <tr>
                        <td style="padding-right:12px;"><strong>Date:</strong> <?php echo esc_html($pickup_date); ?></td>
                        <td><strong>Time:</strong> <?php echo esc_html($pickup_time); ?></td>
                      </tr>
                      <tr>
                        <td style="padding-top:8px;"><strong>Vehicle class:</strong> <?php echo esc_html($vehicle_name); ?></td>
                        <td style="padding-top:8px;"><strong>Flight Number:</strong> <?php echo esc_html($flight_number); ?></td>
                      </tr>
                      <tr>
                        <td style="padding-top:8px;"><strong>Total Baggage:</strong> <?php echo esc_html($bag_count); ?></td>
                        <td style="padding-top:8px;"><strong>Total Pax:</strong> <?php echo esc_html($pax_count); ?></td>
                      </tr>
                    </table>

                    <div style="margin-top:12px;">
                      <p style="margin:6px 0;"><strong>From:</strong> <a href="<?php echo esc_url($from_pin); ?>" style="color:#0b6efd; text-decoration:none;" target="_blank"><?php echo esc_html($from_address); ?></a></p>
                      <p style="margin:6px 0;"><strong>To:</strong> <a href="<?php echo esc_url($to_pin); ?>" style="color:#0b6efd; text-decoration:none;" target="_blank"><?php echo esc_html($to_address); ?></a></p>
                      <?php if ($dirs_url): ?>
                        <p style="margin:6px 0;"><a href="<?php echo esc_url($dirs_url); ?>" style="color:#0b6efd; text-decoration:none;" target="_blank">Open directions in Google Maps</a></p>
                      <?php endif; ?>
                    </div>

                    <hr style="border:none; height:1px; background:#eef2f7; margin:12px 0;">

                    <?php if (empty($config['price_hide'])): ?>
                      <h3 style="margin:0 0 8px 0; font-size:13px;">Price &amp; Tax</h3>
                      <table role="presentation" width="100%">
                        <tr>
                          <td>
                            <div style="font-size:12px; color:#6b7280;">Fare</div>
                            <div style="font-size:16px; font-weight:700; margin-top:4px;"><?php echo esc_html($net_formatted); ?></div>
                            <?php if ($tax_amount > 0): ?>
                              <div style="font-size:12px; color:#6b7280; margin-top:6px;">
                                IVA <?php echo esc_html(number_format_i18n($tax_rate,2)); ?>% (Incidência: <?php echo esc_html($tax_base_formatted); ?>) — <?php echo esc_html($tax_amount_formatted); ?>
                              </div>
                            <?php endif; ?>
                          </td>
                          <td style="text-align:right;">
                            <div style="font-size:12px; color:#6b7280;">Invoice Total</div>
                            <div style="font-size:16px; font-weight:700; margin-top:4px;"><?php echo esc_html($gross_formatted); ?></div>
                          </td>
                        </tr>
                      </table>
                      <p style="font-size:10px; color:#6b7280; margin-top:10px;">Services subject to the reverse charge mechanism. VAT to be accounted for by the recipient as per Article 196 of Council Directive 2006/112/EC.</p>
                      <hr style="border:none; height:1px; background:#eef2f7; margin:12px 0;">
                    <?php endif; ?>

                    <table role="presentation"><tr>
                      <td>
                        <a href="<?php echo esc_url($config['web_voucher_url']); ?>" style="display:inline-block; padding:10px 14px; border-radius:10px; background:#0b6efd; color:#ffffff; text-decoration:none; font-weight:600;">View / Print Voucher</a>
                      </td>
                      <td style="padding-left:8px;">
                        <a href="mailto:info@tourdelisboa.com?subject=<?php echo rawurlencode('Booking '.$booking_title.' - Inquiry'); ?>" style="display:inline-block; padding:10px 14px; border-radius:10px; border:1px solid #dbeafe; color:#0b6efd; text-decoration:none; font-weight:600;">Email Support</a>
                      </td>
                    </tr></table>

                  </td></tr>
                </table>
              </td>

              <td style="vertical-align:top; width:35%; padding-left:12px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:10px;">
                  <tr><td>
                    <h3 style="margin:0 0 8px 0; font-size:14px;">Contact Details</h3>
                    <p style="margin:6px 0;"><strong>Phone:</strong> +351 910 910 122</p>
                    <p style="margin:6px 0;"><strong>Email:</strong> <a href="mailto:info@tourdelisboa.com" style="color:#0b6efd; text-decoration:none;">info@tourdelisboa.com</a></p>
                    <p style="margin:6px 0;"><strong>Web:</strong> <a href="https://www.tourdelisboa.com" target="_blank" rel="noopener" style="color:#0b6efd; text-decoration:none;">www.tourdelisboa.com</a></p>

                    <hr style="border:none; height:1px; background:#eef2f7; margin:12px 0;">

                    <p style="margin:6px 0; color:#6b7280;">Billing to</p>
                    <?php if ($client_name || $client_phone): ?>
                      <p style="margin:6px 0;"><strong><?php echo esc_html($client_name ?: '-'); ?></strong><br><?php echo esc_html($client_phone ?: ''); ?></p>
                    <?php endif; ?>
                    <?php if ($client_email): ?>
                      <p style="margin:6px 0;"><strong>Email:</strong> <a href="mailto:<?php echo esc_attr($client_email_safe); ?>" style="color:#0b6efd; text-decoration:none;"><?php echo esc_html($client_email_safe); ?></a></p>
                    <?php endif; ?>

                    <hr style="border:none; height:1px; background:#eef2f7; margin:12px 0;">

                    <p style="margin:6px 0;"><strong>Waiting time:</strong><br>60 min from landing (complimentary)</p>
                  </td></tr>
                </table>
              </td>

            </tr>
          </table>
        </td>
      </tr>

      <tr><td style="padding:0 18px 18px 18px;">
        <table role="presentation" width="100%">
          <tr><td style="font-size:13px; color:#6b7280;">
            <div><?php echo wp_kses_post($config['meeting_point']); ?></div>
            <div style="margin-top:10px;">
              <strong>Cancellation &amp; Changes</strong>
              <p style="margin:6px 0;"><?php echo esc_html($config['cancellation']); ?></p>
            </div>
            <div style="margin-top:10px;">
              <strong>Disclaimer</strong>
              <p style="margin:6px 0;"><?php echo esc_html($config['disclaimer']); ?></p>
            </div>
          </td></tr>
        </table>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>
