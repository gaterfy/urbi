<?php
/*
 * setProfile.php
 *
 * authors : Thomas Lombart - Martin Vasilev
 *
 * Updates the profile of a user that is to say its informations (but not the avatar ! this process is handled in uploadAvatar.php)
 */

 // We start the session because we need to know if a user is already logged or not
session_start();
require('../helpers/connection.php');
require('../helpers/initData.php');
require('../helpers/crypt.php');

$data = initData("post");
$args = $data["args"];
// We obviously dont wan't the response to show the password so we keep it in a variable
$password = isset($args['password']) ? $args['password'] : null;
unset($data["args"]['password']);
unset($args['password']);

if (isset($_SESSION['ident'])) {
    // Update name
    $stmt = $connection->prepare('UPDATE users SET name=:name WHERE login=:user');
    $stmt->bindValue(':name', $args['name']);
    $stmt->bindValue(':user', $_SESSION['ident']);
    $stmt->execute();
    // Update description
    $stmt = $connection->prepare('UPDATE users SET description=:description WHERE login=:user');
    $stmt->bindValue(':description', $args['description']);
    $stmt->bindValue(':user', $_SESSION['ident']);
    $stmt->execute();
    // Update password if it is set
    if (isset($password)) {
        $stmt = $connection->prepare('UPDATE users SET password=:password WHERE login=:user');
        $password = crypt($password, generateSalt());
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':user', $_SESSION['ident']);
        $stmt->execute();
    }
    $user = array(
        'ident' => $_SESSION['ident'],
        'name' => $args['name'],
        'description' => $args['description']
    );
    $data['result'] = $user;
} else {
    $data['status'] = 'error';
    $data['result'] = null;
    $data['message'] = 'user is not signed in';
}

echo json_encode($data);
?>
