<?php

namespace VeiligLanceren\LaravelSeoSitemap\Support\Enums;

enum ChangeFrequency: string
{
    case ALWAYS = 'always';
    case HOURLY = 'hourly';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
    case NEVER = 'never';

    /**
     * Validate if the given string is a valid change frequency.
     *
     * @param string $value
     * @return bool
     */
    public static function isValid(string $value): bool
    {
        return collect(self::cases())
            ->pluck('value')
            ->contains($value);
    }
}