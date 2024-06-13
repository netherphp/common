<?php

namespace Nether\Common;

#[Meta\Date('2024-06-13')]
class TypedConst {

	const
	TypeList = [
		// self::Thing => [ 'Singular', 'Plural' ],
		// ...
	];

	const
	WordSingular = 0,
	WordPlural   = 1;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	Keys():
	Datastore {

		$Keys = new Datastore(array_keys(static::TypeList));

		return $Keys;
	}

	static public function
	List():
	Datastore {

		$List = new Datastore(static::TypeList);

		return $List;
	}

	static public function
	Word(string $Key, int $Kind=self::WordSingular):
	string {

		if(array_key_exists($Key, static::TypeList))
		return static::TypeList[$Key][$Kind];

		return 'Unknown';
	}

};
