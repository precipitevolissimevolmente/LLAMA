<?php
include("php/config.php");
include('php/userClass.php');
$userClass = new userClass();

$errorMsgReg = '';
$errorMsgLogin = '';
/* Login Form */
if (!empty($_POST['loginSubmit'])) {
    $usernameEmail = $_POST['usernameEmail'];
    $password = $_POST['password'];
    if (strlen(trim($usernameEmail)) > 1 && strlen(trim($password)) > 1) {
        $uid = $userClass->userLogin($usernameEmail, $password);
        if ($uid) {
            $url = BASE_URL . 'home.php';
            header("Location: $url"); // Page redirecting to home.php
        } else {
            $errorMsgLogin = "Please check login details.";
        }
    }
}

/* Signup Form */
if (!empty($_POST['signupSubmit'])) {
    $username = $_POST['usernameReg'];
    $email = $_POST['emailReg'];
    $password = $_POST['passwordReg'];
    $name = $_POST['nameReg'];
    /* Regular expression check */
    $username_check = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $username);
//    $email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
//    $password_check = preg_match('~^[A-Za-z0-9!@#$%^&*()_]{6,20}$~i', $password);
    if ($username_check && strlen(trim($name)) > 0) {
        $uid = $userClass->userRegistration($username, $password, $email, $name);
        if ($uid) {
            $errorMsgReg = "AAAAAAAAAABD";
            $url = BASE_URL . 'home.php';
            header("Location: $url"); // Page redirecting to home.php
        } else {
            $errorMsgReg = "Username or Email already exists.";
        }
    } else {
        $errorMsgReg = "please fill data";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div id="mainContainer">
    <div id="login">
        <h3>Login</h3>
        <form method="post" action="" name="login">
            <label>Username or Email</label>
            <input type="text" name="usernameEmail" autocomplete="off"/>
            <label>Password</label>
            <input type="password" name="password" autocomplete="off"/>
            <div class="errorMsg"><?php echo $errorMsgLogin; ?></div>
            <input type="submit" class="button" name="loginSubmit" value="Login">
        </form>
    </div>

    <div id="signup" class="clear">
        <h3>Registration</h3>
        <form method="post" action="" name="signup">
            <label>*Username</label>
            <input type="text" name="usernameReg" autocomplete="off"/>
            <label>Email (optional)</label>
            <input type="text" name="emailReg" autocomplete="off"/>
            <label>*Password</label>
            <input type="password" name="passwordReg" autocomplete="off"/>
            <div class="errorMsg"><?php echo $errorMsgReg; ?></div>
            <input type="submit" class="button" name="signupSubmit" value="Signup">
        </form>
    </div>
</div>
</body>
</html>