<?php 
namespace Elmer\Entity;


use Illuminate\Support\ServiceProvider;

class EntityServiceProvider extends ServiceProvider {

    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @return void
     */
    public function register()
    {
        $this->app['Entity'] = $this->app->share(
            function($app)
            {
                return new EntityService();
            }
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return array('Entity');
    }

}