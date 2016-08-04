<?php
namespace Elmer\Contracts\Support;
interface CollectionAble{
	/**
     * Get the instance as an collection.
     *
     * @return array
     */
	public function toCollection();
}