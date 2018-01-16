<?php
/*
 * getMessage.php
 *
 * authors : Berfy
 *
 * Gets the message in the database.
 * We can get the message only with its id
 */

require('../helpers/connection.php');
require('../helpers/initData.php');

$data = initData();
$args = $data["args"];

// Check if the parameter given in the URL is correct
if (isset($args['id'])) {
    // Check if the id exists in the database
    $stmt = $connection->prepare('SELECT EXISTS(SELECT * FROM messages where id=:id)');
    $stmt->bindValue(':id', $args['id']);
    $stmt->execute();
    $arr = $stmt->fetch();
    // If message exists, we can retrieve its informations
    if ($arr['exists']) {
        $query = 'SELECT content, author, date FROM messages WHERE id=:id';
        $stmt  = $connection->prepare($query);
        $stmt->bindValue(':id', $args['id']);
        $stmt->execute();
        $messageInDB = $stmt->fetch();
        $message = array(
            'id' => $args['id'],
            'author' => $messageInDB['author'],
            'content' => $messageInDB['content'],
            'datetime' => $messageInDB['date']
        );
        $data['result'] = $message;
    } else {
        $data['status'] = 'error';
        $data['result'] = null;
        $data['message'] = 'message is not in the database';
    }
} else {
    $data['status'] = 'error';
    $data['result'] = null;
    $data['message'] = 'no id parameter was given';

}

echo json_encode($data);
?>
