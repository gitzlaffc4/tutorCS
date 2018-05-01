<?php
// We need to include these two files in order to work with the database
include_once('config.php');
include_once('dbutils.php');


// get a handle to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

// get data from the angular controller
// decode the json object
$data = json_decode(file_get_contents('php://input'), true);

// get each piece of data
//AVAILSLOTID	"1"
//HAWKID	"cmurphy3_t"
//DAY_OF_WEEK	"Monday"
//AVAILABLE	"1"
//TIME_START	"11:00:00"
//TIME_STOP	"12:00:00"

$AVAILSLOTID = $data['AVAILSLOTID'];
$HAWKID = $data['HAWKID'];
$DAY_OF_WEEK = $data['DAY_OF_WEEK'];
$AVAILABLE = $data["AVAILABLE"];
$TIME_START = $data["TIME_START"];
$TIME_STOP = $data["TIME_STOP"];

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
	$insertquery = "INSERT INTO AVAILABILITY_T (AVAILSLOTID, HAWKID, DAY_OF_WEEK, AVAILABLE, TIME_START, TIME_STOP) VALUES ('$AVAILSLOTID', '$HAWKID', '$DAY_OF_WEEK', '$AVAILABLE', '$TIME_START', '$TIME_STOP');";
	
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