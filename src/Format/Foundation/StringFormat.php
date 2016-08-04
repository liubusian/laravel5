<?php
namespace Elmer\Format\Foundation;

use Elmer\Format\BaseFormat;
/**
* 欄位等於空值時格式化
*/
class StringFormat extends BaseFormat
{
	protected $format = [
		"pad_left",
		"pad_right",
	];

	protected $param;
	protected $length;
	protected $padString;

	protected function beforeCheck(){
		return !(is_array($this->data) || is_object($this->data));
	}

	protected function checkOperate(){

		$operate = $this->operate;

		$param = explode(',', $operate);
		
		if(in_array($operate[0], $this->format)){
			list($operate, $padString, $length) = $param;
			$this->operate = $operate;
			$this->padString = $padString;
			$this->length = $length;
			return TRUE;
		}else{

			return FALSE;
		}
		
	}

	protected function padString($action){
		$action = 'STR_'.strtoupper($action);
		return str_pad($this->data, $this->length, $this->padString, $action);
	}

	protected function getFormat(){
		return $this->padString($this->operate);
	}



}