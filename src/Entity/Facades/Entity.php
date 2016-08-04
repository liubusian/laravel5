<?php 
namespace Elmer\Entity\Facades;


use Illuminate\Support\Facades\Facade;

class Entity extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Entity';
    }

}