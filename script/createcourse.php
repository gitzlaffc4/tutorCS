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
$COURSE_NAME = $data['COURSE_NAME'];
$SEMESTER = $data['SEMESTER'];
$PROFESSOR_1 = $data['PROFESSOR_1'];
$PROFESSOR_2 = $data['PROFESSOR_2'];
$PROFESSOR_3 = $data['PROFESSOR_3'];


// set up variables to handle errors
// is complete will be false if we find any problems when checking on the data
$isComplete = true;

// error message we'll send back to angular if we run into any problems
$errorMessage = "";

//
// Validation
//

// check if data meets criteria
if (!isset($COURSE_NAME) || (strlen($COURSE_NAME) < 4)) {
    $isComplete = false;
    $errorMessage .= "Please enter a valid Course Name";
}

if (!isset($SEMESTER) || (strlen($SEMESTER) < 9)) {
    $isComplete = false;
    $errorMessage .= "Please enter a valid semester.";

}
if (!isset($PROFESSOR_1) || (strlen($PROFESSOR_1) == null)) {
    $isComplete = false;
    $errorMessage .= "Please select at least one professor ";
}    

// check if professor_2 or professor_3 is == to professor_1

if ($PROFESSOR_2 == $PROFESSOR_1){
	$isComplete = false;
    $errorMessage .= "Please select two different professors";
}

if ($PROFESSOR_3 == $PROFESSOR_1){
	$isComplete = false;
    $errorMessage .= "Please select two different professors";
}

if ($PROFESSOR_3 != null){
		if($PROFESSOR_3 == $PROFESSOR_2){
			$isComplete = false;
			$errorMessage .= "Please select two different professors";
		}
}


// check if we already have a Course that matches the one the user entered
if ($isComplete) {
    // set up a query to check if this HAWKID is in the database already
    $query = "SELECT COURSE_ID FROM COURSE_T WHERE COURSE_NAME='$COURSE_NAME' AND SEMESTER = '$SEMESTER';";
    
    // we need to run the query
    $result = queryDB($query, $db);
	
    
    // check on the number of records returned
    if (nTuples($result) > 0) {
        // if we get at least one record back it means the HAWKID is taken
        $isComplete = false;
        $errorMessage .= "The course name is already taken. Please enter a different Course Name. ";
    }
}

// if we got this far and $isComplete is true it means we should add the player to the database
if ($isComplete) {
	// insert new course into course_T
	$newCourse = "INSERT INTO COURSE_T(COURSE_NAME, SEMESTER) VALUES ('$COURSE_NAME','$SEMESTER');";
	queryDB($newCourse, $db);
	
	// get new courseID from auto increment
	$getNewCourseID = "SELECT MAX(COURSE_ID) FROM COURSE_T;";
	$IDResult = queryDB($getNewCourseID, $db);
	while ($currCourseID = nextTuple($IDResult)) {
		$CourseID = $currCourseID['MAX(COURSE_ID)'];
	}
	// insert professor_1 into teaches_t with new course_ID	   
	$newTeaches = "INSERT INTO TEACHES_T (HAWKID, COURSE_ID) VALUES ('$PROFESSOR_1','$CourseID');";
	queryDB($newTeaches, $db);
	
	if ($PROFESSOR_2 != null){
		$newTeaches2 = "INSERT INTO TEACHES_T (HAWKID, COURSE_ID) VALUES ('$PROFESSOR_2','$CourseID');";
		queryDB($newTeaches2, $db);
	}
	if ($PROFESSOR_3 != null){
		$newTeaches3 = "INSERT INTO TEACHES_T (HAWKID, COURSE_ID) VALUES ('$PROFESSOR_3','$CourseID');";
		queryDB($newTeaches3, $db);
	}
    
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