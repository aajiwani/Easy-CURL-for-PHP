<?php

/**
 * @author Amir Ali Jiwani
 * @copyright 2011
 */
 
require_once('EasyCurlCookieHelper.php');

class EasyCurlResponse
{
    public $CurrentUrl;
    public $HttpResponseCode;
    public $HttpResponseCodeDescription;
    public $LastModifiedTime;
    public $ContentType;
    public $Cookies;
    public $Headers;
    public $ResposeBody;
    
    public function __construct($easyCurlRequest, $response)
    {
        $curlInfo = curl_getinfo($easyCurlRequest->GetBaseCurlObject());
        
        $this->CurrentUrl = $curlInfo['url'];
        $this->HttpResponseCode = $curlInfo['http_code'];
        $this->HttpResponseCodeDescription = EasyCurlHTTPCodeInfo::$RESPONSE_MESSAGES[$this->HttpResponseCode];
        $this->LastModifiedTime = $curlInfo['filetime'];
        $this->ContentType = $curlInfo['content_type'];
        $this->Headers = array();
        $this->Cookies = array();
        
        $respDoc = preg_split("/(\r\n){2,2}/", $response, 3);
        
        if (count($respDoc) == 1)
        {
            $this->ParseHeaders($respDoc[0]);
        }
        else
        {
            for ($i = 1; $i < count($respDoc) - 1; $i++)
            {
                $this->ParseHeaders($respDoc[$i]);
            }
            
            $this->ResposeBody = $respDoc[count($respDoc) - 1];
        }
    }
    
    private function ParseHeaders($headers)
    {
        $headersArray = preg_split("/(\r\n)+/", $headers);
        
        foreach ($headersArray as $header)
        {
            $headerParts = preg_split("/\s*:\s*/", $header, 2);
            if (count($headerParts) == 2)
            {
                $ech = new EasyCurlHeader($headerParts[0], $headerParts[1]);
                $this->Headers[] = $ech;
            }
        }
        
        $this->Cookies = array_merge($this->Cookies, EasyCurlCookieExtractor::ExtractCookies($this->Headers));
    }
}

?>