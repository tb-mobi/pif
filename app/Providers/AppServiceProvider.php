<?php

namespace mobi2\Providers;

use Illuminate\Support\ServiceProvider;
use MClient;
use Logger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Logger::configure(dirname(__FILE__).'/../../public/tw.xml');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      $this->app->singleton('MClient', function()
      {
        return new MClient();
      });
    }
}
