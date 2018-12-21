<?php

$mysqli = new mysqli("localhost", "root", "root", "digitalSign");

    if(isset($_POST['addOne'])) {
        $sql = "INSERT INTO Bookings (bookingid, starttime, blength) VALUES(1, NOW(), 60) "; 
        $mysqli->query($sql) or die($mysqli->error); 
    }

    if(isset($_POST['addOneHalf'])) {
        $sql = "INSERT INTO Bookings (bookingid, starttime, blength) VALUES(1, NOW(), 90) "; 
        $mysqli->query($sql) or die($mysqli->error); 
    }

    if(isset($_POST['addTwo'])) {
        $sql = "INSERT INTO Bookings (bookingid, starttime, blength) VALUES(1, NOW(), 120) "; 
        $mysqli->query($sql) or die($mysqli->error); 
    }

$result = $mysqli->query("SELECT bookingid, starttime, blength FROM Bookings");

// is able to show data from result
// if($result->num_rows > 0){
//     while($row = $result->fetch_assoc()){
//         echo "<br> id : ". $row["bookingid"]. "<br> Starttime : " . $row["starttime"]. "<br> Length : " . $row["blength"]. "<br>";
//     }
//     echo "succeed";
// } else {
//     echo "0 result found";
// }


?>