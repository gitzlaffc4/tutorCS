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

// set up a query to get all professors HawkID's
$queryProfessors = "SELECT HAWKID, FIRSTNAME, LASTNAME FROM USER_T WHERE PROFESSOR = '1';";

// run the query to get info on user
$resultuser = queryDB($queryuser, $db);
$userinfo = array();
$i = 0;
while ($currUser = nextTuple($resultuser)){
 	$userinfo[$i] = $currUser;
	$profilePic = $userinfo[$i]['PICTURE'];
	$userinfo[$i]['PROFILEPIC'] ="<img src='images/profilepictures/$profilePic' class='img-circle center-block'  alt='profile pic' width='190' height='190'>";
 	$i++;	
}

// RUN THE QUERY TO GET ALL PROFESSORS HAWKID'S
$profresult = queryDB($queryProfessors, $db);
$professors = array();
$j = 0;
while ($currProf = nextTuple($profresult)){
	$professors[$j] = $currProf;
	$j++;
}


// put together a JSON object to send back the data on the players
$response = array();
$response['status'] = 'success';
$response['value']['userinfo'] = $userinfo;
$response['value']['professors'] = $professors;
header('Content-Type: application/json');
echo(json_encode($response));

?> 