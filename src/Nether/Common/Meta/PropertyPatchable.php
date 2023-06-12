<?php

namespace Nether\Common\Meta;

use Attribute;
use Nether\Common\Datastore;
use Nether\Common\Prototype\PropertyInfo;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyPatchable {
/*//
@date 2023-02-02
for marking properties which can be safely patched by api without without
having to care about anything like data integrity or super fancy handling.
//*/

	public function
	__Construct() {

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromClass(string $Class):
	Datastore {
	/*//
	@date 2023-03-17
	fetch a datastore of all the properties that are smartly patchable on
	the specified class.
	//*/

		// @todo 2023-03-17
		// check for PropertyInfoPackage trait if a good way to do that
		// ever becomes to exist.

		// fetch all the patchables from this class.

		$Props = new Datastore(
			($Class)::GetPropertiesWithAttribute(static::class)
		);

		// fetch all the filters for each property.

		$Props->Remap(
			fn(PropertyInfo $Prop)
			=> $Prop->GetAttributes(PropertyFilter::class)
		);

		// strip out the props that had no filters defined as they are not
		// genuinely smartly patchable.

		$Props->Filter(
			fn(array $Filters)
			=> count($Filters) > 0
		);

		return $Props;
	}

}
