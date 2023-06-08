<?php

namespace Atlantis\Struct\EditorJS\Blocks;
use Atlantis;

class Header
extends Atlantis\Struct\EditorJS\Block {

	protected function
	OnReady(array $Raw):
	void {
		parent::OnReady($Raw);

		($this->Data)
		->Text('Atlantis\\Util\\Filters::StrippedText')
		->Level('Atlantis\\Util\\Filters::TypeInt');

		return;
	}

	public function
	__ToString():
	string {

		$Level = $this->Data->Level ?: 1;
		$Tag = "h{$Level}";

		return "<{$Tag} class=\"PostHeading mb-4\">{$this->Data->Text}<hr /></{$Tag}>\n";
	}

}
