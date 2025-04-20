<?php

return [
    'pretty' => false,
    'file' => [
        'disk' => 'public',
        'path' => 'sitemap.xml',
    ],
    'ping_services' => [
        \VeiligLanceren\LaravelSeoSitemap\Services\Ping\IndexNowPingService::class,
        \VeiligLanceren\LaravelSeoSitemap\Services\Ping\GooglePingService::class,
    ],
];