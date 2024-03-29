<?php

namespace Nether\Common\Package;

use Nether\Common\Datastore;
use Nether\Common\Prototype\PropertyInfo;
use Nether\Common\Prototype\PropertyInfoCache;
use ReflectionClass;

trait PropertyInfoPackage {

	static public function
	FetchPropertyInfo(string $PropertyName):
	?PropertyInfo {

		$Props = static::FetchPropertyIndex();

		if(isset($Props[$PropertyName]))
		return $Props[$PropertyName];

		return NULL;
	}

	static public function
	GetPropertyInfo(string $PropertyName):
	?PropertyInfo {

		$Props = static::GetPropertyIndex();

		if(isset($Props[$PropertyName]))
		return $Props[$PropertyName];

		return NULL;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FetchPropertyIndex():
	array {
	/*//
	@date 2022-08-08
	return a list of all the properties on this class.
	//*/

		$RefClass = new ReflectionClass(static::class);
		$RefProp = NULL;
		$Info = NULL;
		$Output = [];

		foreach($RefClass->GetProperties() as $RefProp) {
			$Info = new PropertyInfo($RefProp);
			$Output[$Info->Origin] = $Info;
		}

		return $Output;
	}

	static public function
	GetPropertyIndex():
	array {
	/*//
	@date 2022-08-08
	return a list of all the properties on this class using an inline cache
	system if you intend to be asking a lot of meta programming questions.
	this is the preferred method to use in your userland code.
	//*/

		if(PropertyInfoCache::Has(static::class))
		return PropertyInfoCache::Get(static::class);

		return PropertyInfoCache::Set(
			static::class,
			static::FetchPropertyIndex()
		);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FetchPropertiesWithAttribute(string $AttribName):
	array {
	/*//
	@date 2022-08-08
	return a list of properties on this class that are tagged with the
	specified attribute name.
	//*/

		$RefClass = new ReflectionClass(static::class);
		$RefProp = NULL;
		$RefAttrib = NULL;
		$Info = NULL;
		$Output = [];

		foreach($RefClass->GetProperties() as $RefProp) {
			foreach($RefProp->GetAttributes() as $RefAttrib) {
				if($RefAttrib->GetName() === $AttribName) {
					$Info = new PropertyInfo($RefProp);
					$Output[$Info->Name] = $Info;
				}
			}
		}

		return $Output;
	}

	static public function
	GetPropertiesWithAttribute(string $AttribName):
	array {
	/*//
	@date 2022-08-08
	return a list of properties on this class that are tagged with the
	specified attribute name using the inline cache system if you intend to
	be asking a lot of meta programming questions. this is the preferred
	method to use in userland code.
	//*/

		$PropertyMap = NULL;

		////////

		if(PropertyInfoCache::Has(static::class))
		$PropertyMap = PropertyInfoCache::Get(static::class);

		else
		$PropertyMap = static::GetPropertyIndex();

		////////

		return array_filter(
			$PropertyMap,
			function(PropertyInfo $Property) use($AttribName):
			bool {
				$Inst = NULL;

				foreach($Property->Attributes as $Inst) {
					if($Inst instanceof $AttribName)
					return TRUE;
				}

				return FALSE;
			}
		);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	GetPropertyMap():
	array {
	/*//
	@date 2021-08-16
	returns an assoc array keyed with a data source name and values of the
	data destination name. does not include static properties.
	//*/

		$Output = array_map(
			(fn($Val)=> $Val->Name),
			array_filter(
				static::GetPropertyIndex(),
				(fn($Val)=> !$Val->Static)
			)
		);

		return $Output;
	}

}
