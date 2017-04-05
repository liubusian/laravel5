<?php
namespace Elmer\Entity;

use App;
use Exception;
use Illuminate\Support\Collection;
use Elmer\Traits\FliterInputDataTrait;
use Elmer\Traits\AttributeValidateTrait;
use Elmer\Traits\CheckInstanceTrait;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
* 定義Pojo類
* @author Elmer Liu <elmerliu98133041@gmail.com>
* @link https://github.com/liubusian/laravel5 
* @version 0.1.0 beta
* @package liubusian\laravel5-Plugin
* @copyright © 2016 Liu-Bu-Sian.		
*/
class BaseEntity implements Arrayable,Jsonable
{
	use AttributeValidateTrait, FliterInputDataTrait, CheckInstanceTrait;	

	/**
	 * 此實體的資料
	 * 亦可在此處設定初始值
	 * 取得的資料都以此陣列資料為主
	 * @var Illuminate\Support\Collection
	 */
	protected $attributes = [];

	protected $oriAttribute = [];

	protected $setPrefix = "set";

	protected $getPrefix = "get";

	/**
	 * 建構子
	 * 傳入陣列資料
	 * @param array $inputDatas 資料源
	 */
	public function __construct($inputDatas = []){
		
		$this->attributes = new Collection($this->attributes);

		#預設值與輸入值合併
		$inputDatas = new Collection($inputDatas);
		$inputDatas = $inputDatas-> merge($this->attributes);

		$this->setAttributes($inputDatas);


		$this->boot();
	}

	/**
	 * 呼叫使用者自定義的Set方法
	 * @param  array $datas 資料源
	 * @return void        
	 */
	protected function _callCustomSet($datas){

		$datas = new Collection($datas);

		$self = $this;

		$datas->map(function($value, $attr) use($self){

			$setAction = $this->setPrefix.ucfirst($attr);

			if(method_exists($self, $setAction)){

				call_user_func_array([$self,$setAction], [$value]);
			}
		});

	}

	/**
	 * 設定屬性
	 * 此方法會將原本的屬性重設為空陣列後賦值
	 * @param array $data 資料源
	 */
	public function setAttributes($data){

		#確定為陣列資料
		$data = $this->getArrayableItems($data);

		#過濾資料
		$data = $this->fliterAccepts($data);

		#驗證資料
		if( ! empty($this->rules)){
			$this->validate($data);
		}		

		#備份原始資料
		$this->oriAttribute = new Collection($this->attributes);

		$this->attributes = new Collection([]);

		#合併新資料
		$this->attributes = $this->attributes->merge($data);

		#呼叫自訂set
		$this->_callCustomSet($this->attributes);

	}

	/**
	 * 新增資料
	 * @param  array $data 資料源
	 * @return 
	 */
	public function pushAttributes($data){		

		#確定為陣列資料
		$data = new Collection($data);

		$data = $data->merge($this->attributes);

		$this->setAttributes($data);

		return $this;
	}

	

	/**
	 * 輸出陣列
	 * @return array 實體資料
	 */
	public function toArray(){

		$this->attributes->toArray();
		
	}

	/**
	 * 輸出JSON
	 * @param  integer $options 輸出格式
	 * @return json           
	 */
	public function toJson($options=0){
		return json_encode($this->toArray(), $options);
	}

	/**
	 * 同toArray
	 * @see  toArray 
	 * @return array 
	 */
	public function all(){
		return $this->toArray();
	}

	public function __get($key){

		$method = $this->getPrefix.ucfirst($key);

		if(method_exists($this, $method)){

			return $this->$method();
		}

		if($this->attributes->has($key)){
			return $this->attributes->get($key,null);
		}
	}

	public function __set($k, $v){

		$method = $this->setPrefix.ucfirst($k);

		if(method_exists($this, $method)){

			return $this->$method($v);
		}

		$d = [$k=>$v];

		$this->pushAttributes($d);
	}

    protected function boot(){

    }
}