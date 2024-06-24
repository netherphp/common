<?php ##########################################################################
################################################################################

namespace Nether\Common\Struct\EditorJS\Blocks;

use Nether\Common;

################################################################################
################################################################################

class Quote
extends Common\Struct\EditorJS\Block {

	protected function
	OnReady(Common\Prototype\ConstructArgs $Raw):
	void {

		parent::OnReady($Raw);

		($this->Data)
		->Text(Common\Filters\Text::Trimmed(...))
		->Caption(Common\Filters\Text::Trimmed(...));

		return;
	}

	public function
	__ToString():
	string {

		$Blockquote = NULL;
		$Caption = NULL;
		$Output = NULL;

		////////

		if($this->Data->Text)
		$Blockquote = sprintf(
			'<blockquote class="blockquote">%s</blockquote>',
			$this->Data->Text
		);

		if($this->Data->Caption)
		$Caption = sprintf(
			'<figcaption class="blockquote-footer">%s</figcaption>',
			$this->Data->Caption
		);

		////////

		$Output = sprintf(
			'<div class="blockquote-supercontainer">%s%s</div>',
			($Blockquote ?: ''),
			($Caption ?: '')
		);

		return $Output;
	}

}
