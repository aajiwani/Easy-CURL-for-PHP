<?php

/**
 * @author Amir Ali Jiwani
 * @copyright 2011
 */

require_once('EasyCurlRequest.php');

//$easyCurlRequest = new EasyCurlRequest('http://www.google.com.pk');
$easyCurlRequest = new EasyCurlRequest('http://localhost:81/EasyCurl/Server.php', EasyCurlRequestType::POST);
//$easyCurlRequest = new EasyCurlRequest('http://kjgha7oi234.com', EasyCurlRequestType::POST);
$easyCurlRequest->AddCookie(new EasyCurlCookie('Cookie1', 'Value1'));
$easyCurlRequest->AddCookie(new EasyCurlCookie('Cookie2', 'Value2'));
$easyCurlRequest->AddCookie(new EasyCurlCookie('Cookie3', 'Value3'));

$easyCurlRequest->AddHeader(new EasyCurlHeader('Header1', 'Value1'));
$easyCurlRequest->AddHeader(new EasyCurlHeader('Header2', 'Value2'));
$easyCurlRequest->AddHeader(new EasyCurlHeader('Header3', 'Value3'));

$easyCurlRequest->AddPostParameter(new EasyCurlPostParameter('Param1', 'Value1'));
$easyCurlRequest->AddPostParameter(new EasyCurlPostParameter('Param2', 'Value2'));
$easyCurlRequest->AddPostParameter(new EasyCurlPostParameter('Param3', 'Value3'));
$easyCurlRequest->AddPostParameter(new EasyCurlPostParameter('File1', 'C:\wamp\www\EasyCurl\testCookie - Copy.txt', EasyCurlPostParameter::FILE_PARAMETER));
$easyCurlRequest->AddPostParameter(new EasyCurlPostParameter('File2', 'C:\wamp\www\EasyCurl\testCookie.txt', EasyCurlPostParameter::FILE_PARAMETER));

$easyCurlRequest->SetAutoReferer();
$executionResult = $easyCurlRequest->Execute();

var_dump($executionResult);

if ($executionResult instanceof EasyCurlResponse)
{
    echo "In Response" . "<br />";
    
    echo "Response Text : " . $executionResult->ResposeBody . "<br />";
}
else if ($executionResult instanceof EasyCurlError) 
{
    echo "In Error" . "<br />";
    
    echo "Error Number : " . $executionResult->ErrorNumber . "<br />";
    echo "Error Message : " . $executionResult->ErrorMessage . "<br />";
    echo "Error Short Description : " . $executionResult->ErrorShortDescription . "<br />";
}

?>