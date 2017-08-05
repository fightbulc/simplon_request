<?php

namespace Simplon\Request;

/**
 * Request
 * @package Simplon\Request
 * @author Tino Ehrich (tino@bigpun.me)
 */
class Request
{
    const POST_VARIANT_POST = 'POST';
    const POST_VARIANT_PUT = 'PUT';
    const POST_VARIANT_DELETE = 'DELETE';
    const DATA_FORMAT_QUERY_STRING = 'query-string';
    const DATA_FORMAT_JSON = 'json';

    /**
     * @var array
     */
    private $requestHeaders = array();
    
     /**
     * @var bool
     */
    private $isHttpHeadersLogged = false;
    
     /**
     * @var string
     */
    private $logFileName = 'headers_log.txt';
    
    /**
     * @param string $filename
     */
    public function setLog($filename = '')
    {
	$this->isHttpHeadersLogged = true;
	
	if (!empty($filename))
	{
		$this->logFileName = $filename;  
	}   
    }
    
    /**
     * @param array $reqheaders
     */
    public function setRequestHeaders(array $reqheaders = [])
    {
	if (!empty($reqheaders))
	{
		$this->requestHeaders  = $reqheaders;
	}
    }
    
    public function clearRequestHeaders()
    {
	$this->requestHeaders  = array();
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $optCustom
     *
     * @return RequestResponse
     * @throws RequestException
     */
    public function get($url, array $data = [], array $optCustom = [])
    {
        if (empty($data) === false)
        {
            $url .= '?' . http_build_query($data);
        }

        $opt = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
        ];

        return $this->process($opt, $optCustom);
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $optCustom
     * @param string $dataType
     *
     * @return RequestResponse
     */
    public function post($url, array $data = [], array $optCustom = [], $dataType = self::DATA_FORMAT_QUERY_STRING)
    {
        return $this->postVariant(self::POST_VARIANT_POST, $url, $data, $optCustom, $dataType);
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $optCustom
     * @param string $dataType
     *
     * @return RequestResponse
     */
    public function put($url, array $data = [], array $optCustom = [], $dataType = self::DATA_FORMAT_QUERY_STRING)
    {
        return $this->postVariant(self::POST_VARIANT_PUT, $url, $data, $optCustom, $dataType);
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $optCustom
     * @param string $dataType
     *
     * @return RequestResponse
     */
    public function delete($url, array $data = [], array $optCustom = [], $dataType = self::DATA_FORMAT_QUERY_STRING)
    {
        return $this->postVariant(self::POST_VARIANT_DELETE, $url, $data, $optCustom, $dataType);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $params
     * @param int $id
     * @param array $optCustom
     *
     * @return RequestResponse
     * @throws RequestException
     */
    public function jsonRpc($url, $method, array $params = [], $id = 1, array $optCustom = [])
    {
        $opt = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST           => 1,
            CURLOPT_HTTPHEADER     => ['Content-type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode([
                'jsonrpc' => '2.0',
                'id'      => $id,
                'method'  => $method,
                'params'  => $params,
            ]),
        ];

        // request
        $requestResponse = $this->process(array_merge($opt, $optCustom));

        // decode json
        $decoded = json_decode($requestResponse->getBody(), true);

        // if decoding fails throw exception with received response
        if ($decoded === null)
        {
            throw new RequestException($requestResponse);
        }

        return $requestResponse->setBody($decoded);
    }

    /**
     * @param string $url
     */
    public function redirect($url)
    {
        // redirect now
        header('Location: ' . $url);

        // exit script
        exit;
    }

    /**
     * @param string $key
     * @param string $fallbackValue
     *
     * @return mixed|null
     */
    public function getGetData($key = null, $fallbackValue = null)
    {
        return $this->readData($_GET, $key, $fallbackValue);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasGetData($key = null)
    {
        return $this->hasData($_GET, $key);
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return $this->isRequestMethod('GET');
    }

    /**
     * @param string $key
     * @param string $fallbackValue
     *
     * @return mixed|null
     */
    public function getPostData($key = null, $fallbackValue = null)
    {
        return $this->readData($_POST, $key, $fallbackValue);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasPostData($key = null)
    {
        return $this->hasData($_POST, $key);
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return $this->isRequestMethod('POST');
    }

    /**
     * @param bool $isJson
     *
     * @return array|string
     */
    public function getInputStream($isJson = true)
    {
        $requestJson = (string)file_get_contents('php://input');

        if ($isJson === true)
        {
            return (array)json_decode($requestJson, true);
        }

        return $requestJson;
    }

    /**
     * @return bool
     */
    public function hasInputStream()
    {
        return $this->hasData($this->getInputStream());
    }

    /**
     * @param string $key
     * @param string $fallbackValue
     *
     * @return mixed|null
     */
    public function getSessionData($key = null, $fallbackValue = null)
    {
        return $this->readData($_SESSION, $key, $fallbackValue);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasSessionData($key = null)
    {
        return $this->hasData($_SESSION, $key);
    }

    /**
     * @param string $key
     * @param string $fallbackValue
     *
     * @return mixed|null
     */
    public function getServerData($key = null, $fallbackValue = null)
    {
        return $this->readData($_SERVER, $key, $fallbackValue);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasFileData($key = null)
    {
        return $this->hasData($_FILES, $key);
    }

    /**
     * @param string $key
     * @param string $fallbackValue
     *
     * @return mixed|null
     */
    public function getFileData($key = null, $fallbackValue = null)
    {
        return $this->readData($_FILES, $key, $fallbackValue);
    }

    /**
     * @param array $source
     * @param string $key
     * @param string $fallbackValue
     *
     * @return null|mixed
     */
    private function readData($source, $key = null, $fallbackValue = null)
    {
        if (isset($source))
        {
            if ($key === null)
            {
                return $source;
            }

            if (isset($source[$key]))
            {
                return $source[$key];
            }
        }

        return $fallbackValue;
    }

    /**
     * @param array $source
     * @param string $key
     *
     * @return bool
     */
    private function hasData($source, $key = null)
    {
        return $this->readData($source, $key) !== null;
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    private function isRequestMethod($method)
    {
        return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === strtoupper($method);
    }

    /**
     * @param string $type
     * @param string $url
     * @param array $data
     * @param array $optCustom
     * @param string $dataFormat
     *
     * @return RequestResponse
     * @throws RequestException
     */
    private function postVariant($type, $url, array $data = [], $optCustom = [], $dataFormat)
    {
        $opt = [
            CURLOPT_URL            => $url,
            CURLOPT_CUSTOMREQUEST  => $type,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST           => 1,
        ];

        if (empty($data) === false)
        {
            switch ($dataFormat)
            {
                case self::DATA_FORMAT_JSON:
                    $data = json_encode($data);
                    break;

                default:
                    $data = http_build_query($data);
            }

            $opt[CURLOPT_POSTFIELDS] = $data;
        }

        return $this->process($opt, $optCustom);
    }

    /**
     * @param array $opt
     * @param array $optCustom
     *
     * @return RequestResponse
     * @throws RequestException
     */
    private function process(array $opt, array $optCustom = [])
    {
        $curl = curl_init();

        // add options to retrieve header
        $opt[CURLOPT_HEADER] = 1;

        // merge options
        foreach ($optCustom as $key => $val)
        {
            $opt[$key] = $optCustom[$key];
        }
        
        // set request headers
        if (!empty($this->requestHeaders))
        {
        	$opt[CURLOPT_HTTPHEADER] = $this->requestHeaders;
        } 
        
        // log headers
        if ($this->isHttpHeadersLogged)
        {
        
        	$f = fopen( $this->logFileName, 'a');
        	
		$opt[CURLOPT_VERBOSE] = 1;
		$opt[CURLOPT_CERTINFO] = 1;
        	$opt[CURLOPT_STDERR] = $f;
        
        	curl_setopt_array($curl, $opt);
        
        	// run request
        	$response = curl_exec($curl);
        
        	fwrite($f, "\r\n");
        	fflush($f);
        	fclose($f);

        	// parse header
        	$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        	$header = $this->parseHttpHeaders(substr($response, 0, $header_size));

        	// parse body
        	$body = substr($response, $header_size);

        	// cache error if any occurs
        	$error = curl_error($curl);

        	// cache http code
        	$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        	// url on which we eventually might have ended up (301 redirects)
        	$lastUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

		
        	curl_close($curl);

        	// throw if request failed
        	if ($response === false)
        	{
            		throw new RequestException($error);
        	}

        	// --------------------------------------

        	return (new RequestResponse())
            		->setHttpCode($httpCode)
            		->setHeader($header)
            		->setBody($body)
            		->setLastUrl($lastUrl);
	} // if $this->isHttpHeadersLogged
    
	curl_setopt_array($curl, $opt);
        
        // run request
        $response = curl_exec($curl);       

        // parse header
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = $this->parseHttpHeaders(substr($response, 0, $header_size));

        // parse body
        $body = substr($response, $header_size);

        // cache error if any occurs
        $error = curl_error($curl);

        // cache http code
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // url on which we eventually might have ended up (301 redirects)
        $lastUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

        curl_close($curl);

        // throw if request failed
        if ($response === false)
        {
            throw new RequestException($error);
        }

        // --------------------------------------

        return (new RequestResponse())
            	->setHttpCode($httpCode)
            	->setHeader($header)
            	->setBody($body)
            	->setLastUrl($lastUrl);
    
    }
    
    /**
     * @param string $headers
     *
     * @return ResponseHeader
     */
    private function parseHttpHeaders($headers)
    {
        $data = [];
        $lines = explode("\r\n", chop($headers));

        $data['http-status'] = array_shift($lines);

        foreach ($lines as $line)
        {
            $parts = explode(':', $line);
            $key = strtolower(array_shift($parts));
            if (!empty($data[$key]))
            {
		$data[$key] = $data[$key].trim(join(':', $parts)); // sometimes response has 2 headers Set-Cookie
	    }
            else
            {
            	$data[$key] = trim(join(':', $parts));
            }

        }

        return new ResponseHeader($data);
    }
}
