<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Tests;

use Kocal\SymfonyMailerTesting\Normalizer\EmailNormalizer;
use Kocal\SymfonyMailerTesting\Normalizer\MessageEventNormalizer;
use Kocal\SymfonyMailerTesting\Normalizer\MessageEventsNormalizer;
use Kocal\SymfonyMailerTesting\Normalizer\MessageNormalizer;
use Kocal\SymfonyMailerTesting\Normalizer\RawMessageNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Event\MessageEvents;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

class MessageEventsNormalizerTest extends TestCase
{
    /**
     * @var MessageEventsNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new MessageEventsNormalizer(
            new MessageEventNormalizer(
                new RawMessageNormalizer(),
                new MessageNormalizer(),
                new EmailNormalizer(),
            ),
        );
    }

    public function testNormalize(): void
    {
        $messageEvents = new MessageEvents();
        $messageEvents->add($this->createMessageEvent(
            (new Email())
                ->subject('message 1')
                ->from('john@example.com')
                ->to('jeanne@example.com')
                ->cc('jack@example.com')
                ->bcc('jennifer@example.com')
                ->text('Text content')
                ->html('<b>HTML content</b>')
                ->attach('Attachment #1 body', 'attachment_1.txt')
        ));
        $messageEvents->add($this->createQueuedMessageEvent(
            (new Email())
                ->subject('message 2')
                ->from('leo@example.com')
                ->to('lea@example.com')
                ->cc('lionel@example.com')
                ->bcc('lilou@example.com')
                ->text('Text content')
                ->html('<b>HTML content</b>')
                ->attach('Attachment #1 body', 'attachment_1.txt')
                ->attach('Attachment #2 body', 'attachment_2.txt')
        ));

        static::assertSame([
            'events'     => [
                [
                    'message'   => [
                        'headers'     => [
                            'Subject: message 1',
                            'From: john@example.com',
                            'To: jeanne@example.com',
                            'Cc: jack@example.com',
                            'Bcc: jennifer@example.com',
                        ],
                        'from'        => ['john@example.com'],
                        'to'          => ['jeanne@example.com'],
                        'cc'          => ['jack@example.com'],
                        'bcc'         => ['jennifer@example.com'],
                        'subject'     => 'message 1',
                        'text'        => [
                            'body'    => 'Text content',
                            'charset' => 'utf-8',
                        ],
                        'html'        => [
                            'body'    => '<b>HTML content</b>',
                            'charset' => 'utf-8',
                        ],
                        'attachments' => [
                            'application/octet-stream disposition: attachment filename: attachment_1.txt',
                        ],
                    ],
                    'transport' => 'null://',
                    'queued'    => false,
                ],
                [
                    'message'   => [
                        'headers'     => [
                            'Subject: message 2',
                            'From: leo@example.com',
                            'To: lea@example.com',
                            'Cc: lionel@example.com',
                            'Bcc: lilou@example.com',
                        ],
                        'from'        => ['leo@example.com'],
                        'to'          => ['lea@example.com'],
                        'cc'          => ['lionel@example.com'],
                        'bcc'         => ['lilou@example.com'],
                        'subject'     => 'message 2',
                        'text'        => [
                            'body'    => 'Text content',
                            'charset' => 'utf-8',
                        ],
                        'html'        => [
                            'body'    => '<b>HTML content</b>',
                            'charset' => 'utf-8',
                        ],
                        'attachments' => [
                            'application/octet-stream disposition: attachment filename: attachment_1.txt',
                            'application/octet-stream disposition: attachment filename: attachment_2.txt',
                        ],
                    ],
                    'transport' => 'null://',
                    'queued'    => true,
                ],
            ],
            'transports' => ['null://'],
        ], $this->normalizer->normalize($messageEvents));
    }

    protected function createMessageEvent(RawMessage $message, string $transport = 'null://'): MessageEvent
    {
        return new MessageEvent($message, Envelope::create($message), $transport);
    }

    protected function createQueuedMessageEvent(RawMessage $message, string $transport = 'null://'): MessageEvent
    {
        return new MessageEvent($message, Envelope::create($message), $transport, true);
    }
}
