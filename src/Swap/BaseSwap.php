<?php
namespace Elmer\Swap;
/**
* 欄位轉換器
*/
class BaseSwap
{
	protected $rules = [];

	protected $results = [];


	public function __construct($fields){

		if( ! is_array($fields)){

			$fields = [$fields];
		}

		$tmp = [];

		foreach ($fields as $list => $field) {
			if(array_key_exists($field, $this->rules)){
				$tmp[] = $this->rules[$field];
			}else{
				$tmp[] = $field;
			}
		}

		$this->results = $tmp;
	}

	public function all(){
		return $this->results;
	}

	public function first(){
		return $this->results[0];
	}

}