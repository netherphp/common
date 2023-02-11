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

		$Data = [ 'One'=> 1, 'Two'=> 2 ];
		$Filter = new Datafilter($Data);

		$Filter
		->Zero(fn(DatafilterItem $Item): bool => TRUE)
		->One(fn(DatafilterItem $Item): string => (string)$Item->Value)
		->Two(fn(DatafilterItem $Item): float => (float)$Item->Value);

		$this->AssertIsString($Filter->One);
		$this->AssertTrue($Filter->One === '1');

		$this->AssertIsFloat($Filter->Two);
		$this->AssertTrue($Filter->Two === 2.0);

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

		foreach($Data as $Key => $Val)
		$Data->SetFilter($Key, Datafilters::TypeBool(...));

		foreach($Data as $Key => $Val) {
			if(str_starts_with($Key, 'true'))
			$this->AssertTrue($Val);

			if(str_starts_with($Key, 'false'))
			$this->AssertFalse($Val);

			if(str_starts_with($Key, 'null'))
			$this->AssertFalse($Val);
		}

		////////

		foreach($Data as $Key => $Val)
		$Data->SetFilter($Key, Datafilters::TypeBoolNullable(...));

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

}
