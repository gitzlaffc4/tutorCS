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

// 'HAWKID' matches the name attribute in the form
$HAWKID = $data['HAWKID'];
// 'COURSE_ID' matches the name attribute in the form
$COURSE_ID = $data['COURSE_ID'];
// set up variables to handle errors
// is complete will be false if we find any problems when checking on the data
$isComplete = true;

// if we got this far and $isComplete is true it means we should delete the USER from the database
if ($isComplete) {
	// set up a query to get remove 1 alloc_session from the user's enrollment
	$queryAddAllocSession = "UPDATE ENROLLED_T SET ALLOC_SESSIONS = ALLOC_SESSIONS + 1 WHERE HAWKID = '$HAWKID' AND COURSE_ID = '$COURSE_ID';";

	// run the query
	queryDB($queryAddAllocSession, $db);
        
    // send a response back to angular
    $response = array();
    $response['status'] = 'success';
    header('Content-Type: application/json');
    echo(json_encode($response));    
} else {
    // there's been an error. We need to report it to the angular controller.
    
    // one of the things we want to send back is the data that his php file received
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