<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Controller;

use Kocal\SymfonyMailerTesting\MailerLogger;
use Kocal\SymfonyMailerTesting\Normalizer\MessageEventsNormalizer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class MailerController
{
    private $mailerLogger;

    private $messageEventsNormalizer;

    public function __construct(MailerLogger $mailerLogger, MessageEventsNormalizer $messageEventsNormalizer)
    {
        $this->mailerLogger = $mailerLogger;
        $this->messageEventsNormalizer = $messageEventsNormalizer;
    }

    public function reset(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory): ResponseInterface
    {
        $this->mailerLogger->reset();

        return $responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json')
            ->withBody($streamFactory->createStream(json_encode([
                'success' => true,
            ], JSON_THROW_ON_ERROR)));
    }

    public function getEvents(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory): ResponseInterface
    {
        $events = $this->mailerLogger->getEvents();

        return $responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json')
            ->withBody($streamFactory->createStream(json_encode($this->messageEventsNormalizer->normalize($events), JSON_THROW_ON_ERROR)));
    }
}
