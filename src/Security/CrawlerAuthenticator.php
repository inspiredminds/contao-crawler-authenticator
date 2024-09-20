<?php

declare(strict_types=1);

namespace InspiredMinds\ContaoCrawlerAuthenticator\Security;

use Contao\CoreBundle\Crawl\Escargot\Factory;
use Contao\CoreBundle\Security\User\ContaoUserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class CrawlerAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly ContaoUserProvider $contaoUserProvider,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function supports(Request $request): bool
    {
        return Factory::USER_AGENT === $request->headers->get('User-Agent')
            && $request->server->has('PHP_AUTH_USER')
            && $request->server->has('PHP_AUTH_PW');
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->server->get('PHP_AUTH_USER');
        $password = $request->server->get('PHP_AUTH_PW');

        if (!$username || !$password) {
            throw new AuthenticationException('No username and password given.');
        }

        $user = $this->contaoUserProvider->loadUserByIdentifier($username);

        if ($this->passwordHasher->isPasswordValid($user, $password)) {
            return new SelfValidatingPassport(new UserBadge($username));
        }

        throw new AuthenticationException('Invalid password given.');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): Response|null
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response|null
    {
        return null;
    }
}
