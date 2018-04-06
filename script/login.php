<?php
    include_once('config.php');
    include_once('dbutils.php');
    
    // get data from form
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'];
	$password = $data['password'];
    
   // connect to the database
    $db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);    
    
    // check for required fields
    $isComplete = true;
    $errorMessage = "";
    
    // check if username meets criteria
    if (!isset($username) || (strlen($username) < 2)) {
        $isComplete = false;
        $errorMessage .= "Please enter a username with at least two characters. ";
    } else {
        $username = makeStringSafe($db, $username);
    }
    
    if (!isset($password) || (strlen($password) < 6)) {
        $isComplete = false;
        $errorMessage .= "Please enter a password with at least six characters. ";
    }      
	
    if ($isComplete) {   
    
        // get the hashed password from the user with the email that got entered
        $query = "SELECT id, hashedpass FROM account WHERE username='$username';";
        $result = queryDB($query, $db);
        
        if (nTuples($result) == 0) {
            // no such username
            $errorMessage .= " Username $username does not correspond to any account in the system. ";
            $isComplete = false;
        }
    }
    
    if ($isComplete) {            
        // there is an account that corresponds to the email that the user entered
		// get the hashed password for that account
		$row = nextTuple($result);
		$hashedpass = $row['hashedpass'];
		$id = $row['id'];
		
		// compare entered password to the password on the database
        // $hashedpass is the version of hashed password stored in the database for $username
        // $hashedpass includes the salt, and php's crypt function knows how to extract the salt from $hashedpass
        // $password is the text password the user entered in login.html
		if ($hashedpass != crypt($password, $hashedpass)) {
            // if password is incorrect
            $errorMessage .= " The password you enterered is incorrect. ";
            $isComplete = false;
        }
    }
         
    if ($isComplete) {   
        // password was entered correctly
        
        // start a session
        // if the session variable 'username' is set, then we assume that the user is logged in
        session_start();
        $_SESSION['username'] = $username;
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