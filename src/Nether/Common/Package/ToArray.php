<?php

namespace Nether\Common\Package;

use Nether\Common\Prototype\PropertyInfo;
use Nether\Common\Meta\PropertyListable;

trait ToArray {

	public function
	ToArray():
	array {

		if(method_exists($this, 'GetPropertiesWithAttribute'))
		return array_map(
			(fn(PropertyInfo $P)=> $this->{$P->Name}),
			static::GetPropertiesWithAttribute(PropertyListable::class)
		);

		////////

		return (array)$this;
	}


};
