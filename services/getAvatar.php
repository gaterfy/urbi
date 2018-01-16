<?php
/*
 * getAvatar.php
 *
 * authors : Berfy
 *
 * Gets the avatar of a user and resize it 
 */

require('../helpers/connection.php');
require('../helpers/initData.php');

$data = initData();
$args = $data['args'];

if (isset($args['user'])) {
    $stmt = $connection->prepare('SELECT EXISTS(SELECT * FROM users where login=:user)');
    $stmt->bindValue(':user', $args['user']);
    $stmt->execute();
    $arr = $stmt->fetch();

    if ($arr['exists']) {
      /* Reading from db */
        $query = 'SELECT image,type FROM avatars WHERE login=:user'; /* Modified */
        $stmt = $connection->prepare($query);
        $stmt->bindValue(':user', $args['user']);
        $stmt->execute();
        $stmt->bindColumn('image', $picture, PDO::PARAM_LOB);
        $stmt->bindColumn('type',$type); /* Modified */
        $stmt->fetch();
        /* Set header */
        header("Content-Type: {$type}");

        if (isset($args['size']) && $args['size']=='large') {
          /* No need to resize since images are stored in 256x256 */
          fpassthru($picture);
        } else {
          /* Resizing to the smaller version (48x48) */
          /* In order to use the resizing functions we need a filename (even in temporary) */
          $tmpHandle = tmpfile();
          stream_copy_to_stream($picture,$tmpHandle);
          $metaDatas = stream_get_meta_data($tmpHandle);
          $tmpFilename = $metaDatas['uri'];
          /* We store the image in a variable */
          $image = imagecreatefrompng($tmpFilename);
          /* Now we can resize the image */
          /* We create an empty image with the desired size of 48x48 */
          $result = imagecreatetruecolor(48,48);
          /* We now fill that image with the downsized version */
          imagecopyresampled($result,$image,0,0,0,0,48,48,256,256);
          /* Output the image */
          imagepng($result);
        }
    } else {
        echo json_encode(array('status' => 'error', 'args' => $args, 'result' => null));
        exit();
    }
} else {
    echo json_encode(array('status' => 'error', 'args' => $args, 'result' => null));
    exit();
}
?>
