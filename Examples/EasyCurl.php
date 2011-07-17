<?php

/**
 * @author Amir Ali Jiwani <amir.ali@pi-labs.net>
 * @copyright 2011
 * @link http://www.facebook.com/aajiwani
 * @version 1.0
 */

require_once('EasyCurlRequest.php');

$easyCurlRequest = new EasyCurlRequest('http://localhost/easycurl/SampleServer.php', EasyCurlRequestType::POST);

// Adding cookies to the request, please use EasyCurlCookie object to insert cookies
$easyCurlRequest->AddCookie(new EasyCurlCookie('Cookie1', 'Value1'));
$easyCurlRequest->AddCookie(new EasyCurlCookie('Cookie2', 'Value2'));
$easyCurlRequest->AddCookie(new EasyCurlCookie('Cookie3', 'Value3'));

// Adding headers to the request, please use EasyCurlHeader object to insert headers
$easyCurlRequest->AddHeader(new EasyCurlHeader('Header1', 'Value1'));
$easyCurlRequest->AddHeader(new EasyCurlHeader('Header2', 'Value2'));
$easyCurlRequest->AddHeader(new EasyCurlHeader('Header3', 'Value3'));

// Adding post parameters to the request, please use EasyCurlPostParameter object to insert post parameters
$easyCurlRequest->AddPostParameter(new EasyCurlPostParameter('Param1', 'Value1'));
$easyCurlRequest->AddPostParameter(new EasyCurlPostParameter('Param2', 'Value2'));
$easyCurlRequest->AddPostParameter(new EasyCurlPostParameter('Param3', 'Value3'));

// To add file as the post parameter an extra parameter is passed in the constructor of EasyCurlPostParameter
// that specifies that its a file not a value parameter. EasyCurlPostParameter::FILE_PARAMETER is the constant
$easyCurlRequest->AddPostParameter(new EasyCurlPostParameter('File1', 'C:\wamp\www\curl\testCookie - Copy.txt', EasyCurlPostParameter::FILE_PARAMETER));
$easyCurlRequest->AddPostParameter(new EasyCurlPostParameter('File2', 'C:\wamp\www\curl\testCookie.txt', EasyCurlPostParameter::FILE_PARAMETER));

// Setting auto referer property to true
$easyCurlRequest->SetAutoReferer();

// Executing the curl request
$executionResult = $easyCurlRequest->Execute();

if ($executionResult instanceof EasyCurlResponse)
{
    echo $executionResult->CurrentUrl;                      // Last visited url
    echo "<br>";

    echo $executionResult->HttpResponseCode;                // HTTP response code
    echo "<br>";

    echo $executionResult->HttpResponseCodeDescription;     // HTTP response codes textual representation
    echo "<br>";

    echo $executionResult->LastModifiedTime;                // Last modification date of the response document
    echo "<br>";

    echo $executionResult->ContentType;                     // Content type header of the document
    echo "<br>";

    $allCookies = $executionResult->Cookies;                // Cookies returned in the document (array of EasyCurlCookie)

    foreach ($allCookies as $cookie)
    {
        echo "Cookie Name : ";
        echo $cookie->CookieName;
        echo "<br>";
        echo "Cookie Value : ";
        echo $cookie->CookieValue;
        echo "<br>";
        echo "Cookie Domain : ";
        echo $cookie->Domain;
        echo "<br>";
        echo "Cookie Expires : ";
        echo $cookie->Expires;
        echo "<br>";
        echo "Cookie Path : ";
        echo $cookie->Path;
        echo "<br>";
        echo "Cookie Version : ";
        echo $cookie->Version;
        echo "<br>";
        echo "Cookie IsSecure : ";
        echo $cookie->IsSecure;
        echo "<br>";
        echo "Cookie HttpOnly : ";
        echo $cookie->HttpOnly;
        echo "<br><br>";
    }

    $allHeaders = $executionResult->Headers;                // Headers returned in the document (array of EasyCurlHeader)

    foreach ($allHeaders as $header)
    {
        echo "Header Name : ";
        echo $header->HeaderName;
        echo "<br>";
        echo "Header Value : ";
        echo $header->HeaderValue;
        echo "<br><br>";
    }

    echo $executionResult->ResposeBody;                     // Body of the document
    echo "<br>";
}
else if ($executionResult instanceof EasyCurlError)
{
    echo $executionResult->ErrorNumber;                     // Error number of the curl resource
    echo "<br>";

    echo $executionResult->ErrorMessage;                    // Error message corresponding to the error number
    echo "<br>";

    echo $executionResult->ErrorShortDescription;           // Short curl description of the error
    echo "<br>";
}

?>