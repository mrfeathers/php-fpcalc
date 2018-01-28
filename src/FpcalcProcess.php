<?php

namespace Fpcalc;

use Fpcalc\Exception\FpcalcProcessException;
use Fpcalc\Exception\FpcalcValidationException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FpcalcProcess
{
    const FPCALC = 'fpcalc';

    /** @var Process  */
    private $process;

    /** @var array */
    private $options = [];

    /**
     * FpcalcProcess constructor.
     *
     * @param Process $process
     */
    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    /**
     * @param array $files
     *
     * @return string
     * @throws FpcalcProcessException
     */
    public function generateFingerprint(array $files): string
    {
        $this->process->setCommandLine($this->generateCommandLine($files));
        try {

            $this->process->mustRun();

            return $this->renderOutput($this->process->getOutput());

        } catch (ProcessFailedException $exception) {
            throw new FpcalcProcessException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * @param int $seconds
     *
     * @return FpcalcProcess
     */
    public function setTimeout(int $seconds): FpcalcProcess
    {
        $this->process->setTimeout($seconds);
        return $this;
    }

    /**
     * @param string $name - input format name
     *
     * @return FpcalcProcess
     */
    public function setFormat(string $name): FpcalcProcess
    {
        $this->options[Options::FORMAT] = $name;
        return $this;
    }

    /**
     * @param int $algorithmNumber - algorithm method (default 2). Available since fpcalc version 1.4.3
     *
     * @return FpcalcProcess
     */
    public function setAlgorithm(int $algorithmNumber): FpcalcProcess
    {
        $this->options[Options::ALGORITHM] = $algorithmNumber;
        return $this;
    }

    /**
     * @param int $rate - sample rate of the input
     *
     * @return FpcalcProcess
     */
    public function setRate(int $rate): FpcalcProcess
    {
        $this->options[Options::RATE] = $rate;
        return $this;
    }

    /**
     * @param int $channelsCount - number of channels in the input audio
     *
     * @return FpcalcProcess
     */
    public function setChannels(int $channelsCount): FpcalcProcess
    {
        $this->options[Options::CHANNELS] = $channelsCount;
        return $this;
    }
    /**
     * @param int $length (in seconds)- restricts the duration of the processed input audio (default 120)
     *
     * @return FpcalcProcess
     */
    public function setLength(int $length): FpcalcProcess
    {
        $this->options[Options::LENGTH] = $length;
        return $this;
    }
    /**
     * @param int $chunkDuration (in seconds) - splits the input audio into chunks of $chunkDuration duration
     *
     * @return FpcalcProcess
     */
    public function setChunk(int $chunkDuration): FpcalcProcess
    {
        $this->options[Options::CHUNK] = $chunkDuration;
        return $this;
    }

    /**
     * @param bool $isOverlapped - overlap the chunks slightly to make sure audio on the edge id fingeprinted
     *
     * @return FpcalcProcess
     */
    public function setOverlap(bool $isOverlapped): FpcalcProcess
    {
        $this->options[Options::OVERLAP] = $isOverlapped;
        return $this;
    }

    /**
     * @param bool $isTs - output UNIX timestamps for chunked results, useful when fingerprinting real-time audio stream
     *
     * @return FpcalcProcess
     */
    public function setTs(bool $isTs): FpcalcProcess
    {
        $this->options[Options::TS] = $isTs;
        return $this;
    }

    /**
     * @param bool $isRaw - output fingerprints in the uncompressed format
     *
     * @return FpcalcProcess
     */
    public function setRaw(bool $isRaw = true): FpcalcProcess
    {
        $this->options[Options::RAW] = $isRaw;
        return $this;
    }


    /**
     * @param string $format - output format. Available formats: 'json', 'text', 'plain'
     *
     * @return FpcalcProcess
     * @throws FpcalcValidationException
     */
    public function setOutputFormat(string $format): FpcalcProcess
    {
        $this->options[Options::getOutputFormatOption($format)] = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isJsonOutput(): bool
    {
        return $this->options[Options::JSON] ?? false;
    }

    /**
     * @return bool
     */
    public function isTextOutput(): bool
    {
        return $this->options[Options::TEXT] ?? false;
    }

    /**
     * @return bool
     */
    public function isPlainOutput(): bool
    {
        return $this->options[Options::PLAIN] ?? false;
    }

    /**
     * @param array $files
     *
     * @return array
     */
    private function generateCommandLine(array $files): array
    {
        $commandLine[] = self::FPCALC;

        foreach ($this->options as $option => $value) {
            if (is_bool($value) && $value) {
                $commandLine[] = $option;
            } else {
                $commandLine[] = $option;
                $commandLine[] = $value;
            }
        }

        foreach ($files as $file) {
           $commandLine[] = (string)$file;
        }

        return $commandLine;
    }

    /**
     * @param string $output
     *
     * @return string
     */
    private function renderOutput(string $output): string
    {
        if ($this->isJsonOutput()) {
            return '[' . preg_replace('/}\s{/', '},{', $output) . ']';
        }

        return $output;
    }

}
