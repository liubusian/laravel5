<?php
namespace Elmer\Entity;

use App;
use Exception;
use Transform;
use Elmer\Transform\BaseTransform;
use Illuminate\Support\Collection;
use Elmer\Traits\FliterInputDataTrait;
use Elmer\Traits\AttributeValidateTrait;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
/**
* 定義基本實體功能
* 目的:
* 		因應來源欄位名稱相異但資料內容相同之請求，使用相同的邏輯
* 		則可以定義不同的請求實體，來將資料格式轉換成邏輯服務需求的資料
* 		達到共用相同邏輯之目地
* 		反之，若有不同邏輯使用相同來源資料
* 		則可以將實體綁定不同的Transform來達到共用相同請求來源
* 		亦可以減少因應不同來源使用不同路由導致程式維護困難之情況
* 概念:
* 		1. 設定接收的欄位與規則驗證 - Illuminate\Validation\Validator
*   	2. 將接收的資料轉換成需要的格式或欄位 - Elmer\Transform\Transformer
*    	3. 可以額外定義其他自訂參數
* 	   		設定:
* 		    	public function _get{參數名稱}(paramter) - 參數名稱字首大寫
* 		     	若無參數輸入 會使用__get去取得值後回傳
* 		      	若有參數輸入 會使用__call去取得值後回傳
* 流程:
* 		1. 建構實體 - 傳入請求資料
* 		2. 過濾欄位 - accepts 設定
* 		3. 驗證輸入資料 - validator 設定規則, message 設定自訂訊息
* 		4. 儲存輸入資料 - inputDatas
* 		5. 取回資料 - get(欄位陣列或欄位名稱) | all() | toArray | toJson | toCollect
* 		6. 5.執行Transform (若無設定skipTransform) 回傳結果
* 		
* @author Elmer Liu <elmerliu98133041@gmail.com>
* @link https://github.com/liubusian/laravel5 
* @version 0.1.0 beta
* @package liubusian\laravel5-Plugin
* @copyright © 2016 Liu-Bu-Sian.		
*/
class BaseEntity implements Arrayable,Jsonable
{
	use AttributeValidateTrait, FliterInputDataTrait;	

	/**
	 * 此實體的資料
	 * 亦可在此處設定初始值
	 * 取得的資料都以此陣列資料為主
	 * @var Illuminate\Support\Collection
	 */
	protected $attribute = [];

	/**
	 * 轉換結果 - Elmer\Transform\Foundation\BaseTransform
	 * 綁定的Transform
	 * 可以綁定多個
	 * 根據此實體的資料格式與內容轉換成需要的欄位或格式
	 * 即Swap & Format的集合
	 * @var Illuminate\Support\Collection
	 */
	protected $transforms = [];

	protected $results = [];

	/**
	 * 用來記錄是否有使用Transform
	 * @var boolean
	 */
	protected $hasTransForm = false;
	protected $skipTransform = false;
	protected $transformBeforSet = TRUE;

	public function __construct($inputDatas){
		
		$this->setInputData($inputDatas);
		
		$this->transforms = new Collection($this->transforms);

		$this->boot();
	}	

	protected function setAttribute(){

		/**
		 * 若無設定transforms或有設定skipTransform則不執行Transform
		 */
		if( false === $this->skipTransform && $this->transformBeforSet){

			$this->applyTransform();

			$this->attribute = $this->doSet($this->results);
		}else{
			$this->attribute = $this->doSet($this->inputDatas);
		}
		
	}

	protected function doSet($datas){

		$datas = $this->fliterAccepts($datas);

		if(empty($datas)){
			return new Collection([]);
		}
		
		if( ! empty($this->rules)){
			$this->validate($datas);
		}

		$datas = new Collection($datas);

		$self = $this;

		$attribute = $datas->map(function($value, $attr) use($self){

			$setAction = "_set".ucfirst($attr);

			if(method_exists($self, $setAction)){

				return call_user_func_array([$self,$setAction], [$value]);
	
			}else{

				return $value;
			}
			
		});

		return $attribute;
		
	}

	public function applyTransform(){

		$transforms = $this->transforms->all();

		if(empty($transforms)){
			return [];
		}

		$datas = empty($this->inputDatas)? []: $this->inputDatas;

		if(empty($datas)){
			return [];
		}

		$tmp = [];
		foreach ($transforms as $transform) {			

			$tmp = Transform::make($transform, $datas);
			
		}
		
		$this->hasTransForm = TRUE;
		
		$this->results = new Collection($tmp);
		
		return $this;
	}

	public function pushTransform($transform){

		if( ! $this->checkInstance($transform, BaseTransform::class)){
			throw new Exception("pushTransform() parameter 1 must be BaseTransform");
		}

		$this->transforms->push($transform);

		return $this;
	}

	public function skipTransform($boolen = TRUE){
		$this->skipTransform = $boolen;
		return $this;
	}

	public function transformBeforSet($boolen = FALSE){
		$this->transformBeforSet = $boolen;
		return $this;
	}

	public function toArray(){

		if($this->hasTransForm){	
			return $this->attribute->toArray();

		}else{
			if($this->transformBeforSet){
				$this->setAttribute();
				return $this->attribute->toArray();
			}else{
				$this->applyTransform();
				return $this->results->toArray();
			}
		}
		
	}

	public function toJson($options=0){
		return json_encode($this->toArray());
	}

	public function toCollection(){
		return new Collection($this->toArray());
	}

	public function all(){
		return $this->toArray();
	}

	public function get($key){

		$results = $this->toCollection();


		return $results->get($key);
	}

	 

	public function __get($key){

		$method = "_get".ucfirst($key);

		if(method_exists($this, $method)){

			return $this->$method();
		}

		return $this->get($key);
	}

	protected function checkInstance($string, $class){
    	$relflection = new \ReflectionClass($string);

		$inputClass = $relflection->getParentClass()->name;

        return ($class === $inputClass);
    }

    

    protected function boot(){

    }
}