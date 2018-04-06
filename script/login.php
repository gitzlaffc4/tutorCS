<?php
    include_once('../../config.php');
    include_once('dbutils.php');
    
    // get data from form
    $data = json_decode(file_get_contents('php://input'), true);
    $HAWKID = $data['HAWKID'];
	$PASSWORD = $data['PASSWORD'];
    
   // connect to the database
    $db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);    
    
    // check for required fields
    $isComplete = true;
    $errorMessage = "";
    
    // check if HAWKID meets criteria
    if (!isset($HAWKID) || (strlen($HAWKID) < 2)) {
        $isComplete = false;
        $errorMessage .= "Please enter a hawkid with at least two characters. ";
    } else {
        $HAWKID = makeStringSafe($db, $HAWKID);
    }
    
    if (!isset($PASSWORD) || (strlen($PASSWORD) < 6)) {
        $isComplete = false;
        $errorMessage .= "Please enter a password with at least six characters. ";
    }      
	
    if ($isComplete) {   
    
        // get the hashed password from the user with the email that got entered
        $query = "SELECT HAWKID, HASHEDPASS FROM account WHERE HAWKID='$HAWKID';";
        $result = queryDB($query, $db);
        
        if (nTuples($result) == 0) {
            // no such HAWKID
            $errorMessage .= " HawkID $HAWKID does not correspond to any account in the system. ";
            $isComplete = false;
        }
    }
    
    if ($isComplete) {            
        // there is an account that corresponds to the email that the user entered
		// get the hashed password for that account
		$row = nextTuple($result);
		$HASHEDPASS = $row['HASHEDPASS'];
		$id = $row['id'];
		
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
         
    if ($isComplete) {   
        // password was entered correctly
        
        // start a session
        // if the session variable 'HAWKID' is set, then we assume that the user is logged in
        session_start();
        $_SESSION['HAWKID'] = $HAWKID;
		$_SESSION['accountid'] = $id;
        
        // send response back
        $response = array();
        $response['status'] = 'success';
		$response['message'] = 'logged in';
        header('Content-Type: application/json');
        echo(json_encode($response));
    } else {
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