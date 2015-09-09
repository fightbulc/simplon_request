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
     * @param $url
     * @param array $data
     * @param array $optCustom
     *
     * @return RequestResponse
     * @throws RequestException
     */
    public static function get($url, array $data = [], array $optCustom = [])
    {
        if (empty($data) === false)
        {
            $url .= '?' . http_build_query($data);
        }

        $opt = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1
        ];

        return self::process($opt, $optCustom);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $optCustom
     * @param string $dataType
     *
     * @return RequestResponse
     */
    public static function post($url, array $data = [], array $optCustom = [], $dataType = self::DATA_FORMAT_QUERY_STRING)
    {
        return self::postVariant(self::POST_VARIANT_POST, $url, $data, $optCustom, $dataType);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $optCustom
     * @param string $dataType
     *
     * @return RequestResponse
     */
    public static function put($url, array $data = [], array $optCustom = [], $dataType = self::DATA_FORMAT_QUERY_STRING)
    {
        return self::postVariant(self::POST_VARIANT_PUT, $url, $data, $optCustom, $dataType);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $optCustom
     * @param string $dataType
     *
     * @return RequestResponse
     */
    public static function delete($url, array $data = [], array $optCustom = [], $dataType = self::DATA_FORMAT_QUERY_STRING)
    {
        return self::postVariant(self::POST_VARIANT_DELETE, $url, $data, $optCustom, $dataType);
    }

    /**
     * @param $url
     * @param $method
     * @param array $params
     * @param int $id
     * @param array $optCustom
     *
     * @return RequestResponse
     * @throws RequestException
     */
    public static function jsonRpc($url, $method, array $params = [], $id = 1, array $optCustom = [])
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
        $requestResponse = self::process(array_merge($opt, $optCustom));

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
    public static function redirect($url)
    {
        // redirect now
        header('Location: ' . $url);

        // exit script
        exit;
    }

    /**
     * @param null|string $key
     * @param null|string $fallbackValue
     *
     * @return mixed|null
     */
    public static function getGetData($key = null, $fallbackValue = null)
    {
        return self::readData($_GET, $key, $fallbackValue);
    }

    /**
     * @param null|string $key
     *
     * @return bool
     */
    public static function hasGetData($key = null)
    {
        return self::hasData($_GET, $key);
    }

    /**
     * @return bool
     */
    public static function isGet()
    {
        return self::isRequestMethod('GET');
    }

    /**
     * @param null $key
     * @param null $fallbackValue
     *
     * @return array|string
     */
    public static function getPostData($key = null, $fallbackValue = null)
    {
        return self::readData($_POST, $key, $fallbackValue);
    }

    /**
     * @param null|string $key
     *
     * @return bool
     */
    public static function hasPostData($key = null)
    {
        return self::hasData($_POST, $key);
    }

    /**
     * @return bool
     */
    public static function isPost()
    {
        return self::isRequestMethod('POST');
    }

    /**
     * @param bool $isJson
     *
     * @return array|string
     */
    public static function getInputStream($isJson = true)
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
    public static function hasInputStream()
    {
        return self::hasData(self::getInputStream());
    }

    /**
     * @param null|string $key
     * @param null|string $fallbackValue
     *
     * @return mixed|null
     */
    public static function getSessionData($key = null, $fallbackValue = null)
    {
        return self::readData($_SESSION, $key, $fallbackValue);
    }

    /**
     * @param null|string $key
     *
     * @return bool
     */
    public static function hasSessionData($key = null)
    {
        return self::hasData($_SESSION, $key);
    }

    /**
     * @param null|string $key
     * @param null|string $fallbackValue
     *
     * @return mixed|null
     */
    public static function getServerData($key = null, $fallbackValue = null)
    {
        return self::readData($_SERVER, $key, $fallbackValue);
    }

    /**
     * @param $source
     * @param null|string $key
     * @param null|string $fallbackValue
     *
     * @return null|mixed
     */
    private static function readData($source, $key = null, $fallbackValue = null)
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
     * @param null|array $source
     * @param null $key
     *
     * @return bool
     */
    private static function hasData($source, $key = null)
    {
        return self::readData($source, $key) !== null;
    }

    /**
     * @param $method
     *
     * @return bool
     */
    private static function isRequestMethod($method)
    {
        return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === strtoupper($method);
    }

    /**
     * @param $type
     * @param $url
     * @param array $data
     * @param array $optCustom
     * @param string $dataFormat
     *
     * @return RequestResponse
     * @throws RequestException
     */
    private static function postVariant($type, $url, array $data = [], $optCustom = [], $dataFormat)
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

        return self::process($opt, $optCustom);
    }

    /**
     * @param array $opt
     * @param array $optCustom
     *
     * @return RequestResponse
     * @throws RequestException
     */
    private static function process(array $opt, array $optCustom = [])
    {
        $curl = curl_init();

        // add options to retrieve header
        $opt[CURLOPT_HEADER] = 1;

        // merge options
        foreach ($optCustom as $key => $val)
        {
            $opt[$key] = $optCustom[$key];
        }

        curl_setopt_array($curl, $opt);

        // run request
        $response = curl_exec($curl);

        // parse header
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = self::parseHttpHeaders(substr($response, 0, $header_size));

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
    private static function parseHttpHeaders($headers)
    {
        $data = [];
        $lines = explode("\r\n", chop($headers));

        $data['http-status'] = array_shift($lines);

        foreach ($lines as $line)
        {
            $parts = explode(':', $line);
            $data[strtolower(array_shift($parts))] = trim(join(':', $parts));
        }

        return new ResponseHeader($data);
    }
}