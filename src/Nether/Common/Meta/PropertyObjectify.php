<?php

namespace Nether\Common\Meta;

use Nether\Common\Prototype\PropertyInfo;
use Nether\Common\Prototype\PropertyInfoInterface;

use Attribute;
use ReflectionProperty;
use ReflectionAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyObjectify
implements PropertyInfoInterface {
/*//
@date 2021-08-09
@related Nether\Common\Prototype::__Construct
when attached to a class property, when the parent object is constructed this
property will get a fresh new instance of whatever type this property is
defined as. arguments given to the attribute will be passed along as arguments
to the object being constructed for that property.
//*/

	public array
	$Args;

	public function
	__Construct(...$Args) {

		$this->Args = $Args;
		return;
	}

	public function
	OnPropertyInfo(PropertyInfo $Attrib, ReflectionProperty $RefProp, ReflectionAttribute $RefAttrib):
	static {

		$Attrib->Objectify = $this;
		return $this;
	}

}
