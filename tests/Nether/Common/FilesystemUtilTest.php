<?php

namespace Nether\Common;

use PHPUnit\Framework\TestCase;
use Exception;
use Throwable;

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

	/** @test */
	public function
	TestRepath():
	void {

		$Linux = [
			'/home/bob/pictures/nsfw'
			=> '/home/bob/pictures/nsfw',

			'c:/users/bob/pictures/nsfw'
			=> 'c:/users/bob/pictures/nsfw',

			'/opt/with \\" a what'
			=> '/opt/with \\" a what'
		];

		$Windows = [
			'/home/bob/pictures/nsfw'
			=> '\\home\\bob\\pictures\\nsfw',

			'c:/users/bob/pictures/nsfw'
			=> 'c:\\users\\bob\\pictures\\nsfw',

			'/opt/with \\" a what'
			=> '\opt\with \\" a what'
		];

		$Input = NULL;
		$Expect = NULL;

		foreach($Linux as $Input => $Expect)
		$this->AssertEquals($Expect, Filesystem\Util::RepathFor('Linux', $Input));

		foreach($Windows as $Input => $Expect)
		$this->AssertEquals($Expect, Filesystem\Util::RepathFor('Windows', $Input));

		switch(PHP_OS_FAMILY) {
			case 'Windows':
				foreach($Windows as $Input => $Expect)
				$this->AssertEquals($Expect, Filesystem\Util::Repath($Input));
			break;

			default:
				foreach($Linux as $Input => $Expect)
				$this->AssertEquals($Expect, Filesystem\Util::Repath($Input));
			break;
		}

		return;
	}

	/** @test */
	public function
	TestMkRmDir():
	void {

		$Dir = './here';
		$Exceptional = FALSE;

		Filesystem\Util::MkDir($Dir);
		$this->AssertTrue(is_dir($Dir));

		Filesystem\Util::RmDir($Dir);
		$this->AssertFalse(is_dir($Dir));

		try {
			Filesystem\Util::RmDir('lkadjflkjaldfafdfa');
		}

		catch(Exception $Err) {
			$Exceptional = TRUE;
			$this->AssertInstanceOf(Error\DirNotFound::class, $Err);
		}

		$this->AssertTrue($Exceptional);

		return;
	}

	/** @test */
	public function
	TestMkRmDirHarder():
	void {

		$Dirs = [
			'./here',
			'./here/there',
			'./here/thurr'
		];

		$Dir = NULL;

		foreach($Dirs as $Dir)
		Filesystem\Util::MkDir($Dir);
		$this->AssertTrue(is_dir($Dir));

		file_put_contents('./here/there/lol.txt', 'lol');
		file_put_contents('./here/thurr/lulz.txt', 'lulz');

		Filesystem\Util::RmDir($Dirs[0]);
		$this->AssertFalse(is_dir($Dirs[0]));

		return;
	}

	/** @test */
	public function
	TestTempFile():
	void {

		// discovery note:
		// on windows, the prefix is truncated to max of 3 char.

		$Filename = Filesystem\Util::MkTempFile();
		$this->AssertTrue(file_exists($Filename));
		$this->AssertTrue(str_contains($Filename, 'nt-'));


		unlink($Filename);
		$this->AssertFalse(file_exists($Filename));

		////////

		$Filename = Filesystem\Util::MkTempFile('gd');
		$this->AssertTrue(file_exists($Filename));
		$this->AssertTrue(str_contains($Filename, 'gd-'));

		unlink($Filename);
		$this->AssertFalse(file_exists($Filename));

		return;
	}

	/** @test */
	public function
	TestTempFileFail():
	void {

		$Exceptional = FALSE;
		$Error = NULL;
		$Filename = NULL;

		////////

		try {
			$Filename = Filesystem\Util::MkTempFile(Path: '/zomgwtfbbq');
		}

		catch(Throwable $Error) {
			$Exceptional = TRUE;

			$this->AssertInstanceOf(
				Error\DirUnwritable::class,
				$Error
			);
		}

		if($Filename && file_exists($Filename))
		unlink($Filename);

		$this->AssertTrue($Exceptional);

		return;
	}

	/** @test */
	public function
	TestChmod():
	void {

		if(PHP_OS_FAMILY !== 'Windows') {
			$Filename = Filesystem\Util::MkTempFile();
			$this->AssertTrue(is_readable($Filename));
			$this->AssertTrue(is_writable($Filename));

			Filesystem\Util::Chmod($Filename, 0o000);
			$this->AssertFalse(is_readable($Filename));
			$this->AssertFalse(is_writable($Filename));

			Filesystem\Util::Chmod($Filename, 0o222);
			$this->AssertFalse(is_readable($Filename));
			$this->AssertTrue(is_writable($Filename));

			Filesystem\Util::Chmod($Filename, 0o444);
			$this->AssertTrue(is_readable($Filename));
			$this->AssertFalse(is_writable($Filename));

			Filesystem\Util::Chmod($Filename, 0o666);
			$this->AssertTrue(is_readable($Filename));
			$this->AssertTrue(is_writable($Filename));

			unlink($Filename);
		}

		else {
			// still have not mastered the crap to emulate
			// this type of permission stuff on windows.
			$this->AssertFalse(FALSE);
		}

		return;
	}

}