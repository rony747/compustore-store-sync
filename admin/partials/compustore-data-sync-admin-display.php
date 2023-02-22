<div class="wrap">
  <!-- Menu Sync Form -->
  <form method="POST"  id="compu_store_sync_form">
    <?php
    $subsites = get_sites();
    echo "<h2>From</h2>";
    echo "<select name='compu_map_sync_from' id='compu_map_sync_from'>";
    foreach ($subsites as $subsite) {
      $subsite_id   = get_object_vars($subsite)[ "blog_id" ];
      $subsite_name = get_blog_details($subsite_id) -> blogname;
      ?>
      <option
          value='<?php echo $subsite_id ?>' <?php echo ( get_current_blog_id() == $subsite_id ) ? 'selected' : ''; ?> ><?php echo $subsite_name ?></option>
      <?php
    }
    echo "</select>";
    echo "<h2>To</h2>";
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
    submit_button('Sync Now', 'primary', 'menu_sync');
    ?>
  </form>
<div id="compu_map_message"></div>
</div>

