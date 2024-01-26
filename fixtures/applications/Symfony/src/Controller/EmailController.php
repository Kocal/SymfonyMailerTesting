<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Fixtures\Applications\Symfony\App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class EmailController extends AbstractController
{
    public function sendBasicEmail(Request $request, MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from(Address::create($request->request->get('from', 'symfony-mailer-testing@example.com')))
            ->to(Address::create($request->request->get('to', 'john@example.com')))
            ->subject($request->request->get('subject', 'Email sent from a Symfony application!'))
            ->text($request->request->get('text', 'Hello world!'))
            ->html($request->request->get('html', '<b>Hello world!</b>'))
        ;

        foreach ($request->request->all('attachments') as $attachment) {
            $email->attach($attachment['body'], $attachment['name'] ?? null, $attachment['contentType'] ?? null);
        }

        $mailer->send($email);

        return $this->json([
            'success' => 'ok',
        ]);
    }
}
