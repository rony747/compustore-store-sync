<?php

class Compustore_Data_Sync_Store {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this -> plugin_name = $plugin_name;
		$this -> version     = $version;

	}
	public function compu_map_sync_callback() {
		$nonce = check_ajax_referer( 'compu_msync_nonce_action', '_ajax_nonce' );
		if ( $nonce ) {

//			/*///////////////////////////////////////// Process data /////////////////////////////////////////////*/
			$alldata   = [];
			$response_text = '';
			wp_parse_str( $_POST[ 'form_data' ], $alldata );
			$fromSite  = $alldata[ 'compu_map_sync_from' ] ?? get_current_blog_id();
			$allSites  = $alldata[ 'compu_map_sync_all_sites' ] ?? [];
			$post_type = 'compustore-store';

			if(!$this->compustore_sync_security($fromSite,$allSites)){
				wp_die('Stop hacking');
			}
			/*///////////////////////////////////////// Get all posts /////////////////////////////////////////////*/
			switch_to_blog( (int) $fromSite );
			$args  = array(
				'post_type'      => $post_type, // Replace with the name of your custom post type
				'posts_per_page' => - 1,
			);
			$posts = get_posts( $args );
			restore_current_blog();
			foreach ( $allSites as $site ) {
				/*///////////////////////////////////////// Delete existing posts /////////////////////////////////////////////*/
				$this -> compustore_sync_delete( $site, $post_type );
				/*///////////////////////////////////////// Insert posts, meta and thumb /////////////////////////////////////////////*/
				$this -> compustore_data_sync_insert( $posts, $site );

			}
			/*/////////////////////////////////////////  /////////////////////////////////////////////*/
			wp_send_json_success( "All Data synced" );

		} else {
			wp_send_json_error( "something is wrong" );
		}
		wp_die();
	}


	/*_________________________________________  _______________________________________________________________*/

	function compustore_sync_security($fromSite,$allSites){
		// Check that site IDs are valid
		if (!is_numeric($fromSite) || !get_blog_details($fromSite)) {
			wp_send_json_error( "Site id is not valid" );
			return false;
		}
		if (!is_array($allSites)) {
			wp_send_json_error( "Site id is not valid" );
			return false;
		}
		if (empty($allSites)) {
			wp_send_json_error( "Please select atleast one site" );
			return false;
		}
		foreach ($allSites as $site) {
			if (!is_numeric($site) || !get_blog_details($site)) {
				wp_send_json_error( "Site id is not valid" );
				return false;
			}

		}
		return true;
	}

	function compustore_sync_delete( $site, $post_type ) {
		global $wpdb;
		switch_to_blog( (int) $site );
		// Get all post IDs of the post type
		$post_ids = $wpdb -> get_col( $wpdb -> prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s", $post_type ) );
		foreach ( $post_ids as $post_id ) {
			wp_delete_post( $post_id, true );
			if ( has_post_thumbnail( $post_id ) ) {
				$attachment_id = get_post_thumbnail_id( $post_id );
				wp_delete_attachment( $attachment_id, true );
			}
		}
		// Restore the main site
		restore_current_blog();
	}

	function compustore_data_sync_insert( $posts, $site ) {

		foreach ( $posts as $post ) {
			$metadata      = get_post_meta( $post -> ID );
			$thumbnail_id  = get_post_thumbnail_id( $post -> ID );
			$thumbnail_url = wp_get_attachment_image_src( $thumbnail_id, 'full' );
			switch_to_blog( $site );
			$post_data = array(
				'post_title'   => $post -> post_title,
				'post_content' => $post -> post_content,
				'post_status'  => $post -> post_status,
				'post_type'    => $post -> post_type,
			);
			$post_id   = wp_insert_post( $post_data );
			/*///////////////////////////////////////// Insert meta /////////////////////////////////////////////*/
			foreach ( $metadata as $key => $value ) {
				update_post_meta( $post_id, $key, $value[ 0 ] );
			}
			/*///////////////////////////////////////// inset Thumbnail /////////////////////////////////////////////*/
			if ( $thumbnail_id ) {
				$thumbnail_url  = $thumbnail_url[ 0 ];
				$thumbnail_data = file_get_contents( $thumbnail_url );
				$thumbnail_file = basename( $thumbnail_url );
				$upload_dir     = wp_upload_dir();
				$thumbnail_path = $upload_dir[ 'path' ] . '/' . $thumbnail_file;
				file_put_contents( $thumbnail_path, $thumbnail_data );
				$attachment    = array(
					'post_mime_type' => 'image/jpeg',
					'post_title'     => sanitize_file_name( $thumbnail_file ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);
				$attachment_id = wp_insert_attachment( $attachment, $thumbnail_path, $post_id );
				set_post_thumbnail( $post_id, $attachment_id );
			}
			restore_current_blog();
		} // Post loop ends here
	}
}