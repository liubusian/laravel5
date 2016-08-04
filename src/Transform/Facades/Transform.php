<?php 
namespace Elmer\Transform\Facades;


use Illuminate\Support\Facades\Facade;

class Transform extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Transform';
    }

}