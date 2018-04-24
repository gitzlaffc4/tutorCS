 <?php

// We need to include these two files in order to work with the database
include_once('../../config.php');
include_once('dbutils.php');

// get a connection to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);
	

// this query and response is for the viewroles.html page
// set up a query to get information on all users in USER_T
$queryall = "SELECT * FROM USER_T;";
// run the query to get info on all users
$resultall = queryDB($queryall, $db);
$allinfo = array();
$j = 0;
while ($currAccount = nextTuple($resultall)){
	if (($currAccount['STUDENT'] == '1') and ($currAccount['TUTOR'] == '1')){
		$currAccount['ROLE'] = "Student and Tutor";
	}
	if (($currAccount['PROFESSOR'] == '1') and ($currAccount['ADMIN'] == '1')){
		$currAccount['ROLE'] = "Professor and Admin";
	}
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
	$allinfo[$j] = $currAccount;
	$j++;
}

// put together a JSON object to send back the data on the players
$response = array();
$response['status'] = 'success';
$response['value']['allinfo'] = $allinfo;
header('Content-Type: application/json');
echo(json_encode($response));
?> 