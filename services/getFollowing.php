<?php
/*
 * getFollowers.php
 *
 * authors : Berfy
 *
 * Gets the followers of a user or the users he is following
 */

require('../helpers/connection.php');
require('../helpers/initData.php');

$data = initData();
$args = $data['args'];

/*Check if the user parameter is set */
if (isset($args['user'])) {
  /* If it is set we can prepare the query */
  /* Procedure to check if the login exists */
  $stmt = $connection->prepare('SELECT EXISTS(SELECT * FROM users WHERE login=:user)');
  $stmt->bindValue(':user',$args['user']);
  $stmt->execute();
  $arr = $stmt->fetch();
  if ($arr['exists']) {
    /* If it exists ... */
    if (isset($args['r'])) {
      /* ... and we want to get the users followed by this user */
      $stmt = $connection->prepare('SELECT login,name FROM users JOIN followers ON users.login = followers.user_followed WHERE user_following = :user');
      $stmt->bindValue(':user',$args['user']);
      $stmt->execute();
      while ($row = $stmt->fetch()) {
        /* Add the user_followed to the data array */
        array_push($data['result'],array(
          'login' => $row['login'],
          'name' => $row['name']
        ));
      }
    } else {
      /* ... and we want to get the followers of the users */
      /* also the default case */
      $stmt = $connection->prepare('SELECT login,name FROM users JOIN followers ON users.login = followers.user_following WHERE user_followed = :user');
      $stmt->bindValue(':user',$args['user']);
      $stmt->execute();
      while ($row = $stmt->fetch()) {
        /* Add the user_following to the data array */
        array_push($data['result'],array(
          'login' => $row['login'],
          'name' => $row['name']
        ));
      }
    }
  } else {
    /* If not ... */
    $data['result'] = null;
    $data['status'] = 'error';
    $data['message'] = 'user is not in the database';
  }
} else {
  $data['result'] = null;
  $data['status'] = 'error';
  $data['message'] = 'user is not set';
}

echo json_encode($data);
 ?>
