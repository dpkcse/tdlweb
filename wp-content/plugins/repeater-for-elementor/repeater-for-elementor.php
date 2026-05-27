<?php

/**
 * Plugin Name:Repeater Fields for Elementor Forms
 * Plugin URI: https://add-ons.org/plugin/elementor-forms-repeater-fields/
 * Requires Plugins: elementor
 * Description: The add-on that allows specified groups of fields to be repeated by the user.
 * Version: 2.2.7
 * Author: add-ons.org
 * Elementor tested up to: 3.36
 * Elementor Pro tested up to: 3.36
 * Author URI: https://add-ons.org/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (! defined('ABSPATH')) exit; // Exit if accessed directly
if (!defined('ELEMENTOR_REPEATER_PLUGIN_PATH')) {
    define('ELEMENTOR_REPEATER_PLUGIN_PATH', plugin_dir_path(__FILE__));
    define('ELEMENTOR_REPEATER_PLUGIN_URL', plugin_dir_url(__FILE__));
    require_once(ELEMENTOR_REPEATER_PLUGIN_PATH . "fields/frontend.php");
    add_action('elementor_pro/forms/fields/register', 'yeekit_el_add_new_repeater_field');
    function yeekit_el_add_new_repeater_field($form_fields_registrar)
    {
        require_once(ELEMENTOR_REPEATER_PLUGIN_PATH . "fields/repeater_start.php");
        require_once(ELEMENTOR_REPEATER_PLUGIN_PATH . "fields/repeater_end.php");
        $form_fields_registrar->register(new \Yeekit_El_Repeater_Start_Field());
        $form_fields_registrar->register(new \Yeekit_El_Repeater_Field());
    }
    add_action('elementor_pro/forms/actions/register', 'yeekit_el_register_new_form_actions_yeekit_email3');
    function yeekit_el_register_new_form_actions_yeekit_email3($form_actions_registrar)
    {
        include ELEMENTOR_REPEATER_PLUGIN_PATH . 'fields/email3.php';
        $form_actions_registrar->register(new \Yeekit_El_Email3());
    }
}
