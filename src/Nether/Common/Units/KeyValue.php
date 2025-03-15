<?php ##########################################################################
################################################################################

namespace Nether\Common\Units;

use Nether\Common;

################################################################################
################################################################################

class KeyValue {

	public string|int
	$Key;

	public mixed
	$Value;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string|int $Key, mixed $Value) {

		$this->Key = $Key;
		$this->Value = $Value;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromKeyedPair(array $KV):
	static {

		if(count($KV) !== 1)
		throw new Common\Error\FormatInvalid('not a single item array');

		////////

		return new static(key($KV), current($KV));
	}

	static public function
	FromFlatPair(array $KV):
	static {

		if(count($KV) !== 2)
		throw new Common\Error\FormatInvalid('not a two item array');

		if(!array_key_exists(0, $KV))
		throw new Common\Error\FormatInvalid('missing key 0');

		if(!array_key_exists(1, $KV))
		throw new Common\Error\FormatInvalid('missing key 1');

		////////

		return new static($KV[0], $KV[1]);
	}

};
