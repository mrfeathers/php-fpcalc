<?php

namespace Fpcalc;


use Fpcalc\Exception\FpcalcValidationException;

final class Options
{
    const FORMAT = '-format';
    const RATE = '-rate';
    const CHANNELS = '-channels';
    const LENGTH = '-length';
    const CHUNK = '-chunk';
    const OVERLAP = '-overlap';
    const TS = '-ts';
    const RAW = '-raw';
    const JSON = '-json';
    const TEXT = '-text';
    const PLAIN = '-plain';
    const ALGORITHM = '-algorithm';

    const FORMATS = [
        'json' => Options::JSON,
        'text' => Options::TEXT,
        'plain' => Options::PLAIN,
    ];

    /**
     * @param string $format
     *
     * @return string
     * @throws FpcalcValidationException
     */
    public static function getOutputFormatOption(string $format): string
    {
        if (!array_key_exists($format, self::FORMATS)) {
            throw new FpcalcValidationException('Unavailable format');
        }

        return self::FORMATS[$format];
    }
}
