<?php

// scripts registering function
function rd_registerAssets() {
  wp_register_style( 'car-info-rd-styles', plugins_url('cars-info-rd-1.1.0/assets/css/styles.css' ) );
	wp_register_script( 'car-info-rd-jquery', plugins_url('cars-info-rd-1.1.0/assets/js/jquery-3.5.1.min.js') );

  wp_enqueue_style( 'car-info-rd-styles' );
	wp_enqueue_script( 'car-info-rd-jquery' );
}

function createTable(){
  global $wpdb;
  $table_name = $wpdb->prefix . "crid";

  if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    $sql = "CREATE TABLE " . $table_name . " (
  	  id mediumint(9) NOT NULL AUTO_INCREMENT,
  	  api_key text NOT NULL,
  	  owner_id text NOT NULL,
  	  UNIQUE KEY id (id)
  	);";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}

function deleteTable(){
  global $wpdb;
  $table_name = $wpdb->prefix . "crid";

  $sql = "DROP TABLE IF EXISTS $table_name;";
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  $wpdb->query($sql);
}



// getting specifications by car VIN code function
function rd_GetSpecs( ){
?>
<div>
  <form name="rdGetSpecsForm" id="rdGetSpecsForm" class="rdForm" method="GET" action="">
    <div class="rdFormGroup">
      <label for="rdCarVIN">VIN code</label>
      <input type="text" name="rdCarVIN" id="rdCarVIN" class="rdInput" maxlength="17" value="1FAHP3F28CL148530">
    </div>
    <input type="submit" name="submit" id="rdSubmit" class="rdSubmit" value="Submit">
  </form>
</div>

<div id="rdResponse"></div>


<script>
jQuery(document).ready(function($) {
  $( "#rdGetSpecsForm" ).submit(function( event ) {

    $('#rdResponse').html('<div class="rdLoading"><img src="<?php echo plugins_url( 'cars-info-rd-1.1.0/assets/img/loading.gif' ); ?>" alt="Loading"/></div>');

    var rdVINCode = $( "#rdCarVIN" ).val();
    // Getting an API key from database
    var rdAPIKey = '<?php echo getAPIKey(get_current_user_id())->api_key; ?>';

    function onError(error){
      console.log(error.status);
      var html = "<h3 style='text-align: center; color: red;''>";
      if (error.status == 422) html += "<span>Invalid VIN code</span>";
      if (error.status == 403) html += "<span>Invalid API key</span>";
      if (error.status == 429) html += "<span>Too many requests</span>";
      html += "</h3>";
      $('#rdResponse').html(html);
    }
    function onResult(result){
      console.log(result);
      var html = "<table class='rdSpecs'>";
      html += "<tr>";
      html += "<th class='rdTitle'>Name</th>";
      html += "<th class='rdTitle'>Value</th>";
      html += "</tr>";
      // переберём массив arr
      $.each(result.specification,function(index,value){
        html += "<tr>";
        html += "<th>" + index.replace("_", " ") + "</th>";
        html += "<td>" + value + "</td>";
        html += "</tr>";
      });


      html += "</table>";
      $('#rdResponse').html(html);
    }

    const settings = {
      "async": true,
      "crossDomain": true,
      "url": "https://vindecoder.p.rapidapi.com/v2.0/decode_vin?vin=" + rdVINCode,
      "method": "GET",
      "headers": {
        "x-rapidapi-key": rdAPIKey,
        "x-rapidapi-host": "vindecoder.p.rapidapi.com"
      }
    };

    $.ajax(settings)
    .done(function (response) {
      onResult(response);
    })
    .fail( function(xhr, textStatus, errorThrown) {
        onError(xhr);
    });


    event.preventDefault();
  });
});

</script>

<?php
}
?>
