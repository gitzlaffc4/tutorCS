<?php
    // log in user by checking whether the session variable HAWKID is set

    
    session_start();
    if (isset($_SESSION['HAWKID'])) {
        // if the session variable HAWKID is set, then we are logged in
        $isloggedin = true;
        $HAWKID = $_SESSION['HAWKID'];
    } else {
        // if we are not logged in
        $isloggedin = false;
        $HAWKID = "not logged in";        
    }
	$admin = false;
	$professor = false;
	$tutor = false;
	$student = false;

	if ($_SESSION['admin'] == 1){
		$admin = true;
	}
	if ($_SESSION['professor'] == 1){
		$professor = true;
	}
	if ($_SESSION['tutor'] == 1){
		$tutor = true;
	}
	if ($_SESSION['student'] == 1){
		$student = true;
	}


    // send response back
    $response = array();
    $response['status'] = 'success';
    $response['loggedin'] = $isloggedin;
    $response['HAWKID'] = $HAWKID;
	$response['admin'] = $admin;
	$response['professor'] = $professor;
	$response['tutor'] = $tutor;
	$response['student'] = $student;
    header('Content-Type: application/json');
    echo(json_encode($response));    
?>