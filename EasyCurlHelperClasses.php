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
    const DOMAIN_ATTR = 'domain';
    const PATH_ATTR = 'path';
    const EXPIRES_ATTR = 'expires';
    const MAX_AGE_ATTR = 'max-age';
    const VERSION_ATTR = 'version';
    const SECURE_ATTR = 'secure';
    const HTTPONLY_ATTR = 'httponly';
    
    public $Domain;
    public $Expires = null;
    public $Path;
    public $Version;
    public $IsSecure;
    public $HttpOnly;
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
    
    public function SetMaxAge($seconds)
    {
        $this->Expires = time() + $seconds;
    }
    
    public function SetExpires($expiresValue)
    {
        $this->Expires = strtotime($expiresValue);
    }
    
    public function SetSecure($value = true)
    {
        $this->IsSecure = $value;
    }
    
    public function SetHttpOnly($value = true)
    {
        $this->HttpOnly = $value;
    }
    
    public function GetDateWithFormat($format)
    {
        if ($this->Expires)
        {
            return date($format, $this->Expires);
        }
        
        return null;
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
        $this->HeaderName = $name;
        $this->HeaderValue = $value;
    }
}

class EasyCurlHTTPCodeInfo
{
    public static $RESPONSE_MESSAGES = array(
                                        //[Informational 1xx]
                                        100 => "Continue",
                                        101 => "Switching Protocols",
                                        
                                        //[Successful 2xx]
                                        200 => "OK",
                                        201 => "Created",
                                        202 => "Accepted",
                                        203 => "Non-Authoritative Information",
                                        204 => "No Content",
                                        205 => "Reset Content",
                                        206 => "Partial Content",
                                        
                                        //[Redirection 3xx]
                                        300 => "Multiple Choices",
                                        301 => "Moved Permanently",
                                        302 => "Found",
                                        303 => "See Other",
                                        304 => "Not Modified",
                                        305 => "Use Proxy",
                                        306 => "(Unused)",
                                        307 => "Temporary Redirect",
                                        
                                        //[Client Error 4xx]
                                        400 => "Bad Request",
                                        401 => "Unauthorized",
                                        402 => "Payment Required",
                                        403 => "Forbidden",
                                        404 => "Not Found",
                                        405 => "Method Not Allowed",
                                        406 => "Not Acceptable",
                                        407 => "Proxy Authentication Required",
                                        408 => "Request Timeout",
                                        409 => "Conflict",
                                        410 => "Gone",
                                        411 => "Length Required",
                                        412 => "Precondition Failed",
                                        413 => "Request Entity Too Large",
                                        414 => "Request-URI Too Long",
                                        415 => "Unsupported Media Type",
                                        416 => "Requested Range Not Satisfiable",
                                        417 => "Expectation Failed",
                                        
                                        //[Server Error 5xx]
                                        500 => "Internal Server Error",
                                        501 => "Not Implemented",
                                        502 => "Bad Gateway",
                                        503 => "Service Unavailable",
                                        504 => "Gateway Timeout",
                                        505 => "HTTP Version Not Supported"
                                        );
}

?>