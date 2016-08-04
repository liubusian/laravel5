<?php
namespace Elmer\Service;

use App;
use Entity;
use Exception;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
* 商業邏輯
*/
class BaseService
{
	
	protected $repositories = [];
	protected $service;
	protected $requestEntity;
	protected $responseEntity;
	protected $entities = [];

	public function __construct(){
		$this->boot();
	}
	
	public function __get($key){
		$repositories = $this->repositories;

        if (array_key_exists($key, $repositories)) {
            return $repositories[$key];
        }

        if(isset($this->$key)) {
            return $this->$key;
        }

        return null;
	}

	protected function register($class,$name=null){
		
		if(null == $name){
			$name = basename($class);
		}

		if(!class_exists($class)){
			throw new Exception("The class '$class' does not exists.",-2);
		}

		$repository = App::make($class);

		if(!$repository instanceof RepositoryInterface){
			return false;
		}

		$this->repositories[$name] = $repository;
	}

	protected function boot(){

	}

	protected function service($class){
		if(!class_exists($class)){
			throw new Exception("The class '$class' does not exists.",-2);
		}

		return App::make($class);
	}
	
}

?>