<?php
const WRONG = "WRONG";
const CORRECT = "CORRECT";
header("Content-Type:application/json");
require_once('php/config/configE.php');
require('Enum.php');
require('EmailService.php');
include "ResultE.php";
include "TestResult.php";
const END_TEST_SESSION = "END_TEST_SESSION";
const GUESS = "GUESS";

function init()
{
    $_SESSION[Resources::LLAMAE_TRAINING_SOUNDS] = loadSounds('resources/esounds.json');
    $_SESSION[Resources::LLAMAE_TEST_CASES] = loadTestCases('resources/etestquestions.json');
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
        $testCaseIndex = $_SESSION[Resources::TEST_CASE];
        $testCasesMap = $_SESSION[Resources::LLAMAE_TEST_CASES];
        $testCaseKey = array_keys($testCasesMap)[$testCaseIndex];

        $userResponse = $_GET['test-case-response']; //(v1 || v2)
        $response = $testCasesMap[$testCaseKey][$userResponse]["isCorrect"];
        $elapsed = microtime(true) - $_SESSION[Resources::START_TIME];
//        logg(number_format($elapsed, 4));
        $result1 = ResultE::fromJSON($_SESSION[Resources::RESULT]);
        $isCorrect = $response == true ? CORRECT : WRONG;
        logg($isCorrect);
        $result1->addTestResult(TestResult::withAnswerEvaluation($testCaseIndex, $testCaseKey, null, $isCorrect, number_format($elapsed, 4)));
        $_SESSION[Resources::PROGRAM_PHASE] = ProgramPhase::TEST_PHASE;
        $_SESSION[Resources::TEST_CASE] = $_SESSION[Resources::TEST_CASE] + 1;
        if ($response == true) {
            $result1->setFinalResult($result1->getFinalResult() + 2.5);
            deliver_response_result(200, "", ActionImage::NEXT, CORRECT);
        } else {
            $result1->setFinalResult($result1->getFinalResult() - 2.5);
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
        $testCases = array_keys($_SESSION[Resources::LLAMAE_TEST_CASES]);
        if ($testCaseIndex == count($testCases)) {
            deliver_response_result(200, "", ActionImage::CLOSE, END_TEST_SESSION);
            writeResultToFileAndSendEmail();
            logg("End test session...");
            return;
        }

        $_SESSION[Resources::PROGRAM_PHASE] = ProgramPhase::TEST_PHASE_STARTED;
        $_SESSION[Resources::START_TIME] = microtime(true);
        logg("Test for:" . $testCases[$testCaseIndex]);
        $testCaseDTO["soundFileName"] = $testCases[$testCaseIndex];
        $responseOptions = $_SESSION[Resources::LLAMAE_TEST_CASES][$testCases[$testCaseIndex]];
        $testCaseDTO["v1"] = $responseOptions["v1"]["text"];
        $testCaseDTO["v2"] = $responseOptions["v2"]["text"];
        deliver_response_result(200, $testCaseDTO, ActionImage::LISTEN, GUESS);
        return;
    }

    deliver_response(200, "", ActionImage::CLOSE);
}

if (isset($_GET['soundIndex'])) {

    if (!isset($_SESSION[Resources::PROGRAM_PHASE]) || $_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::NOT_STARTED) {
        logg(ProgramPhase::NOT_STARTED);
        deliver_response(200, "", ActionImage::START);
        return;
    }

    if ($_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::LEARN_PHASE_STARTED) {
        $soundFileName = getSoundFileName($_GET['soundIndex']);
        deliver_response(200, $soundFileName, ActionImage::TIMER);
        return;
    }

    if ($_SESSION[Resources::PROGRAM_PHASE] == ProgramPhase::TEST_PHASE) {
        deliver_response(200, "", ActionImage::NEXT);
        return;
    }

    deliver_response(200, "", ActionImage::CLOSE);
}


function getSoundFileName($index)
{
    return $_SESSION[Resources::LLAMAE_TRAINING_SOUNDS][$index];
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

function loadSounds($fileName)
{
    $str = file_get_contents($fileName);
    return json_decode($str, true);
}

function loadTestCases($fileName)
{
    $str = file_get_contents($fileName);
    $test_map = json_decode($str, true);
//    $testCases = Array();
//    foreach ($test_map as $key => $value) {
////                logg($value["questionNumber"]);
////        $result->addTestResult(new TestResult($value["questionNumber"], $value["question"], $value["answer"], $value["answerTimeSeconds"]));
//        $testCaseResponse = new ETestCaseResponse($value["v1"]["text"], $value["v1"]["isCorrect"]);
//        $testCases[$key] = new ETestCase($testCaseResponse, $testCaseResponse);
//    }
    return $test_map;
}

function logg($data)
{
    $file = fopen("logs/logsE.txt", "a");
    fwrite($file, "\n");
    fwrite($file, print_r($data, true));
    fclose($file);
}

function writeResultToFileAndSendEmail()
{
    $result = ResultE::fromJSON($_SESSION[Resources::RESULT]);
    $result->setFinalResult($result->getFinalResult() . "%");
    $file_name = $result->getName() . "_" . (new \DateTime())->format('Y-m-d His') . ".json";
    $nameWithPath = "results/e/" . $file_name;
    $myFile = fopen($nameWithPath, "w") or die("Unable to open file!");
    fwrite($myFile, json_encode($result));
    fclose($myFile);
    sendEmailWithAttachment($file_name, $nameWithPath, "E");
}

/**
 * @return Result
 */
function initResult($data)
{
    $result = new ResultE();
    $result->setName($data["name"]);
    $result->setNrOfSeconds($data["nrOfSeconds"]);
    $result->setFinalResult(10);
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