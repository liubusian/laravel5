<?php
namespace Elmer\Exception;
use Exception;
/**
* 實體寫入檢查錯誤
*/
class EntityValidateException extends Exception
{
	protected $validMsg;

	function __construct($message = "", $code=-5, Exception $previous = null){
		
		$validMsg = json_decode($message);

		if(null !== $validMsg){

			$message = $validMsg->message;
			$this->validMsg = $validMsg;
		}

		parent::__construct($message,$code,$previous);
	}


	public function getValiedError($key=""){

		if(empty($key)){
			return $this->validMsg;
		}
		
		return $this->validMsg->$key;
	}

}