<?php
namespace Elmer\Format\Foundation;

use Elmer\Format\BaseFormat;
/**
* 欄位等於空值時格式化
*/
class ArrayFormat extends BaseFormat
{
	protected $format = [
		"implode" => ",",
	];

	protected $param;

	protected function beforeCheck(){
		return is_array($this->data);
	}

	protected function checkOperate(){

		$operate = $this->operate;
		$param = $this->param;

		if(preg_match('/(\w+),(.+)/', $this->operate, $match) === 1){
			$operate = $match[1];
			$param = $match[2];
		}

		if(array_key_exists($operate, $this->format)){

			if($this->format[$operate] !== 'none'){
				$param = empty($param)? $this->format[$operate] : $param;
			}

			$this->operate = $operate;
			$this->param = $param;
			return TRUE;

		}else{

			return FALSE;
		}
		
	}

	protected function getFormat(){
		return implode($this->param, $this->data);
	}



}