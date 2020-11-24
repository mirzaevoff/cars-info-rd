<?php

// scripts registering function
function rd_adminRegisterAssets() {
  wp_register_style( 'car-info-rd-admin-styles', plugins_url('cars-info-rd/admin/assets/css/styles.css' ) );
	wp_register_script( 'car-info-rd-admin-jquery', plugins_url('cars-info-rd/admin/assets/js/jquery-3.5.1.min.js' ) );

  wp_enqueue_style( 'car-info-rd-admin-styles' );
	wp_enqueue_script( 'car-info-rd-admin-jquery' );
}

// Menu page item
function cird_settingsMenu() {
	add_options_page(
        'Car Info RD',		        // title
        'Car Info RD',						// menu item name
        'manage_options',					// access level
        'cird-settings',					// menu item unique slug
        'cird_settingsPage'				// menu item `echo` function
    );
}

function addAPIKey($api_key, $user_id){
  global $wpdb;
  $table_name = $wpdb->prefix . "crid";
  if(!$wpdb->get_var("SELECT * FROM `$table_name` WHERE `owner_id` = '$user_id'")) {
    $sql = "INSERT INTO $table_name (api_key, owner_id) VALUES ('$api_key', '$user_id')";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $wpdb->query($sql);
  } else {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $wpdb->update($table_name, array('api_key' => $api_key), array('owner_id' => $user_id));
  }
}

function getAPIKey($user_id){
  global $wpdb;
  $table_name = $wpdb->prefix . "crid";
  if($wpdb->get_var("SELECT * FROM `$table_name` WHERE `owner_id` = '$user_id'")) {
    $sql = "SELECT * FROM `$table_name` WHERE `owner_id` = '$user_id'";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    return $wpdb->get_row($sql);
  }
}

function cird_settingsPage(){
    if(isset($_GET['submit']))
    {
      $api_key = $_GET['rdAPIKey'];
      $user_id = get_current_user_id();
      addAPIKey($api_key, $user_id);
    }
  ?>
  <div class="rdContainer">
    <h1 class="rdTitle">Settings</h1>
    <div class="rdFormContainer">
      <form name="rdAPIKeySetForm" id="rdAPIKeySetForm" class="rdForm" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="rdFormGroup">
          <label for="rdAPIKey">API key</label>
          <input type="hidden" name="page" value="cird-settings">
          <input type="text" name="rdAPIKey" id="rdAPIKey" class="rdInput" value="<?php echo getAPIKey(get_current_user_id())->api_key; ?>">
        </div>
        <input type="submit" name="submit" id="rdSubmit" class="rdSubmit" value="Submit">
      </form>
    </div>
  </div>
  <?php
}
