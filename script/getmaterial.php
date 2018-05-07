 <?php

// We need to include these two files in order to work with the database
include_once('config.php');
include_once('dbutils.php');

// get a connection to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);
	
session_start();
// obtain HAWKID from Session variable
$HAWKID = $_SESSION['HAWKID'];

// set up a query to get information on user
$QueryCourse = "SELECT TEACHES_T.*, COURSE_T.*, MATERIALS_T.* FROM TEACHES_T, COURSE_T, MATERIALS_T WHERE TEACHES_T.HAWKID = '$HAWKID' AND COURSE_T.COURSE_ID = TEACHES_T.COURSE_ID AND MATERIALS_T.COURSE_ID = COURSE_T.COURSE_ID;";


// run the query to get info on user
$MaterialsResult = queryDB($QueryCourse, $db);
$Materials = array();
$i = 0;
while ($currMaterial = nextTuple($MaterialsResult)){
 	$Materials[$i] = $currMaterial;
 	$i++;	
}


$enrollmentQuery = "SELECT COURSE_T.* FROM TEACHES_T, COURSE_T WHERE TEACHES_T.COURSE_ID = COURSE_T.COURSE_ID AND TEACHES_T.HAWKID = '$HAWKID';";
$enrollmentResult = queryDB($enrollmentQuery, $db);

$enrollmentInfo = array();
$n = 0;
while ($currCourse = nextTuple($enrollmentResult)){
	$enrollmentInfo[$n] = $currCourse;
	$n++;
}

$courseQuery = "SELECT * FROM COURSE_T;";
$courseResult = queryDB($courseQuery, $db);

$courseInfo = array();
$b = 0;
while($aCourse = nextTuple($courseResult)){
	$courseInfo[$b] = $aCourse;
	$b++;
}




// put together a JSON object to send back the data on the players
$response = array();
$response['status'] = 'success';
$response['value']['Materials'] = $Materials;
$response['value']['enrollmentInfo'] = $enrollmentInfo;
$response['value']['courseInfo'] = $courseInfo;
header('Content-Type: application/json');
echo(json_encode($response));

?> 