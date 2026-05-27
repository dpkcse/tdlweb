<?php
/**
 * Email Footer
 *
 * @since 6.5.0
 * @since 6.7.8 Removed logo from email footer.
 */
use WPTravelEngine\Core\Models\Settings\Options;

$settings = Options::get( 'wp_travel_engine_settings' );
$footer   = $settings['email']['footer'] ?? '';
?>
</td>
	</tr>
		</tbody>
		<tfoot>
			<tr>
				<td style="text-align: center;padding: 24px 0;">
					<p style="margin: 0;"><em><?php echo esc_html( $footer ); ?></em></p>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
