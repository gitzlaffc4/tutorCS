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

$openQuery = "SELECT SESSION_T.*, USER_T.* FROM SESSION_T, USER_T WHERE SESSION_T.SCHEDULED = 'N' AND SESSION_T.TUTOR_HAWKID = USER_T.HAWKID;";
$openResult = queryDB($openQuery, $db);

$studentEnrollment = "SELECT ENROLLED_T.*, COURSE_T.* FROM ENROLLED_T, COURSE_T WHERE ENROLLED_T.COURSE_ID = COURSE_T.COURSE_ID AND ENROLLED_T.HAWKID = '$currentUser';";
$studentResult = queryDB($studentEnrollment, $db);



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

$openSessions = array();
$s = 0;
while ($currSess = nextTuple($openResult)){
	$profilePic = $currSess['PICTURE'];
	$currSess['PROFILEPIC'] ="<img src='images/profilepictures/$profilePic' class='img-circle center-block'  alt='profile pic' width='50' height='50'>";
	$openSessions[$s] = $currSess;
	$s++;
}

$studentEnroll = array();
$e = 0;
while ($currCourse = nextTuple($studentResult)){
	$studentEnroll[$e] = $currCourse;
	$e++;
}



// put together a JSON object to send back the data on the available timesS
$response = array();
$response['status'] = 'success';
$response['value']['avail'] = $avail;
$response['value']['tutorCourses'] = $tutorCourses;
$response['value']['openSessions'] = $openSessions;
$response['value']['studentEnroll'] = $studentEnroll;
header('Content-Type: application/json');
echo(json_encode($response));


?>