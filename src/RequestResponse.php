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
     * @var mixed
     */
    private $content;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     *
     * @return RequestResponse
     */
    public function setContent($content)
    {
        $this->content = $content;

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
}