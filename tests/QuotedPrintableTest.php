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
     * @dataProvider getValid
     *
     * @param string $text
     */
    public function testOK(string $text) : void
    {
        self::expectNotToPerformAssertions();

        (new QuotedPrintable())
            ->validate($text);
    }

    /**
     * @dataProvider getInvalid
     *
     * @param string $text
     * @param string $exception
     */
    public function testInvalid(string $text, string $exception) : void
    {
        self::expectException(QuotedPrintableException::class);
        self::expectExceptionMessage($exception);

        (new QuotedPrintable())
            ->validate($text);
    }

    /**
     * @dataProvider getValid
     *
     * @param string $text
     */
    public function testOKNoExceptions(string $text) : void
    {
        $quotedPrintable = new QuotedPrintable();

        self::assertTrue($quotedPrintable->validateNoExceptions($text));
    }

    /**
     * @dataProvider getInvalid
     *
     * @param string $text
     * @param string $exception
     */
    public function testInvalidNoExceptions(string $text, string $exception) : void
    {
        $quotedPrintable = new QuotedPrintable();

        self::assertFalse($quotedPrintable->validateNoExceptions($text));
    }

    public static function getValid() : array
    {
        return [
            [
                'text' => <<<'TEXT'
                J'interdis aux marchands de vanter trop leurs marchandises. Car ils se font=
                 vite p=C3=A9dagogues et t'enseignent comme but ce qui n'est par essence qu=
                'un moyen, et te trompant ainsi sur la route =C3=A0 suivre les voil=C3=
                =A0 bient=C3=B4t qui te d=C3=A9gradent, car si leur musique est vulgaire il=
                s te fabriquent pour te la vendre une =C3=A2me vulgaire.
                   =E2=80=94=E2=80=89Antoine de Saint-Exup=C3=A9ry, Citadelle (1948)
                TEXT,
            ], [
                'text' => <<<'TEXT'
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
                'text' => <<<'TEXT'
                J'interdis aux marchands de vanter trop leurs marchandises. Car ils se fo=20
                 vite p=C3=A9dagogues et t'enseignent comme but ce qui n'est par essence qu=
                TEXT,
            ], [
                'text' => <<<'TEXT'
                Quoted-printable encoding=0AAny 8-bit byte value may be encoded with 3 char=
                acters: an =3D followed by two hexadecimal digits (0=E2=80=939 or A=
                =E2=80=93F) representing the byte's numeric value. For example, an ASCII fo=
                rm feed character (decimal value 12) can be represented by =3D0C, and an AS=
                CII equal sign (decimal value 61) must be represented by =3D3D. All charact=
                ers except printable ASCII characters or end of line characters (but also =
                =3D) must be encoded in this fashion.=0A=0AAll printable ASCII characters (=
                decimal values between 33 and 126) may be represented by themselves, except=
                 =3D (decimal 61, hexadecimal 3D, therefore =3D3D).=0A=0AASCII tab and spac=
                e characters, decimal values 9 and 32, may be represented by themselves, ex=
                cept if these characters would appear at the end of the encoded line. In th=
                at case, they would need to be escaped as =3D09 (tab) or =3D20 (space), or =
                be followed by a =3D (soft line break) as the last character of the encoded=
                 line. This last solution is valid because it prevents the tab or space fro=
                m being the last character of the encoded line.=0A=0AIf the data being enco=
                ded contains meaningful line breaks, they must be encoded as an ASCII CR LF=
                 sequence, not as their original byte values, neither directly nor via =3D =
                signs. Conversely, if byte values 13 and 10 have meanings other than end of=
                 line (in media types,[2] for example), then they must be encoded as =3D0D =
                and =3D0A respectively.=0A=0ALines of Quoted-Printable encoded data must no=
                t be longer than 76 characters. To satisfy this requirement without alterin=
                g the encoded text, soft line breaks may be added as desired. A soft line b=
                reak consists of an =3D at the end of an encoded line, and does not appear =
                as a line break in the decoded text. These soft line breaks also allow enco=
                ding text without line breaks (or containing very long lines) for an enviro=
                nment where line size is limited, such as the 1000 characters per line limi=
                t of some SMTP software, as allowed by RFC 2821.=0A=0AA slightly modified v=
                ersion of Quoted-Printable is used in message headers; see MIME#Encoded-Wor=
                d.

                TEXT,
            ],
        ];
    }

    public static function getInvalid() : array
    {
        return [
            [
                // first line length > 76
                'text' => <<<'TEXT'
                J'interdis aux marchands de vanter trop leurs marchandises. Car ils se font =
                 vite p=C3=A9dagogues et t'enseignent comme but ce qui n'est par essence qu=
                TEXT,
                'exception' => 'line too long - 77',
            ], [
                // é is forbidden
                'text' => <<<'TEXT'
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
                'text' => <<<'TEXT'
                J'interdis aux marchands de vanter trop leurs marchandises. Car ils se font=
                 vite p=c3=a9dagogues et t'enseignent comme but ce qui n'est par essence qu=
                TEXT,
                'exception' => "invalid hex sequence '=c3'",
            ],
        ];
    }
}
