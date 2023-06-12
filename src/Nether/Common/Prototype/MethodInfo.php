<?php

namespace Nether\Common\Prototype;

use Nether\Common\Prototype\MethodInfoInterface;
use ReflectionMethod;
use ReflectionNamedType;

class MethodInfo {

	public string
	$Class;

	public string
	$Name;

	public string
	$Type;

	public string
	$Access;

	public bool
	$Static;

	public array
	$Args = [];

	public array
	$Attributes = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(ReflectionMethod $RefMethod) {
	/*//
	@date 2022-08-10
	//*/

		$this->DigestBasic($RefMethod);
		$this->DigestArgs($RefMethod);
		$this->DigestAttribs($RefMethod);

		return;
	}

	protected function
	DigestBasic(ReflectionMethod $RefMethod):
	void {

		$RefType = $RefMethod->GetReturnType();

		$this->Class = $RefMethod->GetDeclaringClass()->GetName();
		$this->Name = $RefMethod->GetName();
		$this->Static = $RefMethod->IsStatic();

		$this->Type = match(TRUE) {
			$RefType instanceof ReflectionNamedType
			=> $RefType->GetName(),

			default
			=> 'mixed'
		};

		$this->Access = match(TRUE) {
			($RefMethod->IsProtected())
			=> 'protected',

			($RefMethod->IsPrivate())
			=> 'private',

			default
			=> 'public'
		};

		return;
	}

	protected function
	DigestArgs(ReflectionMethod $RefMethod):
	void {

		$RefParam = NULL;
		$RefParamType = NULL;
		$RefParamName = NULL;
		$RefParamTypeStr = NULL;

		// check what args this method expects.

		foreach($RefMethod->GetParameters() as $RefParam) {
			$RefParamName = $RefParam->GetName();
			$RefParamType = $RefParam->GetType();

			if($RefParamType instanceof ReflectionNamedType) {
				if($RefParamType->IsBuiltIn())
				$RefParamTypeStr = $RefParamType->GetName();

				elseif(class_exists($RefParamType->GetName()))
				$RefParamTypeStr = $RefParamType->GetName();

				else
				$RefParamTypeStr = 'mixed';
			}

			else {
				$RefParamTypeStr = 'mixed';
			}

			$this->Args[$RefParamName] = $RefParamTypeStr;
		}

		return;
	}

	protected function
	DigestAttribs(ReflectionMethod $RefMethod):
	void {

		$Attrib = NULL;
		$AttribName = NULL;
		$Inst = NULL;

		foreach($RefMethod->GetAttributes() as $Attrib) {
			$AttribName = $Attrib->GetName();
			$Inst = $Attrib->NewInstance();

			////////

			if($Inst instanceof MethodInfoInterface)
			$Inst->OnMethodInfo($this, $RefMethod, $Attrib);

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

	public function
	GetArgsOfType(string $ArgType):
	array {

		return array_keys(array_filter(
			$this->Args,
			(fn(string $Type)=> $Type === $ArgType)
		));
	}

	public function
	CountArgsOfType(string $ArgType):
	int {

		return count(array_filter(
			$this->Args,
			(fn(string $Type)=> $Type === $ArgType)
		));
	}

}
