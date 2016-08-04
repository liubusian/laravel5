<?php
namespace Elmer\Entity;


/**
* 編譯結果
*/
class BaseResultEntity extends BaseEntity
{

	public function __construct(array $datas=[]){

		parent::__construct($datas);
		$this->skipResult();
	}


}