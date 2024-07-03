<?php ##########################################################################
################################################################################

namespace Nether\Common\Struct\EditorJS\Blocks;

use Nether\Atlantis;
use Nether\Common;

################################################################################
################################################################################

#[Common\meta\Info('Pairs with editorjs/tools/atl-code.js')]
class Code
extends Common\Struct\EditorJS\Block {

	protected function
	OnReady(Common\Prototype\ConstructArgs $Raw):
	void {

		parent::OnReady($Raw);

		($this->Data)
		->Lang(Common\Filters\Text::Trimmed(...))
		->Code(Common\Filters\Text::Trimmed(...));

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Render():
	string {

		$Classes = Common\Datastore::FromArray([ 'atl-blog-img' ]);
		$UUID = Common\UUID::V7();
		$Props = NULL;
		$Element = NULL;
		$Output = NULL;

		////////

		$Props = Common\Datastore::FromArray([
			'data-uuid' => $UUID,
			'data-lang' => $this->Data->Lang
		]);

		$Element = Atlantis\UI\AceEditor::FromSurfaceWith($this->Surface, [
			'Lang'    => $this->Data->Lang,
			'Content' => $this->Data->Code
		]);

		////////

		$Output = sprintf(
			'<div class="%s" %s>',
			$Classes->Join(' '),
			$Props->MapKeyValue(Common\Values::MapToParams(...))->Join(' ')
		);

		$Output .= $Element->Render();
		$Output .= '</div>';

		////////

		return $Output;
	}

}
