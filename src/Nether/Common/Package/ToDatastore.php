<?php ##########################################################################
################################################################################

namespace Nether\Common\Package;

use Nether\Common;

################################################################################
################################################################################

#[Common\Meta\Date('2024-09-30')]
trait ToDatastore {

	public function
	ToDatastore():
	Common\Datastore {

		if($this instanceof Common\Interfaces\ToArray)
		return Common\Datastore::FromArray($this->ToArray());

		return new Common\Datastore;
	}

};
