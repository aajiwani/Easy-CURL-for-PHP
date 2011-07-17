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
 * EasyCurlExecuter
 * 
 * EasyCurlExecuter is the class which maintains the state of a HTTP Requests added in a queue to be executed.
 */
class EasyCurlExecuter
{
    private $curlSessions = array();
    private $multiThreadHandler = null;
    private $recordResults;
    
    /**
     * Array of results, after the completion of requests. Provided recording of the results is set to true.
     * 
     * @var mixed
     */
    public $Results = null;
    
    /**
     * EasyCurlExecuter::__construct()
     * 
     * @param bool $recordResults Indicates, if recording of the requests is on or off.
     * @return EasyCurlExecuter EasyCurlExecuter instance.
     */
    public function __construct($recordResults = true)
    {
        $this->recordResults = $recordResults;
        
        if ($this->recordResults)
            $this->Results = array();
    }
    
    /**
     * EasyCurlExecuter::AddSession()
     * 
     * Adds a web request in the queue list.
     * 
     * @param EasyCurlRequest $session Web request instance.
     * @param EasyCurlExecuterCallback $callback Callback which has to be attached to the request. Would be called on completion of the request.
     * @param mixed $userObject Any object that has to be returned back in the result and/or callback.
     * 
     * @throws EasyCurlExecuterException If the session is not of type EasyCurlRequest.
     * @throws EasyCurlExecuterCallbackException If the callback is not of type EasyCurlExecuterCallback.
     */
    public function AddSession($session, $callback = null, $userObject = null)
    {
        if (!($session instanceof EasyCurlRequest))
        {
            throw new EasyCurlExecuterException("Session expected to be the type of EasyCurlRequest");
        }
        else
        {
            $session->FinalizeRequest();
            
            $internalSession = new EasyCurlExecuterNode();
            $internalSession->Url = $session->GetUrl();
            $internalSession->CurlResource = $session->GetBaseCurlObject();
            
            if ($callback != null)
            {
                if (!($callback instanceof EasyCurlExecuterCallback))
                {
                    throw new EasyCurlExecuterCallbackException("Callback expected to be the type of EasyCurlExecuterCallback");
                }
                else
                {
                    $internalSession->Callback = $callback;
                }
            }
            else
            {
                $internalSession->Callback = null;
            }
            
            if ($userObject != null)
                $internalSession->UserObject = $userObject;
            
            $this->curlSessions[] = $internalSession;
        }
    }
    
    /**
     * EasyCurlExecuter::ExecuteRequests()
     * 
     * Executes the curl multiple requests queue. Calls the attached callback and record results if recording is on.
     * 
     * @throws EasyCurlExecuterException If any error occured in internet connection. Or multi select call of php curl.
     */
    public function ExecuteRequests()
    {
        $this->multiThreadHandler = curl_multi_init();
        
        $running = null;
        
        foreach ($this->curlSessions as &$curlSession)
        {
            $current = $curlSession->CurlResource;
            curl_multi_add_handle($this->multiThreadHandler, $current);
        }
        
        do
        {
            while(($execrun = curl_multi_exec($this->multiThreadHandler, $running)) == CURLM_CALL_MULTI_PERFORM);
            
            if ($execrun != CURLM_OK) throw new EasyCurlExecuterException("Check your internet connection");
            
            while($done = curl_multi_info_read($this->multiThreadHandler))
            {
                foreach ($this->curlSessions as &$curlSession)
                {
                    if ($curlSession->CurlResource === $done['handle'])
                    {
                        $response = curl_multi_getcontent($done['handle']);
                        
                        $resultResponse = new EasyCurlExecuterResult();
                        
                        if ($response != null && $response != FALSE && $response != "")
                        {
                            $resultResponse->Response = new EasyCurlResponse($done['handle'], $response);
                        }
                        else
                        {
                            $resultResponse->Response = new EasyCurlError($done['handle']);
                        }
                        
                        $resultResponse->Url = $curlSession->Url;
                        $resultResponse->UserState = $curlSession->UserObject;
                        
                        if ($curlSession->Callback != null)
                        {
                            $curlSession->Callback->Call($resultResponse);
                        }
                        
                        if ($this->recordResults)
                            $this->Results[] = $resultResponse;
                        
                        curl_multi_remove_handle($this->multiThreadHandler, $done['handle']);
                    }
                }
            }

            if (!$running) break;
            
            while (($res = curl_multi_select($this->multiThreadHandler)) === 0);
            if ($res === false) throw new EasyCurlExecuterException("Encountered error while curl_multi_select");
        }
        while (true);
        
        curl_multi_close($this->multiThreadHandler);
        
        array_splice($this->curlSessions, 0);
    }
}

?>