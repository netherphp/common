<?php

namespace NetherTestSuite\Common\Prototype;

use Throwable;
use PHPUnit\Framework\TestCase;
use Nether\Common\Datastore;
use Nether\Common\Prototype;
use Nether\Common\Prototype\Flags;
use Nether\Common\Prototype\PropertyInfo;
use Nether\Common\Prototype\MethodInfo;
use Nether\Common\Prototype\ConstructArgs;
use Nether\Common\Meta\PropertyOrigin;
use Nether\Common\Meta\PropertyObjectify;
use Nether\Common\Meta\PropertyFactory;
use Nether\Common\Error\MissingCallableFunc;
use Nether\Common\Error\RequiredDataMissing;

class LocalTest2
extends Prototype {

	#[PropertyOrigin('number_one')]
	public int
	$One;

	#[PropertyOrigin('number_two')]
	public int
	$Two;

	static public function
	New(int $One=NULL, int $Two=NULL, int $Three=NULL):
	static {

		return parent::New($One, $Two, $Three);
	}

	static public function
	NewRelaxed(int $One=NULL, int $Two=NULL, int $Three=NULL):
	static {

		return new static([
			'One'   => $One,
			'Two'   => $Two,
			'Three' => $Three
		], NULL, 0);
	}

};

class LocalTest3
extends Prototype {

	public int
	$TypedProperty;

	public
	$UntypedProperty;

	public function
	TypedMethod():
	int {

		return 1;
	}

	public function
	UntypedMethod() {

		return;
	}

};

class LocalTest4
extends Prototype {

	public ConstructArgs
	$Args;

	protected function
	OnReady(ConstructArgs $Args):
	void {

		$this->Args = $Args;
		return;
	}

};

class LocalTest5
extends Prototype {

	#[PropertyFactory]
	public Datastore
	$Datastore;

};

class LocalTest6
extends Prototype {

	#[PropertyFactory('asdfadsf')]
	public string
	$Value;

};


class LocalTest7
extends Prototype {

	#[PropertyFactory([ LocalTest7::class, 'Value' ], 'Input')]
	public string
	$Value;

	static public function
	Value(string $V):
	string {

		return $V;
	}

};

class PrototypeConstructTest
extends TestCase {

	/** @test */
	public function
	TestEmpty() {
	/*//
	check that when used on its own or with invalid input that we have created
	an object which has no properties.
	//*/

		$Obj = new Prototype;
		$this->AssertTrue(count(get_object_vars($Obj)) === 0);

		return;
	}

	/** @test */
	public function
	TestInput() {
	/*//
	check that if given input that it gets copied into the object as it was
	given to it.
	//*/

		$Key = NULL;
		$Value = NULL;

		$Input = [
			'PropertyOne' => 1,
			'PropertyTwo' => 2
		];

		$Object = new Prototype($Input, NULL, 0);

		foreach($Input as $Key => $Value) {
			$this->AssertTrue(property_exists($Object, $Key));
			$this->AssertEquals($Object->{$Key}, $Value);
		}

		$Object2 = new Prototype($Input);

		foreach($Input as $Key => $Value)
		$this->AssertFalse(property_exists($Object2, $Key));

		return;
	}

	/** @test */
	public function
	TestDefaults() {
	/*//
	check that any properties missing from the input get set to the specified
	default values.
	//*/

		$Key = NULL;
		$Value = NULL;

		$Input = [
			'PropertyOne' => 1,
			'PropertyTwo' => 2
		];

		$Default = [
			'PropertyOne'   => -1,
			'PropertyTwo'   => -2,
			'PropertyThree' => -3
		];

		$Result = $Input + $Default;

		$Object = new Prototype($Input,$Default);
		foreach($Result as $Key => $Value) {
			$this->AssertTrue(property_exists($Object, $Key));
			$this->AssertEquals($Object->{$Key},$Value);
		}

		return;
	}

	/** @test */
	public function
	TestDefaultsStrict() {
	/*//
	check that any properties missing from the input get set to the specified
	default values.
	//*/

		$Key = NULL;
		$Value = NULL;

		$Input = [
			'One' => 1,
			'Two' => 2
		];

		$Default = [
			'One'   => -1,
			'Three' => 3
		];

		$Result = $Input + $Default;

		$Object = new LocalTest2($Input, $Default, Flags::StrictDefault);
		$this->AssertTrue(property_exists($Object, 'One'));
		$this->AssertTrue(property_exists($Object, 'Two'));
		$this->AssertFalse(property_exists($Object, 'Three'));

		$this->AssertEquals($Object->One, 1);
		$this->AssertEquals($Object->Two, 2);


		return;
	}

	/** @test */
	public function
	TestDefaultsWithCulling() {
	/*//
	check that any missing properties missing from the input get set to the
	specified default values, but if it did not have a default then it did
	not get copied in.
	//*/

		$Key = NULL;
		$Value = NULL;

		$Input = [
			'PropertyOne' => 1,
			'PropertyTwo' => 2
		];

		$Default = [
			'PropertyTwo'   => -2,
			'PropertyThree' => -3
		];

		// check the properties that should exist.

		$Result = $Input + $Default;
		unset($Result['PropertyOne']);

		$Object = new Prototype(
			$Input,
			$Default,
			Prototype\Flags::CullUsingDefault
		);

		foreach($Result as $Key => $Value) {
			$this->AssertTrue(property_exists($Object, $Key));
			$this->AssertEquals($Object->{$Key},$Value);
		}

		// make sure that one property does /not/ exist as it should have been
		// culled by not having a key in the default array.

		$this->AssertFalse(property_exists($Object,'PropertyOne'));

		return;
	}

	/** @test */
	public function
	TestObjectInputs() {
	/*//
	check that any properties missing from the input get set to the specified
	default values.
	//*/

		$Key = NULL;
		$Value = NULL;

		$Input = [
			'PropertyOne' => 1,
			'PropertyTwo' => 2
		];

		$Default = [
			'PropertyOne'   => -1,
			'PropertyTwo'   => -2,
			'PropertyThree' => -3
		];

		$Result = $Input + $Default;

		$Object = new Prototype((object)$Input, (object)$Default);
		foreach($Result as $Key => $Value) {
			$this->AssertTrue(property_exists($Object, $Key));
			$this->AssertEquals($Object->{$Key},$Value);
		}

		return;
	}

	/** @test */
	public function
	TestConstructArgsThings() {

		$Input = [
			'PropertyOne' => 1
		];

		$Defaults = [
			'PropertyOne' => -1
		];

		$O1 = new LocalTest4($Input);
		$O2 = new LocalTest4($Input, $Defaults);

		$this->AssertTrue($O1->Args->InputHas('PropertyOne'));
		$this->AssertTrue($O1->Args->InputExists('PropertyOne'));
		$this->AssertTrue($O1->Args->InputGet('PropertyOne') === 1);
		$this->AssertFalse($O1->Args->InputHas('Nope'));
		$this->AssertFalse($O1->Args->InputExists('Nope'));
		$this->AssertNull($O1->Args->InputGet('Nope'));

		$this->AssertFalse($O1->Args->DefaultHas('PropertyOne'));
		$this->AssertFalse($O1->Args->DefaultExists('PropertyOne'));
		$this->AssertNull($O1->Args->DefaultGet('PropertyOne'));
		$this->AssertFalse($O1->Args->DefaultHas('Nope'));
		$this->AssertFalse($O1->Args->DefaultExists('Nope'));
		$this->AssertNull($O1->Args->DefaultGet('Nope'));

		$this->AssertTrue($O2->Args->DefaultHas('PropertyOne'));
		$this->AssertTrue($O2->Args->DefaultExists('PropertyOne'));
		$this->AssertTrue($O2->Args->DefaultGet('PropertyOne') === -1);

		return;
	}

	/** @test */
	public function
	TestTypecasting() {
	/*//
	check that typecasting via the colon syntax gets applied.
	//*/

		$Input = [
			'PropertyInt'    => '1',
			'PropertyFloat'  => '1.234',
			'PropertyString' => 42,
			'PropertyBool1'  => 0,
			'PropertyBool2'  => 1,
			'PropertyBool3'  => 'five',
			'PropertyWhat'   => '42.42',
			'PropertyNullableString' => 'asdf',
			'PropertyNullableNulled' => NULL
		];

		$Object = new class($Input) extends Prototype {
			public int $PropertyInt;
			public float $PropertyFloat;
			public string $PropertyString;
			public bool $PropertyBool1;
			public bool $PropertyBool2;
			public bool $PropertyBool3;
			public mixed $PropertyWhat;
			public ?string $PropertyNullableString;
			public ?string $PropertyNullableNulled;
		};

		//var_dump($Object::GetPropertyMap());

		//$Object = new Prototype($Input);
		$this->AssertTrue($Object->PropertyInt === 1);
		$this->AssertTrue($Object->PropertyFloat === 1.234);
		$this->AssertTrue($Object->PropertyString === '42');
		$this->AssertTrue($Object->PropertyBool1 === FALSE);
		$this->AssertTrue($Object->PropertyBool2 === TRUE);
		$this->AssertTrue($Object->PropertyBool3 === TRUE);
		$this->AssertTrue($Object->PropertyWhat === '42.42');
		$this->AssertTrue($Object->PropertyNullableString === 'asdf');
		$this->AssertTrue($Object->PropertyNullableNulled === NULL);

		return;
	}

	/** @test */
	public function
	TestDefaultsWithTypecasting() {
	/*//
	check that the typecasting worked on defaults as well.
	//*/

		$Input = [];
		$Default = [
			'PropertyFloat' => '9000.1'
		];

		$Object = new class($Input,$Default) extends Prototype {
			public float $PropertyFloat;
		};

		$this->AssertTrue($Object->PropertyFloat === 9000.1);
		return;
	}

	/** @test */
	public function
	TestPropertyObjectify():
	void {
	/*//
	check that methods attributed with PropertyObjectify create new instances
	where wanted.
	//*/

		$Object = new class() extends Prototype {
			#[PropertyObjectify]
			public Datastore $Data;
		};

		$this->AssertInstanceOf(
			'Nether\\Common\\Datastore',
			$Object->Data
		);

		return;
	}

	/** @test */
	public function
	TestGetPropertyMap() {

		$Map = LocalTest2::GetPropertyMap();

		$this->AssertTrue(is_array($Map));
		$this->AssertTrue(count($Map) === 2);
		$this->AssertTrue(array_key_exists('number_one',$Map));
		$this->AssertTrue($Map['number_one'] === 'One');
		$this->AssertTrue(array_key_exists('number_two',$Map));
		$this->AssertTrue($Map['number_two'] === 'Two');

		return;
	}

	/** @test */
	public function
	TestPropertyFactory():
	void {
	/*//
	check that methods attributed with PropertyFactory create new instances
	where wanted.
	//*/

		$Obj = new class() extends Prototype {
			public array $Input = [ 1, 2, 3];

			#[PropertyFactory( 'NewMerged', 'Input' )]
			public Datastore $Data1;

			#[PropertyFactory( 'NewMerged', 'Input', [ 4, 5, 6 ] )]
			public Datastore $Data2;
		};

		$this->AssertInstanceOf('Nether\\Common\\Datastore', $Obj->Data1);
		$this->AssertEquals(3, $Obj->Data1->Count());

		$this->AssertInstanceOf('Nether\\Common\\Datastore', $Obj->Data2);
		$this->AssertEquals(6, $Obj->Data2->Count());

		return;
	}

	/** @test */
	public function
	TestFromArray():
	void {

		$Data = [ 'One'=> 1, 'Two'=> 2 ];
		$Obj = Prototype::FromArray($Data);

		$this->AssertTrue(property_exists($Obj, 'One'));
		$this->AssertTrue(property_exists($Obj, 'Two'));
		$this->AssertTrue($Obj->One === 1);
		$this->AssertTrue($Obj->Two === 2);

		return;
	}

	/** @test */
	public function
	TestPropertyFactoryCallableFail1():
	void {

		// if the callable filter func was not found.

		$Failed = FALSE;
		$Obj = NULL;
		$Error = NULL;

		////////

		try {
			$Obj = new LocalTest6;
		}

		catch(Throwable $Error) {
			$Failed = TRUE;

			$this->AssertInstanceOf(
				MissingCallableFunc::class,
				$Error
			);
		}

		$this->AssertTrue($Failed);

		return;
	}

	/** @test */
	public function
	TestPropertyFactoryCallableFail2():
	void {

		// if a callable filter function was found but the input
		// property was not.

		$Failed = FALSE;
		$Obj = NULL;
		$Error = NULL;

		////////

		try {
			$Obj = new LocalTest7;
		}

		catch(Throwable $Error) {
			$Failed = TRUE;

			$this->AssertInstanceOf(
				RequiredDataMissing::class,
				$Error
			);
		}

		$this->AssertTrue($Failed);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	/** @test */
	public function
	TestThatAllowNullOnNullCallLogicFailWithUntypedProps() {

		// tests a failure in my logic that went unnoticed for a while
		// since i tend to strict type everything, that we ran into at
		// work like a month after the release. it was calling a method
		// a reflection type when there was no type defined gg.

		try {
			$Props = LocalTest3::GetPropertyIndex();
			$Prop = $Props['UntypedProperty'];
		}

		catch(Throwable $E) {
			$this->AssertFalse(TRUE, 'the problem exists.');
		}

		$this->AssertTrue($Prop instanceof PropertyInfo);
		$this->AssertEquals($Prop->Type, 'mixed');
		$this->AssertTrue($Prop->Nullable);

		return;
	}

	/** @test */
	public function
	TestThatAllowNullOnNullCallLogicFailWithUntypedMethods() {

		// tests a failure in my logic that went unnoticed for a while
		// since i tend to strict type everything, that we ran into at
		// work like a month after the release. it was calling a method
		// a reflection type when there was no type defined gg.

		try {
			$Methods = LocalTest3::GetMethodIndex();
			$Method = $Methods['UntypedMethod'];
		}

		catch(Throwable $E) {
			$this->AssertFalse(TRUE, 'the problem exists.');
		}

		$this->AssertTrue($Method instanceof MethodInfo);
		$this->AssertEquals($Method->Type, 'mixed');

		return;
	}

}
