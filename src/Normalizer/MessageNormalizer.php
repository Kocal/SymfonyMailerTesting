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
    /**
     * @return array<string, mixed>
     */
    public function normalize(Message $message): array
    {
        return [
            'headers' => $message->getHeaders()->toArray(),
            'body'    => null === $message->getBody() ? null : [
                'headers' => $message->getBody()->getHeaders()->toArray(),
                'body'    => $message->getBody()->bodyToString(),
            ],
        ];
    }
}
