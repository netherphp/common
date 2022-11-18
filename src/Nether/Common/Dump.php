<?php

namespace Nether\Common;

class Dump {

	static public function
	Var(mixed $Input, bool $Pre=FALSE) {

		ob_start();
		var_dump($Input);
		$Output = ob_get_clean();

		$Output = preg_replace(
			'/]=>[\h\s]*/ms',
			"] => ",
			$Output
		);

		if($Pre)
		echo "<pre>{$Output}</pre>";
		else
		echo $Output;

		return;
	}

}
