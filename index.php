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
    if (strlen(trim($usernameEmail)) > 1 && strlen(trim($password)) > 0) {
        $uid = $userClass->userLogin($usernameEmail, $password);
        if ($uid) {
            $url = BASE_URL . 'home.php';
            header("Location: $url"); // Page redirecting to home.php
        } else {
            $errorMsgLogin = "Please check login details.";
        }
    }
}

/* SignUp Form */
if (!empty($_POST['signupSubmit'])) {
    $username = $_POST['usernameReg'];
    $email = $_POST['emailReg'];
    $password = $_POST['passwordReg'];
//    $name = $_POST['nameReg'];
    /* Regular expression check */
    $username_check = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $username);
//    $email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
//    $password_check = preg_match('~^[A-Za-z0-9!@#$%^&*()_]{6,20}$~i', $password);
    if ($username_check && strlen(trim($username)) > 0 && strlen(trim($password)) > 0) {
        $uid = $userClass->userRegistration($username, $password, $email, $username);
        if ($uid) {
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
    <title>LLAMA language aptitude test</title>
    <!--    <script src="js/angular-1.5.5/angular.js"></script>-->
</head>
<body>
<div id="mainContainer">
    <h2>LLAMA language aptitude test</h2>
    <h4>Please use your student number for registration.</h4>
    <div id="login">
        <h3>Login</h3>
        <form method="post" action="" name="login">
            <label for="usernameEmail">Username or Email</label>
            <input id="usernameEmail" type="text" name="usernameEmail" autocomplete="off" required/>
            <label for="login-password">Password</label>
            <input id="login-password" type="password" name="password" autocomplete="off" required/>
            <div class="errorMsg"><?php echo $errorMsgLogin; ?></div>
            <input type="submit" class="button" name="loginSubmit" value="Login">
        </form>
    </div>

    <div id="signup" class="clear">
        <h3>Registration</h3>
        <form method="post" action="" name="signup">
            <label for="usernameReg">*Username</label>
            <input id="usernameReg" type="text" name="usernameReg" autocomplete="off" required/>
            <label for="emailReg">Email (optional)</label>
            <input id="emailReg" type="text" name="emailReg" autocomplete="off"/>
            <label for="passwordReg">*Password</label>
            <input id="passwordReg" type="password" name="passwordReg" autocomplete="off" required/>
            <div class="errorMsg"><?php echo $errorMsgReg; ?></div>
            <input type="submit" class="button" name="signupSubmit" value="Signup">
        </form>
    </div>
</div>
</body>
</html>