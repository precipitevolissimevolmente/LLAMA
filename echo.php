<?php
/**
 * Created by PhpStorm.
 * User: CG
 * Date: 6/16/2016
 * Time: 9:22 PM
 */
$str = file_get_contents("soundsTestOrder.json");
$mapT = json_decode($str, true);
echo print_r($mapT, true);
echo print_r(array_keys($mapT), true);



