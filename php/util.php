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
    while($nrOfEmptyCells > $counter) {
        array_push($columns, "");
        $counter++;
    }
}