<?php
/*
 * createUser.php
 *
 * authors : Berfy
 *
 * Creates a user in the users database then sends the freshly created user informations in JSON format
 */

 // We start the session because once the user has created its account, he needs to be logged
session_start();
require('../helpers/connection.php');
require('../helpers/initData.php');
require('../helpers/crypt.php');
require('Users.class.php');

$data = initData('post');
$args = $data["args"];
$paramsAreSet = (isset($args['ident']) && isset($args['password']) && isset($args['name']));
// We obviously dont wan't the response to show the password so we keep it in a variable
$password = isset($args['password']) ? $args['password'] : null;
unset($data["args"]['password']);
unset($args['password']);

if ($paramsAreSet) {
    // We verify server-side the values sent by the script
    $login = inputFilterString('ident');
    $password = inputFilterString('password');
    $name = inputFilterString('name');
    $description = isset($args['description']) ? $args['description'] : null;
    if (isset($args['description'])) {
        $query = 'INSERT INTO users(login, name, password, description) values (:login, :name, :password, :description)';
    } else {
        $query = 'INSERT INTO users(login, name, password) values (:login, :name, :password)';
    }
    $stmt = $connection->prepare($query);
    $stmt->bindValue(':login', $login);
    $stmt->bindValue(':name', $name);
    $password = crypt($password, generateSalt());
    $stmt->bindValue(':password', $password);
    if (isset($args['description'])) {
        $stmt->bindValue(':description', $description);
    }
    $stmt->execute();
    // Dedicated to adding a default image
    // Open the image as stream
    $flux = fopen('../helpers/default.png','r');
    // Prepare query and bind values
    print_r('im here');
    $avatar_stmt = $connection->prepare('INSERT INTO avatars(login, image, type) VALUES (:login, :image, :type)');
    $avatar_stmt->bindValue(':login', $login);
    $avatar_stmt->bindValue(':image', $flux, PDO::PARAM_LOB);
    $avatar_stmt->bindValue('type', 'image/png');
    $avatar_stmt->execute();
    // User has created its account, so he is logged right after
    $_SESSION['ident'] = $args['ident'];
    $_SESSION['name'] = $args['name'];
    $user = array(
        'ident' => $args['ident'],
        'name' => $args['name']
    );
    $data['result'] = $user;// 
    $User = new Users($_SESSION['name']);
    print_r($User->getSolde());
} else {
    $data['status'] = 'error';
    $data['result'] = null;
    $data['message'] = 'some parameters are not set';
}

echo json_encode($data);
?>
