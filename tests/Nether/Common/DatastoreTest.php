<?php

namespace Nether\Common;
use PHPUnit;

use Nether\Common\Datastore;
use Nether\Common\Error\FileUnreadable;
use Nether\Common\Error\FileUnwritable;
use Nether\Common\Error\FileNotFound;
use Nether\Common\Error\FileNotSpecified;
use Nether\Common\Error\DirUnwritable;

use StdClass;
use Throwable;
use Exception;

class DatastoreTest
extends PHPUnit\Framework\TestCase {

	/** @test */
	public function
	TestDataStorageDataset() {

		// testing right out of the box.

		$Key = NULL;
		$Item = NULL;

		$Store = new Datastore;
		$this->AssertTrue($Store->Count() === 0);
		$this->AssertTrue(count($Store->GetData()) === 0);

		// throw some sample data in.

		$Store->SetData([1,2,3,4,5,6]);
		$this->AssertTrue($Store->Count() === 6);
		$this->AssertTrue(count($Store->GetData()) === 6);

		// throw more sample data in and integrity test it.

		$Input = ['zomg','wtfz','bbqueen'];

		$Store->SetData($Input);
		$this->AssertTrue($Store->Count() === count($Input));

		$Output = $Store->GetData();
		$this->AssertTrue(count($Output) === count($Input));

		foreach($Output as $Key => $Item)
		$this->AssertTrue($Item === $Input[$Key]);

		return;
	}

	/** @test */
	public function
	TestDataStorageItemsNormal() {

		$X = NULL;
		$Key = NULL;
		$Value = NULL;

		$Store = new Datastore;

		// test that we can add items one at a time.

		for($X = 1; $X <= 6; $X++)
		$Store->Push($X);

		// and that it properly counted them.

		$this->AssertTrue($Store->Count() === 6);

		// and that they equal to what was expected.

		foreach($Store as $Key => $Value)
		$this->AssertTrue($Value === ($Key + 1));

		// mess with the shove method too.

		$Store->Shove(1, 42);
		$Store->Shove('FourtyTwo', 42);
		$this->AssertEquals(42, $Store->Get(1));
		$this->AssertEquals(42, $Store->Get('FourtyTwo'));

		return;
	}

	/** @test */
	public function
	TestDataStorageItemsAssoc() {

		$Key = NULL;
		$Value = NULL;
		$Store = new Datastore;

		// test that we can add items one at a time.

		$Input = [
			'Forename' => 'Bob',
			'Surname'  => 'Majdak',
			'Suffix'   => 'II'
		];

		foreach($Input as $Key => $Value)
		$Store->Push($Value,$Key);

		$this->AssertTrue($Store->Count() === 3);
		$this->AssertNull($Store['DoesNotExist']);

		// test that we can get items one at time.

		foreach($Input as $Key => $Value)
		$this->AssertTrue($Store->Get($Key) === $Value);

		return;
	}

	/** @test */
	public function
	TestDataStorageRemoval() {

		$Store = new Datastore;
		$Store->SetData([
			1,2,3,4,5,6,
			'Donkey' => 'Kong',
			7,8,9
		]);
		$this->AssertTrue($Store->Count() === 10);
		$this->AssertTrue($Store->Get(1) === 2);

		$Store->Remove(1);
		$this->AssertTrue($Store->Count() === 9);
		$this->AssertTrue($Store->Get(1) === NULL);

		$Store->Reindex();
		$this->AssertTrue($Store->Count() === 9);
		$this->AssertTrue($Store->Get(1) === 3);
		$this->AssertTrue($Store->Get('Donkey') === 'Kong');

		return;
	}

	/** @test */
	public function
	TestDataStoragePushPop() {

		$Store = new Datastore;

		$Store->Push(1);
		$Store->Push(2);
		$Store->Push(3);
		$this->AssertTrue($Store->Count() === 3);

		$this->AssertTrue($Store->Pop() === 3);
		$this->AssertTrue($Store->Pop() === 2);
		$this->AssertTrue($Store->Pop() === 1);
		$this->AssertTrue($Store->Count() === 0);

		return;
	}

	/** @test */
	public function
	TestDataStorageShiftUnshift() {

		$Store = (new Datastore)
		->Push(1)
		->Push(2)
		->Push(3);

		// test shifting. which pops things off the front.

		$this->AssertTrue($Store->Count() === 3);
		$this->AssertTrue($Store->Shift() === 1);
		$this->AssertTrue($Store->Shift() === 2);
		$this->AssertTrue($Store->Shift() === 3);
		$this->AssertTrue($Store->Count() === 0);

		// test unshifting. which pushes things onto the front.

		$Store->Unshift('one');
		$Store->Unshift('two');
		$this->AssertTrue($Store->Count() === 2);
		$this->AssertTrue($Store->Shift() === 'two');

		return;
	}

	/** @test */
	public function
	TestDataIteration() {

		$Store = new Datastore;
		$Store->SetData([1,2,3,4,5,6]);

		$Loop = 1;
		while($Loop <= 4) {

			// run these tests a few times so we can see reset and
			// valid working. for reasons.

			$Iter = 1;
			foreach($Store as $Key => $Item) {
				$this->AssertTrue($Iter === ($Key + 1));
				$this->AssertTrue($Iter === $Item);
				$Iter++;
			}

			$Loop++;
		}

		return;
	}

	/** @test */
	public function
	TestDataMergeRight() {

		/*
		this is what we want to see.
		Array
		(
			[0] => 1
			[1] => 2
			[2] => 3
			[one] => one
			[two] => two    // old data here and up.
			[three] => tres // overwrite
			[3] => 4        // new data here and down.
			[4] => 5
			[5] => 6
			[four] => four
			[five] => five
		)
		*/

		$Store = new Datastore;

		$Store->SetData([
			1, 2, 3,
			'one'=>'one',
			'two'=>'two',
			'three'=>'three'
		]);

		$Store->MergeRight([
			4, 5, 6,
			'three' => 'tres',
			'four'  => 'four',
			'five'  => 'five'
		]);

		// test various data points.

		$this->AssertTrue($Store[0] === 1);
		$this->AssertTrue($Store[3] === 4);
		$this->AssertTrue($Store['three'] === 'tres');
		$this->AssertTrue($Store['five'] === 'five');

		// test dataset edges.

		$Data = $Store->GetData();

		reset($Data);
		$this->AssertTrue(key($Data) === 0);
		$this->AssertTrue(current($Data) === 1);

		end($Data);
		$this->AssertTrue(key($Data) === 'five');
		$this->AssertTrue(current($Data) === 'five');

		// test various data points.

		$Data = $Store->Values();
		$this->AssertTrue($Data[5] === 'tres');
		$this->AssertTrue($Data[6] === 4);
		$this->AssertTrue(count($Data) === $Store->Count(FALSE));

		// test alternate inputs.

		$Store->MergeRight(new Datastore([ 'five' => 'cinco' ]));
		$this->AssertTrue($Store['five'] === 'cinco');

		$Store->MergeRight(json_decode("{ \"five\": \"funf\" }"));
		$this->AssertTrue($Store['five'] === 'funf');

		return;
	}

	/** @test */
	public function
	TestDataMergeLeft() {

		/*
		this is what we want to see.
		Array
		(
			[0] => 4
			[1] => 5
			[2] => 6
			[four] => four
			[five] => five   // new data here and up
			[3] => 1         // old data here and down
			[4] => 2
			[5] => 3
			[one] => one
			[two] => two
			[three] => tres  // except for this overwrite
		)
		*/


		$Store = new Datastore;
		$Store->SetData([1,2,3,'one'=>'one','two'=>'two','three'=>'three']);

		$Store->MergeLeft([
			4, 5, 6,
			'three' => 'tres',
			'four'  => 'four',
			'five'  => 'five'
		]);

		$Data = $Store->GetData();

		$this->AssertTrue($Data[0] === 4);
		$this->AssertTrue($Data[3] === 1);
		$this->AssertTrue($Data['three'] === 'tres');
		$this->AssertTrue($Data['five'] === 'five');

		reset($Data);
		$this->AssertTrue(key($Data) === 0);
		$this->AssertTrue(current($Data) === 4);

		end($Data);
		$this->AssertTrue(key($Data) === 'three');
		$this->AssertTrue(current($Data) === 'tres');

		$Data = array_values($Data);
		$this->AssertTrue($Data[4] === 'five');
		$this->AssertTrue($Data[5] === 1);

		$this->AssertTrue(count($Data) === $Store->Count(FALSE));

		// test alternate inputs.

		$Data = $Store->Values();

		$Store->MergeLeft(new Datastore([ 'five' => 'cinco' ]));
		$this->AssertTrue($Store['five'] === 'cinco');

		$Store->MergeLeft(json_decode("{ \"five\": \"funf\" }"));
		$this->AssertTrue($Store['five'] === 'funf');

		$this->AssertTrue($Data[4] === 'five');


		return;
	}

	/** @test */
	public function
	TestDataBlendRight() {

		/*
		this is what we want to see.
		Array
		(
			[0] => 1
			[1] => 2
			[2] => 3
			[one] => one
			[two] => two
			[three] => three
			[3] => 4
			[4] => 5
			[5] => 6
			[four] => four
			[five] => five
		)
		*/

		$Store = new Datastore;
		$Store->SetData([1,2,3,'one'=>'one','two'=>'two','three'=>'three']);

		$Store->BlendRight([
			4, 5, 6,
			'three' => 'tres',
			'four'  => 'four',
			'five'  => 'five'
		]);

		$Data = $Store->GetData();

		$this->AssertTrue($Data[0] === 1);
		$this->AssertTrue($Data[3] === 4);
		$this->AssertTrue($Data['three'] === 'three');
		$this->AssertTrue($Data['five'] === 'five');

		reset($Data);
		$this->AssertTrue(key($Data) === 0);
		$this->AssertTrue(current($Data) === 1);

		end($Data);
		$this->AssertTrue(key($Data) === 'five');
		$this->AssertTrue(current($Data) === 'five');

		$Data = array_values($Data);
		$this->AssertTrue($Data[5] === 'three');
		$this->AssertTrue($Data[6] === 4);

		$this->AssertTrue(count($Data) === $Store->Count(FALSE));

		// test alternative input.

		$Store->BlendRight(new Datastore([ 'five' => 'cinco' ]));
		$this->AssertTrue($Store['five'] === 'five');

		$Store->BlendRight(json_decode("{ \"five\": \"funf\" }"));
		$this->AssertTrue($Store['five'] === 'five');

		$Store->BlendRight(new Datastore([ 'six' => 'six' ]));
		$this->AssertTrue($Store['six'] === 'six');

		$Store->BlendRight(json_decode("{ \"six\": \"seis\" }"));
		$this->AssertTrue($Store['six'] === 'six');

		return;
	}

	/** @test */
	public function
	TestDataBlendLeft() {

		/*
		this is what we want to see.
		Array
		(
			[0] => 4
			[1] => 5
			[2] => 6
			[four] => four
			[five] => five
			[3] => 1
			[4] => 2
			[5] => 3
			[one] => one
			[two] => two
			[three] => three
		)
		*/

		$Store = new Datastore;
		$Store->SetData([1,2,3,'one'=>'one','two'=>'two','three'=>'three']);

		$Store->BlendLeft([
			4, 5, 6,
			'three' => 'tres',
			'four'  => 'four',
			'five'  => 'five'
		]);

		$Data = $Store->GetData();

		$this->AssertTrue($Data[0] === 4);
		$this->AssertTrue($Data[3] === 1);
		$this->AssertTrue($Data['three'] === 'three');
		$this->AssertTrue($Data['five'] === 'five');

		reset($Data);
		$this->AssertTrue(key($Data) === 0);
		$this->AssertTrue(current($Data) === 4);

		end($Data);
		$this->AssertTrue(key($Data) === 'three');
		$this->AssertTrue(current($Data) === 'three');

		$Data = array_values($Data);
		$this->AssertTrue($Data[4] === 'five');
		$this->AssertTrue($Data[5] === 1);
		$this->AssertTrue($Data[10] === 'three');

		$this->AssertTrue(count($Data) === $Store->Count(FALSE));

		// test alternative input.

		$Store->BlendLeft(new Datastore([ 'five' => 'cinco' ]));
		$this->AssertTrue($Store['five'] === 'five');

		$Store->BlendLeft(json_decode("{ \"five\": \"funf\" }"));
		$this->AssertTrue($Store['five'] === 'five');

		$Store->BlendLeft(new Datastore([ 'six' => 'six' ]));
		$this->AssertTrue($Store['six'] === 'six');

		$Store->BlendLeft(json_decode("{ \"six\": \"seis\" }"));
		$this->AssertTrue($Store['six'] === 'six');

		return;
	}

	/** @test */
	public function
	TestDataClear() {

		$Store = new Datastore;
		$Store->SetData([1,2,3]);

		$this->AssertTrue($Store->Count(FALSE) === 3);

		$Store->Clear();

		$this->AssertTrue($Store->Count(FALSE) === 0);
		$this->AssertTrue($Store->Count(FALSE) === count($Store->GetData()));

		return;
	}

	/** @test */
	public function
	TestWriteFormat() {

		$Store = new Datastore;

		$Store->SetFormat($Store::FormatJSON);
		$this->AssertTrue($Store->GetFormat() === $Store::FormatJSON);

		$Store->SetFormat($Store::FormatPHP);
		$this->AssertTrue($Store->GetFormat() === $Store::FormatPHP);

		$Store->SetFormat(-1);
		$this->AssertTrue($Store->GetFormat() === $Store::FormatPHP);

		return;
	}

	/** @test */
	public function
	TestReadWriteToDisk() {

		$Dataset = [ 1, 2, 3 ];
		$Store = new Datastore($Dataset);
		$Failed = FALSE;
		$Filename = sprintf(
			'%s/nether-object-datastore-%s.phson',
			sys_get_temp_dir(),
			md5(microtime(TRUE))
		);

		// fail because no filename specified.

		try {
			$Failed = FALSE;
			$Store->Write();
		}

		catch(Throwable $Err) {
			$Failed = TRUE;
			$this->AssertInstanceOf(FileNotSpecified::class, $Err);
		}

		$this->AssertTrue($Failed, 'expected FileNotSpecified');

		// fail because a directory cannot be made.

		try {
			$Failed = FALSE;
			$Store->Write(
				PHP_OS_FAMILY === 'Windows'
				? 'zz:/quoth/the/raven/nevermore.lulz'
				: '/quoth/the/raven/nevermore.lulz'
			);
		}

		catch(Throwable $Err) {
			$Failed = TRUE;
			$this->AssertInstanceOf(DirUnwritable::class, $Err);
		}

		$this->AssertTrue($Failed, 'expected DirUnwritable');

		// surely then it would be impossible to fail at writing
		// by this point amirite.

		$Store->Write($Filename);
		$Result = Datastore::NewFromFile($Filename);

		$this->AssertTrue(file_exists($Filename));
		$this->AssertTrue(filesize($Filename) > 0);
		$this->AssertEquals(3, $Result->Count());

		// try with a datastore in the datastore too.

		$Store->Push(new Datastore([ 100, 200, 300 ]));
		$Store->Write($Filename);

		$Result = Datastore::NewFromFile($Filename);
		$this->AssertTrue(file_exists($Filename));
		$this->AssertTrue(filesize($Filename) > 0);
		$this->AssertEquals(4, $Result->Count());

		// fail because we are about to make that file unwritable.
		// rumor has it that php has some built in stuff with
		// the chmod function so that it can toggle the read
		// only flag if the user bit doesn't have read so just
		// am trusting that this works on windows lol.

		chmod($Filename, 0000);

		try {
			$Failed = FALSE;
			$Store->Write($Filename);
		}

		catch(Throwable $Err) {
			$Failed = TRUE;
			$this->AssertInstanceOf(FileUnwritable::class, $Err);
		}

		$this->AssertTrue($Failed, 'expected FileUnwritable');

		chmod($Filename, 0666);
		unlink($Filename);

		return;
	}

	/** @test */
	public function
	TestReadFromDisk() {

		$Iter = NULL;
		$HadException = FALSE;
		$Dataset = [1,2,3];

		$Filename = sprintf(
			'%s/nether-object-datastore-%s.phson',
			sys_get_temp_dir(),
			md5(microtime(TRUE))
		);

		$Jsonname = sprintf(
			'%s/nether-object-datastore-%s.json',
			sys_get_temp_dir(),
			md5(microtime(TRUE))
		);

		// try to fail reading a file without ever setting the name.

		$HadException = FALSE;

		try {
			$Store = new Datastore;
			$Store->Read();
		}

		catch(Throwable $Err) {
			$HadException = TRUE;
			$this->AssertInstanceOf(FileNotSpecified::class, $Err);
		}

		$this->AssertTrue(
			$HadException,
			'we should have had a read exception (no filename)'
		);

		// try to fail reading a file that does not exist.

		$HadException = FALSE;

		try {
			$Store = new Datastore;
			$Store->Read('/quoth/the/raven/nevermore');
		}

		catch(Throwable $Err) {
			$HadException = TRUE;
			$this->AssertInstanceOf(FileNotFound::class, $Err);
		}

		$this->AssertTrue(
			$HadException,
			'we should have had a read exception (doesnt exist)'
		);

		// write a file.

		$Store = new Datastore;
		$Store->SetData($Dataset);
		$Store->Write($Filename);
		$this->AssertTrue(file_exists($Filename));

		// read a file.

		unset($Store);
		$Store = new Datastore;
		$Store->Read($Filename);

		for($Iter = 0; $Iter < count($Dataset); $Iter++)
		$this->AssertTrue($Store->Get($Iter) === $Dataset[$Iter]);

		// read a file again.

		$Store = Datastore::NewFromFile($Filename);

		for($Iter = 0; $Iter < count($Dataset); $Iter++)
		$this->AssertTrue($Store->Get($Iter) === $Dataset[$Iter]);

		// try to fail at reading a file due to permissions.

		if(PHP_OS_FAMILY === 'Windows') {
			$Filename = Filesystem\Util::Repath($Filename);
			system(sprintf(
				'icacls %s /deny everyone:R 2>&1 1>NUL',
				$Filename
			));
		}
		else {
			chmod($Filename, 0000);
		}

		$HadException = FALSE;
		try { $Store = Datastore::NewFromFile($Filename); }

		catch(Throwable $Err) {
			$HadException = TRUE;
			$this->AssertInstanceOf(FileUnreadable::class, $Err);
		}

		$this->AssertTrue(
			$HadException,
			'we should have had a read exception (unreadable perms)'
		);

		// try to read and write the same thing but in json format.

		$Store = new Datastore($Dataset);
		$Store->SetFilename($Jsonname);
		$Store->Write();

		$Result = Datastore::NewFromFile($Jsonname);
		$this->AssertEquals(3, $Result->Count());

		// try to read a jsonfile that wasn't the format we expected.

		file_put_contents($Jsonname, "{}");
		$Result = Datastore::NewFromFile($Jsonname);

		if(PHP_OS_FAMILY === 'Windows') {
			system(sprintf(
				'icacls %s /grant everyone:R 2>&1 1>NUL',
				$Filename
			));
		}
		else {
			chmod($Filename, 0666);
		}

		unlink($Filename);
		unlink($Jsonname);
		return;
	}

	/** @test */
	public function
	TestReadWithAppend():
	void {

		$Dataset1 = [ 1, 2, 3 ];
		$Dataset2 = [ 4, 5, 6 ];
		$Store = new Datastore($Dataset1);

		////////

		$Filename = sprintf(
			'%s/nether-object-datastore-%s.json',
			sys_get_temp_dir(),
			md5(microtime(TRUE))
		);

		file_put_contents($Filename, json_encode($Dataset2));

		// edit and confirm data.

		$this->AssertEquals(3, $Store->Count());
		$Store[0] = 90;
		$Store[1] = 91;
		unset($Store[2]);
		$this->AssertEquals(2, $Store->Count());

		$this->AssertEquals(90, $Store[0]);
		$this->AssertEquals(91, $Store[1]);
		$this->AssertFalse($Store->HasKey(2));

		// append and confirm filedata.

		$Store->Read($Filename, TRUE);
		$this->AssertEquals(5, $Store->Count());

		$this->AssertEquals(90, $Store[0]);
		$this->AssertEquals(91, $Store[1]);
		$this->AssertEquals(4, $Store[2]);
		$this->AssertEquals(5, $Store[3]);
		$this->AssertEquals(6, $Store[4]);

		unset($Filename);
		return;
	}

	/** @test */
	public function
	TestReadFromDisk_ThatOneFuckingPhpBugTheyRefuseToFix() {
	/*//
	this test passing or failing does not mean anything in regards to the
	library being broken or not. this is purely to test the existance of
	that one json decode bug that can fuck you if you're unaware of the
	content of your dataset. https://bugs.php.net/bug.php?id=45959
	//*/

		$Dataset = [ 1, 'two'=>2, 3 ];
		$Filename = sprintf(
			'%s/nether-object-datastore-%s.json',
			sys_get_temp_dir(),
			md5(microtime(TRUE))
		);

		// write a file.

		$Store = new Datastore;
		$Store->SetData($Dataset);
		$Store->Write($Filename);
		$this->AssertTrue(file_exists($Filename));

		// read a file.

		unset($Store);
		$Store = new Datastore;
		$Store->Read($Filename);

		if(version_compare(phpversion(),'7.2.0-dev','>=')) {
			// holy ballweasles this was fixed in 7.2.0.
			// these three asserts passing means the bug will not fuck us
			// because it has been fixed but im deatly afraid that this
			// could have an accidental reversion one day.
			$this->AssertTrue($Store->Get(0) === 1);
			$this->AssertTrue($Store->Get(2) === NULL);
			$this->AssertTrue($Store->Get('two') === 2);

		} else {
			// these three asserts passing means yes, the bug is gonna
			// fuck us. we are going to have to make the library warn you
			// about mixing assoc and non-assoc keys when using json.
			$this->AssertTrue($Store->Get(0) === NULL);
			$this->AssertTrue($Store->Get(2) === NULL);
			$this->AssertTrue($Store->Get('two') === 2);
		}

		unlink($Filename);
		return;
	}

	/** @test */
	public function
	TestMapAndRemap() {

		$Store = new Datastore;
		$Store->Push('1')->Push('2')->Push('3');
		$New = NULL;

		// make sure i have a store of strings.

		$this->AssertEquals(3,$Store->Count());

		$Store->Each(function($Val){
			$this->AssertTrue(is_string($Val));
			return;
		});

		// make sure i have an old store of strings and a new store
		// of integers.

		$New = $Store->Map(function($Val){ return (Int)$Val; });
		$this->AssertEquals(3,$Store->Count());
		$this->AssertEquals(3,$New->Count());

		$Store->Each(function($Val){
			$this->AssertTrue(is_string($Val));
			return;
		});

		$New->Each(function($Val){
			$this->AssertTrue(is_int($Val));
			return;
		});

		// make sure my store of its is now a store of strings.

		$New->Remap(function($Val){ return (String)$Val; });
		$this->AssertEquals(3,$New->Count());

		$New->EachKeyValue(function($Key, $Val){
			$this->AssertTrue(is_string($Val));
			return;
		});

		return;
	}

	/** @test */
	public function
	TestFilterAndDistill() {

		$Store = new Datastore;
		$Other = NULL;

		$Store->Push(1)->Push(2)->Push(3);
		$this->AssertEquals(3,$Store->Count());

		$Other = $Store->Distill(function($Val){ return $Val !== 2; });
		$this->AssertEquals(2,$Other->Count());
		$this->AssertEquals(3,$Store->Count());
		$this->AssertTrue($Other->HasValue(2) === FALSE);
		$this->AssertTrue($Store->HasValue(2) !== FALSE);
		$this->AssertTrue($Store !== $Other);

		$Other->Push(4);
		$this->AssertEquals(3,$Other->Count());
		$this->AssertEquals(3,$Store->Count());
		$this->AssertTrue($Other->HasValue(4) !== FALSE);
		$this->AssertTrue($Store->HasValue(4) === FALSE);

		$Other = $Store->Filter(function($Val){ return $Val !== 2; });
		$this->AssertEquals(2,$Store->Count());
		$this->AssertTrue($Store->HasValue(2) === FALSE);
		$this->AssertTrue($Store === $Other);

		return;
	}

	/** @test */
	public function
	TestJsonise() {

		$Object = new Datastore([
			'one', 'two', 'three'
		]);

		$JSON = json_encode($Object);
		$Data = json_decode($JSON);
		$this->AssertIsArray($Data);
		$this->AssertCount(3, $Data);

		$Object->SetFullJSON(TRUE);

		$JSON = json_encode($Object);
		$Data = json_decode($JSON);
		$this->AssertIsObject($Data);
		$this->AssertInstanceOf(StdClass::class, $Data);

		return;
	}

	/** @test */
	public function
	TestGetFirstLastHasKeys() {

		$Data = new Datastore([
			'one',
			'two',
			'three'
		]);

		$Key = NULL;
		$Val = NULL;

		$this->AssertTrue($Data->HasKey(0));
		$this->AssertFalse($Data->HasKey(-1));
		$this->AssertEquals(0, $Data->GetFirstKey());
		$this->AssertEquals(2, $Data->GetLastKey());

		foreach($Data as $Key => $Val) {
			if($Val === 'one')
			$this->AssertTrue(
				$Data->IsFirstKey($Key)
				&& !$Data->IsLastKey($Key)
			);

			if($Val === 'two')
			$this->AssertTrue(
				!$Data->IsFirstKey($Key)
				&& !$Data->IsLastKey($Key)
			);

			if($Val === 'three')
			$this->AssertTrue(
				!$Data->IsFirstKey($Key)
				&& $Data->IsLastKey($Key)
			);
		}

		return;
	}

	/** @test */
	public function
	TestTheThingWithEmptyArrayBrackets() {

		$Data = new Datastore;
		$this->AssertEquals(0,$Data->Count());

		$Data[] = 'one';
		$this->AssertEquals(1,$Data->Count());

		$Data[] = 'two';
		$this->AssertEquals(2,$Data->Count());

		return;
	}

	/** @test */
	public function
	TestGetRef():
	void {

		$Data = [ 1, 2 ];
		$Store = new Datastore($Data);

		$this->AssertTrue($Store->Get(0) === 1);
		$this->AssertTrue($Store->Get(1) === 2);
		$this->AssertNull($Store->Get(3));

		$Hack = &$Store->GetRef(0);
		$Hack = 42;

		$this->AssertTrue($Store->Get(0) === 42);
		$this->AssertTrue($Store->Get(1) === 2);
		$this->AssertNull($Store->Get(3));

		////////

		try { $Fail = &$Store->GetRef(3); }
		catch(Throwable $Err) {
			$this->AssertInstanceOf(
				Exception::class,
				$Err
			);
		}

		return;
	}

	/** @testt */
	public function
	_TestFullDebug():
	void {

		$Store = new Datastore([1, 2, 3]);

		$this->AssertFalse($Store->GetFullDebug());
		$this->AssertIsArray($Store->__DebugInfo());
		$this->AssertCount(3, $Store->__DebugInfo());

		$Store->SetFullDebug(TRUE);
		$this->AssertTrue($Store->GetFullDebug());
		$this->AssertGreaterThan(3, count($Store->__DebugInfo()));

		return;
	}

	/** @test */
	public function
	TestInvokeSyntax():
	void {

		$Store = new Datastore([1, 2, 3]);
		$Result = $Store();

		$this->AssertIsArray($Result);
		$this->AssertCount(3, $Result);

		return;
	}

	/** @test */
	public function
	TestGetSetFilename():
	void {

		$Store = new Datastore([1, 2, 3]);

		$this->AssertNull($Store->GetFilename());

		$Store->SetFilename('output.json');
		$this->AssertEquals('output.json', $Store->GetFilename());

		return;
	}

	/** @test */
	public function
	TestGetSetFullJSON():
	void {

		$Store = new Datastore([1, 2, 3]);

		$this->AssertFalse($Store->GetFullJSON());

		$Result = json_decode(json_encode($Store));
		$this->AssertIsArray($Result);
		$this->AssertCount(3, $Result);

		////////

		$Store->SetFullJSON(TRUE);
		$this->AssertTrue($Store->GetFullJSON());

		$Result = json_decode(json_encode($Store));
		$this->AssertIsObject($Result);

		return;
	}

	/** @test */
	public function
	TestGetSetFullSerialize():
	void {

		$Store = new Datastore([1, 2, 3]);
		$Store->SetTitle('Dataset Title');
		$this->AssertFalse($Store->GetFullSerialize());

		// after a small serialize we expect to lose the debug settings
		// while keeping relevant data fields.

		$Store->SetFullSerialize(FALSE);
		$Store->SetFullDebug(TRUE);

		$Result = unserialize(serialize($Store));
		$this->AssertEquals($Store->GetTitle(), $Result->GetTitle());
		$this->AssertEquals($Store->Count(), $Result->Count());
		$this->AssertFalse($Result->GetFullDebug());

		// after a full serialize we expect to keep everything as it was
		// when the entire thing is rehydrated.

		$Store->SetFullSerialize(TRUE);
		$Store->SetFullDebug(TRUE);

		$Result = unserialize(serialize($Store));
		$this->AssertEquals($Store->GetTitle(), $Result->GetTitle());
		$this->AssertEquals($Store->Count(), $Result->Count());
		$this->AssertTrue($Result->GetFullDebug());

		////////

		return;
	}

	/** @test */
	public function
	TestGetSetFullSerializeBubbleDown():
	void {

		$Store = new Datastore([
			'First' => new Datastore([ 1, 2, 3 ]),
			'Second' => new Datastore([ 4, 5, 6 ])
		]);

		$Store->SetFullDebug(TRUE);
		$this->AssertFalse($Store['First']->GetFullDebug());
		$this->AssertFalse($Store['Second']->GetFullDebug());

		// with a full serialize we would expect to see the full debug
		// property three times with this structure.

		$Store->SetFullSerialize(TRUE);
		$Data = serialize($Store);
		$this->AssertEquals(3, substr_count($Data, 'FullDebug'));
		$this->AssertEquals(3, substr_count($Data, 'Filename'));
		$this->AssertEquals(3, substr_count($Data, 'Format'));


		// with a small serialize we would expect to see none of the
		// debug properties with this structure.

		$Store->SetFullSerialize(FALSE);
		$Data = serialize($Store);
		$this->AssertEquals(0, substr_count($Data, 'FullDebug'));
		$this->AssertEquals(0, substr_count($Data, 'Filename'));
		$this->AssertEquals(0, substr_count($Data, 'Format'));

		// with a small serialize we may get additional properties if they
		// were actually defined.

		$Store->SetFilename('test.phson');
		$Store->SetTitle('Test Dataset');

		$Store->SetFullSerialize(FALSE);
		$Data = serialize($Store);
		$this->AssertEquals(0, substr_count($Data, 'FullDebug'));
		$this->AssertEquals(1, substr_count($Data, 'Filename'));
		$this->AssertEquals(1, substr_count($Data, 'Format'));

		return;
	}

	/** @test */
	public function
	TestGetSetTitle():
	void {

		$Store = new Datastore([1, 2, 3]);

		$this->AssertNull($Store->GetTitle());

		$Store->SetTitle('HerpDerp');
		$this->AssertEquals('HerpDerp', $Store->GetTitle());

		return;
	}

	/** @test */
	public function
	TestGetSetSorter():
	void {

		$Store = new Datastore([2, 1, 3]);
		$Sorter = (fn($A, $B)=> $B <=> $A);

		// check initial sort

		$this->AssertNull($Store->GetSorter());
		$this->AssertEquals(2, array_values($Store->GetData())[0]);

		// check sort using default asort

		$Store->Sort();
		$this->AssertEquals(1, array_values($Store->GetData())[0]);

		// check sort using custom sorter

		$Store->SetSorter($Sorter);
		$this->AssertTrue($Store->GetSorter() === $Sorter);

		$Store->Sort();
		$this->AssertEquals(3, array_values($Store->GetData())[0]);

		// confirm asort but sorted

		$Store->Rewind();
		$this->AssertEquals(2, $Store->Key());
		$this->AssertEquals(3, $Store->Current());

		// confirm forced sort func.

		$Store->SetSorter(NULL);
		$Store->Sort();
		$Store->Rewind();
		$this->AssertEquals(1, $Store->Key());
		$this->AssertEquals(1, $Store->Current());

		$Store->Rewind();
		$Store->Sort($Sorter);
		$this->AssertEquals(2, $Store->Key());
		$this->AssertEquals(3, $Store->Current());

		return;
	}

	/** @test */
	public function
	TestArrayAccess():
	void {

		$Source = [1, 2, 3];
		$Store = new Datastore($Source);
		$Iter = 0;

		// check reading via OffsetGet.

		for($Iter = 0; $Iter < 3; $Iter++)
		$this->AssertEquals($Source[$Iter], $Store[$Iter]);

		// check writing via OffsetSet.

		$Store[] = 9001;
		$this->AssertEquals(9001, $Store[3]);

		$Store[1] = 42;
		$this->AssertEquals(42, $Store[1]);

		$Store['One'] = 'Won';
		$this->AssertEquals('Won', $Store['One']);

		// check unsetting via OffsetUnset

		unset($Store['One']);
		$this->AssertFalse($Store->HasKey('One'));

		// check existing via OffsetExists

		$this->AssertTrue(isset($Store[1]));
		$this->AssertFalse(isset($Store['One']));

		return;
	}

	/** @test */
	public function
	TestMethodKeysValues():
	void {

		$Store = new Datastore([
			1, 2, 3,
			'One' => 'Won'
		]);

		$Keys = $Store->Keys();
		$this->AssertIsArray($Keys);
		$this->AssertCount(4, $Keys);
		$this->AssertArrayHasKey(0, $Keys);
		$this->AssertContains('One', $Keys);

		$Values = $Store->Values();
		$this->AssertInstanceOf($Store::class, $Values);
		$this->AssertCount(4, $Values);
		$this->AssertEquals(1, $Values[0]);
		$this->AssertEquals('Won', $Values[3]);

		$Values = $Store->Values(TRUE);
		$this->AssertIsArray($Values);
		$this->AssertCount(4, $Values);
		$this->AssertEquals(1, $Values[0]);
		$this->AssertEquals('Won', $Values[3]);

		$Store->Revalue();
		$this->AssertEquals(1, $Store[0]);
		$this->AssertEquals('Won', $Store[3]);

		return;
	}

	/** @test */
	public function
	TestMethodAccumulate():
	void {

		$Store = new Datastore([ 1, 2, 3 ]);
		$Result = $Store->Accumulate(
			9000,
			(fn(int $Prev, int $Val)=> $Prev + $Val )
		);

		$this->AssertEquals(9006, $Result);

		return;
	}

	/** @test */
	public function
	TestMethodJoin():
	void {

		$Store = new Datastore([ 1, 2, 3 ]);
		$Result = $Store->Join('-');

		$this->AssertEquals('1-2-3', $Result);

		return;
	}

	/** @test */
	public function
	TestMethodMapKeysRemapKeys():
	void {

		$Store = new Datastore([
			'One'   => 1,
			'Two'   => 2,
			'Three' => 3
		]);

		$Remapper = function($Key, $Val, $Me) {
			return [ "{$Key}{$Val}" => $Val ];
		};

		// remap the keys into a new store.

		$Mapped = $Store->MapKeys($Remapper);

		// check the new store looks right.

		$this->AssertTrue($Mapped->HasKey('One1'));
		$this->AssertEquals(1, $Mapped->Get('One1'));
		$this->AssertFalse($Mapped->HasKey('One'));

		// check the old store is unchanged.

		$this->AssertTrue($Store->HasKey('One'));
		$this->AssertEquals(1, $Store->Get('One'));
		$this->AssertFalse($Store->HasKey('One1'));

		// remap the current store.

		$Store->RemapKeys($Remapper);

		$this->AssertTrue($Store->HasKey('One1'));
		$this->AssertEquals(1, $Store->Get('One1'));
		$this->AssertFalse($Store->HasKey('One'));


		return;
	}

	/** @test */
	public function
	TestMethodShuffle():
	void {

		$Store = new Datastore([ 1, 2, 3, 4, 5, 6 ]);
		$OG = $Store->Join('');

		// test the shuffle making a provision just in case that
		// somehow rng magically shuffled it into the same order
		// roflmao.

		$Attempt = 10;
		$Different = 0;
		$Line = NULL;

		while($Attempt > 0) {
			$Attempt--;

			$Store->Shuffle();
			$Line = $Store->Join('');

			if($Line !== $OG)
			$Different++;
		}

		$this->AssertGreaterThan(0, $Different);

		return;
	}

	/** @test */
	public function
	TestProtectExpose():
	void {

		$Data = [
			'Public1'    => 'public value 1',
			'Public2'    => 'public value 2',
			'Private1'   => 'private value 1',
			'Private2'   => 'private value 2',
			'PrivateInt' => 69
		];

		$Store = new Datastore($Data);

		// test that the store works like normal.

		$Result = $Store->__DebugInfo();
		$this->AssertCount(5, $Result);
		$this->AssertArrayHasKey('Public1', $Result);
		$this->AssertArrayHasKey('Public2', $Result);
		$this->AssertArrayHasKey('Private1', $Result);
		$this->AssertArrayHasKey('Private2', $Result);
		$this->AssertEquals($Result['Private1'], $Data['Private1']);
		$this->AssertEquals($Result['Private2'], $Data['Private2']);

		// test that we can protect sensitive data by replacing it
		// giving it each key as a string.

		$Store
		->Protect('Private1')
		->Protect('Private2')
		->Protect('PrivateInt');

		$Result = $Store->__DebugInfo();
		$this->AssertCount(5, $Result);
		$this->AssertArrayHasKey('Public1', $Result);
		$this->AssertArrayHasKey('Public2', $Result);
		$this->AssertArrayHasKey('Private1', $Result);
		$this->AssertArrayHasKey('Private2', $Result);
		$this->AssertNotEquals($Result['Private1'], $Data['Private1']);
		$this->AssertNotEquals($Result['Private2'], $Data['Private2']);
		$this->AssertArrayHasKey('PrivateInt', $Result);
		$this->AssertNotEquals($Result['PrivateInt'], $Data['PrivateInt']);

		// test that we can remove the protected keys specifying them as
		// individual strings.

		$Store
		->Expose('Private1')
		->Expose('Private2');

		$Result = $Store->__DebugInfo();
		$this->AssertCount(5, $Result);
		$this->AssertArrayHasKey('Public1', $Result);
		$this->AssertArrayHasKey('Public2', $Result);
		$this->AssertArrayHasKey('Private1', $Result);
		$this->AssertArrayHasKey('Private2', $Result);
		$this->AssertEquals($Result['Private1'], $Data['Private1']);
		$this->AssertEquals($Result['Private2'], $Data['Private2']);
		$this->AssertArrayHasKey('PrivateInt', $Result);
		$this->AssertNotEquals($Result['PrivateInt'], $Data['PrivateInt']);

		// test that we can protect sensitive data by replacing it
		// giving an arra of keys.

		$Store->Protect([ 'Private1', 'Private2', 'PrivateInt' ]);

		$Result = $Store->__DebugInfo();
		$this->AssertCount(5, $Result);
		$this->AssertArrayHasKey('Public1', $Result);
		$this->AssertArrayHasKey('Public2', $Result);
		$this->AssertArrayHasKey('Private1', $Result);
		$this->AssertArrayHasKey('Private2', $Result);
		$this->AssertNotEquals($Result['Private1'], $Data['Private1']);
		$this->AssertNotEquals($Result['Private2'], $Data['Private2']);
		$this->AssertArrayHasKey('PrivateInt', $Result);
		$this->AssertNotEquals($Result['PrivateInt'], $Data['PrivateInt']);

		// test that we can remove the protected keys specifying them as
		// an array of keys

		$Store->Expose([ 'Private1', 'Private2' ]);

		$Result = $Store->__DebugInfo();
		$this->AssertCount(5, $Result);
		$this->AssertArrayHasKey('Public1', $Result);
		$this->AssertArrayHasKey('Public2', $Result);
		$this->AssertArrayHasKey('Private1', $Result);
		$this->AssertArrayHasKey('Private2', $Result);
		$this->AssertEquals($Result['Private1'], $Data['Private1']);
		$this->AssertEquals($Result['Private2'], $Data['Private2']);
		$this->AssertArrayHasKey('PrivateInt', $Result);
		$this->AssertNotEquals($Result['PrivateInt'], $Data['PrivateInt']);

		// test that we can completely omit sensitive keys if we dont even
		// want a hint that a value with a name exists.

		$Store
		->Protect('Private1', TRUE)
		->Protect('Private2', TRUE)
		->Protect('PrivateInt', TRUE);

		$Result = $Store->__DebugInfo();
		$this->AssertCount(2, $Result);
		$this->AssertArrayHasKey('Public1', $Result);
		$this->AssertArrayHasKey('Public2', $Result);
		$this->AssertArrayNotHasKey('Private1', $Result);
		$this->AssertArrayNotHasKey('Private2', $Result);
		$this->AssertArrayNotHasKey('PrivateInt', $Result);

		// test that we can flush all of the protected keys.


		$Store->Expose(TRUE);

		$Result = $Store->__DebugInfo();
		$this->AssertCount(5, $Result);
		$this->AssertArrayHasKey('Public1', $Result);
		$this->AssertArrayHasKey('Public2', $Result);
		$this->AssertArrayHasKey('Private1', $Result);
		$this->AssertArrayHasKey('Private2', $Result);
		$this->AssertArrayHasKey('PrivateInt', $Result);
		$this->AssertEquals($Result['Private1'], $Data['Private1']);
		$this->AssertEquals($Result['Private2'], $Data['Private2']);
		$this->AssertEquals($Result['PrivateInt'], $Data['PrivateInt']);

		// test that full debug bypasses all of that.

		$Store->Protect([ 'Private1', 'Private2', 'PrivateInt' ]);
		$Store->SetFullDebug(TRUE);

		$Result = $Store->__DebugInfo();
		$this->AssertTrue(count($Result) > 5);
		$this->AssertArrayHasKey('Private1', $Result["\0*\0Data"]);
		$this->AssertArrayHasKey('Private2', $Result["\0*\0Data"]);
		$this->AssertArrayHasKey('PrivateInt', $Result["\0*\0Data"]);
		$this->AssertEquals($Result["\0*\0Data"]['Private1'], $Data['Private1']);
		$this->AssertEquals($Result["\0*\0Data"]['Private2'], $Data['Private2']);
		$this->AssertEquals($Result["\0*\0Data"]['PrivateInt'], $Data['PrivateInt']);

		return;
	}

	/** @test */
	public function
	TestProtectExposeAndThatTheLanguageRespectsItWhereExpected():
	void {

		$PrintR = function(Datastore $Input) {

			ob_start();
			print_r($Input);
			return ob_get_clean();
		};

		$VarDump = function(Datastore $Input) {

			ob_start();
			var_dump($Input);
			return ob_get_clean();
		};

		$Data = [
			'Public1'  => 'public value 1',
			'Public2'  => 'public value 2',
			'Private1' => 'private value 1',
			'Private2' => 'private value 2'
		];

		$Store = new Datastore($Data);

		////////

		// test that there was no protection.

		$Result = $PrintR($Store);
		$this->AssertStringContainsString('Private1', $Result);
		$this->AssertStringContainsString('Private2', $Result);
		$this->AssertStringContainsString($Data['Private1'], $Result);
		$this->AssertStringContainsString($Data['Private2'], $Result);

		$Result = $VarDump($Store);
		$this->AssertStringContainsString('Private1', $Result);
		$this->AssertStringContainsString('Private2', $Result);
		$this->AssertStringContainsString($Data['Private1'], $Result);
		$this->AssertStringContainsString($Data['Private2'], $Result);

		// test that there was protection.

		$Store->Protect(['Private1', 'Private2']);

		$Result = $PrintR($Store);
		$this->AssertStringContainsString('Private1', $Result);
		$this->AssertStringContainsString('Private2', $Result);
		$this->AssertStringNotContainsString($Data['Private1'], $Result);
		$this->AssertStringNotContainsString($Data['Private2'], $Result);

		$Result = $VarDump($Store);
		$this->AssertStringContainsString('Private1', $Result);
		$this->AssertStringContainsString('Private2', $Result);
		$this->AssertStringNotContainsString($Data['Private1'], $Result);
		$this->AssertStringNotContainsString($Data['Private2'], $Result);

		// test that there was omission.

		$Store->Protect(['Private1', 'Private2'], TRUE);

		$Result = $PrintR($Store);
		$this->AssertStringNotContainsString('Private1', $Result);
		$this->AssertStringNotContainsString('Private2', $Result);
		$this->AssertStringNotContainsString($Data['Private1'], $Result);
		$this->AssertStringNotContainsString($Data['Private2'], $Result);

		$Result = $VarDump($Store);
		$this->AssertStringNotContainsString('Private1', $Result);
		$this->AssertStringNotContainsString('Private2', $Result);
		$this->AssertStringNotContainsString($Data['Private1'], $Result);
		$this->AssertStringNotContainsString($Data['Private2'], $Result);
	}

	/** @test */
	public function
	TestDefine():
	void {

		$Store = new Datastore;

		$Store->Define('Green', 'Bean');
		$this->AssertEquals('Bean', $Store['Green']);

		$Store->Define('Green', 'Pea');
		$this->AssertEquals('Bean', $Store['Green']);

		$Store->Set('Green', 'Machine');
		$this->AssertEquals('Machine', $Store['Green']);

		return;
	}

	/** @test */
	public function
	TestIsTrueFalseNull():
	void {

		$Store = new Datastore;

		$Store['True'] = TRUE;
		$Store['Trueish'] = 'true';
		$Store['False'] = FALSE;
		$Store['Falseish'] = 'false';
		$Store['Null'] = NULL;
		$Store['Nullish'] = 'null';

		// test normal cases.

		$this->AssertTrue($Store->IsTrue('True'));
		$this->AssertTrue($Store->IsTrue('Trueish'));
		$this->AssertFalse($Store->IsFalse('True'));
		$this->AssertFalse($Store->IsFalse('Trueish'));

		$this->AssertTrue($Store->IsFalse('False'));
		$this->AssertTrue($Store->IsFalse('Falseish'));
		$this->AssertFalse($Store->IsTrue('False'));
		$this->AssertFalse($Store->IsTrue('Falseish'));

		$this->AssertTrue($Store->IsNull('Null'));
		$this->AssertTrue($Store->IsNull('Nullish'));
		$this->AssertFalse($Store->IsNull('True'));
		$this->AssertFalse($Store->IsNull('Trueish'));
		$this->AssertFalse($Store->IsNull('False'));
		$this->AssertFalse($Store->IsNull('Falseish'));

		// test undefined cases.

		$this->AssertFalse($Store->IsTrue('TrueUndef'));
		$this->AssertTrue($Store->IsFalse('TrueUndef'));

		$this->AssertFalse($Store->IsTrue('FalseUndef'));
		$this->AssertTrue($Store->IsFalse('FalseUndef'));

		// test changing the meaning of null.

		$this->AssertTrue($Store->IsTrueEnough('TrueUndef'));
		$this->AssertTrue($Store->IsFalseEnough('FalseUndef'));


		return;
	}

	/** @test */
	public function
	TestSortByKeys():
	void {

		$Dataset = [
			'One'   => 1,
			'Two'   => 2,
			'Three' => 3
		];

		$Store = new Datastore;

		// ksort will do a normal sort by default.

		$Store->SetData($Dataset);
		$Store->SortKeys();
		$Array = $Store->Values();
		$this->AssertEquals(1, $Array[0]);
		$this->AssertEquals(3, $Array[1]);
		$this->AssertEquals(2, $Array[2]);

		// test a passed in sort for reverse.

		$Store->SetData($Dataset);
		$Store->SortKeys(fn($A, $B)=> $B <=> $A);
		$Array = $Store->Values();
		$this->AssertEquals(2, $Array[0]);
		$this->AssertEquals(3, $Array[1]);
		$this->AssertEquals(1, $Array[2]);

		// test a defined sorter.

		$Store->SetData($Dataset);
		$Store->SetSorter(fn($A, $B)=> $B <=> $A);
		$Array = $Store->SortKeys()->Values();
		$this->AssertEquals(2, $Array[0]);
		$this->AssertEquals(3, $Array[1]);
		$this->AssertEquals(1, $Array[2]);


		return;
	}

	/** @test */
	public function
	TestDataRef():
	void {

		$Store = new Datastore([1, 2, 3]);

		$Ref = &$Store->GetDataRef();
		$Ref[1] = 9;

		$this->AssertEquals(9, $Store[1]);

		return;
	}

	/** @test */
	public function
	TestNewMergeBlend():
	void {

		$Num = [ 'one'=> 1, 'two'=> 2 ];
		$Rom = [ 'two'=> 'ii', 'three'=> 'iii' ];

		$ExpectedMerge = [ 'one'=> 1, 'two'=> 'ii', 'three'=> 'iii' ];
		$ExpectedBlend = [ 'one'=> 1, 'two'=> '2', 'three'=> 'iii' ];

		$Merge = Datastore::NewMerged($Num, $Rom);
		$Blend = Datastore::NewBlended($Num, $Rom);
		$Key = 0;

		foreach([ 'one', 'two', 'three' ] as $Key) {
			$this->AssertEquals($ExpectedMerge[$Key], $Merge[$Key]);
			$this->AssertEquals($ExpectedBlend[$Key], $Blend[$Key]);
		}

		return;
	}

	/** @test */
	public function
	TestReverse():
	void {

		$Data = new Datastore([ 1, 2, 3 ]);
		$Key = NULL;
		$Val = NULL;

		$Data->Reverse();

		foreach([ 3, 2, 1 ] as $Key => $Val)
		$this->AssertEquals($Val, $Data[$Key]);

		return;
	}

	/** @test */
	public function
	TestFromArray():
	void {

		$Store = Datastore::FromArray([ 'One'=> 1, 'Two'=> 2 ]);

		$this->AssertTrue($Store->HasKey('One'));
		$this->AssertTrue($Store->HasKey('Two'));
		$this->AssertFalse($Store->HasKey('Three'));

		return;
	}

	/** @test */
	public function
	TestFromJSON():
	void {

		$Store = Datastore::FromJSON('{ "One": 1, "Two": 2 }');
		$this->AssertTrue($Store->HasKey('One'));
		$this->AssertTrue($Store->HasKey('Two'));
		$this->AssertFalse($Store->HasKey('Three'));

		$Store = Datastore::FromJSON('oh snap');
		$this->AssertFalse($Store->HasKey('One'));
		$this->AssertFalse($Store->HasKey('Two'));
		$this->AssertFalse($Store->HasKey('Three'));

		return;
	}

	/** @test */
	public function
	TestMapKeyValue():
	void {

		$Store = Datastore::FromArray(array_flip([
			'Zero', 'One', 'Two'
		]));

		// test the mapping returning new.

		$New = $Store->MapKeyValue(fn($K, $V)=> strtolower($K));

		$this->AssertTrue($Store['Zero'] === 0);
		$this->AssertTrue($New['Zero'] === 'zero');

		$this->AssertTrue($Store['Two'] === 2);
		$this->AssertTrue($New['Two'] === 'two');

		// test the mapping modding itself.

		$Store->RemapKeyValue(fn($K, $V)=> strtolower($K));

		$this->AssertTrue($Store['Zero'] === 'zero');
		$this->AssertTrue($Store['Two'] === 'two');

		return;
	}

	/** @test */
	public function
	TestHasAnyKeyValue():
	void {

		$Data = new Datastore([
			'one'   => 1,
			'two'   => 2,
			'three' => 3,
			'four'  => 4
		]);

		$this->AssertTrue($Data->HasAnyKey('one', 'two', 'ten'));
		$this->AssertFalse($Data->HasAnyKey('eight', 'nine', 'ten'));

		$this->AssertTrue($Data->HasAnyValue(1, 2, 10));
		$this->AssertFalse($Data->HasAnyValue(8, 9, 10));

		return;
	}

	/** @test */
	public function
	TestIsList():
	void {

		$List = new Datastore([
			1, 2, 3, 4
		]);

		$Pile = new Datastore([
			'one'   => 1,
			'two'   => 2,
			'three' => 3,
			'four'  => 4
		]);

		$this->AssertTrue($List->IsList());
		$this->AssertFalse($Pile->IsList());

		$List->Remove(2);
		$this->AssertFalse($List->IsList());

		$List->Revalue();
		$this->AssertTrue($List->IsList());

		$Pile->Revalue();
		$this->AssertTrue($Pile->IsList());

		return;
	}

	/** @test */
	public function
	TestImportExportToArray():
	void {

		$Array = [ 1, 2, 3 ];
		$Data = new Datastore;

		// the store should be empty.

		$this->AssertTrue($Data->Count() === 0);

		// the store should be filled.

		$Data->Import($Array);
		$this->AssertTrue($Data->Count() === 3);

		// it should be spitting an array out.

		$this->AssertIsArray($Data->Export());
		$this->AssertIsArray($Data->ToArray());
		$this->AssertTrue(count($Data->Export()) === 3);

		// it should be spitting an empty array out but the original
		// data that was sourced from should remain.

		$Data->Clear();
		$this->AssertTrue(count($Array) === 3);
		$this->AssertTrue($Data->Count() === 0);
		$this->AssertTrue(count($Data->Export()) === 0);

		return;
	}

	/** @test */
	public function
	TestMagicReference():
	void {

		$Data = new Datastore([ 1, 2, 3 ]);
		$Magic = &$Data->Reference();

		// check that the magic worked.

		$this->AssertTrue(is_array($Magic));
		$this->AssertTrue(count($Magic) === 3);

		// check that they are changing the same pointer.

		$Data->Remove(2);
		$this->AssertTrue($Data->Count() === 2);
		$this->AssertTrue(count($Magic) === 2);

		$Magic[] = 'orange';
		$this->AssertTrue(in_array('orange', $Magic));
		$this->AssertTrue($Data->HasValue('orange'));

		$Data->Clear();
		$this->AssertTrue($Data->Count() === 0);
		$this->AssertTrue(count($Magic) === 0);

		return;
	}

}
