<?php

namespace VeiligLanceren\LaravelSeoSitemap\Exceptions;

use InvalidArgumentException;

class InvalidDynamicRouteCallbackException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('The callback for ->dynamic() must return a DynamicRoute or iterable of parameter arrays.');
    }
}
