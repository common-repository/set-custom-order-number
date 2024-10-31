<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('SCON_Function_Class')):

    class SCON_Function_Class
    {
        /**
         * @var null
         */
        protected static $_instance = null;
        protected static $_prefixVal = "Ord-";
        protected static $_suffixVal = "-Num";

        /**
         * SOW_Function_Class constructor.
         */
        public function __construct()
        {
            define('scon_sunarc_version', '1.0.1');
            add_filter('woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50);
            add_action('woocommerce_settings_tabs_scon_settings_tab', __CLASS__ . '::settings_tab');
            add_action('woocommerce_update_options_scon_settings_tab', __CLASS__ . '::update_settings');

            $prefixValue = get_option('scon_order_number_prefix');
            if (!is_null(self::$_prefixVal)) {
                self::$_prefixVal = isset($prefixValue) ? $prefixValue : "Ord-";
            }
            $suffixValue = get_option('scon_order_number_suffix');
            if (!is_null(self::$_suffixVal)) {
                self::$_suffixVal = isset($suffixValue) ? $suffixValue : "-Num";
            }
            if (is_admin()) {
                $this->frontend_css_js();
            }
        }

        /**
         *  Embed Styles & Scripts for Frontend.
         */
        public function frontend_css_js()
        {
            add_action('admin_enqueue_scripts', array($this, 'scon_frontend_scripts'));
        }

        /**
         *
         */
        public function scon_frontend_scripts()
        {
            wp_enqueue_script('scon-frontend-js', scon_sunarc_plugin_url . 'assets/js/custom.js', array('jquery', 'wp-color-picker'), scon_sunarc_version, true);
            wp_enqueue_style('woocommerce_admin_styles');
        }

        /**
         * @return SOW_Function_Class|null
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Add a new settings tab to the WooCommerce settings tabs array.
         *
         * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
         * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
         */
        public static function add_settings_tab($settings_tabs)
        {
            $settings_tabs['scon_settings_tab'] = __('Set Custom Order Number', 'scon-settings-tab');
            return $settings_tabs;
        }

        /**
         * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
         *
         * @uses woocommerce_admin_fields()
         * @uses self::get_settings()
         */
        public static function settings_tab()
        {
            woocommerce_admin_fields(self::get_settings());
        }

        /**
         * Get all the settings for this plugin for @return array Array of settings for @see woocommerce_admin_fields() function.
         * @see woocommerce_admin_fields() function.
         *
         */
        public static function get_settings()
        {
            $settings = array(
                'section_title' => array(
                    'name' => __('Set Custom Order Number', 'scon-settings-tab'),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'scon_tab_section_title'
                ),
                'config' => array(
                    'title'    => __( 'Enable Custom Order Number', 'scon-settings-tab' ),
                    'desc'     => __( 'This controls allows you to set custom order number.', 'scon-settings-tab' ),
                    'id'       => 'scon_sunarc_enabled',
                    'class'    => 'wc-enhanced-select',
                    'css'      => 'min-width:300px;',
                    'default'  => 'no',
                    'type'     => 'select',
                    'options'  => array(
                        'yes'  => __( 'Yes', 'scon-settings-tab' ),
                        'no' => __( 'No', 'scon-settings-tab' ),
                    ),
                    'desc_tip' => __('Select Yes for enable custom order number, keep No to disable.', 'scon-settings-tab'),
                ),
                'attribute' => array(
                    'name' => __( 'Prefix', 'scon-settings-tab' ),
                    'type' => 'text',
                    'required' => false,
                    'value'  => self::$_prefixVal,
                    'desc' => __( 'Enter prefix of order number (Allowed character length 4).', 'scon-settings-tab' ),
                    'id'   => 'scon_order_number_prefix',
                ),
                'attributeval' => array(
                    'name' => __( 'Suffix', 'scon-settings-tab' ),
                    'type' => 'text',
                    'required' => false,
                    'value'  => self::$_suffixVal,
                    'desc' => __( 'Enter suffix of order number (Allowed character length 4).', 'scon-settings-tab' ),
                    'id'   => 'scon_order_number_suffix',
                ),
                'section_end' => array(
                    'type' => 'sectionend',
                    'id' => 'scon_tab_section_end'
                )
            );
            return apply_filters('wc_settings_sunarc_set_custom_order_number_settings', $settings);
        }

        /**
         * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
         *
         * @uses woocommerce_update_options()
         * @uses self::get_settings()
         */
        public static function update_settings()
        {
            $settingData = self::get_settings();
            $settingData['attribute']['value'] = substr($settingData['attribute']['value'], 0, 4);
            $settingData['attributeval']['value'] = substr($settingData['attributeval']['value'], 0, 4);
            woocommerce_update_options($settingData);
        }
    }
endif;
?>