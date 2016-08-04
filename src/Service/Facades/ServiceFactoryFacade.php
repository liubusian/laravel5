<?php 
namespace Elmer\Service\Facades;


use Illuminate\Support\Facades\Facade;

class ServiceFactoryFacade extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ServiceFactory';
    }

}