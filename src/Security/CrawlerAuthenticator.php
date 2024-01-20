<?php

declare(strict_types=1);

namespace InspiredMinds\ContaoCrawlerAuthenticator\Security;

use Contao\CoreBundle\Crawl\Escargot\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class CrawlerAuthenticator extends AbstractGuardAuthenticator
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function supports(Request $request): bool
    {
        return Factory::USER_AGENT === $request->headers->get('User-Agent')
            && $request->server->has('PHP_AUTH_USER')
            && $request->server->has('PHP_AUTH_PW');
    }

    public function getCredentials(Request $request): array
    {
        return [
            'username' => $request->server->get('PHP_AUTH_USER'),
            'password' => $request->server->get('PHP_AUTH_PW'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface|null
    {
        return $userProvider->loadUserByIdentifier($credentials['username']);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): Response|null
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response|null
    {
        return null;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    public function start(Request $request, AuthenticationException|null $authException = null): Response|null
    {
        return null;
    }
}
