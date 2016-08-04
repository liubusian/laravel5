<?php 
namespace Elmer\Response;

class APIResponseService {

	public function make($StatusCode, $Message='', $Results=[], $merge=[]){
        $Response =  new APIResponse($StatusCode, $Message, $Results, $merge);

        #response()->json($Response->toArray());
        return $Response;
    }

    public function useException(\Exception $e){
    	return $this->make($e->getCode(),$e->getMessage());
    }

    

}