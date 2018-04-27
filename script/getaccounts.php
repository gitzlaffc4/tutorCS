 <?php

// We need to include these two files in order to work with the database
include_once('../../config.php');
include_once('dbutils.php');

// get a connection to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);
	

// this query and response is for the viewroles.html page
// set up a query to get information on all users in USER_T
$queryall = "SELECT USER_T.HAWKID, USER_T.FIRSTNAME, USER_T.LASTNAME, USER_T.EMAIL, USER_T.STUDENT, USER_T. TUTOR,  USER_T.PROFESSOR, USER_T.ADMIN, USER_T.NICK_NAME, USER_T.PHONE_NUMBER, USER_T.PICTURE, ACCOUNT_T.ACCESS FROM USER_T INNER JOIN ACCOUNT_T ON USER_T.HAWKID = ACCOUNT_T.HAWKID;";
// run the query to get info on all users
$resultall = queryDB($queryall, $db);

$studentQuery = "SELECT STUDENT_T.HAWKID, ENROLLED_T.COURSE_ID, COURSE_T.COURSE_NAME FROM STUDENT_T, ENROLLED_T, COURSE_T WHERE STUDENT_T.HAWKID = ENROLLED_T.HAWKID AND ENROLLED_T.COURSE_ID = COURSE_T.COURSE_ID;";
$studentResult = queryDB($studentQuery,$db);

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
	$studentInfo[$k] = $currStudent;
	$k++;
}

// put together a JSON object to send back the data on the players
$response = array();
$response['status'] = 'success';
$response['value']['allinfo'] = $allinfo;
$response['value']['studentInfo'] = $studentInfo;
header('Content-Type: application/json');
echo(json_encode($response));
?> 