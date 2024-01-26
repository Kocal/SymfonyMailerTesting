<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Normalizer;

use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;

/**
 * The return data type should be synchronized with the TypeScript type `SymfonyMailerTesting.MessageEvent`.
 *
 * @see src/Bridge/JavaScript/index.d.ts
 */
class MessageEventNormalizer
{
    private $rawMessageNormalizer;

    private $messageNormalizer;

    private $emailNormalizer;

    public function __construct(RawMessageNormalizer $rawMessageNormalizer, MessageNormalizer $messageNormalizer, EmailNormalizer $emailNormalizer)
    {
        $this->rawMessageNormalizer = $rawMessageNormalizer;
        $this->messageNormalizer = $messageNormalizer;
        $this->emailNormalizer = $emailNormalizer;
    }

    /**
     * @return array<string, mixed>
     */
    public function normalize(MessageEvent $messageEvent): array
    {
        if ($messageEvent->getMessage() instanceof Email) {
            $message = $this->emailNormalizer->normalize($messageEvent->getMessage());
        } elseif ($messageEvent->getMessage() instanceof Message) {
            $message = $this->messageNormalizer->normalize($messageEvent->getMessage());
        } else {
            $message = $this->rawMessageNormalizer->normalize($messageEvent->getMessage());
        }

        return [
            'message' => $message,
            'transport' => $messageEvent->getTransport(),
            'queued' => $messageEvent->isQueued(),
        ];
    }
}
