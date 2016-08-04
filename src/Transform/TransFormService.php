<?php 
namespace Elmer\Transform;

use App;
use Elmer\Traits\CheckInstanceTrait;

class TransformService 
{

    use CheckInstanceTrait;

    public function make($transform, $data){

    	if(is_object($transform)){
    		if( ! $transform instanceof BaseTransform){
    			throw new Exception("Transform make parameter 1 must be BaseTransform or String");    
    		}
    		return $transform->make($data);		
    	}

        if($this->checkInstance($transform, BaseTransform::class)){

            $transform = App::make($transform);
            return $transform->make($data);
        }
    }

}