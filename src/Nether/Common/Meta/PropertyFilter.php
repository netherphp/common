<?php

namespace Nether\Common\Meta;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
class PropertyFilter {
/*//
@date 2023-02-02
defines a callable that should be used to filter/sanitise the data stored in
the property it is attached to.
//*/

	protected mixed
	$Func;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(callable $Func) {

		$this->Func = $Func;

		return;
	}

	public function
	__Invoke(...$Argv):
	mixed {

		return call_user_func($this->Func, ...$Argv);
	}

}
