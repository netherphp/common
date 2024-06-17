<?php

require('vendor/autoload.php');

$C1 = Nether\Common\Units\Colour2::FromHexString('#FFFF00');
Nether\Common\Dump::Var($C1->H());
Nether\Common\Dump::Var($C1->S());
Nether\Common\Dump::Var($C1->L());
echo PHP_EOL;

$C1 = Nether\Common\Units\Colour2::FromHexString('#88FF00');
Nether\Common\Dump::Var($C1->H());
Nether\Common\Dump::Var($C1->S());
Nether\Common\Dump::Var($C1->L());
echo PHP_EOL;

$C1 = Nether\Common\Units\Colour2::FromHexString('#00FF00');
Nether\Common\Dump::Var($C1->H());
Nether\Common\Dump::Var($C1->S());
Nether\Common\Dump::Var($C1->L());
echo PHP_EOL;

//Nether\Common\Dump::Var($C1->Sat());
//Nether\Common\Dump::Var($C1->Lum());
