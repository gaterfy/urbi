<!--
signin.php

authors : Berfy

Signin page
-->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sign in</title>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="stylesheet" href="css/form.css">
</head>

<body>
    <h2 class="title">Welcome to Urbik !</h2>
    <form id="signin">
        <div id="error-messages">
        </div>
        <div>
            <label for="email">Login</label>
            <input type="text" name="login" placeholder="soldier76" id="login">
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="mySecretPassword" id="password">
            <button type="submit" id="submit" class="submit">Sign In</button>
        </div>
        <a href="signup.php" class="link">No account ? Click here !</a>
    </form>
    <script src="js/account/signin.js" charset="utf-8"></script>
</body>

</html>
