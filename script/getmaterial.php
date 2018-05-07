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

// set up a query to get information on user
$StudQueryCourse = "SELECT ENROLLED_T.*, COURSE_T.*, MATERIALS_T.* FROM ENROLLED_T, COURSE_T, MATERIALS_T WHERE ENROLLED_T.HAWKID = '$HAWKID' AND COURSE_T.COURSE_ID = ENROLLED_T.COURSE_ID AND MATERIALS_T.COURSE_ID = COURSE_T.COURSE_ID;";


// run the query to get info on user
$StudMaterialsResult = queryDB($StudQueryCourse, $db);
$StudMaterials = array();
$u = 0;
while ($currStudMaterial = nextTuple($StudMaterialsResult)){
 	$StudMaterials[$u] = $currStudMaterial;
 	$u++;	
}


$enrollmentQuery = "SELECT COURSE_T.* FROM TEACHES_T, COURSE_T WHERE TEACHES_T.COURSE_ID = COURSE_T.COURSE_ID AND TEACHES_T.HAWKID = '$HAWKID';";
$enrollmentResult = queryDB($enrollmentQuery, $db);

$enrollmentInfo = array();
$n = 0;
while ($currCourse = nextTuple($enrollmentResult)){
	$enrollmentInfo[$n] = $currCourse;
	$n++;
}

$TUTORTUTORS = "SELECT COURSE_T.* FROM TUTORS_T, COURSE_T WHERE TUTORS_T.COURSE_ID = COURSE_T.COURSE_ID AND TUTORS_T.HAWKID = '$HAWKID';";
$TUTORRESULT = queryDB($TUTORTUTORS, $db);

$tutorTutors = array();
$w = 0;
while ($currTutCourse = nextTuple($TUTORRESULT)){
	$tutorTutors[$w] = $currTutCourse;
	$w++;
}



// set up a query to get information on user
$TutorQueryCourse = "SELECT TUTORS_T.*, COURSE_T.*, MATERIALS_T.* FROM TUTORS_T, COURSE_T, MATERIALS_T WHERE TUTORS_T.HAWKID = '$HAWKID' AND COURSE_T.COURSE_ID = TUTORS_T.COURSE_ID AND MATERIALS_T.COURSE_ID = COURSE_T.COURSE_ID;";


// run the query to get info on user
$TutorMaterialsResult = queryDB($TutorQueryCourse, $db);
$TutorMaterials = array();
$x = 0;
while ($currTutorMaterial = nextTuple($TutorMaterialsResult)){
 	$TutorMaterials[$x] = $currTutorMaterial;
 	$x++;	
}



$courseQuery = "SELECT * FROM COURSE_T;";
$courseResult = queryDB($courseQuery, $db);

$courseInfo = array();
$b = 0;
while($aCourse = nextTuple($courseResult)){
	$courseInfo[$b] = $aCourse;
	$b++;
}


$studentQuery = "SELECT USER_T.*, STUDENT_T.GRADE_LEVEL, ENROLLED_T.COURSE_ID, ENROLLED_T.ALLOC_SESSIONS, COURSE_T.COURSE_NAME, COURSE_T.SEMESTER FROM USER_T, STUDENT_T, ENROLLED_T, COURSE_T WHERE STUDENT_T.HAWKID = USER_T.HAWKID AND STUDENT_T.HAWKID = ENROLLED_T.HAWKID AND ENROLLED_T.COURSE_ID = COURSE_T.COURSE_ID;";
$studentResult = queryDB($studentQuery,$db);

$studentInfo = array();
$k = 0;
while ($currStudent = nextTuple($studentResult)){
	if ($currStudent['GRADE_LEVEL'] == '1'){
		$currStudent['CLASS_YEAR'] = "Freshman";
	}
	if ($currStudent['GRADE_LEVEL'] == '2'){
		$currStudent['CLASS_YEAR'] = "Sophomore";
	}
	if ($currStudent['GRADE_LEVEL'] == '3'){
		$currStudent['CLASS_YEAR'] = "Junior";
	}
	if ($currStudent['GRADE_LEVEL'] == '4'){
		$currStudent['CLASS_YEAR'] = "Senior";
	}
	$profilePic = $currStudent['PICTURE'];
	$currStudent['PROFILEPIC'] ="<img src='images/profilepictures/$profilePic' class='img-circle center-block'  alt='profile pic' width='40' height='40'>";
	$studentInfo[$k] = $currStudent;
	$k++;
}




// put together a JSON object to send back the data on the players
$response = array();
$response['status'] = 'success';
$response['value']['Materials'] = $Materials;
$response['value']['StudMaterials'] = $StudMaterials;
$response['value']['TutorMaterials'] = $TutorMaterials;
$response['value']['tutorTutors'] = $tutorTutors;
$response['value']['enrollmentInfo'] = $enrollmentInfo;
$response['value']['courseInfo'] = $courseInfo;

$response['value']['studentInfo'] = $studentInfo;
header('Content-Type: application/json');
echo(json_encode($response));

?> 