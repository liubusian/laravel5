<?php
namespace Elmer\Traits;

use Format;

trait FormatResultTrait {

	protected $format;

	public function format(array $data, array $settings, $reset=false){		
		$this->format = Format::make($data, $formatConfig, $reset)->asArray();
		return $this->format;
	}
}