<?php

// We need to include these two files in order to work with the database
include_once('config.php');
include_once('dbutils.php');


// get a handle to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

session_start();
$currentUser = $_SESSION['HAWKID'];

// set up a query to get information on movies
$Availquery = "SELECT * FROM AVAILABILITY_T;";
// run the query to get info on availability
$result = queryDB($Availquery, $db);

$tutorsQuery = "SELECT COURSE_T.*, TUTORS_T.* FROM TUTORS_T, COURSE_T WHERE TUTORS_T.COURSE_ID = COURSE_T.COURSE_ID AND TUTORS_T.HAWKID = '$currentUser';";
$tutorResult = queryDB($tutorsQuery, $db);



// assign results to an array we can then send back to whomever called
$avail = array();
$i = 0;

// go through the results one by one
while ($currAvail = nextTuple($result)) {
    $avail[$i] = $currAvail; 
    $i++;
}


// assign results to an array we can then send back to whomever called
$tutorCourses = array();
$h = 0;

// go through the results one by one
while ($currCourse = nextTuple($tutorResult)) {
    $tutorCourses[$h] = $currCourse; 
    $h++;
}


// put together a JSON object to send back the data on the available timesS
$response = array();
$response['status'] = 'success';
$response['value']['avail'] = $avail;
$response['value']['tutorCourses'] = $tutorCourses;
header('Content-Type: application/json');
echo(json_encode($response));


?>