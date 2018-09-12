<?php
// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=LLAMA_D_RESULTS.csv');

require('../php/util.php');
include('../php/config.php');
include('../php/session.php');
$userDetails = $userClass->userDetails($session_uid);
if (!isAdmin($userDetails->name)) {
    header('HTTP/1.0 403 Forbidden');
    $url=BASE_URL.'accessForbidden.html';
    header("Location: $url");
}

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

$q_default_order = loadJSON('../soundsTestOrder.json');
$columns = array('');
$i = 0;

foreach ($q_default_order as $question) {
    $i++;
    array_push($columns, $i);
    addEmptyCelLs($columns, 2);
}
array_push($columns, 'Total');
array_push($columns, 'Start Date Time');
// output the column headings for first row
fputcsv($output, $columns);


$questions = array('Question >');
foreach ($q_default_order as $question) {
    array_push($questions, $question);
    addEmptyCelLs($questions, 2);
}
// output questions rows
fputcsv($output, $questions);

$thirdRowColumns = array('Name');
for ($i = 0; $i < sizeof($q_default_order); $i++) {
    array_push($thirdRowColumns, "Is correct");
    array_push($thirdRowColumns, "Answer");
    array_push($thirdRowColumns, "Reaction time (s)");
}
// output rows
fputcsv($output, $thirdRowColumns);

$dir = 'd';
$files = array_filter(scandir($dir), function ($item) {
    return is_file('d/' . $item);
});
usort($files, 'strnatcasecmp');

foreach ($files as $file) {
    $result = loadJSON($dir . '/' . $file);
    $result_cols = array($file);

    $testResults = $result['testResults'];

    foreach ($testResults as $testCaseResult) {
        array_push($result_cols, $testCaseResult['isCorrect'] ? 1 : 0);
        array_push($result_cols, $testCaseResult['answer']);
        array_push($result_cols, $testCaseResult['answerTimeSeconds']);
    }

    array_push($result_cols, $result['finalResult']);
    if (isset($result['startDateTime'])) {
        array_push($result_cols, $result['startDateTime']);
    }
    fputcsv($output, $result_cols);
}