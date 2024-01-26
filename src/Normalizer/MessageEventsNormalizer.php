<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Normalizer;

use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Event\MessageEvents;

/**
 * The return data type should be synchronized with the TypeScript type `SymfonyMailerTesting.MessageEvents`.
 *
 * @see src/Bridge/JavaScript/index.d.ts
 */
class MessageEventsNormalizer
{
    private $messageEventNormalizer;

    public function __construct(MessageEventNormalizer $messageEventNormalizer)
    {
        $this->messageEventNormalizer = $messageEventNormalizer;
    }

    /**
     * @return array<string, mixed>
     */
    public function normalize(MessageEvents $messageEvents)
    {
        return [
            'events' => array_map(function (MessageEvent $messageEvent): array {
                return $this->messageEventNormalizer->normalize($messageEvent);
            }, $messageEvents->getEvents()),
            'transports' => $messageEvents->getTransports(),
        ];
    }
}
