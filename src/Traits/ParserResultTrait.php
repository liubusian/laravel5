<?php
namespace Elmer\Traits;

use Elmer\Support\ParserResult;
/**
* 編譯結果
*/
trait ParserResultTrait
{
	
	public function parserResult(array $data, array $tranforms = [], array $formates= []){
		$result = new ParserResult($data,$tranforms);

		if(!empty($formates)){
			$result->applyFormate($formates);
		}

		return $result->tranform();
	}

}