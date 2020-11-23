<?php

// scripts registering function
function rd_registerAssets() {
  wp_register_style( 'car-info-rd-styles', plugins_url( 'car-info-rd/assets/css/styles.css' ) );
	wp_register_script( 'car-info-rd-jquery', plugins_url( 'car-info-rd/assets/js/jquery-3.5.1.min.js') );

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
function rd_GetSpecs( $atts ){
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
  $( "#rdGetSpecsForm" ).submit(function( event ) {

    $('#rdResponse').html('<div class="rdLoading"><img src="<?php echo plugins_url( 'car-info-rd/assets/img/loading.gif' ); ?>" alt="Loading"/></div>');

    var rdVINCode = $( "#rdCarVIN" ).val();
    var rdAPIKey = '<?php echo getAPIKey(get_current_user_id())->api_key; ?>';

    var myHeaders = new Headers();
    myHeaders.append("Host", "marketcheck-prod.apigee.net");

    var requestOptions = {
      method: 'GET',
      headers: myHeaders,
      redirect: 'follow'
    };

    function onError(error){
      $('#rdResponse').html('<h3 style="text-align: center; color: red;">Sorry, but your API key is invalid.</h3>');
    }
    function onResult(result){
      result = {
        "year": 2012,
        "make": "Ford",
        "model": "Focus",
        "trim": "SE",
        "short_trim": "SE",
        "body_type": "Sedan",
        "vehicle_type": "Car",
        "transmission": "Manual",
        "drivetrain": "FWD",
        "fuel_type": "Regular Unleaded",
        "engine": "2.0L L4 DOHC 16V",
        "engine_size": 2,
        "engine_block": "I",
        "doors": 4,
        "cylinders": 4,
        "made_in": "United States",
        "steering_type": "R&P",
        "antibrake_sys": "4-Wheel ABS",
        "tank_size": "12.4 gallon",
        "overall_height": "57.70 Inches",
        "overall_length": "178.50 Inches",
        "overall_width": "71.80 Inches",
        "std_seating": "5",
        "highway_miles": "36 miles/gallon",
        "city_miles": "26 miles/gallon"
      }


      var html = "<table>";
      html += "<tr>";
      html += "<th>Name</th>";
      html += "<th>Value</th>";
      html += "</tr>";
      // переберём массив arr
      $.each(result,function(index,value){
        html += "<tr>";
        html += "<td>" + index.replace("_", " ") + "</td>";
        html += "<td>" + value + "</td>";
        html += "</tr>";
      });


      html += "</table>";
      $('#rdResponse').html(html);
    }



    fetch("http://api.marketcheck.com/v2/decode/car/" + rdVINCode + "/specs?api_key=" + rdAPIKey, requestOptions)
      .then(response => response.text())
      .then(result => onResult(result))
      .catch(error => onResult(error));


    event.preventDefault();
  });
</script>

<?php
}
?>
