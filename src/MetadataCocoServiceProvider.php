<?php
namespace Biigle\Modules\MetadataCoco;

use Biigle\Services\MetadataParsing\ParserFactory;
use Biigle\Services\Modules;
use Illuminate\Support\ServiceProvider;

class MetadataCocoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return  void
     */
    public function boot(Modules $modules)
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'metadata-coco');

        ParserFactory::extend(CocoParser::class, 'image');
        $modules->register('metadata-coco', [
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
