<?php
/*
 * login.php
 *
 * authors : berfy
 *
 * Signin a user by starting the session
 */

// We start the session as we need to know if a user is already logged or not
session_start();
require('../helpers/connection.php');
require('../helpers/initData.php');

$data = initData("post");
$args = $data["args"];
$paramsAreSet = (isset($args['login']) && isset($args['password']));
// We obviously dont wan't the response to show the password so we keep it in a variable
$password = isset($args['password']) ? $args['password'] : null;
unset($data["args"]['password']);
unset($args['password']);

// Check server-side if params are set and if user is not already logged
if ($paramsAreSet) {
    if (!isset($_SESSION['ident'])) {
        // Check if user exist in database
        $stmt = $connection->prepare('SELECT EXISTS(SELECT * FROM users where login=:login)');
        $stmt->bindValue(':login', $args['login']);
        $stmt->execute();
        $arr = $stmt->fetch();
        if ($arr['exists']) {
            $query = 'SELECT login, name, password FROM users WHERE login=:login';
            $stmt = $connection->prepare($query);
            $stmt->bindValue(':login', $args['login']);
            $stmt->execute();
            $user = $stmt->fetch();
            if (crypt($password, $user['password']) === $user['password']) {
                $data['result'] = $user['login'];
                // We set the id of the user in the session & his name
                $_SESSION['ident'] = $user['login'];
                $_SESSION['name'] = $user['name'];
            } else {
                $data['status'] = 'error';
                $data['result'] = null;
                $data['message'] = 'invalid password';
            }
        } else {
            $data['status'] = 'error';
            $data['result'] = null;
            $data['message'] = 'user is not in the database';
        }
    } else {
        $data['status'] = 'error';
        $data['result'] = null;
        $data['message'] = 'user already logged';
    }
} else {
    $data['status'] = 'error';
    $data['result'] = null;
    $data['message'] = 'some parameters are not set';
}

echo json_encode($data);
?>
