<?php 
namespace Elmer\Transform;


use Illuminate\Support\ServiceProvider;

class TransformServiceProvider extends ServiceProvider {

    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @return void
     */
    public function register()
    {
        $this->app['Transform'] = $this->app->share(
            function($app)
            {
                return new TransformService();
            }
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return array('Transform');
    }

}