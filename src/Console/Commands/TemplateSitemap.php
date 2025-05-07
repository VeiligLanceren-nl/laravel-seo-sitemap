<?php

namespace VeiligLanceren\LaravelSeoSitemap\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TemplateSitemap extends Command
{
    /**
     * @var string
     */
    protected $signature = 'sitemap:template {name : Class name (e.g. PostSitemapTemplate)}';

    /**
     * @var string
     */
    protected $description = 'Create a new SitemapItemTemplate class';

    /**
     * @return void
     */
    public function handle(): void
    {
        $name      = Str::studly($this->argument('name'));
        $namespace = app()->getNamespace() . 'SitemapTemplates';
        $dir       = app_path('SitemapTemplates');
        $path      = "{$dir}/{$name}.php";

        if (File::exists($path)) {
            $this->error("{$path} already exists.");
            return;
        }

        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $stub = <<<PHP
<?php

namespace {$namespace};

use Illuminate\Routing\Route;
use VeiligLanceren\LaravelSeoSitemap\Contracts\SitemapItemTemplate;
use VeiligLanceren\LaravelSeoSitemap\Url;

class {$name} implements SitemapItemTemplate
{
    /**
     * @param Route \$route
     * @return iterable<Url>
     */
    public function generate(Route \$route): iterable
    {
        // Example implementation – adjust to your needs.
        // return YourModel::all()->map(fn (YourModel \$model) =>
        //     Url::make(route(\$route->getName(), \$model))
        //         ->lastmod(\$model->updated_at)
        // );

        return [];
    }

    public function getIterator(): \Traversable
    {
        yield from \$this->generate(app(Route::class));
    }
}
PHP;

        File::put($path, $stub);

        $this->info("Template created at {$path}");
    }
}