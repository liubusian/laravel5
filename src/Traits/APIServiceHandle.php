<?php
namespace Elmer\Laravel5\Traits;

trait APIServiceHandle{

	protected $statusCode = 1;
	protected $message = "";
	protected $results = [];
	protected $merge = [];

	protected function handleResult(){
		return [
			"StatusCode" => $this->statusCode,
			"Message" => $this->message,
			"Results" => $this->results,
			"Merge" => $this->merge,
		];
	}
}