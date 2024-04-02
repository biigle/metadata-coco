<?php

namespace Biigle\Modules\Module;

use Illuminate\Support\ServiceProvider;
use Biigle\Services\MetadataParsing\ParserFactory;

class MetadataIfdoServiceProvider extends ServiceProvider
{

   /**
   * Bootstrap the application events.
   *
   * @return  void
   */
    public function boot()
    {
        ParserFactory::extend(ImageIfdoParser::class, 'image');
        ParserFactory::extend(VideoIfdoParser::class, 'video');
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
