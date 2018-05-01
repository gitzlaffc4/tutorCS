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


// get each piece of data

// '' matches the '' attribute in the for
$id = $data['id'];

//set up variables to handle errors
// is complete will be false if we find any problems when checking on the data
$isComplete = true;

// error message we'll send back to angular if we run into any problems.
$errorMessage = "";

// check if logged in
// F I X ! ! !

// 
//Validation
//

if (!isset($id) && ((int)$id == $id)) {
	$isComplete = false;
	$errorMessage .= "the tutorcancelseesion.php requires an integer id to be sent.";
	
}

// chekc if there is a record in th database matching the id
if ($isComplete) {
	// set up a query to chekc if the id passed to this file correspons to a record in the database
	$deletequery = "SELECT HAWKID FROM AVAILABILITY_T WHERE AVAILSLOTID=$id";
	
	//run the delete statement
	queryDB($deletequery, $db);
	
	//send a response back to angular
	$response = array();
	$response['status'] = 'success';
	header('Content-Type: application/json');
	echo(json_encode($response));
	
	// set up our response array
	$response = array();
	$response['status'] = 'error';
	$response['message'] = $errorMessage . $postdump;
	header('Content-Type: application/json');
	echo(json_encode($response));
}

?>