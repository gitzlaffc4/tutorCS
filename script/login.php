<?php

	// We need to include these two files in order to work with the database
    include_once('../../config.php');
    include_once('dbutils.php');

   	// connect to the database
    $db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);  
    
    // get data from form
    $data = json_decode(file_get_contents('php://input'), true);

	// get each piece of data
    $HAWKID = $data['HAWKID'];
	$PASSWORD = $data['PASSWORD'];
    
    // check for required fields
    $isComplete = true;
    $errorMessage = "";

	// Validation
    
    // check if HAWKID meets criteria
    if (!isset($HAWKID) || (strlen($HAWKID) < 2)) {
        $isComplete = false;
        $errorMessage .= "Please enter a HawkID with at least two characters. ";
    } else {
        $HAWKID = makeStringSafe($db, $HAWKID);
    }
    
    if (!isset($PASSWORD) || (strlen($PASSWORD) < 6)) {
        $isComplete = false;
        $errorMessage .= "Please enter a password with at least six characters. ";
    }      
	
	// Check if the HawkID and Password match HawkID and HashedPass in ACCOUNT_T
    if ($isComplete) {   
    
        // get the hashed password from the user with the email that got entered
        $query = "SELECT HAWKID,HASHEDPASS FROM ACCOUNT_T WHERE HAWKID='$HAWKID';";
        $result = queryDB($query, $db);
        
        if (nTuples($result) == 0) {
            // no such HAWKID
            $errorMessage .= " HawkID $HAWKID does not correspond to any account in the system. ";
            $isComplete = false;
        }
    }

    
    if ($isComplete) {            
        // there is an account that corresponds to the HawkID that the user entered
		// get the hashed password for that account
		$row = nextTuple($result);
		$HASHEDPASS = $row['HASHEDPASS'];
		
		// compare entered password to the password on the database
        // $HASHEDPASS is the version of hashed password stored in the database for $HAWKID
        // $HASHEDPASS includes the salt, and php's crypt function knows how to extract the salt from $HASHEDPASS
        // $password is the text password the user entered in login.html
		if ($HASHEDPASS != crypt($PASSWORD, $HASHEDPASS)) {
            // if PASSWORD is incorrect
            $errorMessage .= " The password you enterered is incorrect. ";
            $isComplete = false;
        }
    }


	if ($isComplete){

		// set up a query to get roles of user
		$query_r = "SELECT STUDENT, TUTOR, PROFESSOR, ADMIN FROM USER_T WHERE HAWKID = '$HAWKID';";
		// run the query to get info on players
		$result_role = queryDB($query_r, $db);
	
		        
        if (nTuples($result_role) == 0){
            $errorMessage .= "Could not find user role in the system";
            $isComplete = false; 
        }
	}
		
    if ($isComplete) {   
        // password was entered correctly
		
		$row_p = array();
        
		// assign results to an array we can then send back to whomever called
		$row_p = nextTuple($result_role);
		
		
		$student = $row_p['STUDENT'];
		$tutor = $row_p['TUTOR'];
		$professor = $row_p['PROFESSOR'];
		$admin= $row_p['ADMIN'];
	}
		
	if ($isComplete){
        // start a session
        // if the session variable 'HAWKID' is set, then we assume that the user is logged in
        session_start();
        $_SESSION['HAWKID'] = $HAWKID;
		$_SESSION['student'] = $student;
		$_SESSION['tutor'] = $tutor;
		$_SESSION['professor'] = $professor;
		$_SESSION['admin'] = $admin;
		
        // send response back
        $response = array();
        $response['status'] = 'success';
		$response['message'] = 'logged in';
		$response['student'] = $student;
		$response['tutor'] = $tutor;
		$response['professor'] = $professor;
		$response['admin'] = $admin;
		
        header('Content-Type: application/json');
        echo(json_encode($response));
	}else {
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