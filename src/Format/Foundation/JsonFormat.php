<?php
namespace Elmer\Format\Foundation;

use Elmer\Format\BaseFormat;
/**
* 欄位轉成JSON
*/
class JsonFormat extends BaseFormat
{
	protected function checkEncode(){
		#避免將JSON格式再次格式
		if(is_string($this->data)){
			return (null !== json_decode($this->data));
		}
		return true;
	}

	protected function checkDecode(){
		return (null !== json_decode($this->data));
	}

	protected function checkOperate(){
		if($this->operate == 'encode'){
			return $this->checkEncode();
		}elseif($this->operate == 'decode'){
			return $this->checkDecode();
		}else{
			return FALSE;
		}
	}

	protected function getFormat(){
		
		if($this->operate == 'encode'){
			
			return json_encode($this->data);

		}elseif($this->operate == 'decode'){

			return json_decode($this->data);

		}else{

			return $this->data;
		}
	}

}