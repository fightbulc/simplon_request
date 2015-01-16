<?php

namespace Simplon\Request;

use Simplon\Helper\Helper;

/**
 * RedirectResponse
 * @package Simplon\Request
 * @author Tino Ehrich (tino@bigpun.me)
 */
class RedirectResponse
{
    /**
     * @var string
     */
    private $url;

    /**
     * @param $url
     * @param null $path
     * @param array $params
     */
    public function __construct($url, $path = null, array $params = [])
    {
        $this->url = Helper::urlRender($url, $path, $params);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return (string)$this->url;
    }
}