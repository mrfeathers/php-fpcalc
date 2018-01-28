<?php

namespace Tests;

use Fpcalc\FpcalcProcess;
use Fpcalc\Options;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FpcalcGenerateTest extends TestCase
{
    /**
     * @param string $returnResult
     *
     * @param array $expectedCommandLine
     *
     * @return MockInterface
     */
    protected function createProcessMock(string $returnResult, array $expectedCommandLine): MockInterface
    {
        $process = \Mockery::mock(Process::class);
        $process->shouldReceive('setCommandLine')
            ->withArgs([$expectedCommandLine]);
        $process->shouldReceive('mustRun');
        $process->shouldReceive('getOutput')
            ->andReturn($returnResult);
        return $process;
    }

    /**
     * @param array $expectedCommandLine
     *
     * @return MockInterface
     */
    protected function createProcessMockWithException(): MockInterface
    {
        $process = \Mockery::mock(Process::class);
        $process->shouldReceive('setCommandLine');
        $process->shouldReceive('mustRun')
            ->once()->andThrow(\Mockery::mock(ProcessFailedException::class));

        return $process;
    }

    public function resultProvider()
    {
        return [
            ['json', '{some result} {some result}', '[{some result},{some result}]'],
            ['json', '{some result}', '[{some result}]'],
            ['text', 'some result', 'some result'],
            ['plain', 'some result', 'some result'],
        ];
    }

    /**
     * @param $format
     * @param $returnResult
     * @param $expected
     *
     * @dataProvider resultProvider
     */
    public function testGenerateFingerprint($format, $returnResult, $expected)
    {
        $process = $this->createProcessMock($returnResult, [FpcalcProcess::FPCALC, Options::getOutputFormatOption($format)]);

        $fpcalc = new FpcalcProcess($process);
        $fpcalc->setOutputFormat($format);

        $actual = $fpcalc->generateFingerprint([]);

        $this->assertEquals($expected, $actual);
    }


    public function filesProvider()
    {
        return [
            [['file.mp3']],
            [['file1.mp3', 'file2.mp3']],
        ];
    }

    /**
     * @test
     * @param array $files
     *
     * @dataProvider filesProvider
     */
    public function shouldAttachFilesToCommandLine(array $files)
    {
        $expectedCommandLine = [FpcalcProcess::FPCALC];
        foreach ($files as $file) {
            $expectedCommandLine[] = $file;
        }

        $process = $this->createProcessMock($result = 'someresult', $expectedCommandLine);

        $fpcalc = new FpcalcProcess($process);

        $actual = $fpcalc->generateFingerprint($files);

        $this->assertEquals($result, $actual);
    }


    /**
     * @test
     * @expectedException \Fpcalc\Exception\FpcalcProcessException
     */
    public function shouldThrowExceptionIfProcessThrows()
    {
        $process = $this->createProcessMockWithException();

        $fpcalc = new FpcalcProcess($process);

        $fpcalc->generateFingerprint([]);
    }
}
