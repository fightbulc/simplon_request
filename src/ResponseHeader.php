<?php

namespace Simplon\Request;

/**
 * Class ResponseHeader
 * @package Simplon\Request
 */
class ResponseHeader
{
    /**
     * @var array
     */
    private $rawData = [];

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $allow;

    /**
     * @var string
     */
    private $expires;

    /**
     * @var string
     */
    private $server;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $contentEncoding;

    /**
     * @var string
     */
    private $contentLanguage;

    /**
     * @var string
     */
    private $contentLength;

    /**
     * @var string
     */
    private $contentLocation;

    /**
     * @var string
     */
    private $contentDisposition;

    /**
     * @var string
     */
    private $connection;

    /**
     * @var string
     */
    private $transferEncoding;

    /**
     * @var string
     */
    private $cacheControl;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $eTag;

    /**
     * @var string
     */
    private $lastModified;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $pragma;

    /**
     * @var string
     */
    private $setCookie;

    /**
     * @var string
     */
    private $xPoweredBy;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->rawData = $data;

        foreach ($data as $key => $val)
        {
            switch ($key)
            {
                case 'http-status':
                    $this->status = $val;
                    break;

                case 'allow':
                    $this->allow = $val;
                    break;

                case 'expires':
                    $this->expires = $val;
                    break;

                case 'server':
                    $this->server = $val;
                    break;

                case 'content-type':
                    $this->contentType = $val;
                    break;

                case 'content-encoding':
                    $this->contentEncoding = $val;
                    break;

                case 'content-language':
                    $this->contentLanguage = $val;
                    break;

                case 'content-length':
                    $this->contentLength = $val;
                    break;

                case 'content-location':
                    $this->contentLocation = $val;
                    break;

                case 'content-disposition':
                    $this->contentDisposition = $val;
                    break;

                case 'connection':
                    $this->connection = $val;
                    break;

                case 'transfer-encoding':
                    $this->transferEncoding = $val;
                    break;

                case 'cache-control':
                    $this->cacheControl = $val;
                    break;

                case 'date':
                    $this->date = $val;
                    break;

                case 'etag':
                    $this->eTag = $val;
                    break;

                case 'last-modified':
                    $this->lastModified = $val;
                    break;

                case 'location':
                    $this->location = $val;
                    break;

                case 'pragma':
                    $this->pragma = $val;
                    break;

                case 'set-cookie':
                    $this->setCookie = $val;
                    break;

                case 'x-powered-by':
                    $this->xPoweredBy = $val;
                    break;

                default:
            }
        }
    }

    /**
     * @param int $code
     *
     * @return bool
     */
    public function isStatus($code)
    {
        return strpos($this->getStatus(), (string)$code) !== false;
    }

    /**
     * @return bool
     */
    public function isJson()
    {
        return $this->getContentType() === 'application/json';
    }

    /**
     * @return bool
     */
    public function isXml()
    {
        return $this->getContentType() === 'application/xml';
    }

    /**
     * @return bool
     */
    public function isHtml()
    {
        return $this->getContentType() === 'text/html';
    }

    /**
     * @return bool
     */
    public function isText()
    {
        return $this->getContentType() === 'text/plain';
    }

    /**
     * @return bool
     */
    public function isStream()
    {
        return $this->getContentType() === 'application/octet-stream';
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getAllow()
    {
        return $this->allow;
    }

    /**
     * @return string
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getContentEncoding()
    {
        return $this->contentEncoding;
    }

    /**
     * @return string
     */
    public function getContentLanguage()
    {
        return $this->contentLanguage;
    }

    /**
     * @return string
     */
    public function getContentLength()
    {
        return $this->contentLength;
    }

    /**
     * @return string
     */
    public function getContentLocation()
    {
        return $this->contentLocation;
    }

    /**
     * @return string
     */
    public function getContentDisposition()
    {
        return $this->contentDisposition;
    }

    /**
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getTransferEncoding()
    {
        return $this->transferEncoding;
    }

    /**
     * @return string
     */
    public function getXPoweredBy()
    {
        return $this->xPoweredBy;
    }

    /**
     * @return string
     */
    public function getCacheControl()
    {
        return $this->cacheControl;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getETag()
    {
        return $this->eTag;
    }

    /**
     * @return string
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getPragma()
    {
        return $this->pragma;
    }

    /**
     * @return string
     */
    public function getSetCookie()
    {
        return $this->setCookie;
    }
}