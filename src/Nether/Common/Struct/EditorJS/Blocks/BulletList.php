<?php ##########################################################################
################################################################################

namespace Nether\Common\Struct\EditorJS\Blocks;

use Nether\Common;

################################################################################
################################################################################

#[Common\meta\Info('Pairs with editorjs/tools/list.js')]
class BulletList
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
	Render():
	string {

		$Item = NULL;
		$Output = '';

		foreach($this->Data->Items as $Item)
		$Output .= $this->GenerateListLevel($Item);

		return "<ul>{$Output}</ul>";
	}

	public function
	GenerateListLevel($Item):
	string {

		$Nested = '';
		$Output = '';
		$Sub = NULL;

		////////

		if(count($Item->items)) {
			foreach($Item->items as $Sub)
			$Nested .= sprintf(
				'<ul>%s</ul>',
				$this->GenerateListLevel($Sub)
			);
		}

		$Output = sprintf(
			'<li>%s%s</li>',
			$Item->content,
			$Nested ?: ''
		);

		return $Output;
	}

}
