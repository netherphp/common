<?php

namespace Nether\Common\Meta;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
#[DateAdded('2023-02-02')]
#[Info('For marking properties as publically listable.')]
class PropertyListable {

	public ?string
	$MethodName;

	public function
	__Construct(string $MethodName=NULL) {

		$this->MethodName = $MethodName;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromClass(string $CName):
	array {

		$Props = ($CName)::GetPropertiesWithAttribute(static::class);

		return $Props;
	}

}
