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
    public function register(){
        $tw=new MClient(dirname(__FILE__).'/../../public/tw.ini');
        $this->app->instance('MClient',$tw);
        //$this->app->singleton('MClient', function($app){return new MClient();});
    }
}
