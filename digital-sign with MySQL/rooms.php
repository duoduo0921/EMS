<!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <title>Rooms Info</title>
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
      <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </head>

  <body>

  <?php
  // EMS Variables defined as constants
  try {
      require 'EMS_VARIABLES.php';
  } catch (Exception $e) {
      exit('Require failed! Error: '.$e);
  }

  date_default_timezone_set(GSD_API_EVENTS_DEFAULT_TIMEZONE);
  // define new soap client object
  $client = new SoapClient(SERT_SERVICE_URL);
  // gets all rooms info
  $roomsparams =  array('UserName' => SERT_USERNAME, 'Password' => SERT_PASSWORD, 'BuildingID' => -1);
  $rooms = simplexml_load_string($client->GetAllRooms($roomsparams)->GetAllRoomsResult);
  $roomInfo=array();
  foreach ($rooms as $value) {
      $id = (string)$value->ID;
      $des = (string)$value->Description;
      array_push($roomInfo, [$id, $des]);
  }
  sort($roomInfo);
  array_shift($roomInfo);
  ?>


  <div class="container-fluid">
      <h2 class="text-center col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1" >

          <form action="#" method="post">
              <select name="Room">
                  <option value=""></option>
                  <?php
                  foreach($roomInfo as $key => $value):
                      echo '<option value="'.$key.'">'.$value[1].'</option>';
                  endforeach;
                  ?>
              </select>
              <button type="submit" name="submit" class="btn btn-dark">Get Bookings</button>
          </form>
          <?php
          if(isset($_POST['submit'])){
              $selected_val = $_POST['Room'];
              echo "Room :" .$roomInfo[$selected_val][1];
          }
          ?>
      </h2><br/>
  </div>

  <?php
  $rid = 1;
  if(isset($_POST['submit'])){
      $selected_val = $_POST['Room'];
      $rid = $roomInfo[$selected_val][0];
  }

  $bookingparams =  array('UserName' => SERT_USERNAME, 'Password' => SERT_PASSWORD,
      'StartDate' => date( 'Y-m-d\T00:00:00' ), 'EndDate' => date("Y-m-d\TH:i:s",time()+(2 * 24 * 60 * 60)),
      'RoomID' => $rid, 'ViewComboRoomComponents' => TRUE);
  $bookinginfo = simplexml_load_string($client->GetAllRoomBookings($bookingparams)->GetAllRoomBookingsResult);

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

  function sortByTime($a, $b) {
      $a = $a[2];
      $b = $b[2];
      if (strtotime($a) == strtotime($b))return 0;
      return (strtotime($a) < strtotime($b)) ? -1 : 1;
  }

  function getTime($time) {
      return date("D g:i A",strtotime($time));
  }

  if (sizeof($listallbookings) == 0) {
      echo "<div class=\"alert alert-success col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1\" role=\"alert\">
      <h4 class=\"alert-heading text-center\"> Available in two days! </h4>
  </div>";
      return;
  } else {
      $uniqueBookings = array_unique($listallbookings, SORT_REGULAR);
      usort($uniqueBookings, "sortByTime");

      function check_in_range($start_date, $end_date, $date_from_user)
      {
          // Convert to timestamp
          $start_ts = strtotime($start_date);
          $end_ts = strtotime($end_date);
          $user_ts = strtotime($date_from_user);

          // Check that user date is between start & end
          return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
      }

      $flag = false;
      $count = 0;
      foreach ($uniqueBookings as $key=>$value) {
          $eventname = $value[1];
          $eventstart = $value[2];
          $eventend = $value[3];
          $endTime = getTime($eventend);

          if (check_in_range($eventstart, $eventend, date("Y-m-d\TH:i:s"))) {
              echo "<div class=\"alert alert-danger col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1\" role=\"alert\">
            <h4 class=\"alert-heading text-center\">Now Booked! </h4>
            <p class='text-center'> $eventname </p>
            <p class='text-center'> Until $endTime </p>
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

      if (!$flag) {
          foreach ($uniqueBookings as $key=>$value) {
              $eventstart = $value[2];
              $start_ts = strtotime($eventstart);
              $startTime = getTime($eventstart);
              if ($start_ts >= strtotime(date("Y-m-d\TH:i:s"))) {
                  echo "<div class=\"alert alert-success col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1\" role=\"alert\">
            <h4 class=\"alert-heading text-center\"> Now Available! </h4>
            <p class='text-center'> Until $startTime  </p>
            </div>";
                  $count = $key;
                  if ($count < sizeof($uniqueBookings)) {
                      echo "<div class=\"alert alert-warning text-center col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1\" role=\"alert\">
                        Next Event: " . $uniqueBookings[$count][1] . "</br> From " .
                          getTime($uniqueBookings[$count][2]) . " to ". getTime($uniqueBookings[$count][3]). "</div>";
                  }
                  break;
              }
          }
      }

      foreach ($uniqueBookings as $outputpiece) {
          $output .= "<tr class=\"table-secondary col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1\">" . "<td>" . $outputpiece[0] . "</td>" .
              "<td>" . $outputpiece[1] . "</td>" .
              "<td>" . getTime($outputpiece[2]) . "</td>" .
              "<td>" . getTime($outputpiece[3]) . "</td>" .
              "<td>" . $outputpiece[4] . "</td>" . "<tr>";

      }

      echo "<div class=\"container-fluid\"><h3 class='text-center col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1'>Next two days: </h3>
  <table class=\"table table-bordered col-xs-12 col-sm-10 col-md-8 offset-md-2 offset-sm-1\">
      <thead class=\"thead-dark\">
      <tr>
          <th>Group Name</th>
          <th>Event Name</th>
          <th>Start Time</th>
          <th>End Time</th>
          <th>Contact</th>
      </tr>
      </thead>
      <tbody>
      $output
      </tbody>
  </table></div>";
  }
  ?>

  </body>
  </html>
