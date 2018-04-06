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
$username = $data['username'];
$password = $data['password'];

// set up variables to handle errors
// is complete will be false if we find any problems when checking on the data
$isComplete = true;

// error message we'll send back to angular if we run into any problems
$errorMessage = "";

//
// Validation
//

// check if username meets criteria
if (!isset($username) || (strlen($username) < 2)) {
    $isComplete = false;
    $errorMessage .= "Please enter a username with at least two characters. ";
} else {
    $username = makeStringSafe($db, $username);
}

if (!isset($password) || (strlen($password) < 6)) {
    $isComplete = false;
    $errorMessage .= "Please enter a password with at least six characters. ";
}  


// check if we already have a username that matches the one the user entered
if ($isComplete) {
    // set up a query to check if this username is in the database already
    $query = "SELECT id FROM account WHERE username='$username'";
    
    // we need to run the query
    $result = queryDB($query, $db);
    
    // check on the number of records returned
    if (nTuples($result) > 0) {
        // if we get at least one record back it means the username is taken
        $isComplete = false;
        $errorMessage .= "The username $username is already taken. Please select a different username. ";
    }
}

// if we got this far and $isComplete is true it means we should add the player to the database
if ($isComplete) {
    // create a hashed version of the password
    $hashedpass = crypt($password, getSalt());
    
    // we will set up the insert statement to add this new record to the database
    $insertquery = "INSERT INTO account(username, hashedpass) VALUES ('$username', '$hashedpass')";
    
    // run the insert statement
    queryDB($insertquery, $db);
    
    // get the id of the account we just entered
    $accountid = mysqli_insert_id($db);
    
    // send a response back to angular
    $response = array();
    $response['status'] = 'success';
    $response['id'] = $accountid;
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