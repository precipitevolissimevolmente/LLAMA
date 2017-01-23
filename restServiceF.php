<?php
header("Content-Type:application/json");
require('Enum.php');
require('EmailService.php');
include('php/config.php');
require('php/util.php');
const WRONG = "WRONG";
const CORRECT = "CORRECT";
const END_TEST_SESSION = "END_TEST_SESSION";
const GUESS = "GUESS";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = json_decode($_GET['data'], true);

    if ($data['action'] == Action::START) {
        $trainingQuestions = loadJSON('resources/fTrainingQuestions.json');
        $testQuestions = loadJSON('resources/fTestQuestions.json');
        $testQuestionsOrder = loadJSON('resources/fTestQuestionsOrder.json');

        $response['trainingQuestions'] = $trainingQuestions;
        $response['testQuestions'] = $testQuestions;
        $response['testQuestionsOrder'] = $testQuestionsOrder;
        $encoded = json_encode($response);

        deliver_response_base64(200, $encoded);
        return;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = file_get_contents('php://input');
    $data = json_decode(base64_decode($data), true);
    writeResultToFile($data);
    return;
}

function logg($data)
{
    $file = fopen("logs/logsF.txt", "a");
    fwrite($file, "\n");
    fwrite($file, print_r($data, true));
    fclose($file);
}

function deliver_response_base64($status, $data)
{
    header("HTTP/1.1 $status");
    $response['status'] = $status;
    $response['data'] = base64_encode($data);

    $json_response = json_encode($response);
    echo $json_response;
}

function writeResultToFile($result)
{
    $username = $result['name'];
    $tst_result = rtrim($result['finalResult'], "%");
    $timeStamp = gmdate("Y-m-d h:m:s");
    $llama = "f";
    saveResultToDb($username, $tst_result, $timeStamp, $llama);

    $file_name = $username . "_" . gmdate("Y-m-d H.i.s") . ".json";
    logg($file_name);
    $nameWithPath = "results/f/" . $file_name;
    $myFile = fopen($nameWithPath, "w") or die("Unable to open file!");
    fwrite($myFile, json_encode($result));
    fclose($myFile);
    sendEmailWithAttachment($file_name, $nameWithPath, "F");
}

?>