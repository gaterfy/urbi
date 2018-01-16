<!--
settings.php

authors : Berfy

User can modify its informations on this page
-->

<?php
session_start();

// If user is not signed in, we redirect him to the signin page
if (!isset($_SESSION['ident'])) {
  header('Location: signin.php');
}
?>

<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <title>Modify my profile</title>
      <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
      <link rel="stylesheet" href="css/form.css">
      <link rel="stylesheet" href="css/feedback.css">
      <link rel="stylesheet" href="css/footer.css">
   </head>
   <body>
     <p class="settings-title">Update your profile</p>
     <div id="feedback" class="feedback"></div>
      <div id="avatar_div">
         <form enctype="multipart/form-data" id="avatar" >
           <input type="file" name="image" class="input-file" id="input-file"/>
           <label for="input-file" class="label-input-file"><i class="fa fa-file-image-o" aria-hidden="true"></i> Choose your new avatar...</label>
           <button type="submit" class="submit">Upload</button>
         </form>
      </div>
      <form action="services/setProfile.php" method="post" id="updateProfile" onsubmit="return initValidateForm()">
         <div id="error-messages-profile">
         </div>
         <div>
            <label for="login">Name</label>
            <input type="text" name="name" id="name">
         </div>
         <div>
            <label for="password">New password</label>
            <input type="password" name="password" id="password">
         </div>
         <div>
            <label for="description">Description</label>
            <textarea name="description" rows="5" id="description"></textarea>
            <button type="submit" id="submit" class="submit">Update my profile</button>
         </div>
         <a href="profile.php" class="link">Go back to my profile</a>
      </form>
      <?php
      require('footer.php');
      ?>
      <!-- This input below is used to get the login of the connected user in JavaScript -->
      <input type="hidden" name="user" value="<?php echo $_SESSION['ident']; ?>" id="user">
      
      <script src="https://use.fontawesome.com/7cf9e4dadf.js"></script>
      <script src="js/profile/updateProfile.js" charset="utf-8"></script>
   </body>
</html>
