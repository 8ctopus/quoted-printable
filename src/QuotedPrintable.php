<?php

/**
 * Quoted printable encoding
 *
 * https://en.wikipedia.org/wiki/Quoted-printable
 * https://datatracker.ietf.org/doc/html/rfc2045#section-6.7
 *
 * used to transfer 8bit data over 7bit
 *
 * All printable ASCII characters from 33 to 126 can be represented by themselves
 *
 * Any 8 bit character can be encoded with 3 characters: = followed 2 hexadecimal digits in uppercase
 *
 * All non printable ASCII characters must be represented in the later variant
 *
 * Tab (0x09) and space (0x20) may be represented by themselves expect if they would appear at the end of the encoded line
 * An ASCII equal sign must be represented by =3D
 *
 * Escape character =
 *
 * Line maximum length 76 characters
 */

declare(strict_types=1);

namespace Oct8pus;

class QuotedPrintable
{
    private array $validInLine;
    private array $validEndOfLine;
    private array $validHex;

    public function __construct()
    {
        $valid = [];

        // all printable ASCII characters from 33 to 126 should be represented by themselves expect 61 (=)
        // !"#$%&\'()*+,-./0123456789:;<>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~
        for ($i = 33; $i < 127; ++$i) {
            $valid[] = chr($i);
        }

        $this->validEndOfLine = $valid;
        $this->validInLine = $valid;

        $this->validInLine[] = ' ';
        $this->validInLine[] = "\t";

        $this->validHex = [];

        // 0-9 A-F
        for ($i = 48; $i < 71; ++$i) {
            if ($i >= 58 && $i < 65) {
                continue;
            }

            $this->validHex[] = chr($i);
        }
    }

    /**
     * Check if text is valid quoted printable
     *
     * @param  string $text
     *
     * @return bool
     */
    public function validate(string $text) : bool
    {
        $lines = explode("\n", $text);

        // go through each line
        foreach ($lines as $line) {
            $length = strlen($line);

            if ($length === 0) {
                continue;
            }

            if ($length > 76) {
                echo 'line too long' . PHP_EOL;
                return false;
            }

            // check that line only contains valid characters
            for ($i = 0; $i < $length - 1; ++$i) {
                if (!in_array($line[$i], $this->validInLine, true)) {
                    $char = $line[$length -1];
                    $hex = ord($char);
                    echo "invalid character '{$char}' {$hex}" . PHP_EOL;
                    return false;
                }
            }

            // check end of line characters
            if (!in_array($line[$length -1], $this->validEndOfLine, true)) {
                $char = $line[$length -1];
                $hex = ord($char);
                echo "invalid character '{$char}' {$hex}" . PHP_EOL;
                return false;
            }

            // check that = is followed by 2 hexadecimal digits in uppercase
            for ($i = 0; $i < $length - 2; ++$i) {
                if ($line[$i] === '=') {
                    $hex = substr($line, $i, 3);

                    if (preg_match('/=[0-9A-F]{2}/', $hex) !== 1) {
                        echo "invalid hex sequence '{$hex}'" . PHP_EOL;
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
