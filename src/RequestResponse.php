<?php

namespace Simplon\Request;

/**
 * RequestResponse
 * @package Simplon\Request
 * @author Tino Ehrich (tino@bigpun.me)
 */
class RequestResponse
{
    /**
     * @var int
     */
    private $httpCode;

    /**
     * @var ResponseHeader
     */
    private $header;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $lastUrl;

    /**
     * @deprecated
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->body;
    }

    /**
     * @deprecated
     *
     * @param mixed $body
     *
     * @return RequestResponse
     */
    public function setContent($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHttpCode()
    {
        return $this->httpCode !== null ? (int)$this->httpCode : null;
    }

    /**
     * @param int $httpCode
     *
     * @return RequestResponse
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;

        return $this;
    }

    /**
     * @return ResponseHeader
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param ResponseHeader $header
     *
     * @return RequestResponse
     */
    public function setHeader(ResponseHeader $header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return RequestResponse
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastUrl()
    {
        return $this->lastUrl;
    }

    /**
     * @param string $lastUrl
     *
     * @return RequestResponse
     */
    public function setLastUrl($lastUrl)
    {
        $this->lastUrl = $lastUrl;

        return $this;
    }
}