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
     * @return string
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
     * @return string
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
     * @return array
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
        $response = self::process($opt);

        // decode json
        $decoded = json_decode($response, true);

        // if decoding fails throw exception with received response
        if ($decoded === null)
        {
            throw new RequestException($response);
        }

        return (array)$decoded;
    }

    /**
     * @param string $url
     * @param array $params
     *
     * @return string
     */
    public static function renderUrl($url, array $params = [])
    {
        // replace placeholders
        if (empty($params) === false)
        {
            foreach ($params as $k => $v)
            {
                $url = str_replace('{{' . $k . '}}', $v, $url);
            }
        }

        return (string)$url;
    }

    /**
     * @param string $url
     * @param array $params
     */
    public static function redirect($url, array $params = [])
    {
        // redirect now
        header('Location: ' . self::renderUrl($url, $params));

        // exit script
        exit;
    }

    /**
     * @param array $opt
     *
     * @return string
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

        curl_close($curl);

        // throw if request failed
        if ($response === false)
        {
            throw new RequestException($error);
        }

        return (string)$response;
    }
}