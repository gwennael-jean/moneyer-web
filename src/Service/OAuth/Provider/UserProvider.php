<?php

namespace App\Service\OAuth\Provider;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\Pure;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserProvider implements UserRepositoryInterface
{
    /**
     * @var ArrayCollection
     */
    private $entities;

    #[Pure]
    public function __construct(
        private UserRepository $userRepository
    )
    {
        $this->entities = new ArrayCollection();
    }

    public function getEntity(string $identifier): ?User
    {
        if (!$this->entities->containsKey($identifier)) {
            $user = $this->userRepository->find($identifier);

            if (null === $user) {
                return null;
            }

            $this->entities->set($identifier, $user);
        }

        return $this->entities->get($identifier);
    }

    /**
     * Cette méthode est appelée pour valider les informations d'identification d'un utilisateur.
     *
     * @param string $username
     * @param string $password
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @return UserEntityInterface|null
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity): ?User
    {
        $user = $this->userRepository->findOneByEmail($username);

        if (null !== $user) {
            $this->entities->set($user->getId(), $user);
        }

        return $user;
    }
}