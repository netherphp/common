<?php

namespace Nether\Common\Meta;

use Nether\Common\Prototype\PropertyInfo;
use Nether\Common\Prototype\PropertyInfoInterface;

use Attribute;
use ReflectionProperty;
use ReflectionAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyFactory
extends PropertyObjectify {

	public mixed
	$Callable;

	public ?string
	$Source;

	public function
	__Construct(mixed $Callable, string $Source=NULL, ...$Args) {

		$this->Callable = $Callable;
		$this->Source = $Source;
		$this->Args = $Args;

		return;
	}

	public function
	OnPropertyInfo(PropertyInfo $Attrib, ReflectionProperty $RefProp, ReflectionAttribute $RefAttrib):
	static {

		$Attrib->Objectify = $this;

		if(!isset($this->Source))
		$this->Source = $Attrib->Name;

		return $this;
	}

}
