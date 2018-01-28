<?php

namespace Tests;

use Fpcalc\FpcalcProcess;
use Fpcalc\Options;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Mockery;
use Symfony\Component\Process\Process;

class FpcalcProcessSettersTest extends TestCase
{
    public function settersProvider()
    {
        return [
            ['setFormat', $value = 'someFormat', [Options::FORMAT, $value]],
            ['setChunk', $value = 10, [Options::CHUNK, $value]],
            ['setAlgorithm', $value = 1, [Options::ALGORITHM, $value]],
            ['setRate', $value = 300, [Options::RATE, $value]],
            ['setChannels', $value = 1, [Options::CHANNELS, $value]],
            ['setLength', $value = 10, [Options::LENGTH, $value]],
            ['setOverlap', true, [Options::OVERLAP]],
            ['setTs', true, [Options::TS]],
            ['setRaw', true, [Options::RAW]],
        ];
    }

    /**
     * @param $setter
     * @param $value
     * @param $expectedCommandLine
     *
     * @dataProvider settersProvider
     */
    public function testSetters($setter, $value, $expectedCommandLine)
    {
        $this->setterTest($setter, $value, $expectedCommandLine);

    }


    public function formatProvider()
    {
        return [
            ['json', [Options::JSON], 'isJsonOutput'],
            ['text', [Options::TEXT], 'isTextOutput'],
            ['plain', [Options::PLAIN], 'isPlainOutput'],
        ];
    }

    /**
     * @param $format
     * @param $expectedCommandLine
     *
     * @dataProvider formatProvider
     */
    public function testOutputFormatSetter($format, $expectedCommandLine, $isFormatMethod)
    {
        $fpcalc = $this->setterTest('setOutputFormat', $format, $expectedCommandLine);
        $this->assertTrue($fpcalc->$isFormatMethod());
    }

    public function testSetTimeout()
    {
        $process = new Process('');
        $fpcalc = new FpcalcProcess($process);

        $fpcalc->setTimeout($timeout = 120);

        $this->assertEquals($process->getTimeout(), $timeout);
    }

    /**
     * @param array $expectedCommandLine
     *
     * @return MockInterface
     */
    private function generateProcessMock(array $expectedCommandLine): MockInterface
    {
        $process = Mockery::mock(Process::class);
        $process->shouldReceive('setCommandLine')
            ->withArgs([$expectedCommandLine]);
        $process->shouldReceive('mustRun');
        $process->shouldReceive('getOutput')
            ->andReturn('');

        return $process;
    }

    /**
     * @param $setter
     * @param $value
     * @param $expectedCommandLine
     *
     * @return FpcalcProcess
     */
    private function setterTest($setter, $value, $expectedCommandLine): FpcalcProcess
    {
        $expectedCommandLine = array_merge([FpcalcProcess::FPCALC], $expectedCommandLine);

        $process = $this->generateProcessMock($expectedCommandLine);

        $fpcalcProcess = new FpcalcProcess($process);

        $fpcalcProcess->$setter($value);

        $actual = $fpcalcProcess->generateFingerprint([]);

        $this->assertInternalType('string', $actual);

        return $fpcalcProcess;
    }
}
