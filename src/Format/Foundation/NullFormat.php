<?php
namespace Elmer\Format\Foundation;

use Elmer\Format\BaseFormat;
/**
* 欄位等於空值時格式化
*/
class NullFormat extends BaseFormat
{
	protected $format = [
		"string" => "",
		"array" => [],
		"int" => 0
	];

	protected function beforeCheck(){
		return empty($this->data);
	}

	protected function checkOperate(){
		$this->operate = strtolower($this->operate);
		return array_key_exists($this->operate, $this->format);
	}



}