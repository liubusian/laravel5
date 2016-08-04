<?php
namespace Elmer\Traits;

trait CheckInstanceTrait {

	protected function checkInstance($string, $class){
		if( ! class_exists($string)){
			return false;
		}

    	$relflection = new \ReflectionClass($string);

		$inputClass = $relflection->getParentClass()->name;
        return ($class === $inputClass);
    }
}