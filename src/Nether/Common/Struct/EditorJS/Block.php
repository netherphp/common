<?php

namespace Nether\Common\Struct\EditorJS;

use Nether\Common;
use Nether\Surface;

use Exception;
use Stringable;

class Block
extends Common\Prototype
implements Stringable {

	#[Common\Meta\PropertyOrigin('type')]
	public ?string
	$Type = NULL;

	public ?Common\Datafilter
	$Data = NULL;

	public Surface\Engine
	$Surface;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(Common\Prototype\ConstructArgs $Args):
	void {

		$this->Data = new Common\Datafilter(
			array_key_exists('data', $Args->Input)?
			$Args->Input['data']: []
		);

		return;
	}

	public function
	__ToString():
	string {

		return $this->Render();
	}

	public function
	Render():
	string {

		return "<div class=\"mb-4\">[EditorJS\Block type={$this->Type}]</div>";
	}

	public function
	SetSurface(Surface\Engine $Surface):
	static {

		$this->Surface = $Surface;

		return $this;
	}

}
