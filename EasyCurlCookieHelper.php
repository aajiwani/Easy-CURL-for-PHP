<?php

/**
 * @author Amir Ali Jiwani
 * @copyright 2011
 */
 
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
    public $Name;
    public $Value;
    
    public function __construct($name, $value)
    {
        $this->Name = $name;
        $this->Value = $value;
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

class EasyCurlCookieExtractor
{
    public static function ExtractCookies($headers)
    {
        if (!is_array($headers))
            throw new EasyCurlCookieExtractorException("Array expected.");
            
        $cookies = array();
        
        foreach ($headers as $header)
        {
            if (!($header instanceof EasyCurlHeader))
                throw new EasyCurlCookieExtractorException("Expecting type EasyCurlHeader.");
                
            $cookie = self::ExtractIndivisualCookie($header);
            
            if ($cookie != null)
                $cookies[] = $cookie;
        }
        
        return $cookies;
    }
    
    private static function ExtractIndivisualCookie($cookieHeader)
    {
        if (strcasecmp($cookieHeader->HeaderName, "set-cookie") == 0)
        {
            $cookiePairs = explode(";", $cookieHeader->HeaderValue);
            $cookieToReturn = null;
            
            foreach ($cookiePairs as $cookiePair)
            {
                $equalToIndex = strpos($cookiePair, '=');
				$keyValue = null;
                
                if ($equalToIndex === FALSE)
                {
                    $keyValue = array(
                        0 => trim($cookiePair)
                    );
                }
                else
                {
                    $keyValue = array(
                        0 => trim(substr($cookiePair, 0, $equalToIndex)),
                        1 => substr($cookiePair, ($equalToIndex + 1))
                    );
                }
                
                if (count($keyValue) == 2)
                {
                    if (strcasecmp($keyValue[0], EasyCurlCookie::DOMAIN_ATTR) == 0)
					{
						$cookieToReturn->Domain = $keyValue[1];
					}
					else if (strcasecmp($keyValue[0], EasyCurlCookie::EXPIRES_ATTR) == 0)
					{
                        $cookieToReturn->SetExpires($keyValue[1]);
					}
					else if (strcasecmp($keyValue[0], EasyCurlCookie::MAX_AGE_ATTR) == 0)
					{
                        $cookieToReturn->SetMaxAge($keyValue[1]);
					}
					else if (strcasecmp($keyValue[0], EasyCurlCookie::PATH_ATTR) == 0)
					{
                        $cookieToReturn->Path = $keyValue[1];
					}
					else if (strcasecmp($keyValue[0], EasyCurlCookie::VERSION_ATTR) == 0)
					{
                        $cookieToReturn->Version = $keyValue[1];
					}
					else
					{
                        $cookieToReturn = new EasyCurlCookie($keyValue[0], $keyValue[1]);
					}
                }
                else
                {
                    if (strcasecmp($keyValue[0], EasyCurlCookie::SECURE_ATTR) == 0)
					{
                        $cookieToReturn->SetSecure();
					}
					else if (strcasecmp($keyValue[0], EasyCurlCookie::HTTPONLY_ATTR) == 0)
					{
                        $cookieToReturn->SetHttpOnly();
					}
                }
            }
            
            return $cookieToReturn;
        }
        
        return null;
	}
 }

?>