<?php
/**
 * setRelation.php
 *
 * authors : Thomas Lombart - Martin Vasilev
 * 
 * description : Service that allows modification of the relation
 * of two users, meaning altering whether one follows the other.
 *
 * only works if a user is authenticated, and only his relation can be modified.
 *
 * default value for the parameter action is 'follow' meaning that if argument is not given or invalid will
 * take default value of 'follow', other option is 'unfollow'.
 *
 * $data['result'] is true if sucessful, null elsewise
*/

// To be able to identify the user
session_start();

require('../helpers/connection.php');
require('../helpers/initData.php');

$data = initData();
$args = $data['args'];

if (isset($_SESSION['ident'])) {
  // We are sure that the user is authenticated
  if (isset($args['followed'])) {
    // A followed user is provided
    // Now we need to check whether the given user is valid (exist in the database)
    $exists = $connection->prepare('SELECT EXISTS(SELECT * FROM users WHERE login=:user)');
    $exists->bindValue(':user',$args['followed']);
    $exists->execute();
    if ($exists->fetch()['exists']) {
      // Users exist we can process the query
      $already_following = $connection->prepare('SELECT EXISTS(SELECT * FROM followers WHERE user_followed = :followed AND user_following = :user)');
      $already_following->bindValue(':followed',$args['followed']);
      $already_following->bindValue(':user',$_SESSION['ident']);
      $already_following->execute();
      $already_following = $already_following->fetch();
      $already_following = $already_following['exists'];
      if (isset($args['action']) && $args['action']==='unfollow') {
        // User $_SESSION['ident'] will unfollow $args['followed'] if he is already following him
        if ($already_following) {
          $delete = $connection->prepare('DELETE FROM followers WHERE user_followed = :followed AND user_following = :user');
          $delete->bindValue(':followed',$args['followed']);
          $delete->bindValue('user',$_SESSION['ident']);
          $delete->execute();
          // Done
          $data['result'] = $already_following;
        } else {
          $data['result'] = $already_following;
          $data['status'] = 'error';
          $data['message'] = "user is already not following this user";
        }
      } else {
        // User $_SESSION['ident'] will now follow $args['followed']
        if ($already_following) {
          $data['result'] = null;
          $data['status'] = 'error';
          $data['message'] = "user is already following this user";
        } else {
          $insert = $connection->prepare('INSERT INTO followers (user_followed,user_following) VALUES (:followed, :user)');
          $insert->bindValue(':followed',$args['followed']);
          $insert->bindValue(':user',$_SESSION['ident']);
          $insert->execute();
          // Done
          $data['result'] = true;
        }
      }
    } else {
      $data['result'] = null;
      $data['status'] = 'error';
      $data['message'] = 'that user doesn\'t exist';
    }
  } else {
    $data['result'] = null;
    $data['status'] = 'error';
    $data['message'] = 'followed user not given';
  }
} else {
  // One of the conditions isn't respected
  $data['result'] = null;
  $data['status'] = 'error';
  $data['message'] = 'authentication error or forbidden action';
}

echo json_encode($data);

?>
