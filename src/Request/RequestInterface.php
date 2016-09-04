<?php

namespace mglaman\AuthNet\Request;

/**
 * Interface AuthNetRequestInterface
 * @package mglaman\AuthNet\Request
 */
interface RequestInterface
{
    /**
     * Gets the request sandbox URL.
     *
     * @return string
     */
    public static function getSandboxUrl();

    /**
     * Gets the request live URL.
     *
     * @return string
     */
    public static function getLiveUrl();
}
