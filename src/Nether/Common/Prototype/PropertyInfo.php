<?php

namespace Nether\Common\Prototype;

use Attribute;
use ReflectionProperty;
use ReflectionNamedType;
use ReflectionUnionType;
use Nether\Common\Meta\PropertyObjectify;
use Nether\Common\Prototype\PropertyInfoInterface;

class PropertyInfo {
/*//
@date 2021-08-09
this class defines everything via pre-processing about a class property that
the prototype system will want to know about.
//*/

	public string
	$Class;

	public string
	$Name;

	public string
	$Type;

	public string
	$Origin;

	public bool
	$Castable;

	public string
	$Access;

	public bool
	$Static;

	public bool
	$Nullable;

	public ?PropertyObjectify
	$Objectify = NULL;

	public array
	$Attributes = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(ReflectionProperty $Prop) {
	/*//
	@date 2021-08-09
	@mopt busyunit, avoid-obj-prop-rw
	//*/

		$Type = $Prop->GetType();
		$Attrib = NULL;
		$AttribName = NULL;
		$Inst = NULL;
		$StrType = 'mixed';
		$Nullable = TRUE;
		$UTypes = NULL;

		// get some various info.

		if($Type instanceof ReflectionUnionType) {
			$UTypes = $Type->GetTypes();

			// for now the only union supported are super basic ones for
			// property promotion support. things like array|Datastore.
			// ReflectionUnionType::getTypes does not return in the same
			// order as they are written for some reason so filter out
			// the builtins and the remainder must be the promotion type.

			if(count($UTypes) === 2) {
				$UTypes = array_values(array_filter(
					$UTypes,
					fn(ReflectionNamedType $T)=> !$T->IsBuiltIn()
				));

				if(count($UTypes) === 1)
				if($UTypes[0] instanceof ReflectionNamedType) {
					$StrType = $UTypes[0]->GetName();
					$Nullable = $UTypes[0]->AllowsNull();
				}
			}
		}

		elseif($Type instanceof ReflectionNamedType) {
			$StrType = $Type->GetName();
			$Nullable = $Type->AllowsNull();
		}

		$this->Class = $Prop->GetDeclaringClass()->GetName();
		$this->Name = $Prop->GetName();
		$this->Type = $StrType;
		$this->Nullable = $Nullable;
		$this->Origin = $this->Name;
		$this->Static = $Prop->IsStatic();
		$this->Access = match(TRUE) {
			($Prop->IsProtected())
			=> 'protected',

			($Prop->IsPrivate())
			=> 'private',

			default
			=> 'public'
		};

		// determine if it can be progamatically typecast.

		$this->Castable = (
			$Type instanceof ReflectionNamedType
			&& $Type->IsBuiltIn()
			&& $StrType !== 'mixed'
		);

		foreach($Prop->GetAttributes() as $Attrib) {
			$AttribName = $Attrib->GetName();
			$Inst = $Attrib->NewInstance();

			////////

			if($Inst instanceof PropertyInfoInterface)
			$Inst->OnPropertyInfo($this, $Prop, $Attrib);

			////////

			if($Attrib->IsRepeated()) {
				if(!isset($this->Attributes[$AttribName]))
				$this->Attributes[$AttribName] = [];

				$this->Attributes[$AttribName][] = $Inst;
			}

			else {
				$this->Attributes[$AttribName] = $Inst;
			}
		}

		return;
	}

	public function
	HasAttribute(string $Type):
	bool {

		return isset($this->Attributes[$Type]);
	}

	public function
	GetAttribute(string $Type):
	mixed {

		if(isset($this->Attributes[$Type]))
		return $this->Attributes[$Type];

		return NULL;
	}

	public function
	GetAttributes(?string $Type=NULL):
	array {

		if($Type === NULL)
		return $this->Attributes;

		////////

		$Output = $this->GetAttribute($Type);

		if(is_array($Output))
		return $Output;

		if($Output)
		return [ $Output ];

		return [ ];
	}

}
