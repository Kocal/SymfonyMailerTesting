<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Normalizer;

use Symfony\Component\Mime\Message;

/**
 * The return data type should be synchronized with the TypeScript type `SymfonyMailerTesting.Message`.
 *
 * @see src/Bridge/JavaScript/index.d.ts
 */
class MessageNormalizer
{
    private $headersNormalizer;

    public function __construct(HeadersNormalizer $headersNormalizer)
    {
        $this->headersNormalizer = $headersNormalizer;
    }

    /**
     * @return array<string, mixed>
     */
    public function normalize(Message $message): array
    {
        return [
            'headers' => $this->headersNormalizer->normalize($message->getHeaders()),
            'body'    => null === $message->getBody() ? null : [
                'headers' => $this->headersNormalizer->normalize($message->getBody()->getHeaders()),
                'body'    => $message->getBody()->bodyToString(),
            ],
        ];
    }
}
