<?php
header("Content-Type:application/json");
require('Enum.php');
require('EmailService.php');

/**
 * @param $data
 * @return mixed
 */
function getVocabulary($data)
{
    $pictureKeyVsName = loadVocabulary();
    if ($data['randomisationSequence'] != 2003) {
        //randomise todo
    }
    return $pictureKeyVsName;
}

function getTestCases($pictureKeyVsName)
{
    $randomizedNames = $pictureKeyVsName;
    shuffle($randomizedNames);
    return $randomizedNames;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = json_decode($_GET['data'], true);

    if ($data['action'] == Action::START) {
        logg($data);
        $vocabulary = getVocabulary($data);
        $testCases = getTestCases($vocabulary);
        $response['vocabulary'] = $vocabulary;
        $response['testCases'] = $testCases;

        deliver_response_base64(200, json_encode($response));
        return;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    writeResultToFile($data);
    logg($data);
    return;
}

function deliver_response_base64($status, $data)
{
    header("HTTP/1.1 $status");
    $response['status'] = $status;
    $response['data'] = base64_encode($data);

    $json_response = json_encode($response);
    echo $json_response;
}

function deliver_response($status, $data, $nextImgAction)
{
    header("HTTP/1.1 $status");
    $response['status'] = $status;
    $response['data'] = $data;
    $response['next_action'] = $nextImgAction;

    $json_response = json_encode($response);
    echo $json_response;
}

function get_hash($word)
{
    return hash('sha256', $word);
}

function loadVocabulary()
{
    $str = file_get_contents('vocabulary.json');
    return json_decode($str, true);
}

function logg($data)
{
    $file = fopen("logs/logs.txt", "a");
    fwrite($file, "\n");
    fwrite($file, print_r($data, true));
    fclose($file);
}

function writeResultToFile($result)
{
    $file_name = $result['name'] . "_" . (new \DateTime())->format('Y-m-d His') . ".json";
    $nameWithPath = "results/b/" . $file_name;
    $myFile = fopen($nameWithPath, "w") or die("Unable to open file!");
    fwrite($myFile, json_encode($result));
    fclose($myFile);
    sendEmailWithAttachment($file_name, $nameWithPath, "B");
}

?>