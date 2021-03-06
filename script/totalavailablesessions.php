<?php

// We need to include these two files in order to work with the database
include_once('config.php');
include_once('dbutils.php');


// get a handle to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);



// set up a query to get information on AVAILABILITY_T
$query = "SELECT COUNT(AVAILABLE) FROM AVAILABILITY_T WHERE AVAILABLE='1' ;";

// run the query to get info on AVAILABILITY_T
$result = queryDB($query, $db);

// assign results to an array we can then send back to whomever called
$avail = array();
$i = 0;

// go through the results one by one
while ($currAvail = nextTuple($result)) {
    $avail[$i] = $currAvail; 
    $i++;
}

// put together a JSON object to send back the data on the available timesS
$response = array();
$response['status'] = 'success';
$response['value']['avail'] = $avail;
header('Content-Type: application/json');
echo(json_encode($response));


?>