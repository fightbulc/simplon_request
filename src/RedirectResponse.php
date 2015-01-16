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
     * @param string|array $url
     * @param array $params
     */
    public function __construct($url, array $params = [])
    {
        $this->url = Helper::urlRender($url, $params);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return (string)$this->url;
    }
}