<?php
if ( ! current_user_can( 'manage_options' ) ) {
	return;
}
//Get the active tab from the $_GET param
$default_tab = null;
$tab         = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : $default_tab;
?>
<div class="wrap">
  <nav class="nav-tab-wrapper">
    <a href="?page=compu_settings" class="nav-tab <?php if ( $tab === null ): ?>nav-tab-active<?php endif; ?>">Compustore
      stores sync</a>
    <!--    <a href="?page=compu_settings&tab=settings" class="nav-tab -->
	  <?php //if($tab==='settings'):?><!--nav-tab-active--><?php //endif; ?><!--">Settings</a>-->
    <!--    <a href="?page=compu_settings&tab=tools" class="nav-tab --><?php //if($tab==='tools'):?><!--nav-tab-active-->
	  <?php //endif; ?><!--">Tools</a>-->
  </nav>

  <div class="tab-content">
	  <?php switch ( $tab ) :
		  case 'menu':
			  echo 'Menu';
			  break;
		  case 'slider':
			  echo 'Slider';
			  break;
		  default:
			  include_once plugin_dir_path( dirname( __FILE__ ) ) . "/partials/compustore-data-sync_menu_content.php";
			  break;
	  endswitch; ?>
  </div>

	<?php ?>

<!--  <div id="loading_div">-->
<!--    <div class="spinner-box">-->
<!--      <div class="circle-border">-->
<!--        <div class="circle-core"></div>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
  <div id="compu_map_message"></div>
</div>

