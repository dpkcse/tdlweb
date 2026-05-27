<?php

namespace WPTRAVELENGINEEB;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Elementor_Import_Templates {

	/**
	 * Elementor_Import_Templates verison.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var Elementor_Import_Templates|null
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	public $templates;

	/**
	 * Main Elementor_Import_Templates Instance.
	 *
	 * Ensures only one instance of Elementor_Import_Templates is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Elementor_Import_Templates - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		require_once WPTRAVELENGINEEB_PATH . 'includes/import-templates/class-import-content.php';
		require WPTRAVELENGINEEB_PATH . 'includes/import-templates/class-template-design.php';
		$this->templates = Templates_Design::instance();
		add_action( 'wp_enqueue_scripts', array( $this, 'import_style' ), 988 );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'import_assets' ), 988 );
	}

	/**
	 *  Enqueue style for the modal in the backend editor.
	 *
	 * @return void
	 */
	public function import_style() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_enqueue_style(
				'cw-elementor-modal-admin',
				plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'includes/import-templates/css/templates' . $suffix . '.css',
				array(),
				filemtime( WPTRAVELENGINEEB_PATH . 'includes/import-templates/css/templates.min.css' )
			);
		}
	}

	/**
	 * Enqueue script for the modal in the backend editor.
	 *
	 * @return void
	 */
	public function import_assets() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script(
			'cw-elementor-modal-admin',
			plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'includes/import-templates/js/templates' . $suffix . '.js',
			array( 'jquery' ),
			filemtime( WPTRAVELENGINEEB_PATH . 'includes/import-templates/js/templates.min.js' ),
			true
		);

		wp_localize_script(
			'cw-elementor-modal-admin',
			'etAdmin',
			array(
				'url'           => get_site_url(),
				'ajaxURL'       => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'elementor_import_site' ),
				'activePlugin'  => $this->templates->get_active_plugins(),
				'demoList'      => $this->templates->get_server_list(),
				'templatesText' => array(
					'header'  => __( 'Templates', 'wptravelengine-elementor-widgets' ),
					'back'    => __( 'Back', 'wptravelengine-elementor-widgets' ),
					'import'  => __( 'Import', 'wptravelengine-elementor-widgets' ),
					'loading' => __( 'Loading', 'wptravelengine-elementor-widgets' ),
					'stay'    => __( 'Importing content - kindly stay on this page.', 'wptravelengine-elementor-widgets' ),
				),
				'btnIcon'       => '<svg width="62" height="40" viewBox="0 0 62 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M61.4482 20.3508C62.2879 19.0931 61.0376 18.0332 61.0376 18.0332C61.0376 18.0332 59.5784 17.2906 58.7424 18.5482C57.9027 19.8059 55.7867 22.9856 55.7867 22.9856L47.1545 23.1872L45.7064 25.3592L53.0361 27.1095L49.7482 31.0804C50.7708 31.4798 51.6404 32.215 52.6256 32.7076L55.7419 28.9121L60.1793 35.0028L61.6273 32.8308L58.4887 24.7882C58.4924 24.7882 60.6122 21.6085 61.4482 20.3508Z" fill="#3F494B"/>
                    <path d="M30.6514 18.5632C33.2587 18.5632 35.3724 16.4495 35.3724 13.8421C35.3724 11.2348 33.2587 9.12109 30.6514 9.12109C28.044 9.12109 25.9304 11.2348 25.9304 13.8421C25.9304 16.4495 28.044 18.5632 30.6514 18.5632Z" fill="url(#paint0_linear_79721_47)"/>
                    <path d="M56.7943 24.0605C56.0105 23.5678 54.973 23.8029 54.4804 24.5904C49.681 32.2112 44.6689 36.3762 39.9852 36.63C34.055 36.9584 30.1513 31.1849 26.7439 27.3297C24.9376 25.2846 23.2955 23.1013 21.8624 20.78C20.7279 18.9476 19.4739 16.7569 19.4739 14.54C19.4702 8.3747 24.486 3.35884 30.6513 3.35884C36.8129 3.35884 41.8288 8.3747 41.8288 14.54C41.8288 18.231 37.3877 24.1426 33.5735 28.4568C32.9727 29.136 33.0399 30.1698 33.7079 30.7856C33.7191 30.7931 33.7266 30.8043 33.7377 30.8117C34.417 31.4387 35.4843 31.379 36.0964 30.6886C40.0374 26.2586 45.1914 19.5148 45.1914 14.5363C45.1876 6.51987 38.6678 0 30.6513 0C22.6349 0 16.1113 6.51987 16.1113 14.54C16.1113 19.2237 20.6756 25.4712 24.5084 29.8936L24.4972 29.8824C25.5123 30.8416 26.3856 32.0022 27.3448 33.0211C29.2071 34.9916 31.1104 37.0927 33.5026 38.4139C33.5064 38.4176 33.5138 38.4176 33.5288 38.4288C35.1298 39.3021 37.1488 40 39.5149 40C39.7127 40 39.9143 39.9963 40.1195 39.9851C46.0348 39.6902 51.8232 35.111 57.3205 26.3781C57.8169 25.5943 57.578 24.5568 56.7943 24.0605Z" fill="#3F494B"/>
                    <path d="M27.0388 36.2459C26.5499 35.7831 25.8333 35.6674 25.2138 35.9287C24.0942 36.3989 22.7842 36.7124 21.3175 36.6303C16.6338 36.3765 11.6217 32.2116 6.84097 24.6169L3.11639 18.4478C2.63869 17.6529 1.60492 17.3991 0.813725 17.8768C0.0188002 18.3582 -0.23871 19.3883 0.242723 20.1832L3.98223 26.3784C9.47952 35.1114 15.2679 39.6906 21.1832 39.9854C21.3885 39.9966 21.59 40.0003 21.7878 40.0003C23.5754 40.0003 25.169 39.601 26.5312 39.0263C27.6545 38.5523 27.9307 37.0893 27.0462 36.2496L27.0388 36.2459Z" fill="url(#paint1_linear_79721_47)"/>
                    <defs>
                    <linearGradient id="paint0_linear_79721_47" x1="23.3553" y1="7.66187" x2="38.0334" y2="20.8808" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#1FC0A1"/>
                    <stop stop-color="#1FC0A1"/>
                    <stop offset="1" stop-color="#00A89F"/>
                    </linearGradient>
                    <linearGradient id="paint1_linear_79721_47" x1="-7.51925" y1="14.1794" x2="27.2481" y2="52.7788" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#1FC0A1"/>
                    <stop stop-color="#1FC0A1"/>
                    <stop offset="1" stop-color="#00A89F"/>
                    </linearGradient>
                    </defs>
                </svg>',
			)
		);
	}
}
Elementor_Import_Templates::instance();
