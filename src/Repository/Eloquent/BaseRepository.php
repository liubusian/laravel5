<?php

namespace Elmer\Laravel5\Repository\Eloquent;
use Prettus\Repository\Eloquent\BaseRepository as PrettusRepository;
/**
* 擴展Prettus\Repository\Eloquent\BaseRepository方法
*/
abstract BaseRepository extends PrettusRepository
{
	public function setBetween($field,$form,$to){
        $this->model = $this->model->whereBetween($field,[$form,$to]);
        return $this;
    }

    public function setWhere($field,$operator="=",$value=null,$boolean="and"){
        if($value == null){
            $value = $operator;
            $operator = "=";
        }
        $this->model = $this->model->where($field,$operator,$value,$boolean);
        return $this;
    }

    public function setLike($field,$match,$operator="both",$boolean="and"){
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

    public function setSort($field, $direction="asc"){
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

    public function getPage($pageNum,$limit=null,$columns=['*']){    	
    	$this->applyCriteria();
        $this->applyScope();
        $limit = request()->get('perPage', is_null($limit) ? config('repository.pagination.limit', 15) : $limit);
        $result = $this->model->paginate($limit,$columns,'page',$pageNum);
        $this->resetModel();
        $this->resetScope();
        return $this->parserResult($results);
    }
}