<?php
namespace Elmer\Entity;

use Illuminate\Contracts\Support\Arrayable;
/**
* API回應實體
*/
class APIResponseEntity extends BaseEntity
{
	protected $accepts = ["StatusCode", "Message", "Results"];

	protected $attribute = [
		"StatusCode" => -2,
		"Message" => "",
		"Results" => [],
	];

	protected $rules = [
		"StatusCode"=>"required|numeric",
	];

	protected $config = [];

	public function __construct(array $datas=[]){

		$this->config = config("response.api");
		parent::__construct($datas);

	}

	public function setStatusCode($value){
		if(!array_key_exists($value,$this->config)){
			$this->attribute["StatusCode"] = -2;
		}else{
			$this->attribute["StatusCode"] = $value;
		}
	}

	public function setMessage($value){
		
		if(empty($value)){
			$this->attribute["Message"] = $this->config[$this->attribute["StatusCode"]];
		}else{
			$this->attribute["Message"] = $value;
		}
	}

	public function setResults($value){

		$this->attribute["Results"] = $this->getArrayableItems($value);
	}
}