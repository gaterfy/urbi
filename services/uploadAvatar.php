<?php
/*
 * uploadAvatar.php
 *
 * authors : Thomas Lombart - Martin Vasilev
 *
 * Uploads the avatar of a user and resizes it if necessary
 */

session_start();
require('../helpers/connection.php');
require('../helpers/initData.php');

$data = initData('post');
$args = $data["args"];


if (isset($_SESSION['ident'])) {
  /* Authentication succesful */
  if ($_FILES['image']['size']==0 || $_FILES['image']['name']=="") {
    /* No file was uplaoded */
    $data['result'] = null;
    $data['status'] = 'error';
    $data['message'] = 'No file was uploaded';
    echo json_encode($data);
    exit();
  }
  $type = $_FILES['image']['type'];
  $login = $_SESSION['ident'];
  /* Create the image file */
  if ($type == "image/jpeg") {
    $image = imagecreatefromjpeg($_FILES['image']['tmp_name']);
  } else if ($type == "image/png") {
    $image = imagecreatefrompng($_FILES['image']['tmp_name']);
  } else {
    $data['status'] = 'error';
    $data['result'] = null;
    $data['message'] = 'unsupported img type';
    echo json_encode($data);
    exit();
  }
  /* Get width et height of the image */
  list($w,$h) = getimagesize($_FILES['image']['tmp_name']);
  /* Request to check if user has a profile picture */
  $stmt = $connection->prepare('SELECT EXISTS(SELECT * FROM avatars where login=:user)');
  $stmt->bindValue(':user', $login);
  $stmt->execute();
  $arr = $stmt->fetch();
  /* Check if image is square */
  if ($w<256 || $h<256) {
    $data['result'] = null;
    $data['status'] = 'error';
    $data['message'] = 'image too small!';
    echo json_encode($data);
    exit();
  }
  if ($w != $h) {
    /* Resize the image if needed to be square */
    $new_size = min($w,$h); /* The new size of the square image */
    /* Create an empty image */
    $image_square = imagecreatetruecolor($new_size,$new_size);
    /* Create the biggest centered square possible */
    if ($new_size != $h) {
      imagecopyresampled($image_square,$image,0,0,0,(max($w,$h)-$new_size)/2,$new_size,$new_size,$new_size,$new_size);
    } else {
      imagecopyresampled($image_square,$image,0,0,(max($w,$h)-$new_size)/2,0,$new_size,$new_size,$new_size,$new_size);
    }

    /* Then we reduce it 256x256 */
    /* Final empty image */
    $image_final = imagecreatetruecolor(256,256);
    /* Now we reduce */
    imagecopyresampled($image_final,$image_square,0,0,0,0,256,256,$new_size,$new_size);
  } else {
    /* Image is already square, only reduction to 256x256 is necessary */
    $image_final = imagecreatetruecolor(256,256);
    imagecopyresampled($image_final,$image,0,0,0,0,256,256,$w,$h);
  }
  /* We need to now convert our image-type resource to a stream resource */
  /* We create a new empty *stream* resource, $flux */
  $flux = tmpfile();
  /* This will now wait for a command to read the standard channel */
  ob_start();
  /* Output the image data */
  imagepng($image_final);
  /* Collect it & Store it in a variable */
  $image_data = ob_get_contents();
  /* Clean ?*/
  ob_end_clean();
  /* Write the collected data to our stream */
  fwrite($flux,$image_data);
  /* Reset the stream at the start */
  fseek($flux,0);
  /* Insert it into the database */
  $insert = $connection->prepare("UPDATE avatars SET image=:image, type=:type WHERE login=:login");
  $insert->bindValue(':login', $login);
  $insert->bindValue(':image', $flux,PDO::PARAM_LOB);
  $insert->bindValue(':type', $type);
  $insert->execute();
  /* Close the stream */
  fclose($flux);
  /* Avatar has been added */
  unset($data['args']['image']['tmp_name']);
  $data['result'] = true;
} else {
  $data['status'] = 'error';
  $data['result'] = null;
  $data['message'] = 'user is not logged in';
}

echo json_encode($data);

?>
