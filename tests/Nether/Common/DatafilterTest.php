<?php

namespace Nether\Common;

use Exception;
use PHPUnit\Framework\TestCase;
use Nether\Common\Datafilter;
use Nether\Common\Struct\DatafilterItem;

class DatafilterTest
extends TestCase {

	/** @test */
	public function
	TestReadWrite():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2 ];
		$Filter = new Datafilter($Data);

		// basic reading.

		$this->AssertTrue($Filter->Exists('One'));
		$this->AssertFalse($Filter->Exists('Three'));

		$this->AssertIsInt($Filter->One);
		$this->AssertIsInt($Filter->Two);
		$this->AssertEquals(1, $Filter->One);
		$this->AssertEquals(2, $Filter->Two);
		$this->AssertNull($Filter->Zero);

		// basic writing.

		$Filter->Three = 3;
		$this->AssertIsInt($Filter->Three);
		$this->AssertEquals(3, $Filter->Three);

		// test cache clear on write.

		$Filter['Three'] = 3;
		$Filter->Three(fn($Item)=> (float)$Item->Value);
		$this->AssertIsFloat($Filter->Three);
		$this->AssertEquals(3, $Filter->Three);
		$this->AssertTrue($Filter->CacheHas('Three'));

		// test countable

		$this->AssertEquals(3, count($Filter));

		// test offset Get and Unset

		$this->AssertTrue(isset($Filter['One']));

		unset($Filter['One']);
		$this->AssertFalse(isset($Filter['One']));
		$this->AssertNull($Filter['One']);

		return;
	}

	/** @test */
	public function
	TestFiltering():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2, 'Three'=> 3 ];
		$Filter = new Datafilter($Data);

		$Filter
		->Zero(fn(DatafilterItem $Item): bool => TRUE)
		->One(fn(DatafilterItem $Item): string => (string)$Item->Value)
		->Two(fn(DatafilterItem $Item): float => (float)$Item->Value)
		->Three(fn(DatafilterItem $Item): int => $Item());

		$this->AssertIsString($Filter->One);
		$this->AssertTrue($Filter->One === '1');

		$this->AssertIsFloat($Filter->Two);
		$this->AssertTrue($Filter->Two === 2.0);

		$this->AssertIsInt($Filter->Three);
		$this->AssertTrue($Filter->Three === 3);

		$this->AssertTrue($Filter->Zero);

		ob_start();
		var_dump($Filter);
		$Buffer = ob_get_clean();

		return;
	}

	/** @test */
	public function
	TestIteration():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2, 'Three'=> 3, 'Four'=> 4 ];
		$Keys = array_keys($Data);

		$Filter = new Datafilter($Data);
		$Key = NULL;
		$Val = NULL;

		$Iter = 1;
		foreach($Filter as $Key => $Val) {
			$this->AssertEquals($Val, $Iter);
			$this->AssertEquals($Key, strtolower($Keys[$Iter - 1]));

			$Iter++;
		}

		$Iter = 1;
		foreach($Data as $Key => $Val) {
			$this->AssertEquals($Filter[$Key], $Iter);

			$Iter++;
		}

		return;
	}

	/** @test */
	public function
	TestUncallable():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2, 'Three'=> 3, 'Four'=> 4 ];
		$Filter = new Datafilter($Data);
		$HadExcept = FALSE;

		try {
			$Filter->One('func_does_not_exist_my_dudes');
		}

		catch(Exception $Err) {
			$HadExcept = TRUE;
		}

		$this->AssertTrue($HadExcept);

		return;
	}

	/** @test */
	public function
	TestCallableWithMoreArgs():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2, 'Three'=> 3, 'Four'=> 4 ];
		$Filter = new Datafilter($Data);
		$Key = NULL;
		$Val = NULL;
		$MinMax = function(DatafilterItem $Item, int $Min, int $Max) {
			if($Item->Value < $Min)
			return $Min;

			if($Item->Value > $Max)
			return $Max;

			return $Item->Value;
		};

		$Filter->One($MinMax, 2, 3);
		$Filter->Two($MinMax, 2, 3);
		$Filter->Three($MinMax, 2, 3);
		$Filter->Four($MinMax, 2, 3);

		foreach($Filter as $Key => $Val)
		$this->AssertEquals(
			match($Key){
				'one'   => 2,
				'two'   => 2,
				'three' => 3,
				'four'  => 3
			},
			$Val
		);

		return;
	}

	/** @test */
	public function
	TestCaseSensitivity():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2, 'Three'=> 3, 'Four'=> 4 ];
		$Filter = new Datafilter($Data, Case: TRUE);

		$this->AssertEquals(1, $Filter->One);
		$this->AssertNull($Filter->one);

		$Filter->SetCaseSensitive(FALSE);
		$this->AssertEquals(1, $Filter->One);
		$this->AssertEquals(1, $Filter->one);

		return;
	}

	/** @test */
	public function
	TestCacheToggle():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2, 'Three'=> 3, 'Four'=> 4 ];

		$Filter = new Datafilter($Data, Cache: FALSE);
		$this->AssertEquals(1, $Filter->One);
		$this->AssertFalse($Filter->CacheHas('One'));

		$Filter->SetCacheOutput(TRUE);
		$this->AssertEquals(1, $Filter->One);
		$this->AssertTrue($Filter->CacheHas('One'));

		$Filter->SetCacheOutput(FALSE);
		$this->AssertEquals(1, $Filter->One);
		$this->AssertFalse($Filter->CacheHas('One'));

		return;
	}

	/** @test */
	public function
	TestQueryString():
	void {

		$Data = new Datafilter([
			'One'   => 1,
			'Two'   => 2,
			'Three' => 'Three'
		]);

		$Exp = 'one=1&two=2&three=Three';

		$this->AssertEquals($Exp, $Data->GetQueryString());

		return;
	}

	/** @test */
	public function
	TestStackedFilters():
	void {

		$Dataset = [ 'Who'=> 'Geordi' ];
		$Filters = new Datafilter($Dataset);

		$Filters
		->Who(fn(DatafilterItem $V)=> strtolower($V->Value))
		->Who(fn(DatafilterItem $V)=> strrev($V->Value));

		$this->AssertEquals('idroeg', $Filters->Who);

		return;
	}

	/** @test */
	public function
	TestStackedFilters2():
	void {

		$Dataset = [ 'Who'=> 'Geordi' ];
		$Filters = new Datafilter($Dataset);

		// handle giving it an array of callables.

		$Filters
		->Who([
			fn(DatafilterItem $V)=> strtolower($V->Value),
			fn(DatafilterItem $V)=> strrev($V->Value)
		]);

		$this->AssertEquals('idroeg', $Filters->Who);

		return;
	}

	/** @test */
	public function
	TestStackedFilters3():
	void {

		$Dataset = [ 'Who'=> 'Geordi' ];
		$Filters = new Datafilter($Dataset);

		// handle giving it an array of array descripters of callables with args.

		$Filters
		->Who([
			[ fn(DatafilterItem $V, string $L)=> strtolower("{$L}: {$V->Value}"), 'Person' ],
			[ fn(DatafilterItem $V)=> strrev($V->Value) ]
		]);

		$this->AssertEquals('idroeg :nosrep', $Filters->Who);

		return;
	}

	public function
	TestStackedFilters4():
	void {

		$Dataset = [ 'Who'=> 'Geordi' ];
		$Filters = new Datafilter($Dataset);

		// handle mix-matching the verbose inputs.

		$Filters
		->Who([
			[ fn(DatafilterItem $V, string $L)=> strtolower("{$L}: {$V->Value}"), 'Person' ],
			fn(DatafilterItem $V)=> strrev($V->Value)
		]);

		$this->AssertEquals('idroeg :nosrep', $Filters->Who);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/** @test */
	public function
	TestFiltersNumeric():
	void {

		$Data = new Datafilter([
			'Zero' => '0',
			'One' =>  '1',
			'Two' =>  '2'
		], Cache: FALSE);

		$this->AssertIsString($Data->Zero);
		$this->AssertIsString($Data->One);
		$this->AssertIsString($Data->Two);
		$this->AssertNull($Data->Three);

		////////

		$Data
		->Zero(Datafilters::TypeInt(...))
		->One(Datafilters::TypeInt(...))
		->Two(Datafilters::TypeInt(...))
		->Three(Datafilters::TypeInt(...));


		$this->AssertIsInt($Data->Zero);
		$this->AssertIsInt($Data->One);
		$this->AssertIsInt($Data->Two);
		$this->AssertIsInt($Data->Three);
		$this->AssertEquals(0, $Data->Three);

		$Data
		->Zero(Datafilters::TypeIntNullable(...))
		->One(Datafilters::TypeIntNullable(...))
		->Two(Datafilters::TypeIntNullable(...))
		->Three(Datafilters::TypeIntNullable(...));

		$this->AssertNull($Data->Zero);
		$this->AssertIsInt($Data->One);
		$this->AssertIsInt($Data->Two);
		$this->AssertNull($Data->Three);

		////////

		$Data
		->Zero(Datafilters::TypeFloat(...))
		->One(Datafilters::TypeFloat(...))
		->Two(Datafilters::TypeFloat(...))
		->Three(Datafilters::TypeFloat(...));

		$this->AssertIsFloat($Data->Zero);
		$this->AssertIsFloat($Data->One);
		$this->AssertIsFloat($Data->Two);
		$this->AssertIsFloat($Data->Three);
		$this->AssertEquals(0.0, $Data->Three);

		$Data
		->Zero(Datafilters::TypeFloatNullable(...))
		->One(Datafilters::TypeFloatNullable(...))
		->Two(Datafilters::TypeFloatNullable(...))
		->Three(Datafilters::TypeFloatNullable(...));

		$this->AssertNull($Data->Zero);
		$this->AssertIsFloat($Data->One);
		$this->AssertIsFloat($Data->Two);
		$this->AssertNull($Data->Three);

		return;
	}

	/** @test */
	public function
	TestFiltersTrulean():
	void {

		$Dataset = [
			'TrueC'     => TRUE,
			'FalseC'    => FALSE,
			'NullC'     => NULL,
			'TrueOne1'  => '1',
			'TrueOne2'  => 1,
			'TrueT1'    => 'T',
			'TrueT2'    => 't',
			'TrueTRUE1' => 'TRUE',
			'TrueTRUE2' => 'true',
			'TrueY1'    => 'Y',
			'TrueY2'    => 'y',
			'TrueYes1'  => 'YES',
			'TrueYes2'  => 'yes',
			'FalseOne1'  => '0',
			'FalseOne2'  => 0,
			'FalseT1'    => 'f',
			'FalseT2'    => 'f',
			'FalseTRUE1' => 'FALSE',
			'FalseTRUE2' => 'false',
			'FalseY1'    => 'n',
			'FalseY2'    => 'n',
			'FalseYes1'  => 'NO',
			'FalseYes2'  => 'no',
			'NullNULL1'  => 'NULL',
			'NullNULL2'  => 'null'

		];

		$Data = new Datafilter($Dataset, Cache: FALSE);
		$Key = NULL;
		$Val = NULL;

		////////

		foreach($Data as $Key => $Val) {
			$Data->ResetFilters($Key);
			$Data->SetFilter($Key, Datafilters::TypeBool(...));
		}

		foreach($Data as $Key => $Val) {
			if(str_starts_with($Key, 'true'))
			$this->AssertTrue($Val);

			if(str_starts_with($Key, 'false'))
			$this->AssertFalse($Val);

			if(str_starts_with($Key, 'null'))
			$this->AssertFalse($Val);
		}

		////////

		foreach($Data as $Key => $Val) {
			$Data->ResetFilters($Key);
			$Data->SetFilter($Key, Datafilters::TypeBoolNullable(...));
		}

		foreach($Data as $Key => $Val) {
			if(str_starts_with($Key, 'true'))
			$this->AssertTrue($Val);

			if(str_starts_with($Key, 'false'))
			$this->AssertFalse($Val);

			if(str_starts_with($Key, 'null'))
			$this->AssertNull($Val);
		}

		return;
	}

	/** @test */
	public function
	TestFiltersStrings():
	void {

		$Data = new Datafilter([
			'One1'   => 1,
			'One2'   => 1.0,
			'One3'   => 1.25,
			'Zero1'  => 0,
			'Zero2'  => 0.0,
			'Zero3'  => '0.0',
			'True1'  => TRUE,
			'False1' => FALSE,
			'Null1'  => NULL
		], Cache: FALSE);

		$Key = NULL;
		$Val = NULL;

		////////

		foreach($Data as $Key => $Val)
		$Data->SetFilter($Key, Datafilters::TypeString(...));

		foreach($Data as $Key => $Val)
		$this->AssertIsString($Val);

		$this->AssertEquals('1', $Data->One1);
		$this->AssertEquals('1', $Data->One2);
		$this->AssertEquals('1.25', $Data->One3);
		$this->AssertEquals('0', $Data->Zero1);
		$this->AssertEquals('0', $Data->Zero2);
		$this->AssertEquals('0.0', $Data->Zero3);
		$this->AssertEquals('1', $Data->True1);
		$this->AssertEquals('', $Data->False1);
		$this->AssertEquals('', $Data->Null1);

		////////

		foreach($Data as $Key => $Val)
		$Data->SetFilter($Key, Datafilters::TypeStringNullable(...));

		$this->AssertEquals('1', $Data->One1);
		$this->AssertEquals('1', $Data->One2);
		$this->AssertEquals('1.25', $Data->One3);
		$this->AssertNull($Data->Zero1);
		$this->AssertNull($Data->Zero2);
		$this->AssertEquals('0.0', $Data->Zero3);
		$this->AssertEquals('1', $Data->True1);
		$this->AssertNull($Data->False1);
		$this->AssertNull($Data->Null1);

		return;
	}

	/** @test */
	public function
	TestFiltersBase64():
	void {

		$StringOG = 'yee dudes this is an test';
		$StringEnc = 'eWVlIGR1ZGVzIHRoaXMgaXMgYW4gdGVzdA==';
		$StringGud = 'eWVlIGR1ZGVzIHRoaXMgaXMgYW4gdGVzdA';

		$this->AssertEquals($StringEnc, base64_encode($StringOG));
		$this->AssertEquals($StringGud, Datafilters::Base64Encode($StringOG));
		$this->AssertEquals($StringOG, Datafilters::Base64Decode($StringEnc));
		$this->AssertEquals($StringOG, Datafilters::Base64Decode($StringGud));

		$StringOGB = sprintf(
			'%s%s%s%s',
			chr(0b11111100), chr(0), chr(0),
			chr(0b11111000), chr(0), chr(0)
		);
		$StringEncB = '/AAA+A==';
		$StringGudB = '_AAA-A';

		$this->AssertEquals($StringEncB, base64_encode($StringOGB));
		$this->AssertEquals($StringGudB, Datafilters::Base64Encode($StringOGB));
		$this->AssertEquals($StringOGB, Datafilters::Base64Decode($StringEncB));
		$this->AssertEquals($StringOGB, Datafilters::Base64Decode($StringGudB));

		return;
	}

	/** @test */
	public function
	TestFiltersTrimmedText():
	void {

		$Dataset = [
			'This is text with end space.  ',
			'  This is text with start space.',
			'  '
		];

		$Data = new Datafilter($Dataset, Cache: FALSE);

		$Key = NULL;
		$Val = NULL;

		////////

		foreach($Data as $Key => $Val)
		$Data->SetFilter($Key, Datafilters::TrimmedText(...));

		foreach($Data as $Key => $Val) {
			$this->AssertTrue(!str_starts_with($Val, ' '));
			$this->AssertTrue(!str_ends_with($Val, ' '));

			$this->AssertEquals(
				(strlen($Dataset[$Key]) - 2),
				strlen($Val)
			);
		}

		foreach($Data as $Key => $Val)
		$Data->SetFilter($Key, Datafilters::TrimmedTextNullable(...));

		foreach($Data as $Key => $Val) {
			if(trim($Dataset[$Key]) === '') {
				$this->AssertNull($Val);
				continue;
			}

			$this->AssertTrue(!str_starts_with($Val, ' '));
			$this->AssertTrue(!str_ends_with($Val, ' '));

			$this->AssertEquals(
				(strlen($Dataset[$Key]) - 2),
				strlen($Val ?: '')
			);

			if($Key === 2)
			$this->AssertNull($Val);
			else
			$this->AssertNotNull($Val);
		}

		return;
	}

	/** @test */
	public function
	TestFiltersEncodedText():
	void {

		$StringOG = '<script type="module">jQuery(function(){});</script>';
		$StringEnc = '&lt;script type=&quot;module&quot;&gt;jQuery(function(){});&lt;/script&gt;';

		$this->AssertEquals(
			$StringEnc,
			Datafilters::EncodedText($StringOG)
		);

		return;
	}

	/** @test */
	public function
	TestFiltersStrippedText():
	void {

		$StringOG = '<script type="module">jQuery(function(){});</script>';
		$StringEnc = 'jQuery(function(){});';

		$this->AssertEquals(
			$StringEnc,
			Datafilters::StrippedText($StringOG)
		);

		return;
	}

	/** @test */
	public function
	TestFiltersEmail():
	void {

		$Dataset = [
			'bmajdak@php.net' => TRUE,
			'bob@localhost'   => FALSE, // grr.
			'bob'             => FALSE,
			'@php.net'        => FALSE,
			'@localhost'      => FALSE,
			'42'              => FALSE
		];

		$Email = NULL;
		$Valid = NULL;

		foreach($Dataset as $Email => $Valid) {
			if($Valid)
			$this->AssertEquals($Email, Datafilters::Email($Email));
			else
			$this->AssertNull(Datafilters::Email($Email));
		}

		return;
	}

	/** @test */
	public function
	TestFiltersPathableKey():
	void {

		$Dataset = [
			'This Is An Test'       => 'this-is-an-test',
			'this is an test'       => 'this-is-an-test',
			'this is/an test'       => 'this-is/an-test',
			'this  is / an  test'   => 'this-is/an-test',
			'this-is-an-test.jpg'   => 'this-is-an-test.jpg',
			'this-is-an-test..jpg'  => 'this-is-an-test.jpg',
			'this/../../is-an-test' => 'this/is-an-test',
			'this/../is-an-test'    => 'this/is-an-test',
			'this/is//an///test.ok' => 'this/is/an/test.ok'
		];

		$Old = NULL;
		$New = NULL;

		foreach($Dataset as $Old => $New)
		$this->AssertEquals($New, Datafilters::PathableKey($Old));

		return;
	}

	/** @test */
	public function
	TestFiltersSlottableKey():
	void {

		$Dataset = [
			'This Is An Test'       => 'this-is-an-test',
			'this is an test'       => 'this-is-an-test',
			'this is/an test'       => 'this-is-an-test',
			'this  is / an  test'   => 'this-is-an-test',
			'this-is-an-test.jpg'   => 'this-is-an-test.jpg',
			'this-is-an-test..jpg'  => 'this-is-an-test.jpg',
			'this/../../is-an-test' => 'this-is-an-test',
			'this/../is-an-test'    => 'this-is-an-test',
			'this/is//an///test.ok' => 'this-is-an-test.ok'
		];

		$Old = NULL;
		$New = NULL;

		foreach($Dataset as $Old => $New)
		$this->AssertEquals($New, Datafilters::SlottableKey($Old));

		return;
	}

	/** @test */
	public function
	TestFiltersPascalFromKey():
	void {

		$Dataset = [
			'this-is-an-test'   => 'ThisIsAnTest',
			'thisIsAnTest'      => 'ThisIsAnTest',
			'ThisIsAnTest'      => 'ThisIsAnTest',
			'This Is An Test'   => 'ThisIsAnTest',
			'This Is. An Test.' => 'ThisIsAnTest'

		];

		$Old = NULL;
		$New = NULL;

		foreach($Dataset as $Old => $New)
		$this->AssertEquals($New, Datafilters::PascalFromKey($Old));

		return;
	}

	/** @test */
	public function
	TestFiltersArrayOf():
	void {

		$Array = Datafilters::ArrayOf(NULL);
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertEquals(NULL, $Array[0]);

		$Array = Datafilters::ArrayOf('');
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertEquals('', $Array[0]);

		$Array = Datafilters::ArrayOf(0);
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertEquals(0, $Array[0]);

		$Array = Datafilters::ArrayOf('42');
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertEquals('42', $Array[0]);

		////////

		$Array = Datafilters::ArrayOf(['42']);
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertIsString($Array[0]);

		$Array = Datafilters::ArrayOf(['42'], Datafilters::TypeInt(...));
		$this->AssertTrue(is_array($Array));
		$this->AssertEquals(1, count($Array));
		$this->AssertIsInt($Array[0]);

		return;
	}

	/** @test */
	public function
	TestFiltersUUID():
	void {

		$Dataset = [
			42                                     => FALSE,
			'fourty-two'                           => FALSE,
			'1eb04d3f-4302-69d8-95a7-b39455551a19' => TRUE,
			'1eb04d3f-4302-69d8-95a7-z39455551a19' => FALSE
		];

		$Dataset[UUID::V4()] = TRUE;
		$Dataset[UUID::V7()] = TRUE;

		$Input = NULL;
		$Expect = NULL;

		foreach($Dataset as $Input => $Expect) {
			$UUID = Datafilters::UUID($Input);

			if(!$Expect) {
				$this->AssertNull($UUID);
				continue;
			}

			$this->AssertEquals($Input, $UUID);
		}

		return;
	}

	/** @test */
	public function
	TestFiltersPageNumber():
	void {

		$Dataset = [
			-42         => 1,
			-1          => 1,
			0           => 1,
			1           => 1,
			42          => 42,
			'-300'      => 1,
			'cheese'    => 1,
			'false'     => 1,
			'0'         => 1,
			'1'         => 1,
			'42'        => 42,
			PHP_INT_MAX => PHP_INT_MAX
		];

		$Input = NULL;
		$Expect = NULL;
		$Result = NULL;

		foreach($Dataset as $Input => $Expect)
		$this->AssertEquals($Expect, Datafilters::PageNumber($Input));

		return;
	}

	/** @test */
	public function
	TestFiltersTypeIntRange():
	void {

		$Dataset = [
			-42         => -32,
			-1          => -1,
			0           => 0,
			1           => 1,
			42          => 32,
			'-300'      => -32,
			'cheese'    => 0,
			'false'     => 0,
			'0'         => 0,
			'1'         => 1,
			'42'        => 42,
			'900d9'     => 32,
			PHP_INT_MAX => 32
		];

		$Input = NULL;
		$Expect = NULL;
		$Result = NULL;

		foreach($Dataset as $Input => $Expect)
		$this->AssertEquals($Expect, Datafilters::TypeIntRange($Input, -32, 32, 42));

		return;
	}

	/** @test */
	public function
	TestFilterWebsiteURL():
	void {

		$Dataset = [
			'https://pegasusgate.net' => 'https://pegasusgate.net',
			'http://pegasusgate.net'  => 'http://pegasusgate.net',
			'pegasusgate.net'         => 'http://pegasusgate.net'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Datafilters::WebsiteURL($In));

		return;
	}

	/** @test */
	public function
	TestFilterFacebookURL():
	void {

		$Dataset = [
			'https://facebook.com/facebook' => 'https://facebook.com/facebook',
			'facebook'                      => 'https://facebook.com/facebook',
			'facebook.com/facebook'         => 'http://facebook.com/facebook'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Datafilters::FacebookURL($In));

		return;
	}

	/** @test */
	public function
	TestFilterTwitterURL():
	void {

		$Dataset = [
			'https://twitter.com/twitter' => 'https://twitter.com/twitter',
			'twitter'                     => 'https://twitter.com/twitter',
			'@twitter'                    => 'https://twitter.com/twitter',
			'twitter.com/twitter'         => 'http://twitter.com/twitter'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Datafilters::TwitterURL($In));

		return;
	}

	/** @test */
	public function
	TestFilterInstagramURL():
	void {

		$Dataset = [
			'https://instagram.com/instagram' => 'https://instagram.com/instagram',
			'instagram'                       => 'https://instagram.com/instagram',
			'@instagram'                      => 'https://instagram.com/instagram',
			'instagram.com/instagram'         => 'http://instagram.com/instagram'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Datafilters::InstagramURL($In));

		return;
	}

	/** @test */
	public function
	TestFilterTikTokURL():
	void {

		$Dataset = [
			'https://tiktok.com/@tiktok' => 'https://tiktok.com/@tiktok',
			'tiktok'                     => 'https://tiktok.com/@tiktok',
			'@tiktok'                    => 'https://tiktok.com/@tiktok',
			'tiktok.com/@tiktok'         => 'http://tiktok.com/@tiktok'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Datafilters::TikTokURL($In));

		return;
	}

	/** @test */
	public function
	TestFilterYouTubeURL():
	void {

		$Dataset = [
			'https://youtube.com/channel/youtube' => 'https://youtube.com/channel/youtube',
			'youtube'                             => 'https://youtube.com/channel/youtube',
			'@youtube'                            => 'https://youtube.com/channel/youtube',
			'youtube.com/channel/youtube'         => 'http://youtube.com/channel/youtube'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Datafilters::YouTubeURL($In));

		return;
	}

}
