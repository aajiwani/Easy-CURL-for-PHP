<?php

/**
 * @author Amir Ali Jiwani <amir.ali@pi-labs.net>
 * @copyright 2011
 * @link http://www.facebook.com/aajiwani
 * @version 1.0
 */

/**
 * Note for the Multi threaded EasyCurlExecuter
 *
 * Callback function : Please note that callback function should have atleast one parameter to obtain the response as
 *                      argument, else nothing can be caught in the callback.
 *
 *          Example of the callback function
 *              function callbackSample($response);             Here response would be sent from EasyCurlExecuter
 *
 *
 *
 * User state : User state can be anyobject, complex or array. Else it can be null also.
 *              Nothing would be sent to the callback in case of null userstate.
 *              Else whole object would be sent to callback.
 */

require_once('EasyCurlExecuter.php');
require_once('EasyCurlRequest.php');

function SampleCallbackFunction($response)                          // A simple call back without class
{
    echo $response->Url;            // Its the url with which the request was initiated or for which the request has been made
    echo "<br>";

    // $response->Response;
    // Respond to the response dealing in EasyCurl.php, its the same.
    // Either it would be an instance of EasyCurlResponse or EasyCurlError

    echo $response->UserState;      // Any additional information user have associated with the curl session would be returned here
    echo "<br>";
}

class SampleCallbackClass                                           // A simple class contaning several call backs
{
    public static function SampleCallbackFunction1($response)       // A simple but a static call back function
    {
        echo $response->Url;            // Its the url with which the request was initiated or for which the request has been made
        echo "<br>";

        // $response->Response;
        // Respond to the response dealing in EasyCurl.php, its the same.
        // Either it would be an instance of EasyCurlResponse or EasyCurlError

        echo $response->UserState;      // Any additional information user have associated with the curl session would be returned here
        echo "<br>";
    }

    public function SampleCallbackFunction2($response)              // A simple class method call back function
    {
        echo $response->Url;            // Its the url with which the request was initiated or for which the request has been made
        echo "<br>";

        // $response->Response;
        // Respond to the response dealing in EasyCurl.php, its the same.
        // Either it would be an instance of EasyCurlResponse or EasyCurlError

        echo $response->UserState;      // Any additional information user have associated with the curl session would be returned here
        echo "<br>";
    }
}

// Creating the simple EasyCurlRequest instances

$easyCurlRequest1 = new EasyCurlRequest('http://localhost/easycurl/SampleServer.php', EasyCurlRequestType::POST);

$easyCurlRequest1->AddCookie(new EasyCurlCookie('Cookie1', 'Value1'));
$easyCurlRequest1->AddCookie(new EasyCurlCookie('Cookie2', 'Value2'));
$easyCurlRequest1->AddCookie(new EasyCurlCookie('Cookie3', 'Value3'));

$easyCurlRequest1->AddHeader(new EasyCurlHeader('Header1', 'Value1'));
$easyCurlRequest1->AddHeader(new EasyCurlHeader('Header2', 'Value2'));
$easyCurlRequest1->AddHeader(new EasyCurlHeader('Header3', 'Value3'));

$easyCurlRequest1->AddPostParameter(new EasyCurlPostParameter('Param1', 'Value1'));
$easyCurlRequest1->AddPostParameter(new EasyCurlPostParameter('Param2', 'Value2'));
$easyCurlRequest1->AddPostParameter(new EasyCurlPostParameter('Param3', 'Value3'));
$easyCurlRequest1->AddPostParameter(new EasyCurlPostParameter('File1', 'C:\wamp\www\curl\testCookie - Copy.txt', EasyCurlPostParameter::FILE_PARAMETER));
$easyCurlRequest1->AddPostParameter(new EasyCurlPostParameter('File2', 'C:\wamp\www\curl\testCookie.txt', EasyCurlPostParameter::FILE_PARAMETER));

$easyCurlRequest1->SetAutoReferer();

$easyCurlRequest2 = new EasyCurlRequest('http://www.google.com.pk');
$easyCurlRequest3 = new EasyCurlRequest('http://www.yahoo.com');
$easyCurlRequest4 = new EasyCurlRequest('http://www.msn.com');
$easyCurlRequest5 = new EasyCurlRequest('http://www.hotmail.com');

$callbackClassInstance = new SampleCallbackClass();                     // Creating a callback class's instance

$curlExec = new EasyCurlExecuter();                                     // Creating a multi threaded curl executer instance

// Adding EasyCurlRequest object in the queue with a non class, non static call back function and a user state as string
$curlExec->AddSession($easyCurlRequest1, new EasyCurlExecuterCallback('SampleCallbackFunction'), "LocalRequest");

// Adding EasyCurlRequest object in the queue with a static class call back function and a user state as string
$curlExec->AddSession($easyCurlRequest2, new EasyCurlExecuterCallback('SampleCallbackClass::SampleCallbackFunction1'), "Google Request");

// Adding EasyCurlRequest object in the queue with a class method call back function and a user state as string
$curlExec->AddSession($easyCurlRequest3, new EasyCurlExecuterCallback('SampleCallbackFunction2', $callbackClassInstance), "Yahoo Request");

// Adding EasyCurlRequest object in the queue without any callback and a user state as string
$curlExec->AddSession($easyCurlRequest4, null, "MSN Request");

// Adding EasyCurlRequest object in the queue without any callback and without any user state
$curlExec->AddSession($easyCurlRequest5);

// Executing the requests in queue
$curlExec->ExecuteRequests();

var_dump($curlExec->Results);
// Results, if recorded is the array of objects with type EasyCurlExecuterResult
// Each EasyCurlExecuterResult is the same which is passed to any call back function
?>