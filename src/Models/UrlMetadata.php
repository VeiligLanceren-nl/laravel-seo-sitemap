<?php

namespace VeiligLanceren\LaravelSeoSitemap\Models;

use Illuminate\Database\Eloquent\Model;

class UrlMetadata extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'route_name',
        'priority',
        'lastmod'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'lastmod' => 'datetime',
    ];
}