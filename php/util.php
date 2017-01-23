<?php
function loadJSON($file)
{
    $str = file_get_contents($file);
    return json_decode($str, true);
}

/**
 * @return mixed
 */
function addEmptyCelLs(&$columns, $nrOfEmptyCells)
{
    $counter = 0;
    while ($nrOfEmptyCells > $counter) {
        array_push($columns, "");
        $counter++;
    }
}

function saveResultToDb($username, $tst_result, $timeStamp, $llama)
{
    $db = getDB();
    $st = $db->prepare("SELECT uid FROM users WHERE username=:username");
    $st->bindParam("username", $username, PDO::PARAM_STR);
    $st->execute();
    $uid = $st->fetch(PDO::FETCH_OBJ)->uid;

    $stmt = $db->prepare("INSERT INTO results(user_id,result, timestamp, llama) VALUES (:user_id, :result, :timestamp, :llama)");
    $stmt->bindParam("user_id", $uid, PDO::PARAM_INT);
    $stmt->bindParam("result", $tst_result, PDO::PARAM_INT);
    $stmt->bindParam("timestamp", $timeStamp, PDO::PARAM_STR);
    $stmt->bindParam("llama", $llama, PDO::PARAM_STR);
    $stmt->execute();
    $db = null;
}

function getResultFromDb($username, $llama)
{
    $db = getDB();
    $st = $db->prepare("SELECT uid FROM users WHERE username=:username");
    $st->bindParam("username", $username, PDO::PARAM_STR);
    $st->execute();
    $uid = $st->fetch(PDO::FETCH_OBJ)->uid;

    $st = $db->prepare("SELECT result FROM results WHERE user_id=:user_id AND llama=:llama ORDER BY id_result DESC LIMIT 1");
    $st->bindParam("user_id", $uid, PDO::PARAM_INT);
    $st->bindParam("llama", $llama, PDO::PARAM_STR);
    $st->execute();
    $count= $st->rowCount();
    $result = "";
    if($count > 0) {
        $result = $st->fetch(PDO::FETCH_OBJ)->result;
    }
    $db = null;
    return $result;
}