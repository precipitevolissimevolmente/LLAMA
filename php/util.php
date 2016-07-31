<?php
function loadJSON($file)
{
    $str = file_get_contents($file);
    return json_decode($str, true);
}