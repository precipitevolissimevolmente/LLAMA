<?php
header("Content-Type:application/json");
require('Enum.php');
require('EmailService.php');
include "ResultD.php";

function getSoundMap() {
    return loadSounds('sounds.json');
}

function getSoundTestOrder() {
    return loadSounds('soundsTestOrder.json');
}

function init()
{
    $_SESSION[Resources::SOUND_MAP] = loadSounds('sounds.json');
    $_SESSION[Resources::SOUND_TEST_ORDER] = loadSounds('soundsTestOrder.json');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = file_get_contents('php://input');
    $data = json_decode(base64_decode($data), true);
    writeResultToFile($data);
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = json_decode($_GET['data'], true);

    if ($data['action'] == Action::START) {
        $soundsMap = getSoundMap();
        $testOrder = getSoundTestOrder();
        $response['soundMap'] = $soundsMap;
        $response['testOrder'] = $testOrder;
        $encoded = json_encode($response);

        deliver_response_base64(200, $encoded);
        return;
    }
}

function deliver_response_base64($status, $data)
{
    header("HTTP/1.1 $status");
    $response['status'] = $status;
    $response['data'] = base64_encode($data);

    $json_response = json_encode($response);
    echo $json_response;
}

function loadSounds($fileName)
{
    $str = file_get_contents($fileName);
    return json_decode($str, true);
}

function logg($data)
{
    $file = fopen("logs/logsD.txt", "a");
    fwrite($file, "\n");
    fwrite($file, print_r($data, true));
    fclose($file);
}

function writeResultToFile($result)
{
    $file_name = $result['name'] . "_" . gmdate("Y-m-d H.i.s") . ".json";
    logg($file_name);
    $nameWithPath = "results/d/" . $file_name;
    $myFile = fopen($nameWithPath, "w") or die("Unable to open file!");
    fwrite($myFile, json_encode($result));
    fclose($myFile);
    sendEmailWithAttachment($file_name, $nameWithPath, "D");
}

?>