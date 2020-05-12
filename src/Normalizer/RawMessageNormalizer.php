<?php

declare(strict_types=1);

namespace Yproximite\SymfonyMailerTesting\Normalizer;

use Symfony\Component\Mime\RawMessage;

/**
 * The return data type should be synchronized with the TypeScript type `SymfonyMailerTesting.RawMessage`.
 *
 * @see src/Bridge/JavaScript/index.d.ts
 */
class RawMessageNormalizer
{
    /**
     * @return array<string, mixed>
     */
    public function normalize(RawMessage $message): array
    {
        return [
            'message' => $message->toString(),
        ];
    }
}
