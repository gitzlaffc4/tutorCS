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

// 'name' matches the name attribute in the form
$HAWKID = $data['HAWKID'];


// set up variables to handle errors
// is complete will be false if we find any problems when checking on the data
$isComplete = true;

// if we got this far and $isComplete is true it means we should delete the USER from the database
if ($isComplete) {
    // we will set up the delate statement to remove the user from the database
    $deletequery = "UPDATE ACCOUNT_T SET ACCESS='0' WHERE HAWKID='$HAWKID';";
	
    
    // run the delete statement
    queryDB($deletequery, $db);
        
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