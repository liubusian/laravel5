<?php 
namespace Elmer\Format;

use Exception;
use Elmer\Format\BaseFormat;
use Illuminate\Support\Collection;
use Elmer\Traits\CheckInstanceTrait;

class FormatService 
{
    use CheckInstanceTrait;

    protected $formats = [];

    protected $data;

    protected $rules;

    public function __construct(){

        $this->formats = new Collection($this->formats);

        $this->pushBaseFormat();

    }

    protected function pushBaseFormat(){
        $this->pushFormat("Elmer\Format\Foundation\NullFormat",'null');
        $this->pushFormat("Elmer\Format\Foundation\DateFormat",'date');
        $this->pushFormat("Elmer\Format\Foundation\JsonFormat",'json');
        $this->pushFormat("Elmer\Format\Foundation\StringFormat",'string');
    }

    public function pushFormat($format, $formatName=''){

        if( ! is_string($format)){
            throw new Exception("The argument 1 must be string, ".gettype($format).' given');
        }

        if( ! $this->checkInstance($format, BaseFormat::class)){
            throw new Exception("The argument 1 must be instance of  'Elmer\Format\BaseFormat'");
        }

        if(empty($formatName)){
          $formatName = basename($format);  
        }

        $this->formats->put($formatName, $format);
        
    }

	public function make($data, $rules){

        $this->data = $data;

        if( ! is_string($rules)){
            throw new Exception('The argument 2 $rules must be string');
        }

        $this->rules = $rules;

        return $this->render();
    }

    protected function parseRules(){
        $rules = $this->rules;
        $rules = explode('|', $rules);

        $ruleArr = [];
        foreach ($rules as $rule) {
            if(preg_match('/(\w+):(.+)/', $rule, $match) === 1){
                $formatName = $match[1];
                $operate = $match[2];
                $isClass = FALSE;
            }elseif(preg_match('/(.+)@(.+)/', $rule, $match) === 1){
                $formatName = $match[1];
                $operate = $match[2];
                $isClass = TRUE;
            }else{
                throw new Exception("無法辨識的規則::'$ruel'", -2);
            }
            
            $ruleArr[] = [$formatName, $operate, $isClass];
        }

        $this->rules = $ruleArr;
    }

    protected function render(){

        $this->parseRules();

        $data = $this->data;

        foreach ($this->rules as $rule) {

            list($formatName, $operate, $isClass) = $rule;

            if($isClass){

                $formatClass = $formatName;
                
            }else{

                if( ! $this->formats->has(strtolower($formatName))){
                    throw new Exception("未註冊的方法名稱 '$formatName'",-2);
                }

                $formatClass = $this->formats[$formatName];
            }

            $format = new $formatClass($data, $operate);

            $data = $format->get();
            
        }

        return $data;        
    }



}