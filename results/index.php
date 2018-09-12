<?php
include('../php/config.php');
include('../php/session.php');
include('../php/util.php');
$userDetails = $userClass->userDetails($session_uid);
if (!isAdmin($userDetails->name)) {
    header('HTTP/1.0 403 Forbidden');
    $url=BASE_URL.'accessForbidden.html';
    header("Location: $url");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Results</title>
</head>
<body>
<ul>
    <li>
        <a href="resultsB.php" target="_blank">Llama B</a>
    </li>
    <li>
        <a href="resultsD.php" target="_blank">Llama D</a>
    </li>
    <li>
        <a href="resultsE.php" target="_blank">Llama E</a>
    </li>
    <li>
        <a href="resultsF.php" target="_blank">Llama F</a>
    </li>
</ul>

</body>
</html>