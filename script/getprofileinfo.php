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
$queryuser = "SELECT * FROM USER_T WHERE HAWKID = '$HAWKID';";
// run the query to get info on user
$resultuser = queryDB($queryuser, $db);
$userinfo = array();
$i = 0;
while ($currUser = nextTuple($resultuser)){
 	$userinfo[$i] = $currUser;
	$profilePic = $userinfo[$i]['PICTURE'];
	$userinfo[$i]['PROFILEPIC'] ="<img src='images/profilepictures/$profilePic' class='img-circle center-block'  alt='profile pic' width='190' height='190'>";
	
	$currHawkID = $userinfo[$i]['HAWKID'];
	// if ($userinfo[$i]['STUDENT'] == '1'){
	//	$upcomingQuery = "SELECT * FROM SCHEDULED_T WHERE STUD_HAWKID = '$currHawkID' AND COMPLETED = '0';";
	//	$resultUpcoming = queryDB($upcomingQuery, $db);
	//	$upcoming = array();
	//	$j = 0;
	//	if (nTuples($resultUpcoming) > 0){
			
	//	}
	
 	$i++;
	
}

// set up query to get information on students who have upcoming appointments



// put together a JSON object to send back the data on the players
$response = array();
$response['status'] = 'success';
$response['value']['userinfo'] = $userinfo;
header('Content-Type: application/json');
echo(json_encode($response));

?> 