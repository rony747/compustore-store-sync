<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://tirony.me
 * @since      1.0.0
 *
 * @package    Compustore_Data_Sync
 * @subpackage Compustore_Data_Sync/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Compustore_Data_Sync
 * @subpackage Compustore_Data_Sync/admin
 * @author     t.i.rony <admin@tirony.me>
 */
class Compustore_Data_Sync_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this -> plugin_name = $plugin_name;
		$this -> version     = $version;

	}

	public function enqueue_styles() {

		wp_enqueue_style( $this -> plugin_name, plugin_dir_url( __FILE__ ) . 'css/compustore-data-sync-admin.css', array(), $this -> version, 'all' );

	}

	public function enqueue_scripts() {

		wp_enqueue_script( 'compu_sync_script', plugin_dir_url( __FILE__ ) . 'js/compustore-data-sync-admin.js', array( 'jquery' ), $this -> version, false );
		wp_localize_script( 'compu_sync_script', 'compu_map_sync', array(
			'ajax_url'          => admin_url( 'admin-ajax.php' ),
			'compu_msync_nonce' => wp_create_nonce( 'compu_msync_nonce_action' )
		) );

	}

	/*/////////////////////////////////////////  Add admin Menu / content  /////////////////////////////////////////////*/
	public function add_admin_menu() {
		add_menu_page( __( 'Compustore Sync', 'multisite-list' ), __( 'Compustore Sync', 'multisite-list' ), 'manage_options', 'compu_settings', [
			$this,
			'compu_sync_page_html'
		], 'dashicons-welcome-learn-more' );
	}

	public function compu_sync_page_html() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . "admin/partials/compustore-data-sync-admin-display.php";
	}

	/*/////////////////////////////////////////  /////////////////////////////////////////////*/




}
