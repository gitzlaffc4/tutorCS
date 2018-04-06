<?php
    // log user out by unsetting session variable called email, and destroying the session
    
    session_start();
    if (isset($_SESSION['username'])) {
        // unsetting the username variable makes it so we assume that we are not logged in
        unset($_SESSION['username']);
    }
    session_destroy();
    
    // send response back
    $response = array();
    $response['status'] = 'success';
    header('Content-Type: application/json');
    echo(json_encode($response));    
?>