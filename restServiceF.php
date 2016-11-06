<?php
const WRONG = "WRONG";
const CORRECT = "CORRECT";
header("Content-Type:application/json");
require_once('php/config/configF.php');
require('Enum.php');
require('EmailService.php');
include "ResultE.php";
include "TestResult.php";
const END_TEST_SESSION = "END_TEST_SESSION";
const GUESS = "GUESS";

function init()
{
    $_SESSION[Resources::LLAMAF_TRAINING_INFO] = loadFromJSON('resources/fTraining.json');
    $_SESSION[Resources::LLAMAF_TEST_CASES] = loadTestCases('resources/ftestquestions.json');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data['action'] == Action::START) {
        init();
        $result = initResult($data);
        $_SESSION[Resources::RESULT] = json_encode($result);
        $_SESSION[Resources::PROGRAM_PHASE] = ProgramPhase::LEARN_PHASE_STARTED;
        logg($data["name"]);
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

if (!empty($_GET['test-case-response'])) {
    if (!isset($_SESSION[Resources::PROGRAM_PHASE]) || $_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::NOT_STARTED) {
        logg(ProgramPhase::NOT_STARTED);
        deliver_response(200, "", ActionImage::START);
        return;
    }

    if ($_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::TEST_PHASE) {
        deliver_response(200, "", ActionImage::NEXT);
        return;
    }

    if ($_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::TEST_PHASE_STARTED) {
        $elapsed = microtime(true) - $_SESSION[Resources::START_TIME];

        $testCaseIndex = $_SESSION[Resources::TEST_CASE];
        $testCasesMap = $_SESSION[Resources::LLAMAF_TEST_CASES];
        $testCaseKey = array_keys($testCasesMap)[$testCaseIndex];

        $userResponse = $_GET['test-case-response']; //response options: v1 || v2
        $response = $testCasesMap[$testCaseKey][$userResponse]["isCorrect"];
        $answer = $testCasesMap[$testCaseKey][$userResponse]["text"];
//        logg(number_format($elapsed, 4));
        $result1 = ResultE::fromJSON($_SESSION[Resources::RESULT]);
        $isCorrect = $response == true ? CORRECT : WRONG;
        logg($isCorrect);
        $question = $testCasesMap[$testCaseKey]["v1"]["isCorrect"] ? $testCasesMap[$testCaseKey]["v1"]["text"] : $testCasesMap[$testCaseKey]["v2"]["text"];
        $result1->addTestResult(TestResult::withAnswerEvaluation($testCaseIndex, $question, $answer, $isCorrect, number_format($elapsed, 4)));
        $_SESSION[Resources::PROGRAM_PHASE] = ProgramPhase::TEST_PHASE;
        $_SESSION[Resources::TEST_CASE] = $_SESSION[Resources::TEST_CASE] + 1;
        if ($response == true) {
            $result1->setFinalResult($result1->getFinalResult() + 1);
            deliver_response_result(200, "", ActionImage::NEXT, CORRECT);
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
        $testCaseIndex = $_SESSION[Resources::TEST_CASE];
        $testCases = array_keys($_SESSION[Resources::LLAMAF_TEST_CASES]);
        if ($testCaseIndex == count($testCases)) {
            deliver_response_result(200, "", ActionImage::CLOSE, END_TEST_SESSION);
            writeResultToFileAndSendEmail();
            logg("End test session...");
            return;
        }

        $_SESSION[Resources::PROGRAM_PHASE] = ProgramPhase::TEST_PHASE_STARTED;
        $_SESSION[Resources::START_TIME] = microtime(true);
        logg("Test for:" . $testCases[$testCaseIndex]);
        $testCaseDTO["pictureName"] = $testCases[$testCaseIndex];
        $responseOptions = $_SESSION[Resources::LLAMAF_TEST_CASES][$testCases[$testCaseIndex]];
        $testCaseDTO["v1"] = $responseOptions["v1"]["text"];
        $testCaseDTO["v2"] = $responseOptions["v2"]["text"];
        deliver_response_result(200, $testCaseDTO, ActionImage::LISTEN, GUESS);
        return;
    }

    deliver_response(200, "", ActionImage::CLOSE);
}

if (isset($_GET['pictureIndex'])) {

    if (!isset($_SESSION[Resources::PROGRAM_PHASE]) || $_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::NOT_STARTED) {
        logg(ProgramPhase::NOT_STARTED);
        deliver_response(200, "", ActionImage::START);
        return;
    }

    if ($_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::LEARN_PHASE_STARTED) {
        $pictureAndSentence = getPictureAndSentence($_GET['pictureIndex']);
        deliver_response(200, $pictureAndSentence, ActionImage::TIMER);
        return;
    }

    if ($_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::TEST_PHASE) {
        deliver_response(200, "", ActionImage::NEXT);
        return;
    }

    deliver_response(200, "", ActionImage::CLOSE);
}


function getPictureAndSentence($index)
{
    return $_SESSION[Resources::LLAMAF_TRAINING_INFO][$index];
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

function loadFromJSON($fileName)
{
    $str = file_get_contents($fileName);
    return json_decode($str, true);
}

function loadTestCases($fileName)
{
    $str = file_get_contents($fileName);
    $test_map = json_decode($str, true);
    return $test_map;
}

function logg($data)
{
    $file = fopen("logs/logsF.txt", "a");
    fwrite($file, "\n");
    fwrite($file, print_r($data, true));
    fclose($file);
}

function writeResultToFileAndSendEmail()
{
    $result = ResultE::fromJSON($_SESSION[Resources::RESULT]);
    $finalResult = $result->getFinalResult();
    if (($finalResult - 10) > 0) {
        $finalResult = 10 * $finalResult - 100;
        $result->setFinalResult($finalResult . "%");
    } else {
        $result->setFinalResult("0%");
    }
    $file_name = $result->getName() . "_" . gmdate("Y-m-d H.i.s") . ".json";
    $nameWithPath = "results/f/" . $file_name;
    $myFile = fopen($nameWithPath, "w") or die("Unable to open file!");
    fwrite($myFile, json_encode($result));
    fclose($myFile);
    sendEmailWithAttachment($file_name, $nameWithPath, "F");
}

/**
 * @return ResultE
 */
function initResult($data)
{
    $result = new ResultE();
    $result->setName($data["name"]);
    $result->setNrOfSeconds($data["nrOfSeconds"]);
    $result->setFinalResult(0);
    $result->setStartDateTime(gmdate("Y-m-d H:i:s"));
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