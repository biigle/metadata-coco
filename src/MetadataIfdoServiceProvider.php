<?php
namespace Biigle\Modules\MetadataIfdo;

use Biigle\Services\MetadataParsing\ParserFactory;
use Biigle\Services\Modules;
use Illuminate\Support\ServiceProvider;

class MetadataIfdoServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return  void
     */
    public function boot(Modules $modules)
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'metadata-ifdo');

        ParserFactory::extend(IfdoParser::class, 'image');
        ParserFactory::extend(IfdoParser::class, 'video');
        $modules->register('metadata-ifdo', [
            'viewMixins' => [
                'metadataParsers',
            ],
            // 'apidoc' => [__DIR__.'/Http/Controllers/Api/'],
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return  void
     */
    public function register()
    {
        //
    }
}
