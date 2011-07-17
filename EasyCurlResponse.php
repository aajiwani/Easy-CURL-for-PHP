<?php

/**
 * @author Amir Ali Jiwani <amir.ali@pi-labs.net>
 * @copyright 2011
 * @link http://www.facebook.com/aajiwani
 * @version 1.0
 */
 
require_once('EasyCurlCookieHelper.php');

/**
 * EasyCurlResponse
 * 
 * EasyCurlResponse is the class which maintains the state of a HTTP Response.
 */
class EasyCurlResponse
{
    /**
     * Last visited url.
     * 
     * @var string
     */
    public $CurrentUrl;
    
    /**
     * Http response code as returned from the response.
     * 
     * @var int
     */
    public $HttpResponseCode;
    
    /**
     * Textual description of response code.
     * 
     * @var string
     */
    public $HttpResponseCodeDescription;
    
    /**
     * Last modification time of the returned web document.
     * 
     * @var string
     */
    public $LastModifiedTime;
    
    /**
     * Content type header of the web request.
     * 
     * @var string
     */
    public $ContentType;
    
    /**
     * Cookies returned in the document.
     * Array of EasyCurlCookie
     * 
     * @var mixed
     */
    public $Cookies;
    
    /**
     * Headers returned in the document, including the Set-Cookie headers.
     * Array of EasyCurlHeader
     * 
     * @var mixed
     */
    public $Headers;
    
    /**
     * Response content of the document returned by web request.
     * 
     * @var string
     */
    public $ResposeBody;
    
    /**
     * EasyCurlResponse::__construct()
     * 
     * @param mixed $curlObject Must be a curl resource.
     * @param string $response Response returned from the curl resource.
     * @return EasyCurlResponse EasyCurlResponse instance.
     */
    public function __construct($curlObject, $response)
    {
        $curlInfo = curl_getinfo($curlObject);
        
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
            for ($i = 0; $i < count($respDoc) - 1; $i++)
            {
                $this->ParseHeaders($respDoc[$i]);
            }
            
            $this->ResposeBody = $respDoc[count($respDoc) - 1];
        }
    }
    
    /**
     * EasyCurlResponse::ParseHeaders()
     * 
     * Parses the simple textual header to a structure of Array of EasyCurlHeader and parses Cookies too from those headers.
     * 
     * @param mixed $headers Must be an array of string in the form of Name: Value
     */
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