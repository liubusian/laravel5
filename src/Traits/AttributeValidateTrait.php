<?php
namespace Elmer\Traits;

use Validator;
use Elmer\Exception\EntityValidateException;

trait AttributeValidateTrait{
	/**
	 * 輸入資料的驗證規則
	 * @var array
	 */
	protected $rules = [];

	/**
	 * 驗證失敗自訂訊息
	 * @var array
	 */
	protected $messages = [];

	/**
	 * 驗證結果
	 * @var array
	 */
	protected $validFalied = [];

	protected function validate(array $datas){
		
		$validator = Validator::make($datas, $this->rules, $this->messages);

		if ($validator->fails()) {
			$this->setValidResult($validator);
		}

		return true;
	}

	protected function setValidResult($validator){

		$field = array_keys($validator->failed())[0];

		$rule = array_keys(array_values($validator->failed())[0])[0];

		$value = $validator->getData();
		
		if(isset($value[$field])){
			if(is_array($value[$field])){

				$value = json_encode($value[$field]);
			}else{
				$value = $value[$field];
			}			
		}else{
			$value = "null";
		}

		$message = $validator->errors()->toArray()[$field][0]." : ".$value;

		$this->validFalied = compact("field","rule","message");
		
		throw new EntityValidateException(json_encode($this->validFalied));
	}
}