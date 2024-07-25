<?php

namespace App\Security;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginAuthAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'api_login';

    private string $jwtSecret;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordEncoder,
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            throw new \Exception('Invalid credentials');
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $password)) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');
        }

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        $payload = [
            'user_id' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'exp' => time() + 3600
        ];

        $jwt = JWT::encode($payload, $_ENV['JWT_SECRET_KEY'], 'HS256');

        return new JsonResponse([
            'user'  => $user->getUserIdentifier(),
            'token' => $jwt,
        ]);
    }

    protected function getLoginUrl(Request $request): string
    {

        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
