<?php 
namespace Elmer\Service;

use App;
use Exception;
use Elmer\Service\BaseService;
use Elmer\Traits\CheckInstanceTrait;
class ServiceFactory 
{
    use CheckInstanceTrait;

	public function make($service, $parameters=[]){
        if($this->checkInstance($service, BaseService::class)){

            return App::make($service, $parameters);
        }else{
            throw new Exception("The service must be instance of 'Elmer\Service\BaseService'");
        }
    }
}