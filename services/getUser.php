<?php
/*
 * getUser.php
 *
 * authors : Berfy
 *
 * Gets the information's profile of a user.
 */

require('../helpers/connection.php');
require('../helpers/initData.php');

$data = initData();
$args = $data["args"];

// Check if the parameter given in the URL is correct
if (isset($args['user'])) {
    // Check if such a user exists in the database
    $stmt = $connection->prepare('SELECT EXISTS(SELECT * FROM users where login=:user)');
    $stmt->bindValue(':user', $args['user']);
    $stmt->execute();
    $arr = $stmt->fetch();
    // If user exists, we can retrieve its informations
    if ($arr['exists']) {
        $query = 'SELECT login, name FROM users WHERE login=:user';
        // Handle the case where we want the description of the user
        if (isset($args['type'])) {
            if ($args['type'] === 'long') {
                $longUserWanted = true;
                $query = 'SELECT login, name, description FROM users WHERE login=:user';
            }
        }
        // Get basic informations of the user
        $stmt = $connection->prepare($query);
        $stmt->bindValue(':user', $args['user']);
        $stmt->execute();
        $userInDB = $stmt->fetch();
        $user = array(
            "ident" => $userInDB['login'],
            "name" => $userInDB['name']
        );
        if (isset($longUserWanted)) {
            $user['description'] = $userInDB['description'];
        }

        /* STATS OF USER */
        // Messages first
        $stmt = $connection->prepare('SELECT COUNT(*) FROM messages where author=:user');
        $stmt->bindValue(':user', $args['user']);
        $stmt->execute();
        $nbOfMessages = $stmt->fetch()['count'];

        // Number of followers
        $stmt = $connection->prepare('SELECT COUNT(*) FROM followers where user_followed=:user');
        $stmt->bindValue(':user', $args['user']);
        $stmt->execute();
        $nbOfFollowers = $stmt->fetch()['count'];

        $user['stats'] = array(
            "messages" => $nbOfMessages,
            "followers" => $nbOfFollowers
        );

        $data['result'] = $user;
    } else {
        $data['status'] = 'error';
        $data['result'] = null;
        $data['message'] = 'user is not in the database';
    }
} else {
    $data['status'] = 'error';
    $data['result'] = null;
    $data['message'] = 'no user parameter was given';
}

echo json_encode($data);
?>
