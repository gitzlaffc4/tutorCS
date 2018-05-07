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

// 
$COURSE_ID = $data['COURSE_ID'];
$TUTORLIST = $data['TUTORS'];

// set up variables to handle errors
// is complete will be false if we find any problems when checking on the data
$isComplete = true;

// error message we'll send back to angular if we run into any problems
$errorMessage = "";

//
// Validation
//

if (!isset($COURSE_ID) || (strlen($COURSE_ID) < 1)) {
    $isComplete = false;
    $errorMessage .= "Please select a course to enroll hire tutors for";
}


$NumberTutors = count($TUTORLIST);
if ($NumberTutors < 1){
	$isComplete = false;
	$errorMessage .= "Please select at least one tutor";
}


// if we got this far and $isComplete is true it means we should add the player to the database
if ($isComplete) {
	
	
	$s = 0;
	while ($s < $NumberTutors){
		$currTutor = $TUTORLIST[$s];
		
		
		$query = "SELECT HAWKID FROM TUTORS_T WHERE HAWKID='$currTutor' AND COURSE_ID = $COURSE_ID;";
    
		// we need to run the query
		$result = queryDB($query, $db);

		// check on the number of records returned
		if (nTuples($result) > 0) {
			$s++;
		}else {
			// we will set up the insert statement to add this new record to the database
			$hireTutor = "INSERT INTO TUTORS_T(HAWKID, COURSE_ID) VALUES ('$currTutor', $COURSE_ID);";

			// run the insert statement
			queryDB($hireTutor, $db);
			$s++;
		}
		
		
	}

    
    // send a response back to angular
    $response = array();
    $response['status'] = 'success';
    header('Content-Type: application/json');
    echo(json_encode($response));    
}
else {
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