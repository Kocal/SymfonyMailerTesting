<?php

namespace Kocal\SymfonyMailerTesting\Normalizer;

use Symfony\Component\Mime\Header\Headers;

class HeadersNormalizer
{
    private const DELIMITER = ': ';

    /**
     * @return list<array{name: string, body: string}>
     */
    public function normalize(Headers $headers): array
    {
        $ret = [];

        /** @var string $header */
        foreach ($headers->toArray() as $header) {
            $i = strpos($header, static::DELIMITER);

            $ret[] = [
                'name' => substr($header, 0, $i),
                'body' => substr($header, $i + strlen(static::DELIMITER)),
            ];
        }

        return $ret;
    }
}
