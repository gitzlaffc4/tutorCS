<?php
// We need to include these two files in order to work with the database
include_once('config.php');
include_once('dbutils.php');


// get a handle to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

// get data from the angular controller
// decode the json object
$data = json_decode(file_get_contents('php://input'), true);


// get user that is logged in
session_start();
$HAWKID = $_SESSION['HAWKID'];
$SESSIONID = $data['SESSIONID'];
$COURSE_ID = $data['COURSE_ID'];

// Set up variables to handle errors
// is complete will be false if we findany problems when checkin on the data
$isComplete = true;

// error message we'll send back to angular if we run into any prolems
$errorMessage = "";

// D O  T H I S  L A T E R
//Validation
//

// check if we already have a avail time with the tutor

// Add availability to the database

if($isComplete) {
	// query to update session_t to reflect change in schedule status
	$sessionUpdate = "UPDATE SESSION_T SET SCHEDULED = 'Y' WHERE SESSION_ID = $SESSIONID;";
	queryDB($sessionUpdate, $db);
	
	// remove one allocated session from students enrollment for course being reserved
	$allocSessionUpdate = "UPDATE ENROLLED_T SET ALLOC_SESSIONS = ALLOC_SESSIONS - 1 WHERE HAWKID = '$HAWKID' AND COURSE_ID = $COURSE_ID;";
	queryDB($allocSessionUpdate, $db);
	
	
	// insert reserved session into scheduled_t
	$shedInsert = "INSERT INTO SCHEDULED_T(SESSION_ID, STUD_HAWKID) VALUES ($SESSIONID, '$HAWKID');";
	//Run the insert statement
	queryDB($shedInsert, $db);
	
	// send a response back to angular
	$response = array();
	$response['status'] = 'success';
	$response['id'] = $availid;
	header('Content-Type: application/json');
	echo(json_encode($response));
} else {
	// there's been an error. We need to report it to the angular controller.
	
	// one of the things we want to send back is the data that this php file received
	ob_start();
	var_dump($data);
	$postdump = ob_get_clean();
	
	// set up our response array
	$response = array();
	$response['status'] = 'error';
	$response['message'] = $errorMessage . $postdump;
	header('Content-Type: application/json');
	echo(json_encode($response));
}
?>
