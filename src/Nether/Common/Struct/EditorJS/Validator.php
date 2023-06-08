<?php

namespace Nether\Common\Struct\EditorJS;

use Nether\Common;

use JsonSerializable;
use Stringable;

class Validator
extends Common\Prototype
implements JsonSerializable, Stringable {
/*//
@date 2020-10-09
intended to recieve and return a blob of data that we would have expected to see
from editor.js - and if someone gave us garbage this should throw it away and hand
you back at a conforming structure.
//*/

	#[Common\Meta\PropertyOrigin('version')]
	public ?string
	$Version = NULL;

	#[Common\Meta\PropertyOrigin('time')]
	public ?int
	$Time = NULL;

	#[Common\Meta\PropertyOrigin('blocks')]
	public ?array
	$Blocks = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string|object|array $Input) {
	/*//
	@date 2021-04-13
	//*/

		if(is_string($Input))
		$Input = json_decode($Input);

		if(!is_object($Input) && !is_array($Input))
		$Input = [];

		parent::__Construct($Input);
		return;
	}

	public function
	__ToString():
	string {
	/*//
	@date 2020-10-09
	//*/

		return json_encode($this->JsonSerialize());
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(Common\Prototype\ConstructArgs $Args):
	void {
	/*//
	@date 2020-10-09
	//*/

		if(!is_array($this->Blocks))
		$this->Blocks = [];

		$this->Blocks = array_filter(
			$this->Blocks,
			(fn($Block)=> is_object($Block) && property_exists($Block,'type'))
		);

		return;
	}

	public function
	JsonSerialize():
	array {
	/*//
	@date 2020-10-09
	//*/

		return $this->ToArray();
	}

	public function
	ToStruct():
	Content {
	/*//
	@date 2021-04-13
	//*/

		return new Content($this->ToArray());
	}

	public function
	ToArray() {
	/*//
	@date 2021-04-13
	//*/

		return [
			'version' => $this->Version,
			'time'    => $this->Time,
			'blocks'  => $this->Blocks
		];
	}

}
