<?php 
namespace Elmer\Format\Facades;


use Illuminate\Support\Facades\Facade;

class Format extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Format';
    }

}