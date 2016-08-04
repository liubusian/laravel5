<?php

namespace Elmer\Encrypts;

/**
 * 
* @author Elmer liu <elmerliu98133041@gmail.com>
* @version 0.1 
* 
*/
class ElmerEncrypt
{
	/**
	 * 要加密的字串
	 * @var string
	 */
	protected $string;
	/**
	 * 加密金鑰
	 * 由1~9組成之[8,16,32...長度]字串
	 * @var string
	 */
	protected $key;
	


	/**
	 * 加密種子
	 * 指定[A-Z a-z]某字元做為加密種子
	 * @var string
	 */
	protected $seed;
	/**
	 * 種子演算階距
	 * 1-9
	 * @var integer
	 */
	protected $degree;


	protected $splitStr =[];
	protected $splitKey = [];
	protected $splitOriStr = [];

	#a-z 97-123
    #A-Z 65-91
    #0~9 48-57
	const ASCII_NUMBER_MIN = 48;
	const ASCII_NUMBER_MAX = 57;
	const ASCII_LOWER_CHAR_MIN = 97;
	const ASCII_LOWER_CHAR_MAX = 123;
	const ASCII_UPPER_CHAR_MIN = 65;
	const ASCII_UPPER_CHAR_MAX = 91;

	const TRANSFORM_RATE = 27;



	protected $same = [];
	
	private $pattern = ['!','@','#','$','%','^','&','*','(',')'];

	function __construct($string, $key='12345678', $option=[])
	{
		$this->string = $string;
		$this->key = $key;
		$this->splitKey = str_split($key);
		$this->splitStr = $this->splitOriStr = str_split($string);
		$this->setOption($option);
	}

	protected function setOption($option){
		#設定
		if(isset($option['degree'])){
			if(0 < $option['degree'] && 9>= $option['degree']){
				$this->degree = $option['degree'];
			}
		}

		if(isset($option['seed'])){
			if(preg_match("/^[A-Za-z]{1}$/",$option['seed'])){
				$this->seed = $option['seed'];
			}
		}
	}

	protected function getSame($arr){
		$same = [];
		foreach ($this->splitKey as $list => $index) {
            if(empty($arr[$index-1])){
                $same[] = $this->splitOriStr[$index-1];
            }else{
                $same[] = $arr[$index-1];
            }
        }
        return $same;
	}
	
	protected function getDiff($arr){
		$diff = [];
		foreach ($arr as $key => $value) {
            $index = $key +1;
            if(!in_array("$index", $this->splitKey)){
                $diff[] = $value;
            }
        }
        return $diff;
	}
	
	protected function restructuring(){
		$splitStr = $this->splitStr;
		$a = count($this->splitKey)-count($splitStr);
		#加密金鑰長度大於目前的加密字串
		#用原始的字串來補
		if( $a >= 0 ){
			$same = $splitStr;
			for($i=$a;$i>0;$i--){
				$index = count($this->splitOriStr) - $i;
				$same[] = $this->splitOriStr[$index-1];
			}
			$this->same = array_merge($this->same,$same);
		}else{
			$this->same = array_merge($this->same,$this->getSame($splitStr));
			$diff = $this->getDiff($splitStr);
		}
        /**
         * 遞迴處理直到沒有不相同的字串
         */
        if(!empty($diff)){       	
        	$this->splitStr = $diff;
        	$this->encoding();
        }
        return $this->same;
	}
	
	/**
	 * 計算亂數的種子
	 * 用餘轉換大小寫
	 * @param  array $splitkey 金鑰陣列
	 */
	protected function setRandSeed(array $splitKey){
		#a-z 97-123
        #A-Z 65-91
        if(!empty($this->seed)){
        	return;
        }
        $max = max($splitKey);
        $min = min($splitKey);
        $a =$max*10+$min;
		if(self::ASCII_UPPER_CHAR_MIN <= $a && self::ASCII_UPPER_CHAR_MAX >= $a){
			#大寫
			$this->seed = $a;
		}else{
			#小寫
			$this->seed = self::ASCII_LOWER_CHAR_MIN + ($max*$min) % 26;
		}
	}

	/**
	 * 設定種子階距
	 * @param array $splitKey 金鑰陣列
	 */
	protected function setDegree(array $splitKey){
		if(!empty($this->degree)){
			return;
		}
		#重複的數量
        $seedCount = array_count_values($splitKey);
        #重複的數量最大值
		$max = max($seedCount);
		#重複最多次的Key
		$this->degree = array_search($max,$seedCount);
	}

	public function encoding(){
		$restructStr = $this->restructuring();
		$splitKey = $this->splitKey;
		$this->setDegree($splitKey);
		$this->setRandSeed($splitKey);
		#開始加密
        $tmpStr = [];
        foreach($restructStr as $list => $value){        	
        	#a-z 97-123
        	#A-Z 65-91
        	#0~9 48-57
        	$asc = ord($value);
        	$index = $list + 1;
        	$rate = $asc % self::TRANSFORM_RATE;
        	#若是數字則轉為符號
        	if($asc >=self::ASCII_NUMBER_MIN 
        		&& $asc <= self::ASCII_NUMBER_MAX){
        		$tmpStr[] = $this->pattern[$value];
        	#小寫字處理
        	}elseif($asc >=self::ASCII_LOWER_CHAR_MIN 
        		&& $asc <= self::ASCII_LOWER_CHAR_MAX){

        		#字元轉換
        		$chrIndex = self::ASCII_LOWER_CHAR_MIN + $rate;

        		#大小寫轉換        	
        		if($this->seed % $index == $this->degree){
        			$chrIndex = $chrIndex-32;
        		}

        		#儲存結果
        		if(26 == $rate){
        			$tmpStr[] = "~";
        		}else{
        			$tmpStr[] = chr($chrIndex);
        		}
        	}elseif($asc >= self::ASCII_UPPER_CHAR_MIN 
        		&& $asc <= self::ASCII_UPPER_CHAR_MAX){

        		#字元轉換
        		$chrIndex = self::ASCII_UPPER_CHAR_MIN + $rate;

        		#大小寫轉換
        		if($this->seed % $index == $this->degree){
        			$chrIndex = $chrIndex+32;
        		}
        		#儲存結果
        		if(26 == $rate){
        			$tmpStr[] = "=";
        		}else{
        			$tmpStr[] = chr($chrIndex);
        		}
        	}else{
        		#特定符號轉數字
        		if(in_array($value, $this->pattern)){
        			$tmpStr[] = array_search($value, $this->pattern);
        		}else{
        		#其餘字符串不變動
        			$tmpStr[] = $value;
        		}
        		
        	}
        }
        return implode('',$tmpStr);
	}


}

