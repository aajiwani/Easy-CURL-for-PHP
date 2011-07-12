<?php

/**
 * @author Amir Ali Jiwani
 * @copyright 2011
 */

class EasyCurlResponse
{
    private $header;
    private $body;

    public $CurrentUrl;
    public $HttpResponseCode;
    public $HttpResponseCodeDescription;
    public $LastModifiedTime;
    public $ContentType;
    
    public function __construct($easyCurlRequest, $response)
    {
        $this->ResponseText = $response;
        $curlInfo = curl_getinfo($easyCurlRequest->GetBaseCurlObject());
        
        $this->CurrentUrl = $curlInfo['url'];
        $this->HttpResponseCode = $curlInfo['http_code'];
        $this->HttpResponseCodeDescription = EasyCurlHTTPCodeInfo::$RESPONSE_MESSAGES[$this->HttpResponseCode];
        $this->LastModifiedTime = $curlInfo['filetime'];
        $this->ContentType = $curlInfo['content_type'];
        
        $respDoc = preg_split("/(\r\n){2,2}/", $response, 2);
        
        if (count($respDoc) == 1)
        {
            $this->header = $respDoc[0];
        }
        else if (count($respDoc) == 2)
        {
            $this->header = $respDoc[0];
            $this->body = $respDoc[1];
        }
    }
    
    private function ParseHeaders($headers)
    {
        
    }
}

?>