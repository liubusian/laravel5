<?php
namespace Elmer\Traits;

use Exception;
use Illuminate\Support\Collection;
use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

trait FliterInputDataTrait {
	/**
	 * 接受的欄位
	 * 用來過濾掉多餘的資訊
	 * @var array
	 */
	protected $accepts = [];

	/**
	 * 輸入的資料
	 * 驗證完後的資料儲存在這裡
	 * @var Illuminate\Support\Collection
	 */
	protected $inputDatas = [];

	public function setInputData($attribute){
		
		$attribute = $this->getArrayableItems($attribute);

		$this->inputDatas = $attribute;
		
	}

	public function pushInputData($data){
		$data = $this->getArrayableItems($data);
		$this->inputDatas = $this->fliterAccepts(array_merge($this->inputDatas, $data));
		return $this;
	}

	protected function fliterAccepts($data){

		if(empty($this->accepts)){
			return $data;
		}

		if( ! is_array($this->accepts)){
			throw new Exception("The property 'accepts' must be array, ".gettype($this->accepts)." setting", 1);
		}

		$data = new Collection($data);

		$data = $data->only($this->accepts)->all();

		return $data;
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
}