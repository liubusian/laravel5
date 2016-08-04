<?php
namespace Elmer\Format;

use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
/**
* 資料轉換器
* 根據傳入的資料與參數轉換結果
* 優先判斷是否有自訂的format操作
* 否則會檢查format屬性的設定
* format屬性非必要性
* 採用 checkOperate()和getFormat()來做客制處理
*/
class BaseFormat
{
	/**
	 * 儲存輸入的資料
	 * @var mixed
	 */
	protected $data;

	/**
	 * 儲存輸入的參數
	 * @var string
	 */
	protected $operate;

	/**
	 * 設定基本轉型 - 僅在沒有自訂方法時使用
	 * 若通過驗證則直接回傳設定的值
	 * 可以覆寫beforeCheck()
	 * 或checkFormat()
	 * [
	 * 		'operate' => value
	 * ]
	 * @var array
	 */
	protected $format =[];

	/**
	 * 當beforeCheck或checkFormat錯誤時是否跳出例外
	 * 若為False則回傳原本的值
	 * @var boolean
	 */
	protected $throw = FALSE;

	/**
	 * 儲存結果
	 * @var mixed
	 */
	protected $results;

	public function __construct($data, $parameter=''){
		$this->data = $data;
		$this->operate = $parameter;

		
		$this->render();
	}

	protected function render(){

		if( ! $this->beforeCheck()){

			if($this->throw){
				throw new Exception("資料格式不正確::".get_class($this), -2);
			}

			$this->results = $this->data;
			return;
		}

		$action = 'do'.ucfirst($this->operate);
		
		if(method_exists($this, $action)){
			$this->results = $this->$action();
		}elseif($this->checkOperate()){
			$this->results = $this->getFormat();
		}else{
			$this->results = $this->data;
		}
	}

	/**
	 * 設定檢查點
	 * @return boolen 
	 */
	protected function beforeCheck(){
		return !empty($this->data);
	}

	/**
	 * 檢查傳入的operate
	 * @return boolen 
	 */
	protected function checkOperate(){
		if($this->throw){
			throw new Exception("參數設定不正確::".get_class($this), -2);
		}
		return (array_key_exists($this->operate, $this->format));
	}

	/**
	 * 根據operate設定結果
	 * @return mixed
	 */
	protected function getFormat(){
		return $this->format[$this->operate];
	}

	protected function isAssoc($array){

		$array = $this->getArrayableItems($array);

    	$keys = array_keys($array);

        return (array_keys($keys) !== $keys);
    }

    protected function isLists($array){
    	return (false ==  $this->isAssoc($array));
    }

    /**
     * Results array of items.
     *
     * @param  mixed  $items
     * @return array
     */
    protected function getArrayableItems($items)
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof Arrayable) {
            return $items->toArray();
        } elseif ($items instanceof Jsonable) {
            return json_decode($items->toJson(), true);
        } elseif ($items instanceof JsonSerializable) {
            return $items->jsonSerialize();
        }

        return (array) $items;
    }

	public function get(){
		return $this->results;
	}

}