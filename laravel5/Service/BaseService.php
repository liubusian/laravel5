<?php
namespace Elmer\Laravel5\Service;

use App;
use Exception;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
* 商業邏輯
*/
class BaseService
{
	
	protected $repositories = [];
	protected $service;

	public function __construct(){
		$this->boot();
	}
	
	protected function __get($key){
		$repositories = $this->repositories();

        if (array_key_exists($key, $repositories)) {
            return $repositories[$key];
        } elseif(isset($this->$key)) {
            return $this->$key;
        }
        return null;
	}

	protected function register($class,$name=null){
		
		if(null == $name){
			$name = basename($class);
		}

		if(!class_exists($class)){
			throw new Exception("The class '$class' does not exists.");
		}

		$repository = App::make($class);

		if(class_exists(RepositoryInterface)){
			if(!$repository instanceof RepositoryInterface){
				return false;
			}
		}

		$this->repositories[$name] = $repository;
	}

	protected function boot(){

	}

	protected function service($class){
		if(!class_exists($class)){
			throw new Exception("The class '$class' does not exists.");
		}

		return App::make($class);
	}
	
}

?>