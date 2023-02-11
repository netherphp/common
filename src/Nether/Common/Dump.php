<?php

namespace Nether\Common;

class Dump {

	static public function
	Var(mixed $Input, bool $Pre=FALSE) {

		ob_start();
		var_dump($Input);
		$Output = ob_get_clean();

		// add some space.

		$Output = preg_replace(
			'/]=>[\h\s]*/ms',
			"] => ",
			$Output
		);

		// add some accessiblity.

		$Output = preg_replace_callback(
			'/^[\s\h]+/ms',
			fn($Found)=> str_repeat(chr(9), (int)(strlen($Found[0]) / 2)),
			$Output
		);

		// add preformatting.

		if($Pre)
		echo "<pre>{$Output}</pre>";
		else
		echo $Output;

		return;
	}

}
