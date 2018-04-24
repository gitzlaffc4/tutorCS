 <?php

// We need to include these two files in order to work with the database
include_once('../../config.php');
include_once('dbutils.php');

// get a connection to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);
	
session_start();
// obtain HAWKID from Session variable
$HAWKID = $_SESSION['HAWKID'];

// set up a query to get information on user
$queryuser = "SELECT * FROM USER_T WHERE HAWKID = '$HAWKID';";
// run the query to get info on user
$resultuser = queryDB($queryuser, $db);
$userinfo = array();
$i = 0;
while ($currUser = nextTuple($resultuser)){
 	$userinfo[$i] = $currUser;
 	$i++;
}


// put together a JSON object to send back the data on the players
$response = array();
$response['status'] = 'success';
$response['value']['userinfo'] = $userinfo;
header('Content-Type: application/json');
echo(json_encode($response));

?> 