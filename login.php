<?php
session_start();
?>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="./css/login.css">
</head>
<body>
<div id="login-box">
    <div id="shadow"></div>
    <div id="inner_box">
        <form action="process-login.php" method="post" id="LoginForm">
            <p class="form-label">Username</p>
            <input type="text" placeholder="Username" name="user" id="user" class="login-input">
            <p class="form-label">Password</p>
            <input type="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" name="pass" id="pass" class="login-input">
            <br>
            <input type="submit" name="submit" value="Login" class="login-submit">
        </form>
    </div>
</div>
</body>
</html>