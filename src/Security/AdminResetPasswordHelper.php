<?php


namespace App\Security;


use App\Repository\ResetAdminPasswordRequestRepository;
use SymfonyCasts\Bundle\ResetPassword\Exception\ExpiredResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\InvalidResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;
use Throwable;

class AdminResetPasswordHelper //implements ResetPasswordHelperInterface
{
    /**
     * The first 20 characters of the token are a "selector".
     */
    private const SELECTOR_LENGTH = 20;
    private $repository;

    public function __construct(private readonly ResetPasswordTokenGenerator $tokenGenerator, private readonly ResetPasswordCleaner $resetPasswordCleaner, ResetAdminPasswordRequestRepository $repository, /**
     * @var int How long a token is valid in seconds
     */
    private readonly int $resetRequestLifetime, /**
     * @var int Another password reset cannot be made faster than this throttle time in seconds
     */
    private readonly int $requestThrottleTime)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     *
     * Some of the cryptographic strategies were taken from
     * https://paragonie.com/blog/2017/02/split-tokens-token-based-authentication-protocols-without-side-channels
     *
     * @throws TooManyPasswordRequestsException
     */
    public function generateResetToken(object $user): ResetPasswordToken
    {
        $this->resetPasswordCleaner->handleGarbageCollection();

        if ($availableAt = $this->hasUserHitThrottling($user)) {
            throw new TooManyPasswordRequestsException($availableAt);
        }

        $expiresAt = new \DateTimeImmutable(\sprintf('+%d seconds', $this->resetRequestLifetime));

        $tokenComponents = $this->tokenGenerator->createToken($expiresAt, $this->repository->getUserIdentifier($user));

        $passwordResetRequest = $this->repository->createResetPasswordRequest(
            $user,
            $expiresAt,
            $tokenComponents->getSelector(),
            $tokenComponents->getHashedToken()
        );

        $this->repository->persistResetPasswordRequest($passwordResetRequest);

        // final "public" token is the selector + non-hashed verifier token
        return new ResetPasswordToken(
            $tokenComponents->getPublicToken(),
            $expiresAt
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws ExpiredResetPasswordTokenException
     * @throws InvalidResetPasswordTokenException
     */
    public function validateTokenAndFetchUser(string $fullToken): object
    {
        $this->resetPasswordCleaner->handleGarbageCollection();

        if (40 !== \strlen($fullToken)) {
            throw new InvalidResetPasswordTokenException();
        }

        $resetRequest = $this->findResetPasswordRequest($fullToken);

        if (null === $resetRequest) {
            throw new InvalidResetPasswordTokenException();
        }

        if ($resetRequest->isExpired()) {
            throw new ExpiredResetPasswordTokenException();
        }

        $user = $resetRequest->getUser();

        $hashedVerifierToken = $this->tokenGenerator->createToken(
            $resetRequest->getExpiresAt(),
            $this->repository->getUserIdentifier($user),
            \substr($fullToken, self::SELECTOR_LENGTH)
        );

        if (false === \hash_equals($resetRequest->getHashedToken(), $hashedVerifierToken->getHashedToken())) {
            throw new InvalidResetPasswordTokenException();
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidResetPasswordTokenException
     */
    public function removeResetRequest(string $fullToken): void
    {
        $request = $this->findResetPasswordRequest($fullToken);

        if (null === $request) {
            throw new InvalidResetPasswordTokenException();
        }

        $this->repository->removeResetPasswordRequest($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenLifetime(): int
    {
        return $this->resetRequestLifetime;
    }

    private function findResetPasswordRequest(string $token): ?ResetPasswordRequestInterface
    {
        $selector = \substr($token, 0, self::SELECTOR_LENGTH);

        return $this->repository->findResetPasswordRequest($selector);
    }

    private function hasUserHitThrottling(object $user): ?\DateTimeInterface
    {
        /** @var \DateTime|\DateTimeImmutable|null $lastRequestDate */
        $lastRequestDate = $this->repository->getMostRecentNonExpiredRequestDate($user);

        if (null === $lastRequestDate) {
            return null;
        }

        $availableAt = (clone $lastRequestDate)->add(new \DateInterval("PT{$this->requestThrottleTime}S"));

        if ($availableAt > new \DateTime('now')) {
            return $availableAt;
        }

        return null;
    }
}