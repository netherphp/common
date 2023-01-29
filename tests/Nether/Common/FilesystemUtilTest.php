<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;

class FilesystemUtilTest
extends TestCase {

	/** @test */
	public function
	TestPathify():
	void {

		$Datasets = [
			'C:\\Users\\bob\\My Documents\\My Pictures\\NSFW'
			=> [ 'C:\\', 'Users', 'bob', 'My Documents', 'My Pictures', 'NSFW' ],

			'/home/bob/Pictures/NSFW'
			=> [ '/', 'home', 'bob', 'Pictures', 'NSFW' ]
		];

		$Goal = NULL;
		$Bits = NULL;

		// first off testing that the version of the function that allows
		// us to specify a path separator works so that regardless of the
		// os we can test it.

		foreach($Datasets as $Goal => $Bits) {
			if(str_starts_with($Goal, '/'))
			$this->AssertEquals(
				$Goal,
				Filesystem\Util::PathifyWith('/', ...$Bits)
			);

			else
			$this->AssertEquals(
				$Goal,
				Filesystem\Util::PathifyWith('\\', ...$Bits)
			);
		}

		// trusting that the two main style of os pathing are working
		// then lets just cover the magic one too.

		foreach($Datasets as $Goal => $Bits) {
			if(str_starts_with($Goal, '/'))
			if(PHP_OS_FAMILY !== 'Windows') {
				$this->AssertEquals(
					$Goal,
					Filesystem\Util::Pathify(...$Bits)
				);

				continue;
			}

			if(str_starts_with($Goal, 'C:'))
			if(PHP_OS_FAMILY === 'Windows') {
				$this->AssertEquals(
					$Goal,
					Filesystem\Util::Pathify(...$Bits)
				);

				continue;
			}

		}

		return;
	}

}