 <?php

// We need to include these two files in order to work with the database
include_once('../../config.php');
include_once('dbutils.php');

// get a connection to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

// get who is logged in
session_start();
$currentUser = $_SESSION['HAWKID'];

// this query and response is for the viewroles.html page
// set up a query to get information on all users in USER_T
$queryall = "SELECT USER_T.*, ACCOUNT_T.ACCESS FROM USER_T, ACCOUNT_T WHERE USER_T.HAWKID = ACCOUNT_T.HAWKID;";
// run the query to get info on all users
$resultall = queryDB($queryall, $db);

$studentQuery = "SELECT USER_T.*, STUDENT_T.GRADE_LEVEL, ENROLLED_T.COURSE_ID, ENROLLED_T.ALLOC_SESSIONS, COURSE_T.COURSE_NAME FROM USER_T, STUDENT_T, ENROLLED_T, COURSE_T WHERE STUDENT_T.HAWKID = USER_T.HAWKID AND STUDENT_T.HAWKID = ENROLLED_T.HAWKID AND ENROLLED_T.COURSE_ID = COURSE_T.COURSE_ID;";
$studentResult = queryDB($studentQuery,$db);

$tutorQuery = "SELECT USER_T.*, TUTOR_T.BIO, TUTORS_T.COURSE_ID, COURSE_T.COURSE_NAME FROM USER_T, TUTOR_T, TUTORS_T, COURSE_T WHERE TUTOR_T.HAWKID = USER_T.HAWKID AND TUTOR_T.HAWKID = TUTORS_T.HAWKID AND TUTORS_T.COURSE_ID = COURSE_T.COURSE_ID;";
$tutorResult = queryDB($tutorQuery,$db);

$professorQuery = "SELECT * FROM USER_T WHERE PROFESSOR = '1';";
$professorResult = queryDB($professorQuery,$db);


$enrollmentQuery = "SELECT COURSE_T.* FROM TEACHES_T, COURSE_T WHERE TEACHES_T.COURSE_ID = COURSE_T.COURSE_ID AND TEACHES_T.HAWKID = '$currentUser';";
$enrollmentResult = queryDB($enrollmentQuery, $db);


$courseQuery = "SELECT * FROM COURSE_T;";
$courseResult = queryDB($courseQuery, $db);

$allinfo = array();
$j = 0;
while ($currAccount = nextTuple($resultall)){
	if ($currAccount['PROFESSOR'] == '1'){
		$currAccount['ROLE'] = "Professor";
	}
	if ($currAccount['ADMIN'] == '1'){
		$currAccount['ROLE'] = "Admin";
	}
	if ($currAccount['STUDENT'] == '1'){
		$currAccount['ROLE'] = "Student";		
	}
	if ($currAccount['TUTOR'] == '1'){
		$currAccount['ROLE'] = "Tutor";
	}
	if (($currAccount['STUDENT'] == '1') and ($currAccount['TUTOR'] == '1')){
		$currAccount['ROLE'] = "Student and Tutor";
	}
	if (($currAccount['PROFESSOR'] == '1') and ($currAccount['ADMIN'] == '1')){
		$currAccount['ROLE'] = "Professor and Admin";
	}
	if ($currAccount['ACCESS'] == '0'){
		$currAccount['ACCESSRIGHTS'] = "<span class='label label-danger'>Disabled</span>";
	}
	if ($currAccount['ACCESS'] == '1'){
		$currAccount['ACCESSRIGHTS'] = "<span class='label label-success'>Enabled</span>";
	}
	$allinfo[$j] = $currAccount;
	$profilePic = $allinfo[$j]['PICTURE'];
	$allinfo[$j]['PROFILEPIC'] ="<img src='images/profilepictures/$profilePic' class='img-circle center-block'  alt='profile pic' width='175' height='175'>";
	$j++;
}

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

$tutorInfo = array();
$l = 0;
while ($currTutor = nextTuple($tutorResult)){
	$tutorHawkID = $currTutor['HAWKID'];
	
	// Query to see how many sessions are available 
	$totalAvailSessionsQuery = "SELECT COUNT(SESSION_ID) FROM SESSION_T WHERE TUTOR_HAWKID = '$tutorHawkID' AND SCHEDULED = '0';";
	$availResult = queryDB($totalAvailSessionsQuery, $db);
	$totalAvail = 0;
	if (nTuples($availResult) > 0){
		$totalAvail = nextTuple($availResult)['COUNT(SESSION_ID)'];
		
	}
	
	
	// Query to see how many sessions are scheduled
	$totalSchedSessionsQuery = "SELECT COUNT(SESSION_ID) FROM SESSION_T WHERE TUTOR_HAWKID = '$tutorHawkID' AND SCHEDULED = '1';";
	$schedResult = queryDB($totalSchedSessionsQuery, $db);
	$totalSched = 0;
	if (nTuples($schedResult) > 0){
		$totalSched = nextTuple($schedResult)['COUNT(SESSION_ID)'];
		
	}
	
	// Query to see how many sessions have been completed by tutor
	$aCourse = $currTutor['COURSE_ID'];
	$completeQuery = "SELECT COUNT(SCHEDULED_T.SESSION_ID) FROM SESSION_T, SCHEDULED_T WHERE SESSION_T.TUTOR_HAWKID = '$tutorHawkID' AND SESSION_T.COURSE_ID = $aCourse AND SCHEDULED_T.COMPLETED = '1';";
	
	$compResult = queryDB($completeQuery, $db);
	$totalComp = 0;
	if (nTuples($compResult) > 0){
		$totalComp = "3";
			// nextTuple($compResult)['COUNT(SCHEDULUED_T.SESSION_ID)'];
	}
	
	$profilePic = $currTutor['PICTURE'];
	$currTutor['PROFILEPIC'] ="<img src='images/profilepictures/$profilePic' class='img-circle center-block'  alt='profile pic' width='40' height='40'>";
	$currTutor['availSessions'] = $totalAvail;
	$currTutor['totalSched'] = $totalSched;
	$currTutor['totalComp'] = $totalComp;
	
	$tutorInfo[$l] = $currTutor;
	$l++;
}



$professorInfo = array();
$m = 0;
while ($currProfessor = nextTuple($professorResult)){
	$currHawkID = $currProfessor['HAWKID'];
	$teachesQuery = "SELECT TEACHES_T.*, COURSE_T.COURSE_NAME, COURSE_T.SEMESTER FROM TEACHES_T, COURSE_T WHERE TEACHES_T.HAWKID = '$currHawkID' AND TEACHES_T.COURSE_ID = COURSE_T.COURSE_ID;";
	$teachesResult = queryDB($teachesQuery, $db);
	$a = 0;
	$firstSession = null;
	while ($currCourse = nextTuple($teachesResult)){
		$currProfessor['TEACHES'][$a] = $currCourse;
		$a++;
		}
	$professorInfo[$m] = $currProfessor;
	$m++;
}

$enrollementInfo = array();
$n = 0;
while ($currCourse = nextTuple($enrollmentResult)){
	$enrollementInfo[$n] = $currCourse;
	$n++;
}

$courseInfo = array();
$b = 0;
while($aCourse = nextTuple($courseResult)){
	$courseInfo[$b] = $aCourse;
	$b++;
}


// put together a JSON object to send back the data on the players
$response = array();
$response['status'] = 'success';
$response['value']['allinfo'] = $allinfo;
$response['value']['studentInfo'] = $studentInfo;
$response['value']['tutorInfo'] = $tutorInfo;
$response['value']['professorInfo'] = $professorInfo;
$response['value']['enrollementInfo'] = $enrollementInfo;
$response['value']['courseInfo'] = $courseInfo;
header('Content-Type: application/json');
echo(json_encode($response));
?> 