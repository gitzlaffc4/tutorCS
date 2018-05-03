<?php

// We need to include these two files in order to work with the database
include_once('../../config.php');
include_once('dbutils.php');


// get a handle to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

// get data from the angular controller
// decode the json object
$data = json_decode(file_get_contents('php://input'), true);


// get each piece of data

// 
$HAWKID = $data['HAWKID'];
$PASSWORD = $data['PASSWORD'];
$EMAIL = $data['EMAIL'];
$ROLE = $data['ROLE'];

// set up variables to handle errors
// is complete will be false if we find any problems when checking on the data
$isComplete = true;

// error message we'll send back to angular if we run into any problems
$errorMessage = "";

//
// Validation
//

// check if HAWKID meets criteria
if (!isset($HAWKID) || (strlen($HAWKID) < 2)) {
    $isComplete = false;
    $errorMessage .= "Please enter a HAWKID with at least two characters. ";
} else {
    $HAWKID = makeStringSafe($db, $HAWKID);
}

if (!isset($PASSWORD) || (strlen($PASSWORD) < 6)) {
    $isComplete = false;
    $errorMessage .= "Please enter a password with at least six characters. ";
}  
if (!isset($EMAIL) || (strlen($EMAIL) < 13)) {
    $isComplete = false;
    $errorMessage .= "Please enter a valid email. ";
}  
if (!isset($ROLE) || (strlen($ROLE) == null)) {
    $isComplete = false;
    $errorMessage .= "Please select a role. ";
}  

// check if we already have a HAWKID that matches the one the user entered
if ($isComplete) {
    // set up a query to check if this HAWKID is in the database already
    $query = "SELECT HAWKID FROM ACCOUNT_T WHERE HAWKID='$HAWKID'";
    
    // we need to run the query
    $result = queryDB($query, $db);
    
    // check on the number of records returned
    if (nTuples($result) > 0) {
        // if we get at least one record back it means the HAWKID is taken
        $isComplete = false;
        $errorMessage .= "The HAWKID $HAWKID is already taken. Please select a different HAWKID. ";
    }
}

// if we got this far and $isComplete is true it means we should add the player to the database
if ($isComplete) {
	
	
    // create a hashed version of the password
    $HASHEDPASS = crypt($PASSWORD, getSalt());
	
	// define blank roles
	$STUDENT = 0;
	$TUTOR = 0;
	$PROFESSOR = 0;
	$ADMIN = 0;

	// set role values to 1 based on selection
	if ($ROLE = 'Student'){
		$STUDENT = 1;
	}
	if ($ROLE = 'Tutor'){
		$TUTOR = 1;
	}
	if ($ROLE = 'Professor'){
		$PROFESSOR = 1;
	}
	if ($ROLE = 'Admin'){
		$ADMIN = 1;
	}
	if ($ROLE = 'StudentTutor'){
		$STUDENT = 1;
		$TUTOR = 1;
	}
	if ($ROLE = 'ProfessorAdmin'){
		$PROFESSOR = 1;
		$ADMIN = 1;
	}
	
    
    // we will set up the insert statement to add this new record to the database
    $insertquery = "INSERT INTO ACCOUNT_T(HAWKID, HASHEDPASS, STUDENT, TUTOR, PROFESSOR, ADMIN) VALUES ('$HAWKID', '$HASHEDPASS','$STUDENT','$TUTOR','$PROFESSOR','$ADMIN')";
    
    // run the insert statement
    queryDB($insertquery, $db);
    
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