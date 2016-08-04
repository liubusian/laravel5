<?php 
namespace Elmer\Format;


use Illuminate\Support\ServiceProvider;

class FormatServiceProvider extends ServiceProvider {

    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @return void
     */
    public function register()
    {
        $this->app['Format'] = $this->app->share(
            function($app)
            {
                return new FormatService();
            }
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return array('Format');
    }

}