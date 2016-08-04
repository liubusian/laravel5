<?php 
namespace Elmer\Service;


use Illuminate\Support\ServiceProvider;

class ServiceFactoryProvider extends ServiceProvider {

    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @return void
     */
    public function register()
    {
        $this->app['ServiceFactory'] = $this->app->share(
            function($app)
            {
                return new ServiceFactory();
            }
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return array('ServiceFactory');
    }

}