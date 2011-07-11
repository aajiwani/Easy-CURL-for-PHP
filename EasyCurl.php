<?php

/**
 * @author Amir Ali Jiwani
 * @copyright 2011
 */

require_once('EasyCurlExceptions.php');
require_once('EasyCurlHelperClasses.php');
require_once('EasyCurlExceptions.php');
require_once('EasyCurlRequest.php');
require_once('EasyCurlResponse.php');

$easyCurlRequest = new EasyCurlRequest('http://www.google.com.pk');
$easyCurlRequest->SetAutoReferer();
$executionResult = $easyCurlRequest->Execute();

var_dump($executionResult);

if ($executionResult instanceof EasyCurlResponse)
{
    echo "In Response" . "<br />";
    
    echo "Response Text : " . $executionResult->ResponseText . "<br />";
}
else if ($executionResult instanceof EasyCurlError) 
{
    echo "In Error" . "<br />";
    
    echo "Error Number : " . $executionResult->ErrorNumber . "<br />";
    echo "Error Message : " . $executionResult->ErrorMessage . "<br />";
    echo "Error Short Description : " . $executionResult->ErrorShortDescription . "<br />";
}

?>