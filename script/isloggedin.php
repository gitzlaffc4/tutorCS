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

    // send response back
    $response = array();
    $response['status'] = 'success';
    $response['loggedin'] = $isloggedin;
    $response['HAWKID'] = $HAWKID;
    header('Content-Type: application/json');
    echo(json_encode($response));    
?>