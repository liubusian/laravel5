<?php
namespace Elmer\Response;

use Elmer\Entity\APIResponseEntity;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
/**
* API回應實體
*/
class APIResponse implements Arrayable,Jsonable
{	
	private $guard = ["StatusCode","Message","Results"];
	protected $responses;
	protected $merge =[];

	public function __construct($StatusCode, $Message='', $Results=[], $merge=[])
	{
		if(is_array($StatusCode) && isset($StatusCode["StatusCode"])){

			$responses = new APIResponseEntity($StatusCode);
		}else{
			$responses = new APIResponseEntity(compact($this->guard));
		}
		$this->responses = $responses->toArray();
		$this->setMerge($merge);

	}

	protected function setMerge($merge){
		if(empty($merge)){
			return;
		}

		$merge = collect($merge);

		if($merge->has($this->guard)){
			throw new \Exception("The attribute has guarded key.");
		}

		$this->merge = $merge->all();

	}

	public function toArray(){
		$datas = array_merge($this->responses,$this->merge);
		return $datas;		
	}

	public function toJson($options = 0){
		return json_encode($this->toArray());
	}

	public function show(){
		echo $this->toJson();
	}
}