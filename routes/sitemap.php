<?php

use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Http\Controllers\SitemapController;

Route::get('/sitemap.xml', [SitemapController::class, 'index']);