<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

/**
 * Tests for the `sitemap:install` command.
 */

beforeEach(function (): void {
    $routesPath = base_path('routes');
    File::ensureDirectoryExists($routesPath);

    File::delete("{$routesPath}/sitemap.php");
    File::delete("{$routesPath}/web.php");
});

it('publishes the sitemap route file and adds the include to web.php', function (): void {
    $webPath = base_path('routes/web.php');
    File::put($webPath, "<?php\n// Laravel web routes\n");

    $exitCode = Artisan::call('sitemap:install');

    expect($exitCode)->toBe(0);
    expect(File::exists(base_path('routes/sitemap.php')))->toBeTrue();

    $includeLine = "require __DIR__.'/sitemap.php';";
    expect(File::get($webPath))->toContain($includeLine);
});

it('does not duplicate the include line when run twice', function (): void {
    $webPath = base_path('routes/web.php');
    File::put($webPath, "<?php\n// Laravel web routes\n");

    $exitCode = Artisan::call('sitemap:install');

    expect($exitCode)->toBe(0);
    $occurrences = substr_count(File::get($webPath), "require __DIR__.'/sitemap.php';");
    expect($occurrences)->toBe(1);
});

it('publishes the route file even when web.php is missing', function (): void {
    // Ensure web.php does not exist
    File::delete(base_path('routes/web.php'));

    $exitCode = Artisan::call('sitemap:install');

    expect($exitCode)->toBe(0);
    expect(File::exists(base_path('routes/sitemap.php')))->toBeTrue();
    expect(File::exists(base_path('routes/web.php')))->toBeFalse();
});
