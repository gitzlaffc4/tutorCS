<?php
// We need to include these two files in order to work with the database
include_once('config.php');
include_once('dbutils.php');


// get a handle to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

// get data from the angular controller
// decode the json object
$data = json_decode(file_get_contents('php://input'), true);

//SESSION_T
//SESSION_ID INT(6) NOT NULL AUTO_INCREMENT,
//	TUTOR_HAWKID VARCHAR(255) NOT NULL,
//	COURSE_ID INT(8) NOT NULL,
//	SESSION_DATE DATE NOT NULL,
//	SESSION_START_TIME TIME NOT NULL,
//	SESSION_END_TIME TIME NOT NULL,
//	SCHEDULED VARCHAR(1) NOT NULL,

$SESSION_ID = $data['SESSION_ID'];
$TUTOR_HAWKID = $data['TUTOR_HAWKID'];
$SESSION_DATE = $data['SESSION_DATE'];
$SESSION_START_TIME = $data["SESSION_START_TIME"];
$SESSION_END_TIME = $data["SESSION_END_TIME"];
$SCHEDULED = $data["SCHEDULED"];

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
	$insertquery = "INSERT INTO SESSION_T (SESSION_ID, TUTOR_HAWKID, SESSION_DATE, SESSION_START_TIME, SESSION_END_TIME, SCHEDULED) VALUES ('$SESSION_ID', '$TUTOR_HAWKID', '$SESSION_DATE', '$SESSION_START_TIME', '$SESSION_END_TIME', '$SCHEDULED')";
	
	//Run the insert statement
	queryDB($insertquery, $db);
	
	// get the id of the avail we just entered
	$availid = mysql_insert_id($db);
	
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

