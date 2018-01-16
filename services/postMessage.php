<?php
/*
 * postMessage.php
 *
 * authors : Berfy
 *
 * Allows a user to post a message in the database
 */

 // We start the session as we need to know if a user is already logged or not
session_start();
require('../helpers/connection.php');
require('../helpers/initData.php');
require('../lib/MessageMentions.class.php');

$data = initData('post');
$args = $data['args'];

if (isset($args['source'])) {
    if (isset($_SESSION['ident'])) {

        // We proceed message first by cheking if there are mentions
        $message = new MessageMentions($args['source']);
        $usersQuoted = $message->getFoundIdentsString();
        $mentionsExisting = $usersQuoted !== '()';
        if ($mentionsExisting) {
            $stmt = $connection->query("SELECT login FROM users WHERE login in $usersQuoted");
            $stmt->execute();

            // We add every user found in an array and encode the message
            $existingUsers = array();
            while ($user = $stmt->fetch()) {
                array_push($existingUsers, $user['login']);
            }
            $message->setMentions($existingUsers);
        }

        $messageToSave = $mentionsExisting ? $message->getEncodedMessage() : $args['source'];

        // We save the message in the database
        $stmt = $connection->prepare('INSERT INTO messages(content, author) VALUES(:content, :author)');
        $stmt->bindValue(':content', $messageToSave);
        $stmt->bindValue(':author', $_SESSION['ident']);
        $stmt->execute();

        // Get the id of the message freshly inserted
        $stmt2 = $connection->query("SELECT currval('messages_id_seq') AS last");
        $stmt2->execute();
        $answer = $stmt2->fetch();
        $last = $answer['last']; // $last est le id attribué lors du insert

        // We add the mentions in the database for every user mentioned if there are ones
        if ($mentionsExisting) {
            foreach ($existingUsers as  $user) {
                $stmt = $connection->prepare('INSERT INTO mentions(message_id, users_id) VALUES(:id, :login)');
                $stmt->bindValue(':id', $last);
                $stmt->bindValue(':login', $user);
                $stmt->execute();
            }
        }

        $data['result'] = $last;
    } else {
        $data['status'] = 'error';
        $data['result'] = null;
        $data['message'] = 'user are not signed in';
    }
} else {
    $data['status'] = 'error';
    $data['result'] = null;
    $data['message'] = 'source parameter is not set';
}

echo json_encode($data);
