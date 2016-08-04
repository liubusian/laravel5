<?php
namespace Elmer\Format\Foundation;

use Elmer\Format\BaseFormat;
/**
* 欄位等於日期時格式化
*/
class DateFormat extends BaseFormat
{
	protected $default = 'Y-m-d H:i:s';
	
	protected function beforeCheck(){
		return (strtotime($this->data) != false);
	}

	protected function getDateFormat(){
		return empty($this->operate)? $this->default:$this->operate;
	}

	protected function checkFormat(){		
		return FALSE !== $this->getFormat();
	}

	protected function getFormat(){
		return date_format($this->input,$this-getDateFormat());
	}

}