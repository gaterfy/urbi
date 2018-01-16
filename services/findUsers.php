<?php
/*
 * findUsers.php
 *
 * authors : Berfy
 *
 * Service that allows to find users based on criterions :
 * - name
 * - login
 */

require('../helpers/connection.php');
require('../helpers/initData.php');

$data = initData();
$args = $data["args"];

if (isset($args['searched'])) {
  $searched = $args['searched'];
  $isLong = false;

  // If we want the description of a user
  if (isset($args['type']) && $args['type']=='long') {
    $query = "SELECT login,name,description FROM users";
    $isLong = true;
  } else {
    $query = "SELECT login,name FROM users";
  }

  // We check whether we search in the name or login of the user
  if (isset($args['scope'])) {
    $scope = $args['scope'];
    if ($scope == 'ident') {
      $query .= " WHERE login LIKE '%{$searched}%'";
    } else if ($scope == "name") {
      $query .= " WHERE name LIKE '%{$searched}%'";
    } else {
      $query .= " WHERE name LIKE '%{$searched}%' OR login LIKE '%{$searched}%'";
    }
  } else {
    $query .= " WHERE name LIKE '%{$searched}%' OR login LIKE '%{$searched}%'";
  }

  $data["result"]["list"] = array();

  $stmt = $connection->prepare($query);
  $stmt->execute();
  while ($user = $stmt->fetch()) {
    $profile = array(
      'ident' => $user['login'],
      'name' => $user['name']
    );
    if ($isLong) {
      $profile['description'] = $user['description'];
    }
    array_push($data['result']["list"], $profile);
  }

} else {
  $data['status'] = 'error';
  $data['result'] = null;
  $data['message'] = 'no value given';
}

echo json_encode($data);
?>
