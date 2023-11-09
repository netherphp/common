<?php

namespace Nether\Common;

use Nether\Common\Prototype\Flags;
use Nether\Common\Prototype\ConstructArgs;

#[Meta\Date('2021-08-05')]
#[Meta\Info('Provides a self-sealing stem object designed to take data dumps and consume their content into itself.')]
class Prototype
implements
	Interfaces\ClassInfoPackage,
	Interfaces\PropertyInfoPackage,
	Interfaces\MethodInfoPackage {

	use
	Package\ClassInfoPackage,
	Package\PropertyInfoPackage,
	Package\MethodInfoPackage;

	public function
	__Construct(array|object|NULL $Raw=NULL, array|object|NULL $Defaults=NULL, ?int $Flags=NULL) {
	/*//
	@date 2021-08-05
	@mopt busyunit, isset, avoid-obj-prop-rw
	//*/

		// this constructor is going to do the bulk of the work to avoid
		// polluting the base object with excess properties and methods
		// that could get sucked up by documentation systems while being
		// pointless.

		// there has also been a lot of micro-optimizations made in this
		// entire flow in regards to the read and write speed of object
		// properties and checking if a key exists.

		if(is_object($Raw))
		$Raw = (array)$Raw;

		if(is_object($Defaults))
		$Defaults = (array)$Defaults;

		$Flags ??= Flags::StrictInput;

		////////

		$Properties = static::GetPropertyIndex();
		$StrictDefaults = ($Flags & Flags::StrictDefault) !== 0;
		$CullUsingDefaults = ($Flags & Flags::CullUsingDefault) !== 0;
		$StrictInput = ($Flags & Flags::StrictInput) !== 0;

		$Src = NULL;
		$Val = NULL;
		$Key = NULL;

		// loop over the default data for population.

		if($Defaults !== NULL)
		foreach($Defaults as $Src => $Val) {
			if($StrictDefaults && !property_exists($this, $Src))
			continue;

			$this->{$Src} = $Val;
		}

		// loop over the supplied data for population.

		if($Raw !== NULL)
		foreach($Raw as $Src => $Val) {
			// start off writing to the same property its keyed to by
			// default.

			$Key = $Src;

			if(isset($Properties[$Src])) {
				// if there is an attribute for the source property
				// update the destination property name.

				$Key = $Properties[$Src]->Name;

				// check if the value needs to be typecast.

				if($Properties[$Src]->Castable)
				if($Val !== NULL || !$Properties[$Src]->Nullable)
				settype($Val, $Properties[$Src]->Type);
			}

			// if StrictInput then do not assign any properties that
			// are not hardcoded on the class.

			if($StrictInput)
			if(!property_exists($this,$Key))
			continue;

			// if CullUsingDefaults then do not assign any properties
			// that are not also mapped in the defaults. honestly i think
			// this is stupid and might be removed.

			if($CullUsingDefaults)
			if(!array_key_exists($Key,$Defaults))
			continue;

			$this->{$Key} = $Val;
		}

		// apply any follow up attribute demands.

		$Src = NULL;
		$Val = NULL;
		$Key = NULL;

		foreach($Properties as $Src => $Val) {
			if($Val->Objectify instanceof Meta\PropertyObjectify) {
				if($Val->Objectify instanceof Meta\PropertyFactory) {

					$Key = $Val->Objectify->Source ?? $Val->Name;
					$Callable = $Val->Objectify->Callable;

					if(!is_callable($Callable))
					$Callable = "{$Properties[$Src]->Type}::{$Val->Objectify->Callable}";

					if(!is_callable($Callable))
					throw new Error\MissingCallableFunc;

					if(!property_exists($this, $Key))
					throw new Error\RequiredDataMissing(
						$Key, 'Object Property'
					);

					$this->{$Val->Name} = ($Callable)(
						(isset($this->{$Key}) ? $this->{$Key} : NULL),
						...$Val->Objectify->Args
					);
				}

				else
				$this->{$Val->Name} = new ($Val->Type)(
					...$Val->Objectify->Args
				);
			}
		}

		// as handy as it was to create this ConstructArgs first thing
		// and have it do the sanitization and stuff i had to refactor this
		// to micro-optimize by avoiding to access object members. having
		// this up there makes it too obviously tempting to use it instead
		// of creating local variables.

		$this->OnReady(new ConstructArgs(
			$Raw,
			$Defaults,
			$Flags,
			$Properties
		));

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(ConstructArgs $Args):
	void {
	/*//
	@date 2021-08-09
	this can/should overriden by children to add construct-time
	processing.
	//*/

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	New():
	static {
	/*//
	@date 2021-09-09
	provides the most extreme generic support for using named properties
	instead of an array to build your objects. implies the strict input
	flag as well so new properties are not created. if you would like your
	code completion to be able to suggest the arguments you would need
	to override this method with one of your own.
	//*/

		return new static(
			func_get_args(),
			NULL,
			Prototype\Flags::StrictInput
		);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\Date('2023-07-26')]
	#[Meta\Info('Factory for creating new instances using an array.')]
	static public function
	FromArray(iterable $Input):
	static {

		return new static($Input, NULL, 0);
	}

}
