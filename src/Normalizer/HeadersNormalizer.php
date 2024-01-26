<?php

declare(strict_types=1);

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
            if (false === $i = strpos($header, self::DELIMITER)) {
                throw new \RuntimeException('Invalid header name/value delimiter, this should not happens.');
            }

            $ret[] = [
                'name' => substr($header, 0, $i),
                'body' => substr($header, $i + strlen(self::DELIMITER)),
            ];
        }

        return $ret;
    }
}
