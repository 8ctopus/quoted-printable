<?php

/**
 * Check quoted printable encoding
 *
 * https://datatracker.ietf.org/doc/html/rfc2045#section-6.7
 *
 * Any 8 bit character can be encoded with 3 characters: = followed 2 hexadecimal digits in uppercase eg. =20
 *
 * All printable ASCII characters from 33 to 126 can be represented by themselves
 *
 * All non printable ASCII characters must be represented in the later variant
 *
 * Tab (0x09) and space (0x20) must be represented in hexadecimal form if at the end of the encoded line
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
    /**
     * @var array<string>
     */
    private array $validInLine;

    /**
     * @var array<string>
     */
    private array $validEndOfLine;

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
    }

    /**
     * Check if text is valid quoted printable
     *
     * @param string $text
     *
     * @return self
     *
     * @throws QuotedPrintableException
     */
    public function validate(string $text) : self
    {
        $lines = explode("\r\n", $text);

        // go through each line
        foreach ($lines as $line) {
            $length = strlen($line);

            if ($length === 0) {
                continue;
            }

            if ($length > 76) {
                throw new QuotedPrintableException("line too long - {$length}");
            }

            // check that line only contains valid characters
            for ($i = 0; $i < $length - 1; ++$i) {
                if (!in_array($line[$i], $this->validInLine, true)) {
                    $char = $line[$i];
                    $hex = ord($char);
                    throw new QuotedPrintableException("invalid character '{$char}' {$hex}");
                }
            }

            // check end of line characters (spaces and tabs are not allowed)
            if (!in_array($line[$length - 1], $this->validEndOfLine, true)) {
                $char = $line[$length - 1];
                $hex = ord($char);
                throw new QuotedPrintableException("invalid character '{$char}' {$hex}");
            }

            // check that = is followed by 2 hexadecimal digits in uppercase
            for ($i = 0; $i < $length - 2; ++$i) {
                if ($line[$i] === '=') {
                    $hex = substr($line, $i, 3);

                    if (preg_match('/=[0-9A-F]{2}/', $hex) !== 1) {
                        throw new QuotedPrintableException("invalid hex sequence '{$hex}'");
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Encode text to quoted printable
     *
     * @param string $text
     *
     * @return string
     */
    public function encode(string $text) : string
    {
        // @codeCoverageIgnoreStart
        return quoted_printable_encode($text);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Decode quoted printable encoded text
     *
     * @param string $text
     *
     * @return string
     */
    public function decode(string $text) : string
    {
        // @codeCoverageIgnoreStart
        return quoted_printable_decode($text);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Check if text is valid quoted printable
     *
     * @param string $text
     *
     * @return bool
     */
    public function validateNoExceptions(string $text) : bool
    {
        try {
            $this->validate($text);
        } catch (QuotedPrintableException $exception) {
            return false;
        }

        return true;
    }
}
