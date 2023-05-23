<?php

declare(strict_types=1);

namespace Tests;

use Oct8pus\QuotedPrintable;
use Oct8pus\QuotedPrintableException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Oct8pus\QuotedPrintable
 */
final class QuotedPrintableTest extends TestCase
{
    /**
     * @dataProvider getOK
     *
     * @param string $test
     */
    public function testOK(string $text) : void
    {
        $quotedPrintable = new QuotedPrintable();

        static::expectNotToPerformAssertions();

        $quotedPrintable->validate($text);
    }

    /**
     * @dataProvider getInvalid
     *
     * @param string $test
     * @param string $exception
     */
    public function testInvalid(string $text, string $exception) : void
    {
        static::expectException(QuotedPrintableException::class);
        static::expectExceptionMessage($exception);

        $quotedPrintable = new QuotedPrintable();

        $quotedPrintable->validate($text);
    }

    /**
     * @dataProvider getInvalid
     *
     * @param string $test
     */
    public function testValidateNoExceptions(string $text) : void
    {
        $quotedPrintable = new QuotedPrintable();

        static::assertFalse($quotedPrintable->validateNoExceptions($text));
    }

    public static function getOK() : array
    {
        return [
            [
                'text' => <<<TEXT
                J'interdis aux marchands de vanter trop leurs marchandises. Car ils se font=
                 vite p=C3=A9dagogues et t'enseignent comme but ce qui n'est par essence qu=
                'un moyen, et te trompant ainsi sur la route =C3=A0 suivre les voil=C3=
                =A0 bient=C3=B4t qui te d=C3=A9gradent, car si leur musique est vulgaire il=
                s te fabriquent pour te la vendre une =C3=A2me vulgaire.
                   =E2=80=94=E2=80=89Antoine de Saint-Exup=C3=A9ry, Citadelle (1948)
                TEXT,
            ], [
                'text' => <<<TEXT
                Any 8-bit byte value may be encoded with 3 characters: an =
                =3D followed by two hexadecimal digits (0=E2=80=939 or A=E2=80=93F) represe=
                nting the byte's numeric value. For example, an ASCII form feed character (=
                decimal value 12) can be represented by =3D0C, and an ASCII equal sign (dec=
                imal value 61) must be represented by =3D3D. All characters except printabl=
                e ASCII characters or end of line characters (but also =3D) must be encoded=
                 in this fashion.

                                All printable ASCII characters (decimal values between 33 a=
                nd 126) may be represented by themselves, except =3D (decimal 61, hexadecim=
                al 3D, therefore =3D3D).=0D=0A
                TEXT
            ], [
                'text' => <<<TEXT
                J'interdis aux marchands de vanter trop leurs marchandises. Car ils se fo=20
                 vite p=C3=A9dagogues et t'enseignent comme but ce qui n'est par essence qu=
                TEXT,
            ]
        ];
    }

    public static function getInvalid() : array
    {
        return [
            [
                // first line length > 76
                'text' => <<<TEXT
                J'interdis aux marchands de vanter trop leurs marchandises. Car ils se font =
                 vite p=C3=A9dagogues et t'enseignent comme but ce qui n'est par essence qu=
                TEXT,
                'exception' => 'line too long - 77',
            ], [
                // é is forbidden
                'text' => <<<TEXT
                J'interdis aux marchands de vanter trop leurs marchandises. Car ils se font=
                 vite pédagogues et t'enseignent comme but ce qui n'est par essence qu=
                TEXT,
                'exception' => "invalid character 'é'",
            ], [
                // tab at end of line is forbidden
                'text' => <<<TEXT
                J'interdis aux marchands de vanter trop leurs marchandises. Car ils se font\t
                 vite pédagogues et t'enseignent comme but ce qui n'est par essence qu=
                TEXT,
                'exception' => "invalid character '\t'",
            ], [
                // space at end of line is forbidden
                'text' => <<<TEXT
                J'interdis aux marchands de vanter trop leurs marchandises. Car ils se font\x20
                 vite pédagogues et t'enseignent comme but ce qui n'est par essence qu=
                TEXT,
                'exception' => "invalid character ' '",
            ], [
                // lowercase hexadecimal sequence
                'text' => <<<TEXT
                J'interdis aux marchands de vanter trop leurs marchandises. Car ils se font=
                 vite p=c3=a9dagogues et t'enseignent comme but ce qui n'est par essence qu=
                TEXT,
                'exception' => "invalid hex sequence '=c3'",
            ]
        ];
    }

}
