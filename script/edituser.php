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
$FIRSTNAME = $data['FIRSTNAME'];
$LASTNAME = $data['LASTNAME'];
$NICK_NAME = $data['NICK_NAME'];
$PHONE_NUMBER = $data['PHONE_NUMBER'];

// set up variables to handle errors
// is complete will be false if we find any problems when checking on the data
$isComplete = true;

// error message we'll send back to angular if we run into any problems
$errorMessage = "";

// check if they are logged in
session_start();
if (!isset($_SESSION['HAWKID'])) {
    // if the session variable username is not set, then the user is not logged in and should not edit the player
    $isComplete = false;
    $errorMessage .= "User is not logged in.";
}

// check to see if profile that is being updated is user that is logged in 
$currentUser = $_SESSION['HAWKID'];
if ($currentUser != $HAWKID){
	$isComplete = false;
    $errorMessage .= "You can only update your own profile.";
} 
//
// Validation
//
if ($isComplete) {
    // check if name meets criteria
    if (!isset($FIRSTNAME) || (strlen($FIRSTNAME) < 2)) {
        $isComplete = false;
        $errorMessage .= "Please enter a firstname with at least 2 characters. ";
    }
    
    if (!isset($LASTNAME) || (strlen($LASTNAME) < 2)) {
        $isComplete = false;
        $errorMessage .= "Please enter a lastname with at least two characters. ";
    } 
    
    if (!isset($NICK_NAME) || (strlen($NICK_NAME) < 1)) {
        $isComplete = false;
        $errorMessage .= "Please enter a prefered name that is at least 1 character ";
    } 
    if (!isset($PHONE_NUMBER) || (strlen($PHONE_NUMBER) < 10)) {
        $isComplete = false;
        $errorMessage .= "Please enter a valid phone number";
    }
} 




// check if the hawkid passed to this api corresponds to an existing record in the database
if ($isComplete) {
    // set up a query to check if this player is in the database already
    $query = "SELECT FIRSTNAME FROM USER_T WHERE HAWKID = $HAWKID";
    
    // we need to run the query
    $result = queryDB($query, $db);
    
    // check on the number of records returned
    if (nTuples($result) == 0) {
        // if we get no results it means the id we got does not correspond to any records in the USER_T table
        $isComplete = false;
        $errorMessage .= "The HawkID $HawkID does not correspond to any user.";
    }
}


// if we got this far and $isComplete is true it means we should edit the user in the database
if ($isComplete) {
    // we will set up the insert statement to add this new record to the database
    $updatequery = "UPDATE USER_T SET FIRSTNAME = '$FIRSTNAME', LASTNAME = '$LASTNAME', NICK_NAME = '$NICK_NAME', PHONE_NUMBER='$PHONE_NUMBER' WHERE HAWKID = '$currentUser';";
    
    // run the update statement
    queryDB($updatequery, $db);
    
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