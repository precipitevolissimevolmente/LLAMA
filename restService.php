<?php
const WRONG = "WRONG";
const CORRECT = "CORRECT";
header("Content-Type:application/json");
require_once('php/config/config.php');
require('Enum.php');
require('EmailService.php');
include "Result.php";
include "TestResult.php";
const END_TEST_SESSION = "END_TEST_SESSION";
const GUESS = "GUESS";

if (!empty($_GET['word'])) {
    $hash_result = get_hash($_GET['word']);
    deliver_response(200, $hash_result, "");
}

/**
 * @param $data
 */
function initVocabulary($data)
{
    $pictureKeyVsName = loadVocabulary();
    if ($data['randomisationSequence'] != 2003) {
        //randomise todo
    }
    //shuffle vocabulary order
//    logg($pictureKeyVsName);
    $randomizedNames = $pictureKeyVsName;
    shuffle($randomizedNames);
//    logg($randomizedNames);
//    logg($pictureKeyVsName);
//    logg(array_flip($pictureKeyVsName));
    $_SESSION[Resources::VOCABULARY_RANDOMIZED_VALUES] = $randomizedNames;
    $_SESSION[Resources::VOCABULARY_KEY_VALUE] = $pictureKeyVsName;
    $_SESSION[Resources::VOCABULARY_VALUE_KEY] = array_flip($pictureKeyVsName);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data['action'] == Action::START) {
        initVocabulary($data);
        $result = initResult($data);
        $_SESSION[Resources::RESULT] = json_encode($result);
        $_SESSION[Resources::PROGRAM_PHASE] = ProgramPhase::LEARN_PHASE_STARTED;
        logg($data["name"]);
//        logg($_SESSION[Resources::PROGRAM_PHASE]);
        return;
    }

    if ($data["action"] == Action::START_TEST) {
        $_SESSION[Resources::TEST_CASE] = 0;
        $_SESSION[Resources::PROGRAM_PHASE] = ProgramPhase::TEST_PHASE;
        logg($_SESSION[Resources::PROGRAM_PHASE]);
        return;
    }

    if ($data["action"] == Action::CLOSE) {
        endSession();
        return;
    }
}

if (!empty($_GET['pic-id'])) {
    if (!isset($_SESSION[Resources::PROGRAM_PHASE]) || $_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::NOT_STARTED) {
        logg(ProgramPhase::NOT_STARTED);
        deliver_response(200, "", ActionImage::START);
        return;
    }

    if ($_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::LEARN_PHASE_STARTED) {
        $pic_name = get_picture_name($_GET['pic-id']);
//        logg("GET name for " . $pic_name . "........");
        deliver_response(200, $pic_name, ActionImage::TIMER);
        return;
    }

    if ($_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::TEST_PHASE) {
        deliver_response(200, "", ActionImage::NEXT);
        return;
    }

    if ($_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::TEST_PHASE_STARTED) {
        $testCase = $_SESSION[Resources::TEST_CASE];
        $testCaseVsName = $_SESSION[Resources::VOCABULARY_RANDOMIZED_VALUES];

        $pic_name = get_picture_name($_GET['pic-id']);
        $actual_pic_name = $testCaseVsName[$testCase];
        logg($pic_name);
        logg($actual_pic_name);
        $elapsed = microtime(true) - $_SESSION[Resources::START_TIME];
//        logg(number_format($elapsed, 4));
        $result1 = Result::fromJSON($_SESSION[Resources::RESULT]);
        $result1->addTestResult(new TestResult($testCase, $actual_pic_name, $pic_name, number_format($elapsed, 4)));
        $_SESSION[Resources::PROGRAM_PHASE] = ProgramPhase::TEST_PHASE;
        $_SESSION[Resources::TEST_CASE] = $_SESSION[Resources::TEST_CASE] + 1;
        if ($pic_name == $actual_pic_name) {
            deliver_response_result(200, "", ActionImage::NEXT, CORRECT);
            $result1->setFinalResult($result1->getFinalResult() + 1);
        } else {
            deliver_response_result(200, "", ActionImage::NEXT, WRONG);
        }
        $_SESSION[Resources::RESULT] = json_encode($result1);
        return;
    }

    deliver_response(200, "", ActionImage::CLOSE);
}

if (!empty($_GET['next'])) {
    if (!isset($_SESSION[Resources::PROGRAM_PHASE]) || $_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::NOT_STARTED) {
        logg(ProgramPhase::NOT_STARTED);
        deliver_response(200, "", ActionImage::START);
        return;
    }

    if ($_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::TEST_PHASE) {
        $testCase = $_SESSION[Resources::TEST_CASE];
        $testCaseVsName = $_SESSION[Resources::VOCABULARY_RANDOMIZED_VALUES];
        if ($testCase == count($testCaseVsName)) {
            deliver_response_result(200, "", ActionImage::CLOSE, END_TEST_SESSION);
            writeResultToFile();
            logg("End test session...");
            return;
        }

        $_SESSION[Resources::PROGRAM_PHASE] = ProgramPhase::TEST_PHASE_STARTED;
        $_SESSION[Resources::START_TIME] = microtime(true);
        logg("Test for:" . $testCaseVsName[$testCase]);
        $itemToGuess = $testCaseVsName[$testCase];
        deliver_response_result(200, $itemToGuess, ActionImage::CHOSE, GUESS);
        return;
    }

    deliver_response(200, "", ActionImage::CLOSE);
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

function deliver_response_result($status, $data, $nextAction, $result)
{
    header("HTTP/1.1 $status");
    $response['status'] = $status;
    $response['data'] = $data;
    $response['next_action'] = $nextAction;
    $response['result'] = $result;

    $json_response = json_encode($response);
    echo $json_response;
}

function get_hash($word)
{
    return hash('sha256', $word);
}

function get_picture_name($id)
{
    $vocabularyKeyValue = $_SESSION[Resources::VOCABULARY_KEY_VALUE];
    return $vocabularyKeyValue[$id];
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

function writeResultToFile()
{
    $result = Result::fromJSON($_SESSION[Resources::RESULT]);
    $result->setFinalResult((($result->getFinalResult() * 100) / 20) . "%");
    $file_name = $result->getName() . "_" . (new \DateTime())->format('Y-m-d His') . ".json";
    $nameWithPath = "results/" . $file_name;
    $myFile = fopen($nameWithPath, "w") or die("Unable to open file!");
    fwrite($myFile, json_encode($result));
    fclose($myFile);
    sendEmailWithAttachment($file_name, $nameWithPath, "B");
}

/**
 * @return Result
 */
function initResult($data)
{
    $result = new Result();
    $result->setName($data["name"]);
    $result->setNrOfSeconds($data["nrOfSeconds"]);
    $result->setRandomisationSequence($data["randomisationSequence"]);
    $result->setFinalResult(0);
    return $result;
}

function endSession()
{
    logg("Close....");
    // remove all session variables
    session_unset();
    // destroy the session
    session_destroy();
}

?>