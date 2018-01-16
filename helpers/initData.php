<?php
/*
 * initData.php
 *
 * authors : KUNSANGABO NDONGALA
 *
 * Initializes the data array at the beginning of a web service
 * this array contains all the values that has been transmitted through a get or post request
 */

/**
 * Initializes the $data array
 * @param $type the type of the request $_POST, $_GET or $_REQUEST
 * @return the $data array initialized
 */

function initData($type = 'request')
{
    switch ($type) {
    case 'request':
      $type = $_REQUEST;
      break;
    case 'post':
      $type = $_POST;
      break;
    case 'get':
      $type = $_GET;
    default:
      $type = $_REQUEST;
  }
    $args = array();
    foreach ($type as $key => $value) {
        $args[$key] = $value;
    }
    $data = array(
      'status' => 'ok',
      'args' => $args,
      'result' => array()
  );
    return $data;
}
?>
