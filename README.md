# quoted printable

[![packagist](https://poser.pugx.org/8ctopus/quoted-printable/v)](https://packagist.org/packages/8ctopus/quoted-printable)
[![downloads](https://poser.pugx.org/8ctopus/quoted-printable/downloads)](https://packagist.org/packages/8ctopus/quoted-printable)
[![min php version](https://poser.pugx.org/8ctopus/quoted-printable/require/php)](https://packagist.org/packages/8ctopus/quoted-printable)
[![license](https://poser.pugx.org/8ctopus/quoted-printable/license)](https://packagist.org/packages/8ctopus/quoted-printable)
[![tests](https://github.com/8ctopus/quoted-printable/actions/workflows/tests.yml/badge.svg)](https://github.com/8ctopus/quoted-printable/actions/workflows/tests.yml)
![code coverage badge](https://raw.githubusercontent.com/8ctopus/quoted-printable/image-data/coverage.svg)
![lines of code](https://raw.githubusercontent.com/8ctopus/quoted-printable/image-data/lines.svg)

Check if a text is quoted printable encoded

    https://datatracker.ietf.org/doc/html/rfc2045#section-6.7

_Disclaimer_: While I've tried my best to implement the RFC, I cannot guarantee this library is bug free.

## install

    composer require 8ctopus/quoted-printable

## demo

```php
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
```

Alternatively you can use the exception variant

```php
use Oct8pus\QuotedPrintable;
use Oct8pus\QuotedPrintableException;

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

try {
    $quotedPrintable->validate($text);
    echo 'valid quoted printable' . PHP_EOL;
} catch (QuotedPrintableException $exception) {
    echo 'invalid quoted printable' . PHP_EOL;
}
```

## clean code

    composer fix(-risky)

## phpstan

    composer phpstan

## phpmd

    composer phpmd
