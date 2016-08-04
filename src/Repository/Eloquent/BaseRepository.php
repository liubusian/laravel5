<?php

namespace Elmer\Laravel5\Repository\Eloquent;
use Prettus\Repository\Eloquent\BaseRepository as PrettusRepository;
/**
* 擴展Prettus\Repository\Eloquent\BaseRepository方法
*/
abstract BaseRepository extends PrettusRepository
{
	/**
	 * 設定條件 => BETWEEN $from AND $TO
	 * @param string $field 欄位名稱
	 * @param mixed $form  起始
	 * @param mixed $to    結束
	 */
	public function setBetween($field,$form,$to){
        $this->model = $this->model->whereBetween($field,[$form,$to]);
        return $this;
    }

    /**
     * 設定條件 => WHERE $field $operator $value
     * @param string $field    欄位名稱
     * @param string $operator 操作( = <> > < >= <=)
     * @param mixed $value    欄位值
     * @param string $boolean  TYPE => AND | OR
     */
    public function setWhere($field,$operator="=",$value=null,$boolean="and"){
    	$boolean = strtolower($boolean);
        if($value == null){
            $value = $operator;
            $operator = "=";
        }
        $this->model = $this->model->where($field,$operator,$value,$boolean);
        return $this;
    }

    /**
     * 設定模糊查詢
     * @param string $field    欄位名稱
     * @param mixed $match    比對值
     * @param string $operator Both | Left | Right
     * @param string $boolean  AND | OR
     */
    public function setLike($field,$match,$operator="both",$boolean="and"){
    	$operator = strtolower($operator);
    	$boolean = strtolower($boolean);
        $both = ["%",null,"%"];
        $left = ["%",null];
        $right = [null,"%"];
        $values = [];
        foreach ($$operator as $list => $value) {
            if($value == null){
                $values[$list] = $match;
            }else{
                $values[$list] = $value; 
            }
        }
        $this->model = $this->model->where($field,"like",implode("",$values),$boolean);
        return $this;
    }

    /**
     * 設定排序
     * @param string $field     欄位名稱
     * @param string $direction asc | desc
     */
    public function setSort($field, $direction="asc"){
    	$direction = strtolower($direction);
        $this->model = $this->model->orderBy($field,$direction);
        return $this;
    }

    /**
	 * 檢查資料列存在
	 * @param  mixed $field    欄位|條件陣列
	 * @param  mixed $value    欄位值|null
	 * @param  string $operator 操作( =| > | < | >= | <= )
	 * @param  string $boolean  and | or
	 * @return boolen        
	 */
	public function checkRowExist($field, $value=null, $operator="=", $boolean = 'and'){
		$boolean = strtolower($boolean);
		try {
			$where= [];
			if(is_array($field)){
				$data = $this->findWhere($field)->all();
			}else{
				
				$where[] = [$field,$operator,$value];
				$data = $this->findWhere($where)->all();
			}
			if(!empty($data)){
				return true;
			}
		} catch (ModelNotFoundException $e) {
			return false;
		}
		return false;
	}

	/**
	 * 檢查主鍵存在
	 * @param  int $value 主鍵值
	 * @return boolen        
	 */
	public function checkPrimyKey($value){
		try {			
			$data = $this->find($value);
			if(empty($data->id)){
				return false;
			}
		} catch (ModelNotFoundException $e) {
			return false;
		}
		return true;
	}
	
}