<?php

/**
 * @author Amir Ali Jiwani <amir.ali@pi-labs.net>
 * @copyright 2011
 * @link http://www.facebook.com/aajiwani
 * @version 1.0
 */

ob_start();

session_start();

setcookie("TestCookie1", 'TestValue1', time()+3600, "/subdir/", ".localhost", 1, 1);
setcookie("TestCookie2", 'TestValue2', time()+3600, "/subdir/", ".localhost", 1, 0);
setcookie("TestCookie3", 'TestValue3', time()+3600, "/subdir/", ".localhost", 0, 0);

header('Bogus Header1: 23');
header('Bogus Header2: 24');

echo "Post Params : " . "<br>";
var_dump($_POST);
echo "<br><br>";

echo "Get Params : " . "<br>";
var_dump($_GET);
echo "<br><br>";

echo "Cookie Params : " . "<br>";
var_dump($_COOKIE);
echo "<br><br>";

echo "Request Params : " . "<br>";
var_dump($_REQUEST);
echo "<br><br>";

echo "Server Params : " . "<br>";
var_dump($_SERVER);
echo "<br><br>";

echo "File Params : " . "<br>";
var_dump($_FILES);
echo "<br><br>";

ob_flush();

?>