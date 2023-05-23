<?php

use Oct8pus\QuotedPrintable;

require_once __DIR__ . '/vendor/autoload.php';

$text = <<<TEXT
J'interdis aux marchands de vanter trop leurs marchandises. Car ils se font=
 vite p=C3=A9dagogues et t'enseignent comme but ce qui n'est par essence qu=
'un moyen, et te trompant ainsi sur la route =C3=A0 suivre les voil=C3=
=A0 bient=C3=B4t qui te d=C3=A9gradent, car si leur musique est vulgaire il=
s te fabriquent pour te la vendre une =C3=A2me vulgaire.
   =E2=80=94=E2=80=89Antoine de Saint-Exup=C3=A9ry, Citadelle (1948)
TEXT;

$quotedPrintable = new QuotedPrintable();

echo ($quotedPrintable->validateNoExceptions($text) ? 'valid quoted printable' : 'invalid quoted printable') . PHP_EOL;
