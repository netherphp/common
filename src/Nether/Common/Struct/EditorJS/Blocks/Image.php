<?php ##########################################################################
################################################################################

namespace Nether\Common\Struct\EditorJS\Blocks;

use Nether\Atlantis;
use Nether\Common;

use Stringable;

################################################################################
################################################################################

#[Common\meta\Info('Pairs with editorjs/tools/atl-image.js')]
class Image
extends Common\Struct\EditorJS\Block
implements Stringable {

	protected function
	OnReady(Common\Prototype\ConstructArgs $Raw):
	void {

		parent::OnReady($Raw);

		($this->Data)
		->ImageID(Common\Filters\Numbers::IntType(...))
		->ImageURL(Common\Filters\Text::Trimmed(...))
		->Caption(Common\Filters\Text::Trimmed(...))
		->AltText(Common\Filters\Text::Trimmed(...))
		->Gallery(Common\Filters\Numbers::BoolType(...))
		->Primary(Common\Filters\Numbers::BoolType(...));

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__ToString():
	string {

		return $this->Render();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Render():
	string {

		$Classes = Common\Datastore::FromArray([ 'atl-blog-img' ]);
		$UUID = Common\UUID::V7();
		$Output = '';
		$Props = NULL;
		$Params = NULL;
		$Image = NULL;
		$Caption = NULL;

		////////

		if($this->Data->Gallery)
		$Classes->Push('atl-blog-img-gallery');

		if($this->Data->Caption)
		$Caption = $this->RenderCaption();

		$Image = match(TRUE) {
			($this->Data->ImageID !== NULL)
			=> $this->RenderSystemImage(),

			default
			=> $this->RenderMiscImage()
		};

		////////

		$Props = Common\Datastore::FromArray([
			'data-uuid'          => $UUID,
			'data-image-url'     => $this->Data->ImageURL,
			'data-image-gallery' => $this->Data->Gallery,
			'data-image-primary' => $this->Data->Primary
		]);

		$Params = $Props->MapKeyValue(Common\Values::MapToParams(...));

		////////

		$Output .= sprintf(
			'<div class="%s" %s>',
			$Classes->Join(' '),
			$Params->Join(' ')
		);

		if($Image)
		$Output .= $Image;

		if($Caption)
		$Output .= $Caption;

		$Output .= '</div>';

		////////

		return $Output;
	}

	protected function
	RenderSystemImage():
	string {

		$Image = Atlantis\Media\File::GetByID($this->Data->ImageID);
		$Output = NULL;

		////////

		if(!$Image)
		return sprintf(
			'<div class="alert alert-info ta-center">Image ID:%d not found</div>',
			$this->Data->ImageID
		);

		////////

		$this->Data->ImageURL = $Image->GetPublicURL();

		$Output = sprintf(
			'<img src="%s" alt="%s" />',
			$Image->GetPreviewURL('lg.'),
			$this->Data->Caption ?? $this->Data->AltText
		);

		return $Output;
	}

	protected function
	RenderMiscImage():
	string {

		return "misc {$this->Data->ImageURL}";
	}

	protected function
	RenderCaption():
	string {

		$Output = sprintf(
			'<figcaption>%s</figcaption>',
			$this->Data->Caption ?: $this->Data->AltText
		);

		return $Output;
	}

}
