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
$AVAILSLOTID = $data['AVAILSLOTID'];

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

if (!isset($AVAILSLOTID) && ((int)$AVAILSLOTID == $AVAILSLOTID)) {
	$isComplete = false;
	$errorMessage .= "the tutorcancelseesion.php requires an integer id to be sent.";
	
}

// chekc if there is a record in th database matching the id
if ($isComplete) {
	// set up a query to chekc if the id passed to this file correspons to a record in the database
	$query = "SELECT HAWKID FROM AVAILABILITY_T WHERE AVAILSLOTID=$AVAILSLOTID";
	
	// run the check query
	$result = queryDB($query, $db);
	
	// check on the number of records returned
	if (nTuples($result) == 0) {
		// if we bet no records back, it means no records match the	AVAILSLOTID that we get
		$isComplete = false;
		$errorMessage .= "the AVAILSLOTID $AVAILSLOTID did not match any records in th AVAILABILITY_T table.";
	}
}

// if we got this far and $isComplete is true it means we should delete the AVAILABILITY_T
if ($isComplete) {
	
	//we will set up the delete statement to remove the movie from the database
	$deletequery = "DELETE FROM AVAILABILITY_T WHERE AVAILSLOTID=$AVAILSLOTID";
	//run the delete statement
	queryDB($deletequery, $db);
	
	//send a response back to angular
	$response = array();
	$response['status'] = 'success';
	header('Content-Type: application/json');
	echo(json_encode($response));
} else {
	// there's been an error. We need to report it the angular controller.
	
	// one of the things we want to send back is the data that this php file recieved.
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