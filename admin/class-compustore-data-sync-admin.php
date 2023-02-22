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
class Compustore_Data_Sync_Admin
{

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
   * @since    1.0.0
   */
  public function __construct($plugin_name, $version)
  {

    $this -> plugin_name = $plugin_name;
    $this -> version     = $version;

  }




  /**
   * Register the stylesheets for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_styles()
  {

    wp_enqueue_style($this -> plugin_name, plugin_dir_url(__FILE__) . 'css/compustore-data-sync-admin.css', array(), $this -> version, 'all');

  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts()
  {

    wp_enqueue_script('compu_sync_script', plugin_dir_url(__FILE__) . 'js/compustore-data-sync-admin.js', array('jquery'), $this -> version, false);
    wp_localize_script('compu_sync_script', 'compu_map_sync', array('ajax_url' => admin_url('admin-ajax.php'), 'compu_msync_nonce' => wp_create_nonce('compu_msync_nonce_action')));

  }

  public function add_admin_menu()
  {
    add_menu_page(__('Compustore Sync', 'multisite-list'), __('Compustore Sync', 'multisite-list'), 'manage_options', 'compu_settings', [$this, 'compu_sync_page_html'], 'dashicons-welcome-learn-more');

  }

  public function compu_sync_page_html()
  {
    require_once plugin_dir_path(dirname(__FILE__)) . "admin/partials/compustore-data-sync-admin-display.php";
  }

  public function compu_map_sync_callback()
  {
    $nonce = check_ajax_referer('compu_msync_nonce_action', '_ajax_nonce');
    if ( $nonce ) {
      $this -> compu_menu_sync();
      wp_send_json_success("Data inserted successfully");

    } else {
      wp_send_json_error("something is wrong");
    }
    wp_die();
  }


  /*_________________________________________  _______________________________________________________________*/
  private function compu_menu_sync()
  {
    $alldata = [];
    wp_parse_str($_POST[ 'form_data' ], $alldata);
    global $wpdb;
    $fromSite  = $alldata[ 'compu_map_sync_from' ] ?? get_current_blog_id();
    $allSites  = $alldata[ 'compu_map_sync_all_sites' ] ?? [];
    $post_type = 'compustore-store';

    /*///////////////////////////////////////// Get all posts /////////////////////////////////////////////*/

    switch_to_blog((int)$fromSite);
    $args  = array(
      'post_type'      => $post_type, // Replace with the name of your custom post type
      'posts_per_page' => -1,
    );
    $posts = get_posts($args);
    restore_current_blog();

    foreach ($allSites as $site) {
      /*///////////////////////////////////////// Delete existing posts /////////////////////////////////////////////*/
      switch_to_blog((int)$site);
      // Get all post IDs of the post type
      $post_ids = $wpdb -> get_col($wpdb -> prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s", $post_type));
      foreach ($post_ids as $post_id) {
        wp_delete_post($post_id, true);
        if ( has_post_thumbnail($post_id) ) {
          $attachment_id = get_post_thumbnail_id($post_id);
          wp_delete_attachment($attachment_id, true);
        }
      }
      // Restore the main site
      restore_current_blog();


      /*_________________________________________ Insert post to new site _______________________________________________________________*/
      foreach ($posts as $post) {
        $metadata      = get_post_meta($post -> ID);
        $thumbnail_id  = get_post_thumbnail_id($post -> ID);
        $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'full');
        switch_to_blog($site);
        $post_data = array(
          'post_title'   => $post -> post_title,
          'post_content' => $post -> post_content,
          'post_status'  => $post -> post_status,
          'post_type'    => $post -> post_type,
        );
        $post_id   = wp_insert_post($post_data);
        /*_________________________________________ Insert meta data _______________________________________________________________*/
        foreach ($metadata as $key => $value) {
          update_post_meta($post_id, $key, $value[ 0 ]);
        }
        /*_________________________________________ Insert Post thumbnail _______________________________________________________________*/
        if ( $thumbnail_id ) {
          $thumbnail_url  = $thumbnail_url[ 0 ];
          $thumbnail_data = file_get_contents($thumbnail_url);
          $thumbnail_file = basename($thumbnail_url);
          $upload_dir     = wp_upload_dir();
          $thumbnail_path = $upload_dir[ 'path' ] . '/' . $thumbnail_file;
          file_put_contents($thumbnail_path, $thumbnail_data);
          $attachment    = array(
            'post_mime_type' => 'image/jpeg',
            'post_title'     => sanitize_file_name($thumbnail_file),
            'post_content'   => '',
            'post_status'    => 'inherit'
          );
          $attachment_id = wp_insert_attachment($attachment, $thumbnail_path, $post_id);
          set_post_thumbnail($post_id, $attachment_id);
        }
        restore_current_blog();

      }

    }

  }


}
