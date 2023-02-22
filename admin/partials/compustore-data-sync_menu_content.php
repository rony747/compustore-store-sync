<!-- Menu Sync Form -->
<div class="wrap">
	<form method="POST"  id="compu_store_sync_form">
		<?php
		$subsites = get_sites();
		echo "<div class='field_wrap'><label class='main_label' for='compu_map_sync_from'>From</label>";
		echo "<select name='compu_map_sync_from' id='compu_map_sync_from'>";
		foreach ($subsites as $subsite) {
			$subsite_id   = get_object_vars($subsite)[ "blog_id" ];
			$subsite_name = get_blog_details($subsite_id) -> blogname;
			?>
			<option
				value='<?php echo sanitize_text_field( $subsite_id) ?>' <?php echo ( get_current_blog_id() == $subsite_id ) ? 'selected' : ''; ?> ><?php echo $subsite_name ?></option>
			<?php
		}
		echo "</select></div>";
		echo "<div class='field_wrap'><label class='main_label' for='compu_map_sync_from'>To</label>";
		foreach ($subsites as $subsite) {
			$subsite_id   = get_object_vars($subsite)[ "blog_id" ];
			$subsite_name = get_blog_details($subsite_id) -> blogname;
			if ( $subsite_id != get_current_blog_id() ) {
				?>
				<input type="checkbox" id="compu_map_sync_all_sites<?php echo $subsite_id ?>" name="compu_map_sync_all_sites[]"
				       value="<?php echo $subsite_id ?>">
				<label for="compu_map_sync_all_sites<?php echo $subsite_id ?>"> <?php echo $subsite_name ?></label>
				<?php
			}
		}
    echo '</div>';
		submit_button('Sync Now', 'primary', 'compu_menu_sync');
		?>
	</form>

</div>
