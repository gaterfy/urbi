<!--
profile.php

authors : berfy

Profile page, user can post a message if it is his profile, otherwise we see the
informations of the user, that is to say, its messages, its followers, its name, ...
-->

<?php
session_start();

if (isset($_SESSION['ident'])) {
  $ident = $_SESSION['ident'];
  $name = $_SESSION['name'];
} else {
  $ident = '';
  $name = '';
}
$login = isset($_GET['user']) ? $_GET['user'] : $ident;
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $login; ?>'s profile</title>
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/popover.css">
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
  </head>
  <body>
    <?php require('header.php'); ?>
    <div class="main-container">
      <div class="profile-container">
        <p class="name" id="name"></p>
        <p class="login" id="login"></p>
        <p class="description" id="description"></p>
        <img class="avatar" src="services/getAvatar.php?size=large&user=<?php echo $login; ?>">
        <br>
        <?php
        if (isset($_SESSION['ident'])) {
            if ($login !== $_SESSION['ident']) {
              echo "<button type=\"button\" id=\"follow\">Follow</button>";
            }
        }
        ?>
        <p id="total-followers"></p>
        <p id="total-messages"></p>
      </div>

      <div class="message-container">
        <?php
        if (isset($_SESSION['ident'])) {
          if ($login === $_SESSION['ident']) {
            echo "<form id='postMessage'>";
            echo "<div id='error-messages'></div>";
            echo "<div><label>What's on your mind ?</label><input type='text' name='source' id='message'>";
            echo "<button type='submit' name='post' class='submit'>Post message</button></div></form>";
          }
        }
        ?>
        <div class="message-thread-container">
        <div>
          <span id='mentioned-button'>Mentioned</span>
          <span id='authored-button' class="active">Authored</span>
        </div>
        <div id="mentioned">
          <p class="message-thread-title">Mentioned Thread</p>
          <div class="message-thread" id="mentioned-thread"></div>
        </div>
        <div id="authored">
          <p class="message-thread-title">Authored Thread</p>
          <div class="message-thread" id="authored-thread"></div>
        </div>
      </div>
      <?php
      require('footer.php');
      ?>
    </div>

      <div class="follow-container">
        <p class="title">Followers</p>
        <div class="followers">
        </div>
        <p class="title">Following</p>
        <div class="following">
        </div>
      </div>
    </div>
    <!-- This input below is used to get the login of the connected user in JavaScript -->
    <input type="hidden" name="user" value="<?php echo $login; ?>" id="user-connected">
    <!-- The user that is viewing the page, used in the javascript -->
    <input type="hidden" name="viewer" value="<?php echo $ident; ?>" id="viewer">
    <input type="hidden" name="viewer_name" value="<?php echo $name; ?>" id="viewer_name">

    <!-- EOSection -->
    
    <script src="https://use.fontawesome.com/7cf9e4dadf.js"></script>
    <script src="js/Profile.js" charset="utf-8"></script>
    <script src="js/Message.js" charset="utf-8"></script>
    <script src="js/profile/buildProfile.js" charset="utf-8"></script>
    <?php
    if (isset($_SESSION['ident'])) {
        if ($login === $_SESSION['ident']) {
          echo '<script src="js/profile/sendMessage.js" charset="utf-8"></script>';
        }
    }
    ?>
  </body>
</html>
