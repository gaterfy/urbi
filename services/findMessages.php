<?php
/*
 * findMessages.php
 *
 * authors : berfy
 *
 * Service that allows to find messages based on criterions :
 * - followers
 * - author
 * - mentioned
 * - before the id of a message
 * - after the id of a message
 * We can also precise how much messages we want with count
 */

require('../helpers/connection.php');
require('../helpers/initData.php');

$data = initData();
$args = $data["args"];

$query = "SELECT * FROM messages";

// If search criterions depends on followers or mentions, we need to join these tables
if (isset($args['follower']) && !empty($args['follower'])) {
    $query.= " JOIN followers ON followers.user_followed=messages.author";
}

if (isset($args['mentioned']) && !empty($args['mentioned'])) {
    $query.= " JOIN mentions ON mentions.message_id=messages.id";
}

// We set this variable to know if we add a AND clause or a WHERE clause to the query
$moreParams = false;

// Search by author
if (isset($args['author']) && !empty($args['author'])) {
    $author = $args['author'];
    $query.= " WHERE messages.author='{$author}'";
    $moreParams = true;
}

// Search by follower
if (isset($args['follower']) && !empty($args['follower'])) {
    $follower = $args['follower'];
    $query .= $moreParams ? " AND followers.user_following='{$follower}'" : " WHERE followers.user_following='{$follower}'";
    $moreParams = true;
}

// Search by mentions
if (isset($args['mentioned']) && !empty($args['mentioned'])) {
    $mentioned = $args['mentioned'];
    $query .= $moreParams ? " AND mentions.users_id ='{$mentioned}'" : " WHERE mentions.users_id='{$mentioned}'";
    $moreParams = true;
}

// Search by id (before)
if (isset($args['before']) && !empty($args['before'])) {
    $before = $args['before'];
    $query .= $moreParams ? " AND messages.id < {$before}" : " WHERE messages.id < {$before}";
    $moreParams = true;
}

// Search by id (after)
if (isset($args['after']) && !empty($args['after'])) {
    $after = $args['after'];
    $query .= $moreParams ? " AND messages.id > {$after}" : " WHERE messages.id > {$after}";
    $moreParams = true;
}

//Get most recent messages
$limit = isset($args['count']) ? $args['count'] : 15;
$query .= " ORDER BY messages.id DESC";

$stmt = $connection->prepare($query);
$stmt->execute();
$data['result']['list'] = array();
$i=0;
while (($i < $limit) && ($messageInDB = $stmt->fetch())) {
    $message = array(
    'id' => $messageInDB['id'],
    'content' => $messageInDB['content'],
    'author' => $messageInDB['author'],
    'datetime' => $messageInDB['date']
  );
    array_push($data['result']['list'], $message);
    $i++;
}

// If the line is non-empty, there is at least one message existing
$data['result']['hasMore'] = ($messageInDB=$stmt->fetch()) ? true : false;

echo json_encode($data);
