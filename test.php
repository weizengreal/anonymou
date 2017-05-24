<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/4/24
 * Time: 下午10:52
 */


$quality="80.8";
$responsible="78.8";
$pass="80";
$inputQua="90";
$inputRes="65";
$inputPass="80";



var_dump(mathCount($quality,$responsible,$pass,$inputQua,$inputRes,$inputPass));



function mathCount($quality,$responsible,$pass,$inputQua,$inputRes,$inputPass) {
    $mathLevel = 8;
    $quality=$quality*((100-$mathLevel)/100)+$inputQua*($mathLevel/100);
    $responsible=$responsible*((100-$mathLevel)/100)+$inputRes*($mathLevel/100);
    $pass=$pass*((100-$mathLevel)/100)+$inputPass*($mathLevel/100);
    return array(
        'quality'=>sprintf("%.2f", $quality),
        'responsible'=>sprintf("%.2f", $responsible),
        'pass'=>sprintf("%.2f", $pass),
    );
}