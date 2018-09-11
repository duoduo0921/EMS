<!DOCTYPE html>
<html lang="en">
<head>
  <title>EMS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<!--    <link href="./css/bootstrap.min.css" rel="stylesheet" media="screen">-->
<!--    <link href="./css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">-->
</head>
<body>

<div class="container">
  <h2 class="text-center">Library Information</h2>

    <form method="get">
        <div class="form-group">
            <label>BuildingID</label>
            <input type="text" class="form-control" size="16" id="buildingid" name="buildingid">
<!--            can add place holder-->
        </div>

<!--        <div class="form-group">-->
<!--            <label for="dtp_input1" class="col-md-2 control-label">Start Time</label>-->
<!--            <div class="input-group date form_datetime col-md-5" data-date="2018-09-07T05:25"-->
<!--                 data-date-format="yyyy-mm-ddTHH:ii" data-link-field="dtp_input1">-->
<!--                <input class="form-control" size="16" type="text" value="">-->
<!--                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>-->
<!--            </div>-->
<!--            <input type="hidden" id="dtp_input1" name="startDate" value="" /><br/>-->
<!--        </div>-->

        <div class="form-group">
            <label>Start Time</label>
            <input type="text" class="form-control" id="from-datepicker" name="startDate">
        </div>

        <div class="form-group">
            <label>End Time</label>
            <input type="text" class="form-control" id="to-datepicker" name="endDate">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
	
	<?php
	
	// EMS Variables defined as constants 
	try {
    	require 'EMS_VARIABLES.php';
	} catch (Exception $e) {
    	exit('Require failed! Error: '.$e);
	}
	
	$bid = -1;
	$start = date( 'Y-m-d' );
	$end = date('Y-m-d', strtotime( '+1 day' ));
	
	if($_GET["buildingid"]) {
		$bid = $_GET["buildingid"];
	}
	
	if($_GET["startDate"]) {
		$start = date( $_GET["startDate"] );
	}
	
	if($_GET["endDate"]) {
		$end = date( $_GET["endDate"] );
	}

    // begin param array  to use with EMS functions 
    $params =  array('UserName' => SERT_USERNAME, 'Password' => SERT_PASSWORD, 'StartDate' => $start, 
    'EndDate' => $end, 'BuildingID' => $bid, 'ViewComboRoomComponents' => TRUE);

    // define new soap client object 
	$client = new SoapClient(SERT_SERVICE_URL);
    
	// test simple soap method from SERT_SERVICE_URL
	$response = $client->GetAllBookings($params);
	
	$xml = simplexml_load_string($response->GetAllBookingsResult);

	$outputarray=array();
	
	foreach ($xml as $value) {
		$groupname = (string)$value->GroupName;
		$eventname = (string)$value->EventName;
		$bookdate = (string)$value->BookingDate;
		$eventstart = (string)$value->TimeEventStart;
		$eventend = (string)$value->TimeEventEnd;
		$reserveid = (string)$value->ReservationID;
		$roomid = (string)$value->RoomID;
		$buildingid = (string)$value->BuildingID;
		
		if($value->StatusID != 1 && $value->StatusID != 7) {continue;}
		if(!$eventstart) {continue;}
	
		array_push($outputarray,array($groupname, $eventname, $bookdate, 
		$eventstart, $eventend, $reserveid, $roomid, $buildingid));
	}
	
	sort($outputarray);
	
	?>

	      
  <table class="table table-bordered">
    <thead class="thead-dark">
      <tr>
        <th>Group Name</th>
        <th>Event Name</th>
        <th>Booking Date</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>ReservationID</th>
        <th>RoomID</th>
        <th>BuildingID</th>
      </tr>
    </thead>
    <tbody>
<?php   
	foreach ($outputarray as $outputpiece) {
		$output .= "<tr class=\"table-secondary\">"."<td>".$outputpiece[0]."</td>".
        "<td>".$outputpiece[1]."</td>".
        "<td>".$outputpiece[2]."</td>".
        "<td>".$outputpiece[3]."</td>".
        "<td>".$outputpiece[4]."</td>".
        "<td>".$outputpiece[5]."</td>".
        "<td>".$outputpiece[6]."</td>".
        "<td>".$outputpiece[7]."</td>"."<tr>";
        
	}
	echo $output;
?>
    </tbody>
  </table>
</div>

</body>
</html>

<!-- the date picker -->
<script src="https://code.jquery.com/jquery-2.2.3.min.js"   integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo="   crossorigin="anonymous"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/css/bootstrap-datepicker3.min.css">
<script>

$( document ).ready(function() {
    $("#from-datepicker").datepicker({
        format: 'yyyy-mm-dd'
    });
    $("#from-datepicker").on("change", function () {
        var fromdate = $(this).val();
    });
    $("#to-datepicker").datepicker({ 
        format: 'yyyy-mm-dd'
    });
    $("#to-datepicker").on("change", function () {
        var todate = $(this).val();
    });
}); 
</script>

<!--<script type="text/javascript" src="./js/jquery-1.8.3.min.js" charset="UTF-8"></script>-->
<!--<script type="text/javascript" src="./js/bootstrap.min.js"></script>-->
<!--<script type="text/javascript" src="./js/bootstrap-datetimepicker.js" charset="UTF-8"></script>-->
<!--<script type="text/javascript" src="./js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>-->
<!--<script type="text/javascript">-->
<!--    $('.form_datetime').datetimepicker({-->
<!--        weekStart: 1,-->
<!--        todayBtn:  1,-->
<!--        autoclose: 1,-->
<!--        todayHighlight: 1,-->
<!--        startView: 2,-->
<!--        forceParse: 0,-->
<!--        showMeridian: 1-->
<!--    });-->
<!--</script>-->
