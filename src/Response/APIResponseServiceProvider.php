<?php 
namespace Elmer\Response;


use Illuminate\Support\ServiceProvider;

class APIResponseServiceProvider extends ServiceProvider {

    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @return void
     */
    public function register()
    {
        $this->app['APIResponse'] = $this->app->share(
            function($app)
            {
                return new APIResponseService();
            }
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return array('APIResponse');
    }

}