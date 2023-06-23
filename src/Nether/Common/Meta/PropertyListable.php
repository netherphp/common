<?php

namespace Nether\Common\Meta;

use Attribute;
use Nether\Common\Prototype\PropertyInfo;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyListable {
/*//
@date 2023-02-02
for marking properties as publically listable.
//*/

	public function
	__Construct() {

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromClass(string $CName):
	array {

		$Props = ($CName)::GetPropertiesWithAttribute(
			static::class
		);

		return $Props;
	}

}
