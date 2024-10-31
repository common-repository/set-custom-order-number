<?php if (!defined('ABSPATH')) exit;
/*
	Plugin Name: Set Custom Order Number
	Plugin URI:
	Description: This plugin allows to set custom order numbers with Prefix and Suffix.
	Version: 1.0.2
	Author: SunArc
	Author URI: https://sunarctechnologies.com/
	Text Domain: set-custom-order-number
	License: GPL2
	This WordPress plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version. This WordPress plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License	along with this WordPress plugin. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

global $wpdb;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
} else {
    clearstatcache();
}

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
define('scon_sunarc_plugin_dir', dirname(__FILE__));
!defined('scon_sunarc_plugin_url') && define('scon_sunarc_plugin_url', plugins_url('/', __FILE__));

function scon_sunarc_plugin_activate()
{
    //Set default case when plugin activates.
    $option_name = 'scon_sunarc_enabled';
    $getOptionVal = get_option($option_name);
    update_option($option_name, $getOptionVal);
}
register_activation_hook(__FILE__, 'scon_sunarc_plugin_activate');

function scon_sunarc_deactivation()
{
    $option_name = 'scon_sunarc_enabled';
    $new_value = 'no';
    update_option($option_name, $new_value);
}
register_deactivation_hook(__FILE__, 'scon_sunarc_deactivation');

/*
 * Check if Woocommerce is installed.
*/
$scon_all_plugins = get_plugins();
$scon_active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if (array_key_exists('woocommerce/woocommerce.php', $scon_all_plugins) && in_array('woocommerce/woocommerce.php', $scon_active_plugins)) {
    $optionVal = get_option('scon_sunarc_enabled');

    if ($optionVal == 'yes') {
        add_filter( 'woocommerce_order_number', 'scon_sunarc_change_woocommerce_order_number' );
        function scon_sunarc_change_woocommerce_order_number( $order_id ) {
            $prefixValue = get_option('scon_order_number_prefix');
            $suffixValue = get_option('scon_order_number_suffix');
            $new_order_id = $prefixValue . $order_id . $suffixValue;
            return $new_order_id;
        }
    }
}

if (!class_exists('scon_sunarc_main_cls')) {
    class scon_sunarc_main_cls
    {
        const ALREADY_BOOTSTRAPED = 1;
        const DEPENDENCIES_UNSATISFIED = 2;

        public function __construct()
        {
            add_action('init', array($this, 'init_scon'));
        }

        public function init_scon()
        {
            try {
                $scon_all_plugins = get_plugins();
                $scon_active_plugins = apply_filters('active_plugins', get_option('active_plugins'));

                if (array_key_exists('woocommerce/woocommerce.php', $scon_all_plugins) && in_array('woocommerce/woocommerce.php', $scon_active_plugins)) {

                    !defined('scon_sunarc_path') && define('scon_sunarc_path', plugin_dir_path(__FILE__));
                    !defined('scon_sunarc_url') && define('scon_sunarc_url', plugins_url('/', __FILE__));

                    require_once(scon_sunarc_plugin_dir . '/classes/function-class.php');

                    SCON_Function_Class::instance();
                } else {
                    deactivate_plugins( plugin_basename( __FILE__ ) );
                    throw new Exception(__('Set custom order number plugin requires WooCommerce to be activated. Plugin will be deactivated.', 'set-custom-order-number'), self::DEPENDENCIES_UNSATISFIED);
                }
            } catch (Exception $e) {
                if (in_array($e->getCode(), array(self::ALREADY_BOOTSTRAPED, self::DEPENDENCIES_UNSATISFIED))) {
                    $this->bootstrap_warning_message = $e->getMessage();
                }
            }
        }

        // Uninstall Pluign
        function sow_uninstall()
        {
            $option_name = 'scon_sunarc_enabled';
            $new_value = 'no';
            update_option($option_name, $new_value);
        }
    }
}
new scon_sunarc_main_cls();
