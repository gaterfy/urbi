<!--
index.php
KUNSANGABO 

Homepage, this is the page that users will see first.
A unregistered user can see the message thread and searches users on this page
A registered and logged user can see the messages of the persons that he follows
-->

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Urbik</title>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/popover.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
  </head>
  <body>
    <?php
    session_start();
    require('header.php');
    ?>
    <div id="search-user">
        <p><i class="fa fa-2x fa-search" aria-hidden="true"></i></p>
        <input type="text" name="searched" id="user">
    </div>
    <div id="found-users"></div>
    <p class="title">Poster quelque chose ici</p>
    <div id="message-thread">
    </div>
    <?php
    require('footer.php');
    ?>
    <script src="https://use.fontawesome.com/7cf9e4dadf.js"></script>
    <script src="js/Profile.js" charset="utf-8"></script>
    <script src="js/Message.js" charset="utf-8"></script>
    <script src="js/index.js" charset="utf-8"></script>
  </body>
</html>
