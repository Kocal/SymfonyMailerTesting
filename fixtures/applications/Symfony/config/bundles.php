<?php

declare(strict_types=1);

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class                              => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class               => ['all' => true],
    // FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle::class => ['test' => true],
    Yproximite\SymfonyMailerTesting\Bridge\Symfony\SymfonyMailerTestingBundle::class   => ['test' => true],
];
