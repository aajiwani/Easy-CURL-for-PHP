<?php

/**
 * @author Amir Ali Jiwani <amir.ali@pi-labs.net>
 * @copyright 2011
 * @link http://www.facebook.com/aajiwani
 * @version 1.0
 */

require_once('EasyCurlExceptions.php');
require_once('EasyCurlHelperClasses.php');
require_once('EasyCurlExceptions.php');
require_once('EasyCurlError.php');
require_once('EasyCurlResponse.php');

/**
 * EasyCurlRequest
 * 
 * EasyCurlRequest is the class which maintains the state of a HTTP Request.
 * This class has many methods that helps in a web request, including post parameters, files etc.
 */
class EasyCurlRequest
{
    private $curlInstance = null;
    private $headers = array();
    private $cookies = array();
    private $requestType = '';
    private $postParams = array();
    private $url;
    
    /**
     * EasyCurlRequest::GetCookieString()
     * 
     * Provides the php curl's type value for all the cookies inserted.
     * 
     * @return string key=value; pairs, if more than one than key1=value1; key2=value2
     */
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
    
    /**
     * EasyCurlRequest::GetPostParams()
     * 
     * Provides the php curl's type value for all the post params.
     * 
     * @return mixed Array of key value pairs, keys as the param name and value as their values
     */
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
    
    /**
     * EasyCurlRequest::GetHeaders()
     * 
     * Provides the php curl's type value for all the headers.
     * 
     * @return mixed Array of string, in a format of name: value
     */
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
    
    /**
     * EasyCurlRequest::AttachCookies()
     * 
     * Adds the local available cookies to the base curl instance.
     */
    private function AttachCookies()
    {
        if (count($this->cookies) > 0)
        {
            curl_setopt($this->curlInstance, CURLOPT_COOKIE, $this->GetCookieString());
        }
    }
    
    /**
     * EasyCurlRequest::AttachPostParameters()
     * 
     * Adds the local available post params to the base curl instance.
     */
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
    
    /**
     * EasyCurlRequest::AttachHeaders()
     * 
     * Adds the local available headers to the base curl instance.
     */
    private function AttachHeaders()
    {
        if (count($this->headers) > 0)
            curl_setopt($this->curlInstance, CURLOPT_HTTPHEADER, $this->GetHeaders());
    }
    
    /**
     * EasyCurlRequest::__construct()
     * 
     * @param string $url The url to make the web request.
     * @param EasyCurlRequestType $requestType Constant to indicate which type of web request is being made, GET or POST.
     * @param bool $allowRedirect Indicate whether the request should allow auto redirects.
     * @param string $cookieFile The complete path to cookie file, if wanted to store in a file.
     * 
     * @return EasyCurlRequest EasyCurlRequest instance.
     */
    public function __construct($url, $requestType = EasyCurlRequestType::GET, $allowRedirect = true, $cookieFile = null)
    {
        $this->url = $url;
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
        
        if ($allowRedirect)
        {
            $this->AllowAutoRedirect();
        }
    }
    
    /**
     * EasyCurlRequest::SetUrl()
     * 
     * Sets the url for which to make the web request.
     * 
     * @param string $url The url to make the web request.
     */
    public function SetUrl($url)
    {
        curl_setopt($this->curlInstance, CURLOPT_URL, $url);
        $this->url = $url;
        
        if( substr_count($url , "https://") > 0)
        {
            $this->NoVerifySSLCertificate();
        }
    }
    
    /**
     * EasyCurlRequest::SetAutoReferer()
     * 
     * Set the auto refer option of the php curl's module. It will pass the referer automatically.
     * 
     * @param bool $value
     */
    public function SetAutoReferer($value = true)
    {
        curl_setopt($this->curlInstance, CURLOPT_AUTOREFERER, $value);
    }
    
    /**
     * EasyCurlRequest::ReturnRawOutput()
     * 
     * Enables binary transfer option of the php curl's module.
     * 
     * @param bool $value
     */
    public function ReturnRawOutput($value = true)
    {
        curl_setopt($this->curlInstance, CURLOPT_BINARYTRANSFER, $value);
    }
    
    /**
     * EasyCurlRequest::ForceGetFileModification()
     * 
     * Forces the page to get the last file modification time.
     * 
     * @param bool $value
     */
    public function ForceGetFileModification($value = true)
    {
        curl_setopt($this->curlInstance, CURLOPT_FILETIME, $value);
    }
    
    /**
     * EasyCurlRequest::SetCurlOption()
     * 
     * Set any other php curl's modules option available. The instance would be updated with the option.
     * 
     * @param $option mixed
     * @param $value mixed
     */
    public function SetCurlOption($option, $value)
    {
        curl_setopt($this->curlInstance, $option, $value);
    }
    
    /**
     * EasyCurlRequest::SetCurlOptions()
     * 
     * @param $options mixed array of options.
     */
    public function SetCurlOptions($options)
    {
        curl_setopt_array($this->curlInstance, $options);
    }
    
    /**
     * EasyCurlRequest::AllowAutoRedirect()
     * 
     * Allows the curl's instance to autoredirect if recieves any redirect from server.
     *  
     * @param $value bool
     * @param $allowResendingAuthInfo bool
     * @param $maxRedirects int (0 for infinite)
     */
    public function AllowAutoRedirect($value = true, $allowResendingAuthInfo = false, $maxRedirects = 0)
    {
        curl_setopt($this->curlInstance, CURLOPT_FOLLOWLOCATION, $value);
        curl_setopt($this->curlInstance, CURLOPT_UNRESTRICTED_AUTH, $allowResendingAuthInfo);
        
        if ($maxRedirects != 0)
        {
            curl_setopt($this->curlInstance, CURLOPT_MAXREDIRS, $maxRedirects);
        }
    }
    
    /**
     * EasyCurlRequest::NoVerifySSLCertificate()
     * 
     * Forces the curl instance to bypass the SSL verification without any certificate.
     * 
     * @param $value bool
     */
    public function NoVerifySSLCertificate($value = false)
    {
        curl_setopt($this->curlInstance, CURLOPT_SSL_VERIFYPEER, $value);
    }
    
    /**
     * EasyCurlRequest::SetConnectionTimeOut()
     * 
     * Set the connection time out.
     * 
     * @param $value int must be in seconds.
     */
    public function SetConnectionTimeOut($value = 0)
    {
        curl_setopt($this->curlInstance, CURLOPT_CONNECTTIMEOUT, $value);
    }
    
    /**
     * EasyCurlRequest::SetExecutionTimeOut()
     * 
     * Set the execution time out for the request.
     * 
     * @param $value int must be in miliseconds.
     */
    public function SetExecutionTimeOut($value = 0)
    {
        curl_setopt($this->curlInstance, CURLOPT_TIMEOUT_MS, $value);
    }
    
    /**
     * EasyCurlRequest::SetHttpVersion()
     * 
     * Set the http version of the web request.
     * 
     * @param $version EasyCurlHTTPVersion
     */
    public function SetHttpVersion($version = EasyCurlHTTPVersion::VERSION_NONE)
    {
        curl_setopt($this->curlInstance, CURLOPT_HTTP_VERSION, $version);
    }
    
    /**
     * EasyCurlRequest::SetReferer()
     * 
     * Set the referer header in the web request.
     * 
     * @param $referer string
     */
    public function SetReferer($referer)
    {
        curl_setopt($this->curlInstance, CURLOPT_REFERER, $referer);
    }
    
    /**
     * EasyCurlRequest::SetUserAgent()
     * 
     * Set the user agent header in the web request.
     * 
     * @param $userAgent string
     */
    public function SetUserAgent($userAgent)
    {
        curl_setopt($this->curlInstance, CURLOPT_USERAGENT, $userAgent);
    }
    
    /**
     * EasyCurlRequest::SetProxy()
     * 
     * Sets the proxy info for the web request.
     * 
     * @param $proxyAddress string
     * @param $proxyPort int
     * @param $proxyUser string
     * @param $proxyPass string
     */
    public function SetProxy($proxyAddress, $proxyPort, $proxyUser, $proxyPass)
    {
        if ($proxyAddress && $proxyPort)
            curl_setopt($this->curlInstance, CURLOPT_PROXY, trim($proxyAddress) . ":" . trim($proxyPort));
            
        if ($proxyUser && $proxyPass)
            curl_setopt($this->curlInstance, CURLOPT_PROXYUSERPWD, trim($proxyUser) . ":" . trim($proxyPass));
    }
    
    /**
     * EasyCurlRequest::AddCookie()
     * 
     * Adds a cookie to the web request.
     * 
     * @param $cookie EasyCurlCookie
     * 
     * @throws EasyCurlCookieException If entered cookie is not a type of EasyCurlCookie.
     */
    public function AddCookie($cookie)
    {
        if ($cookie instanceof EasyCurlCookie)
            $this->cookies[] = $cookie;
        else
            throw new EasyCurlCookieException("Expecting type EasyCurlCookie.");
    }
    
    /**
     * EasyCurlRequest::AddPostParameter()
     * 
     * Adds a post parameter to the web request.
     * 
     * @param $parameter EasyCurlPostParameter
     * 
     * @throws EasyCurlPostParameterException If entered parameter is not a type of EasyCurlPostParameter.
     * @throws EasyCurlRequestTypeException If the web request is not of type EasyCurlRequestType::POST.
     */
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
    
    /**
     * EasyCurlRequest::AddHeader()
     * 
     * Adds a header to the web request.
     * 
     * @param $header EasyCurlHeader
     * 
     * @throws EasyCurlHeaderException If entered header is not a type of EasyCurlHeader.
     */
    public function AddHeader($header)
    {
        if ($header instanceof EasyCurlHeader)
            $this->headers[] = $header;
        else
            throw new EasyCurlHeaderException("Expecting type EasyCurlHeader.");
    }
    
    /**
     * EasyCurlRequest::GetBaseCurlObject()
     * 
     * Returns the underlying curl resource.
     * 
     * @return mixed Underlying curl resource instance.
     */
    public function GetBaseCurlObject()
    {
        return $this->curlInstance;
    }
    
    /**
     * EasyCurlRequest::GetUrl()
     * 
     * Fetches the url for which the call has to be made or has been made.
     * 
     * @return string
     */
    public function GetUrl()
    {
        return $this->url;
    }
    
    /**
     * EasyCurlRequest::FinalizeRequest()
     * 
     * Attaches the local resources of cookies, headers and post parameters to the actual request.
     */
    public function FinalizeRequest()
    {
        $this->AttachHeaders();
        $this->AttachCookies();
        $this->AttachPostParameters();
    }
    
    /**
     * EasyCurlRequest::Execute()
     * 
     * Executes the web request.
     * 
     * @return EasyCurlError|EasyCurlResponse If the request was a success returns EasyCurlResponse else EasyCurlError.
     */
    public function Execute()
    {
        $this->FinalizeRequest();
        $result = curl_exec($this->curlInstance);
        
        if ($result === false)
        {
            return new EasyCurlError($this->curlInstance);
        }
        else
        {
            return new EasyCurlResponse($this->curlInstance, $result);
        }
    }
    
    /**
     * EasyCurlRequest::EndRequest()
     * 
     * Closes the current web request session.
     */
    public function EndRequest()
    {
        curl_close($this->curlInstance);
    }
}

?>