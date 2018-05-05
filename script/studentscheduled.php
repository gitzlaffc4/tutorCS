 <?php

// We need to include these two files in order to work with the database
include_once('config.php');
include_once('dbutils.php');

// get a connection to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);
	
session_start();
// obtain HAWKID from Session variable
$HAWKID = $_SESSION['HAWKID'];

// set up a query to get information on user
$queryScheduled = "SELECT * FROM SCHEDULED_T INNER JOIN SESSION_T ON SCHEDULED_T.SESSION_ID = SESSION_T.SESSION_ID INNER JOIN COURSE_T ON SESSION_T.COURSE_ID = COURSE_T.COURSE_ID INNER JOIN TUTOR_T ON SESSION_T.TUTOR_HAWKID = TUTOR_T.HAWKID INNER JOIN USER_T ON TUTOR_T.HAWKID = USER_T.HAWKID;";

// run the query to get info on user
$resultsched = queryDB($queryScheduled, $db);
$userinfo = array();
$i = 0;
while ($currSession = nextTuple($resultsched)){
 	$schedinfo[$i] = $currSession;
	
 	$i++;
	
}
// put together a JSON object to send back the data on the players
$response = array();
$response['status'] = 'success';
$response['value']['schedinfo'] = $schedinfo;
header('Content-Type: application/json');
echo(json_encode($response));

?> 