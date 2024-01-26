<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Tests;

use Kocal\SymfonyMailerTesting\Normalizer\EmailNormalizer;
use Kocal\SymfonyMailerTesting\Normalizer\HeadersNormalizer;
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
                new MessageNormalizer(new HeadersNormalizer()),
                new EmailNormalizer(new HeadersNormalizer()),
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
                ->subject('message 2 : test')
                ->from('leo@example.com')
                ->to('lea@example.com', 'luna@example.com')
                ->cc('lionel@example.com')
                ->bcc('lilou@example.com')
                ->text('Text content')
                ->html('<b>HTML content</b>')
                ->attach('Attachment #1 body', 'attachment_1.txt')
                ->attach('Attachment #2 body', 'attachment_2.txt')
        ));

        static::assertSame([
            'events' => [
                [
                    'message' => [
                        'headers' => [
                            [
                                'name' => 'Subject',
                                'body' => 'message 1',
                            ],
                            [
                                'name' => 'From',
                                'body' => 'john@example.com',
                            ],
                            [
                                'name' => 'To',
                                'body' => 'jeanne@example.com',
                            ],
                            [
                                'name' => 'Cc',
                                'body' => 'jack@example.com',
                            ],
                            [
                                'name' => 'Bcc',
                                'body' => 'jennifer@example.com',
                            ],
                        ],
                        'from' => ['john@example.com'],
                        'to' => ['jeanne@example.com'],
                        'cc' => ['jack@example.com'],
                        'bcc' => ['jennifer@example.com'],
                        'subject' => 'message 1',
                        'text' => [
                            'body' => 'Text content',
                            'charset' => 'utf-8',
                        ],
                        'html' => [
                            'body' => '<b>HTML content</b>',
                            'charset' => 'utf-8',
                        ],
                        'attachments' => [
                            'application/octet-stream disposition: attachment filename: attachment_1.txt',
                        ],
                    ],
                    'transport' => 'null://',
                    'queued' => false,
                ],
                [
                    'message' => [
                        'headers' => [
                            [
                                'name' => 'Subject',
                                'body' => 'message 2 : test',
                            ],
                            [
                                'name' => 'From',
                                'body' => 'leo@example.com',
                            ],
                            [
                                'name' => 'To',
                                'body' => 'lea@example.com, luna@example.com',
                            ],
                            [
                                'name' => 'Cc',
                                'body' => 'lionel@example.com',
                            ],
                            [
                                'name' => 'Bcc',
                                'body' => 'lilou@example.com',
                            ],
                        ],
                        'from' => ['leo@example.com'],
                        'to' => ['lea@example.com', 'luna@example.com'],
                        'cc' => ['lionel@example.com'],
                        'bcc' => ['lilou@example.com'],
                        'subject' => 'message 2 : test',
                        'text' => [
                            'body' => 'Text content',
                            'charset' => 'utf-8',
                        ],
                        'html' => [
                            'body' => '<b>HTML content</b>',
                            'charset' => 'utf-8',
                        ],
                        'attachments' => [
                            'application/octet-stream disposition: attachment filename: attachment_1.txt',
                            'application/octet-stream disposition: attachment filename: attachment_2.txt',
                        ],
                    ],
                    'transport' => 'null://',
                    'queued' => true,
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
