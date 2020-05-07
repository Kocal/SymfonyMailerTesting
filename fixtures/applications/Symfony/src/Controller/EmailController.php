<?php

declare(strict_types=1);

namespace Yproximite\SymfonyMailerTesting\Fixtures\Applications\Symfony\App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    /**
     * @Route("/send-basic-email", methods={"POST"})
     */
    public function sendBasicEmail(Request $request, MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('symfony-mailer-testing@example.com')
            ->to('john@example.com')
            ->subject($request->request->get('subject', 'Email sent from a Symfony application!'))
            ->text('Hello world!');

        $mailer->send($email);

        return $this->json(['success' => 'ok']);
    }
}
