<?php 
namespace Elmer\Response\Facades;


use Illuminate\Support\Facades\Facade;

class APIResponseFacade extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'APIResponse';
    }

}