<?php
/*
 * isLogged.php
 *
 * authors : Thomas Lombart - Martin Vasilev
 *
 * Tells if a user is connected by sending in JSON format the login of the user connected
 * If no one is connected, an empty string is sent back
 */

session_start();
require('../helpers/initData.php');

$data = initData();
$data["result"] = isset($_SESSION['ident']) ? $_SESSION['ident'] : "";

echo json_encode($data);
?>
