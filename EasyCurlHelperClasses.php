<?php

/**
 * @author Amir Ali Jiwani
 * @copyright 2011
 */
 
class EasyCurlHTTPVersion
{
    const VERSION_NONE = CURL_HTTP_VERSION_NONE;
    const VERSION_1_0 = CURL_HTTP_VERSION_1_0;
    const VERSION_1_1 = CURL_HTTP_VERSION_1_1;
}
 
class EasyCurlRequestType
{
    const GET = 1;          // CURLOPT_HTTPGET -> true
    const POST = 2;         // CURLOPT_POST -> true
}

class EasyCurlCookie
{
    public $CookieName;
    public $CookieValue;
    
    public function __construct($name, $value)
    {
        if (is_string($name) && is_string($value))
        {
            $this->CookieName = $name;
            $this->CookieValue = $value;
        }
        else
        {
            throw new EasyCurlCookieException("Name and value provided for cookie must be of type string.");
        }
    }
}

class EasyCurlPostParameter
{
    public $ParamName;
    public $ParamValue;
    private $ParamType;
    
    const VALUE_PARAMETER = 1;
    const FILE_PARAMETER = 2;
    
    public function __construct($name, $value, $type = EasyCurlPostParameter::VALUE_PARAMETER)
    {
        $this->ParamType = $type;
        
        if ($type == self::FILE_PARAMETER)
        {
            $this->ParamName = $name;
            $this->ParamValue = '@' . $value;
        }
        else if ($type == self::VALUE_PARAMETER)
        {
            $this->ParamName = $name;
            $this->ParamValue = $value;
        }
        else
        {
            throw new EasyCurlPostParameterException("Unknown type of parameter specified.");
        }
    }
}

class EasyCurlHeader
{
    public $HeaderName;
    public $HeaderValue;
    
    public function __construct($name, $value)
    {
        if (is_string($name) && is_string($value))
        {
            $this->HeaderName = $name;
            $this->HeaderValue = $value;
        }
        else
        {
            throw new EasyCurlHeaderException("Name and value provided for header must be of type string.");
        }
    }
}



?>