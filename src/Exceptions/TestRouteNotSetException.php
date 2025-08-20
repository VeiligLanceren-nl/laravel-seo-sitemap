<?php

namespace VeiligLanceren\LaravelSeoSitemap\Exceptions;

use RuntimeException;

class TestRouteNotSetException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Test route not set via setTestRoute().');
    }
}
