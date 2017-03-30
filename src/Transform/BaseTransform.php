<?php
namespace Elmer\Transform;

use App;
use Format;
use Exception;
use JsonSerializable;
use Elmer\Swap\BaseSwap;
use Elmer\Format\BaseFormat;
use Illuminate\Support\Collection;
use Elmer\Traits\CheckInstanceTrait;
use Elmer\Traits\FliterInputDataTrait;
use Elmer\Traits\AttributeValidateTrait;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
* 欄位與值轉換設定器
*/
class BaseTransform implements Jsonable,Arrayable
{
	use AttributeValidateTrait, FliterInputDataTrait, CheckInstanceTrait;

	/**
	 * 定義欄位轉換規則
	 * [
	 * 		'settings'=>[
	 * 			'field' => 'swapClass',
	 * 		 	'parent.field' => 'swapClass'
	 * 		 ],
	 * 		 //當外層與內層有相同欄位名稱
	 * 		 //而只想要轉換外層欄位時可以設定例外
	 * 		 'excepts'=>[ 
	 * 		 	'parent.field'
	 * 		 ]
	 * ]
	 * @var array
	 */
	protected $swapConfig = [];

	/**
	 * 定義格式轉換規則
	 * [
	 * 		//定義格式 formatClass@paramater|formatName:operate
	 * 		//左側格式適用於未註冊的format
	 * 		//右側格式適用於已註冊Format Facade的format
	 * 		//ex:
	 * 		'settings'=>[
	 * 			'paided_at' => "date:'Y-m-d H:i:s'|null:string", //預設有date null json 可用
	 * 		 	'Items.ItemID' => 'formatClass@paramater' //使用未註冊的format
	 * 		 ],
	 * 		 //當外層與內層有相同欄位名稱
	 * 		 //而只想要轉換外層欄位時可以設定例外
	 * 		 'excepts'=>[ 
	 * 		 	'parent.field'
	 * 		 ]
	 * ]
	 * @var array
	 */
	protected $formatConfig = [];

	protected $camels = ["ucfirst","lcfirst","strtolower","strtoupper"];

	protected $useCamel = "lcfirst";

	protected $usePrefix = false;

	protected $prefixSymbol = '';

	protected $items = [];

	/**
	 * 是否為關聯陣列
	 * @param  array  $array match array
	 * @return boolean        
	 */
	protected function isAssoc($array){

    	$keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * 轉換欄位名稱
     * @param  string $field  轉換前欄位
     * @param  string $parent 父欄位名稱
     * @return string         轉換後欄位
     */
    protected function applySwap($field, $parent=''){

    	/**
    	 * 取得設定資訊
    	 */
    	$rules = isset($this->swapConfig['settings'])? $this->swapConfig['settings']:[];
    	$excepts = isset($this->swapConfig['excepts'])? $this->swapConfig['excepts']:[];

    	if(empty($rules)){
    		return $this->doCamel($field);
    	}

		/**
		 * 取得欄位的設定名稱
		 */
		$setting_field = empty($parent)? $field : $parent.'.'.$field;

		/**
		 * 若是設定的欄位在例外中
		 */
		if(in_array($setting_field, $excepts)){
			return $this->doCamel($field);
		}
		
		/**
		 * 過濾沒有被設定的欄位
		 */
		if( ! array_key_exists($setting_field, $rules)){
			return $this->doCamel($field);			
		}

		#轉換後的欄位名稱|使用的轉換器
		$swap = $rules[$setting_field];

		#非轉換器直接轉換
		if( ! class_exists($swap)){
			return $this->doCamel($swap);
		}

		#使用轉換器轉換欄位
		if( $this->checkInstance($swap, BaseSwap::class)){
			return $this->doCamel( App::make($swap,[$field])->first());	
		}

		#沒有變更名稱
		return $this->doCamel($field);
		
	}

	/**
	 * 欄位值轉換器
	 * @param  string $field  欄位名稱
	 * @param  array $data   資料
	 * @param  string $parent 父欄位
	 * @return object         
	 */
	protected function applyFormat($field, $data, $parent=''){

		/**
		 * 取得設定資訊
		 */
		$rules = isset($this->formatConfig['settings'])? $this->formatConfig['settings']:[];
    	$excepts = isset($this->formatConfig['excepts'])? $this->formatConfig['excepts']:[];

    	if(empty($rules)){
    		return $data;
    	}

		/**
		 * 取得欄位的設定名稱
		 */
		$setting_field = empty($parent)? $field : $parent.'.'.$field;

		/**
		 * 若是設定的欄位在例外中
		 */
		if(in_array($setting_field, $excepts)){
			return $data;
		}
		
		/**
		 * 過濾沒有被設定的欄位
		 */
		if( ! array_key_exists($setting_field, $rules)){
			return $data;	
		}

		return Format::make($data, $rules[$setting_field]);
									
	}

	/**
	 * 欄位字串處理
	 * @param  string $field 欄位名稱
	 * @return string        處理後欄位名稱
	 */
	protected function doCamel($field){

		/**
		 * 處理方式
		 */
		$operate = $this->useCamel;

		#不處理
		if($operate == 'none'){
			return $field;
		}

		$prefix = '';

		#是否包含父欄位名稱
    	if(preg_match('/(\w+)\.(\w+)/', $field, $match) == 1){
    		$prefix = $match[1];
    		$field = $match[2];
    	}

    	
    	$prefix = $prefixSymbol = '';
    	
    	#是否使用前輟字元
    	if($this->usePrefix){

    		$prefix = empty($prefix)? '': $operate($prefix);

    		#連接符號
    		$prefixSymbol = $this->prefixSymbol;
    	}
    	
    	$field = $operate($field);

    	$finally = compact('prefix','prefixSymbol','field');

    	return trim(implode('', array_values($finally)));
	}

	/**
	 * 遍歷陣列資料
	 * @param  array $datas  資料源
	 * @param  string $parent 父欄位名稱
	 * @return array         
	 */
	protected function transformList($datas, $parent=""){

		foreach($datas as $list => $data) {
			if(is_array($data)){
				$datas[$list] = $this->transform($data, $parent);
			}else{
				$datas[$list] = $data;
			}			
		}
		return $datas;
	}

	/**
	 * 轉換資料
	 * @param  array $datas  資料源
	 * @param  string $parent 父欄位名稱
	 * @return object         
	 */
	protected function transform($datas, $parent=""){

		#不是關聯陣列
		if( ! $this->isAssoc($datas)){
			return $this->transformList($datas, $parent);
		}

		#處理關連陣列
		foreach($datas as $field => $data) {			

			#取得新欄位名稱
			$newField = $this->applySwap($field, $parent);

			$setting_field = empty($parent)? $field : $parent.'.'.$field;

			#是否轉換欄位值
			if(is_array($data) && !array_key_exists($setting_field, $this->formatConfig['settings'])){

				#欄位值再深一層處理
				$datas[$newField] = $this->transform($data, $field);

			}else{

				#處理值轉換
				$datas[$newField] = $this->applyFormat($field, $data, $parent);
				
			}

			#有轉換欄位，舊的欄位刪掉
			if($newField !== $field){
				unset($datas[$field]);
			}
			
		}
		return $datas;
	}

	public function make($data){


		$this->setInputData($data);

		$this->inputDatas = $this->fliterAccepts($this->inputDatas);

		if( ! empty($this->rules)){
			$this->validate($this->inputDatas);
		}

		$this->items = $this->transform($this->inputDatas);
		
		return $this;
	}

	public function all(){
		return $this->toArray();
	}

	public function toArray(){
		return $this->items;
	}

	public function toJson($options=0){
		return json_encode($this->toArray());
	}
}