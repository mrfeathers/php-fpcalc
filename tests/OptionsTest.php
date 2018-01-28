<?php


namespace Tests;


use Fpcalc\Options;
use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    public function formatProvider()
    {
        return [
            ['json', Options::JSON],
            ['text', Options::TEXT],
            ['plain', Options::PLAIN],
        ];
    }

    /**
     * @param string $format
     * @param string $expected
     *
     * @dataProvider formatProvider
     */
    public function testGetOutputFormatOption(string $format, string $expected)
    {
        $actual = Options::getOutputFormatOption($format);

        $this->assertEquals($actual, $expected);
    }


    /**
     * @test
     * @expectedException \Fpcalc\Exception\FpcalcValidationException
     */
    public function shouldThrowExceptionIfFormatIsUnavailable()
    {
        Options::getOutputFormatOption('wrong-format');
    }
}
