<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
    <title>Room Info</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</head>

<body>

<?php
// EMS Variables defined as constants

$id = $_GET['id'];

try {
    require 'EMS_VARIABLES.php';
} catch (Exception $e) {
    exit('Require failed! Error: '.$e);
}


date_default_timezone_set(GSD_API_EVENTS_DEFAULT_TIMEZONE);
// define new soap client object
$client = new SoapClient(SERT_SERVICE_URL);

$bookingparams =  array('UserName' => SERT_USERNAME, 'Password' => SERT_PASSWORD,
    'StartDate' => date( 'Y-m-d\TH:i:s' ), 'EndDate' => date("Y-m-d\TH:i:s", time()+(6 * 24 * 60 * 60)),
    'RoomID' => $id, 'ViewComboRoomComponents' => TRUE);
$roomparams = array('UserName' => SERT_USERNAME, 'Password' => SERT_PASSWORD,'RoomID' => $id);
$bookinginfo = simplexml_load_string($client->GetAllRoomBookings($bookingparams)->GetAllRoomBookingsResult);
$roomdetail = simplexml_load_string($client->GetRoomDetails($roomparams)->GetRoomDetailsResult);
$roomname = $roomdetail->Data->Description;

$listallbookings=array();

foreach ($bookinginfo as $value) {
    $groupname = (string)$value->GroupName;
    $eventname = (string)$value->EventName;
    $eventstart = (string)$value->TimeEventStart;
    $eventend = (string)$value->TimeEventEnd;
    $contact = (string)$value->ContactEmailAddress;

    if($value->StatusID != 1 && $value->StatusID != 7) {continue;}
    if(!$eventstart) {continue;}

    array_push($listallbookings,array($groupname, $eventname,
        $eventstart, $eventend, $contact));
}

$time = date("F j, Y, g:i a");

echo "</br>.<h3 class=\"text-center\"> $time </h3>.</br>";

echo "<h1 class=\"text-center\"> $roomname </h1>";

function mySort($a, $b) {
    $a = $a[2];
    $b = $b[2];
    if (strtotime($a) == strtotime($b))return 0;
    return (strtotime($a) < strtotime($b)) ? -1 : 1;
}

//print_r($listallbookings);

if (sizeof($listallbookings) == 0) {
    echo "<div class=\"alert alert-success col-12 col-sm-6 col-md-8 offset-md-2\" role=\"alert\">
      <h4 class=\"alert-heading text-center\"> No next event scheduled! </h4>
  </div>";
} else {
    $uniqueBookings = array_unique($listallbookings, SORT_REGULAR);
    usort($uniqueBookings, "mySort");

    function check_in_range($start_date, $end_date, $date_from_user)
    {
        // Convert to timestamp
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $user_ts = strtotime($date_from_user);

        // Check that user date is between start & end
        return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }

    function getTime($time) {
        return date("D g:i A",strtotime($time));
    }

    function getDifference($t1, $t2) {
        return round((strtotime($t2) - strtotime($t1)) / 3600 );
    }


    $flag = false;
    $count = 0;
    foreach ($uniqueBookings as $key=>$value) {
        $eventname = $value[1];
        $eventstart = $value[2];
        $eventend = $value[3];

        //now is between one section
        if (check_in_range($eventstart, $eventend, date("Y-m-d\TH:i:s"))) {
            $endTime = (date('g:i A',strtotime($eventend)));
            echo "<div class=\"alert alert-danger col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1\" role=\"alert\">
            <h4 class=\"alert-heading text-center\">Now Booked! </h4>.
            <p class='text-center'> Current Event: $eventname </p>
            <p class='text-center'> Until Today $endTime </p>
      </div>";
            $count = $key+1;
            if ($count < sizeof($uniqueBookings)) {
                echo "<div class=\"alert alert-warning text-center col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1\" role=\"alert\">
                    Next Event: " . $uniqueBookings[$count][1] . "</br> From " .
                    getTime($uniqueBookings[$count][2]) . " to ". getTime($uniqueBookings[$count][3]) ."</div>";
            }
            $flag = true;
            break;
        }
    }

    $counter = 0;
    //now is available
    if (!$flag) {
        $found = false;
        foreach ($uniqueBookings as $key=>$value) {
            $eventstart = $value[2];
            $start_ts = strtotime($eventstart);
            $startTime = date("D g:i A",strtotime($eventstart));
            $counter = $key+1;
            if ($start_ts > strtotime(date("Y-m-d\TH:i:s"))) {
                $found = true;
            }

            if (check_in_range(date("Y-m-d\TH:i:s"),date("Y-m-d\T23:59:59"),$eventstart)) { // is in today
                if(getDifference(date("Y-m-d\TH:i:s"), $eventstart) < 1) {
                    $dif = "less than an hour!";
                } elseif(getDifference(date("Y-m-d\TH:i:s"), $eventstart) < 2) {
                    $dif = "around ".getDifference(date("Y-m-d\TH:i:s"), $eventstart)." hour!";
                } else {
                    $dif = "around ".getDifference(date("Y-m-d\TH:i:s"), $eventstart)." hours!";
                }
                echo "<div class=\"alert alert-success col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1\" role=\"alert\">
            <h4 class=\"alert-heading text-center\"> Now Available for $dif </h4>
            <p class='text-center'>( Until $startTime ) </p>
            </div>";
                $counter = $key;
                //print_r($uniqueBookings);
                    echo "<div class=\"alert alert-warning text-center col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1\" role=\"alert\">
                        Next Event: " . $uniqueBookings[$counter][1] . "</br> From " .
                        getTime($uniqueBookings[$counter][2]) . " to ". getTime($uniqueBookings[$counter][3]) ."</div>";
                break;
            } elseif($start_ts > strtotime(date("Y-m-d\T23:59:59"))) { //not in today
                echo "<div class=\"alert alert-success col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1\" role=\"alert\">
            <h4 class=\"alert-heading text-center\"> Available for the rest of the day! </h4>
            </div>";
                $counter = $key;
                if ($counter < sizeof($uniqueBookings)) {
                    echo "<div class=\"alert alert-warning text-center col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1\" role=\"alert\">
                        Next Event: " . $uniqueBookings[$counter][1] . "</br> From " .
                        getTime($uniqueBookings[$counter][2]) . " to ". getTime($uniqueBookings[$counter][3]) ."</div>";
                }
                break;
            }
        }

        if (!$found) {
            echo "<div class=\"alert alert-success col-12 col-sm-6 col-md-8 offset-md-2\" role=\"alert\">
                    <h4 class=\"alert-heading text-center\"> No next event scheduled! </h4>
                </div>";
        }
    }
}
?>

<br/><br/>
<h4 class="text-center"> For reservations, contact CGBC Admin Staff </h4>

</body>
</html>
