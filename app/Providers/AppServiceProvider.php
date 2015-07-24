<?php

namespace mobi2\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use mobi2\Adapters\TranzWare;
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
        Logger::configure(dirname(__FILE__).'/../../config/tw.xml');
        define('TRANZWARE_CONFIG_FILE',dirname(__FILE__).'/../../config/tw.ini');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(){
        //$tw=new MClient(dirname(__FILE__).'/../../config/tw.ini');
        //$this->app->instance('MClient',$tw);
        //$this->app->singleton('MClient', function($app){return new MClient(dirname(__FILE__).'/../../config/tw.ini');});
        //$this->app->singleton('VTBIAdapter', function($app){return new mobi2\Adapters\VTBI\Adapter(dirname(__FILE__).'/../../config/tw.ini');});
        //$this->app->singleton('TranzWare', function($app){return new TranzWare(dirname(__FILE__).'/../../config/tw.ini');});
        //$tranzWare=$this->app->make('TranzWare');
        //$cfg=dirname(__FILE__).'/../../config/tw.ini';
        //$this->app->singleton('tranzWare',function($app){return $tw;});
        $this->app->singleton('tranzWare',function($app){
            return new TranzWare();
        });
    }
}
