<?php

namespace Nether\Common\Struct\EditorJS;

use Nether\Common;

use Exception;

class Content
extends Common\Prototype {

	#[Common\Meta\PropertyOrigin('version')]
	public ?string
	$Version = '';

	#[Common\Meta\PropertyOrigin('time')]
	public ?int
	$Time = 0;

	#[Common\Meta\PropertyOrigin('blocks')]
	public array|Common\Datastore
	$Blocks = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(Common\Prototype\ConstructArgs $Args):
	void {

		$this->Blocks = new Common\Datastore($this->Blocks);

		($this->Blocks)
		->Filter(static::LooksLikeAnBlock(...))
		->Remap(static::ConvertToAnBlock(...));

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	FindPrimaryImageID():
	?int {
	/*//
	@date 2021-04-19
	finds the first image block in this structure that is flagged as the
	primary image and contains a local file image id.
	//*/

		$Block = NULL;

		foreach($this->Blocks as $Block) {
			//if($Block instanceof Blocks\Image)
			//if($Block->Data->PrimaryImage && $Block->Data->ImageID)
			//return $Block->Data->ImageID;
		}

		return NULL;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	LooksLikeAnBlock(mixed $Block):
	bool {

		if(is_array($Block))
		if(array_key_exists('type', $Block))
		return TRUE;

		if(is_object($Block))
		if(property_exists($Block, 'type'))
		return TRUE;

		return FALSE;
	}

	static public function
	ConvertToAnBlock(object|array $Block):
	Block {

		if(is_array($Block))
		if(!array_key_exists('type', $Block))
		throw new Exception('does not smell like a block');

		if(is_object($Block))
		if(!property_exists($Block, 'type'))
		throw new Exception('does not smell like a block');

		////////

		$Class = sprintf(
			'Nether\Common\Struct\EditorJS\Blocks\%s',
			Common\Datafilters::PascalFromKey(
				is_array($Block)
				? $Block['type']
				: $Block->type
			)
		);

		if(class_exists($Class))
		return new $Class($Block);

		return new Block($Block);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromString(string $Input):
	self {

		$Object = json_decode(json_encode(
			new Validator($Input)
		));

		return new static($Object);
	}

}
