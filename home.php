<?php
include('php/config.php');
include('php/session.php');
$userDetails=$userClass->userDetails($session_uid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LLMA</title>
    <script language="javascript" type="text/javascript">
        function goclicky(adr, height, width, windowName) {
            var x = screen.width / 2 - width / 2;
            var y = screen.height / 2 - height / 2;
            window.open(adr.href, windowName,
                'toolbar=no, menubar=no, location=no, scrollbars=no, resizable=no, height=' + height + ',width=' + width + ',left=' + x + ',top=' + y);
        }
    </script>
</head>
<body>
<h1>Welcome <?php echo $userDetails->name; ?></h1>
<ul>
    <li>
        <a href="llamab.php" onclick="goclicky(this, 575, 615, 'Llama B'); return false;" target="_blank">Llama B</a>
    </li>
    <li>
        <a href="llamad.php" onclick="goclicky(this, 310, 400, 'Llama D'); return false;"
           target="_blank">Llama D</a>
    </li>
    <li>
        <a href="llamae.php" onclick="goclicky(this, 380, 500, 'Llama E'); return false;"
           target="_blank">Llama E</a>
    </li>
    <li>
        <a href="llamaf.php" onclick="goclicky(this, 440, 610, 'Llama F'); return false;"
           target="_blank">Llama F</a>
    </li>
</ul>
</body>
</html>

<h4><a href="<?php echo BASE_URL; ?>logout.php">Logout</a></h4>