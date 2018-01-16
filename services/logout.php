<?php
/*
 * logout.php
 *
 * authors : Berfy
 *
 * Signout if a user was connected by destroying the session with session_destroy()
 */

 // We start the session as we need to know if a user is already logged or not
session_start();
require('../helpers/initData.php');

$data = initData();

if (isset($_SESSION['ident'])) {
    $data['result'] = $_SESSION['ident'];
    session_destroy();
} else {
    $data['status'] = 'error';
    $data['result'] = null;
    $data['message'] = "user is not signed in";
}

echo json_encode($data);
?>
