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
		$this->responses = new APIResponseEntity([]);

		if($StatusCode instanceof \Exception){
			
			$this->parseExceptionResult($StatusCode);

		}elseif(is_array($StatusCode) && isset($StatusCode["StatusCode"])){

			$this->responses->StatusCode = $StatusCode;

		}else{

			$this->responses = new APIResponseEntity(compact($this->guard));
		}

		$this->setMerge($merge);

	}

	protected function parseExceptionResult($e){

		$this->responses->setStatusCode($e->getCode() >= 0 ? -2 : $e->getCode());

		$this->responses->setMessage($this->getCurryMessage($e->getMessage()));

	}

	protected function getCurryMessage($messages){

		if( ! empty($messages)){

			return $messages;
		}

		$config = config('response.api');

		if(array_key_exists($this->responses->StatusCode, $config)){
			return $config[$this->responses->StatusCode];
		}else{
			$this->responses->StatusCode = -2;
			return $messages;
		}
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

	public function merge($data){

		$merge = collect($this->merge);

		$this->merge = $merge->merge($data)->all();
	}

	public function toArray(){
		$datas = array_merge($this->responses->all(),$this->merge);
		return $datas;		
	}

	public function toJson($options = 0){
		return json_encode($this->toArray());
	}

	public function show(){
		echo $this->toJson();
	}

	public function __get($key){

		return $this->responses->$key;
	}
}