A digital sign designed for Harvard GSD rooms

The version without MySQL is deployed here: http://thegsd.rocks/EMS/?id=1

You need to specify id numbers to see results for each room

More Details:

In EMS_VARIABLES.php:

Defined the time zone, the API we are using, and username and password


In index.php:

Displays individual room's reservation info

Used PHP and Bootstrap CDN

Initialized a soap client and some parameters and get results from "simplexml_load_string".
Looped though the result and put info we need into a new array.

Wrote several helper functions:

mySort: sort by time
check_in_range: check if the time is in the given range
getTime: get a time in the given format
getDifference


In rooms.php:

Displays a panel that can select specific rooms and show their reservation info

The only difference from index.php is that it has a select box that allows users to choose which room to display








For the version that connected to MySQL in PHPMyAdmin:

Data Structure:
bookingid primarykey int not null
starttime TIMESTAMP not null
blength int not null

Click on the three buttons to add quick reservation for one, one and a half or two hours

Buttons are only going to work if you create the digitalSign database and the Bookings table in PHPMyAdmin

If you don't need add quick Reservation, go to check the non-MySQL version