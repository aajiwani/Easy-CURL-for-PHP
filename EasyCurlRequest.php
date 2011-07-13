<?php

/**
 * @author Amir Ali Jiwani
 * @copyright 2011
 */

require_once('EasyCurlExceptions.php');
require_once('EasyCurlHelperClasses.php');
require_once('EasyCurlExceptions.php');
require_once('EasyCurlError.php');
require_once('EasyCurlResponse.php');

class EasyCurlRequest
{
    private $curlInstance = null;
    private $headers = array();
    private $cookies = array();
    private $requestType = '';
    private $postParams = array();
    
    private function GetCookieString()
    {
        $finalString = '';
        
        foreach ($this->cookies as $cookie)
        {
            $finalString .= $cookie->CookieName . '=' . $cookie->CookieValue . '; ';
        }
        
        $finalString = rtrim($finalString, '; ');
        
        array_splice($this->cookies, 0);
        
        return $finalString;
    }
    
    private function GetPostParams()
    {
        $finalArray = array();
        
        foreach ($this->postParams as $postParam)
        {
            $finalArray[$postParam->ParamName] = $postParam->ParamValue;
        }
        
        array_splice($this->postParams, 0);
        
        return $finalArray;
    }
    
    private function GetHeaders()
    {
        $finalArray = array();
        
        foreach ($this->headers as $header)
        {
            $finalArray[] = $header->HeaderName . ': ' . $header->HeaderValue;
        }
        
        array_splice($this->headers, 0);
        
        return $finalArray;
    }
    
    private function AttachCookies()
    {
        if (count($this->cookies) > 0)
        {
            curl_setopt($this->curlInstance, CURLOPT_COOKIE, $this->GetCookieString());
        }
    }
    
    private function AttachPostParameters()
    {
        if ($this->requestType == EasyCurlRequestType::POST)
        {
            if (count($this->postParams) > 0)
            {
                curl_setopt($this->curlInstance, CURLOPT_POSTFIELDS, $this->GetPostParams());
            }
        }
    }
    
    private function AttachHeaders()
    {
        if (count($this->headers) > 0)
            curl_setopt($this->curlInstance, CURLOPT_HTTPHEADER, $this->GetHeaders());
    }
    
    public function __construct($url, $requestType = EasyCurlRequestType::GET, $cookieFile = null)
    {
        $this->curlInstance = curl_init($url);
        curl_setopt($this->curlInstance, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->curlInstance, CURLOPT_HEADER, TRUE);
        
        if( substr_count($url , "https://") > 0)
        {
            $this->NoVerifySSLCertificate();
        }
        
        if ($cookieFile != null)
        {
            curl_setopt($this->curlInstance, CURLOPT_COOKIEJAR, $cookieFile);
        }
        
        $this->requestType = $requestType;
        
        if ($requestType == EasyCurlRequestType::GET)
        {
            curl_setopt($this->curlInstance, CURLOPT_HTTPGET, TRUE);
        }
        else if ($requestType == EasyCurlRequestType::POST)
        {
            curl_setopt($this->curlInstance, CURLOPT_POST, TRUE);
        }
        else
        {
            throw new EasyCurlRequestTypeException("Type not specified in EasyCurlRequestType.");
        }
    }
    
    public function SetUrl($url)
    {
        curl_setopt($this->curlInstance, CURLOPT_URL, $url);
        
        if( substr_count($url , "https://") > 0)
        {
            $this->NoVerifySSLCertificate();
        }
    }
    
    public function SetAutoReferer($value = true)
    {
        curl_setopt($this->curlInstance, CURLOPT_AUTOREFERER, $value);
    }
    
    public function ReturnRawOutput($value = true)
    {
        curl_setopt($this->curlInstance, CURLOPT_BINARYTRANSFER, $value);
    }
    
    public function ForceGetFileModification($value = true)
    {
        curl_setopt($this->curlInstance, CURLOPT_FILETIME, $value);
    }
    
    public function SetCurlOption($option, $value)
    {
        curl_setopt($this->curlInstance, $option, $value);
    }
    
    public function SetCurlOptions($options)
    {
        curl_setopt_array($this->curlInstance, $options);
    }
    
    public function AllowAutoRedirect($value = true, $allowResendingAuthInfo = false, $maxRedirects = 0)
    {
        curl_setopt($this->curlInstance, CURLOPT_FOLLOWLOCATION, $value);
        curl_setopt($this->curlInstance, CURLOPT_UNRESTRICTED_AUTH, $allowResendingAuthInfo);
        
        if ($maxRedirects != 0)
        {
            curl_setopt($this->curlInstance, CURLOPT_MAXREDIRS, $maxRedirects);
        }
    }
    
    public function NoVerifySSLCertificate($value = false)
    {
        curl_setopt($this->curlInstance, CURLOPT_SSL_VERIFYPEER, $value);
    }
    
    public function SetConnectionTimeOut($value = 0)            // Seconds
    {
        curl_setopt($this->curlInstance, CURLOPT_CONNECTTIMEOUT, $value);
    }
    
    public function SetExecutionTimeOut($value = 0)            // MiliSeconds
    {
        curl_setopt($this->curlInstance, CURLOPT_TIMEOUT_MS, $value);
    }
    
    public function SetHttpVersion($version = EasyCurlHTTPVersion::VERSION_NONE)
    {
        curl_setopt($this->curlInstance, CURLOPT_HTTP_VERSION, $version);
    }
    
    public function SetReferer($referer)
    {
        curl_setopt($this->curlInstance, CURLOPT_REFERER, $referer);
    }
    
    public function SetUserAgent($userAgent)
    {
        curl_setopt($this->curlInstance, CURLOPT_USERAGENT, $userAgent);
    }
    
    public function SetProxy($proxyAddress, $proxyPort, $proxyUser, $proxyPass)
    {
        if ($proxyAddress && $proxyPort)
            curl_setopt($this->curlInstance, CURLOPT_PROXY, trim($proxyAddress) . ":" . trim($proxyPort));
            
        if ($proxyUser && $proxyPass)
            curl_setopt($this->curlInstance, CURLOPT_PROXYUSERPWD, trim($proxyUser) . ":" . trim($proxyPass));
    }
    
    public function AddCookie($cookie)
    {
        if ($cookie instanceof EasyCurlCookie)
            $this->cookies[] = $cookie;
        else
            throw new EasyCurlCookieException("Expecting type EasyCurlCookie.");
    }
    
    public function AddPostParameter($parameter)
    {
        if ($this->requestType == EasyCurlRequestType::POST)
        {
            if ($parameter instanceof EasyCurlPostParameter)
                $this->postParams[] = $parameter;
            else
                throw new EasyCurlPostParameterException("Expecting type EasyCurlPostParameter.");
        }
        else
        {
            throw new EasyCurlRequestTypeException("AddPostParameter works with type EasyCurlRequestType::POST, please verify the type.");
        }
    }
    
    public function AddHeader($header)
    {
        if ($header instanceof EasyCurlHeader)
            $this->headers[] = $header;
        else
            throw new EasyCurlHeaderException("Expecting type EasyCurlHeader.");
    }
    
    public function GetBaseCurlObject()
    {
        return $this->curlInstance;
    }
    
    public function FinalizeRequest()
    {
        $this->AttachHeaders();
        $this->AttachCookies();
        $this->AttachPostParameters();
    }
    
    public function Execute()
    {
        $this->FinalizeRequest();
        $result = curl_exec($this->curlInstance);
        
        if ($result === false)
        {
            return new EasyCurlError($this);
        }
        else
        {
            return new EasyCurlResponse($this, $result);
        }
    }
    
    public function EndRequest()
    {
        curl_close($this->curlInstance);
    }
}

?>