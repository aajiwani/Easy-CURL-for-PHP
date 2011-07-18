<?php

/**
 * @author Amir Ali Jiwani <amir.ali@pi-labs.net>
 * @copyright 2011
 * @link http://www.facebook.com/aajiwani
 * @version 1.0
 */

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