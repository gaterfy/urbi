<!--
signup.php

authors : Berfy

Signup page
-->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Signup</title>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="stylesheet" href="css/form.css">
</head>

<body>
    <h2 class="title">Signup and start sharing something at Urbik with the world</h2>
    <form id="signup">
        <div id="error-messages">
        </div>
        <div>
            <label for="email">Login</label>
            <input type="text" name="login" placeholder="soldier76" id="login">
        </div>
        <div>
            <label for="login">Name</label>
            <input type="text" name="name" placeholder="Jack Morrison" id="name">
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="mySecretPassword" id="password">
        </div>
        <div>
            <label for="description">Your description (optional) </label>
            <textarea name="description" rows="5" placeholder="Hi everybody, I'm a special Overwatch agent" id="description"></textarea>
            <button type="submit" id="submit" class="submit">Create my account</button>
        </div>
        <a href="signin.php" class="link">Already have an account ? Click here !</a>
    </form>
    <script src="js/account/signup.js" charset="utf-8"></script>
</body>

</html>
