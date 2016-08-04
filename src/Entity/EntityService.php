<?php 
namespace Elmer\Entity;

use App;
use Elmer\Traits\CheckInstanceTrait;

class EntityService 
{
	use CheckInstanceTrait;

	protected $entity;
	
	public function make($entity, $data=[]){

		if(is_string($entity)){

			if( ! $this->checkInstance($entity, BaseEntity::class)){
					throw new \Exception("the argument 1 must be ".BaseEntity::class.", $inputClass given.");
			}

			$data = empty($data)? $data : [$data];

			return App::make($entity, $data);

		}elseif( $entity instanceof BaseEntity){

			return $entity->pushInputData($data);

		}else{

			throw new \Exception("the argument 1 must be ".BaseEntity::class.", ".gettype($entity)." given.", -2);
		} 
	}
}