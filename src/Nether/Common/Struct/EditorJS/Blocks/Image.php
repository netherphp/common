<?php ##########################################################################
################################################################################

namespace Nether\Common\Struct\EditorJS\Blocks;

use Nether\Atlantis;
use Nether\Common;

################################################################################
################################################################################

#[Common\meta\Info('Pairs with editorjs/tools/atl-image.js')]
class Image
extends Common\Struct\EditorJS\Block {

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

	public function
	__ToString():
	string {

		$Classes = Common\Datastore::FromArray([ 'atl-editorjs-img' ]);
		$UUID = Common\UUID::V7();
		$Output = '';
		$Image = NULL;

		$ImageID = $this->Data->ImageID;
		$ImageURL = $this->Data->ImageURL;
		$GalleryURL = $this->Data->ImageURL;
		$Caption = $this->Data->Caption;
		$AltText = $this->Data->AltText;
		$Gallery = $this->Data->Gallery;
		$Primary = $this->Data->Primary;

		////////

		if($ImageID)
		if($Image = Atlantis\Media\File::GetByID($ImageID)) {
			$UUID = $Image->UUID;
			$ImageURL = $Image->GetPublicURL();
			$GalleryURL = $Image->GetPublicURL();
		}

		if($Gallery)
		$Classes->Push('atl-editorjs-img-gallery');

		$Props = Common\Datastore::FromArray([
			'data-uuid'          => $UUID,
			'data-image-url'     => $GalleryURL,
			'data-image-gallery' => $Gallery,
			'data-image-primary' => $Primary
		]);

		$PropCooker = fn($K, $V)=> sprintf('%s="%s"', $K, $V);

		////////

		$Output .= sprintf(
			'<div class="%s" %s>',
			$Classes->Join(' '),
			$Props->MapKeyValue($PropCooker)->Join(' ')
		);

		if($ImageURL)
		$Output .= sprintf(
			'<img src="%s" alt="%s" />',
			$ImageURL,
			$AltText ?: $Caption
		);

		if($Caption)
		$Output .= sprintf(
			'<figcaption>%s</figcaption>',
			$Caption ?: $AltText
		);

		$Output .= '</div>';

		////////

		return $Output;
	}

}
