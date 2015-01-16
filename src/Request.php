<?php

namespace Simplon\Request;

/**
 * Request
 * @package Simplon\Request
 * @author Tino Ehrich (tino@bigpun.me)
 */
class Request
{
    /**
     * @param $url
     * @param array $data
     *
     * @return RequestResponse
     * @throws RequestException
     */
    public static function get($url, array $data)
    {
        $opt = [
            CURLOPT_URL            => $url . '?' . http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1
        ];

        return self::process($opt);
    }

    /**
     * @param $url
     * @param array $data
     *
     * @return RequestResponse
     * @throws RequestException
     */
    public static function post($url, array $data)
    {
        $opt = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $data
        ];

        return self::process($opt);
    }

    /**
     * @param $url
     * @param $method
     * @param array $params
     * @param int $id
     * @param string $version
     *
     * @return RequestResponse
     * @throws RequestException
     */
    public static function jsonRpc($url, $method, array $params = [], $id = 1, $version = '2.0')
    {
        $opt = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST           => 1,
            CURLOPT_HTTPHEADER     => ['Content-type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode([
                'jsonrpc' => $version,
                'id'      => $id,
                'method'  => $method,
                'params'  => $params,
            ]),
        ];

        // request
        $requestResponse = self::process($opt);

        // decode json
        $decoded = json_decode($requestResponse->getContent(), true);

        // if decoding fails throw exception with received response
        if ($decoded === null)
        {
            throw new RequestException($requestResponse);
        }

        return $requestResponse->setContent($decoded);
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
     *
     * @return array|mixed|null
     */
    public static function getGetData($key = null)
    {
        return self::readData($_GET, $key);
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
     * @param null|string $key
     *
     * @return array|mixed|null
     */
    public static function getPostData($key = null)
    {
        return self::readData($_POST, $key);
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
     * @param null|string $key
     *
     * @return array|mixed|null
     */
    public static function getSessionData($key = null)
    {
        return self::readData($_SESSION, $key);
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
     * @param $source
     * @param null|string $key
     *
     * @return array|null|mixed
     */
    private static function readData($source, $key = null)
    {
        if (isset($source))
        {
            if ($key === null)
            {
                return (array)$source;
            }

            if (isset($source[$key]))
            {
                return $source[$key];
            }
        }

        return null;
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
     * @param array $opt
     *
     * @return RequestResponse
     * @throws RequestException
     */
    private static function process(array $opt)
    {
        $curl = curl_init();
        curl_setopt_array($curl, $opt);

        // run request
        $response = curl_exec($curl);

        // cache error if any occurs
        $error = curl_error($curl);

        // cache http code
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        // throw if request failed
        if ($response === false)
        {
            throw new RequestException($error);
        }

        // --------------------------------------

        return (new RequestResponse())
            ->setHttpCode($httpCode)
            ->setContent($response);
    }
}