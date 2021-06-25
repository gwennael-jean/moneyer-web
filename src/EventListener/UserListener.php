<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    public function prePersist(User $user)
    {
        $this->checkPassword($user);
    }

    public function preUpdate(User $user)
    {
        $this->checkPassword($user);
    }

    private function checkPassword(User $user)
    {
        if (null !== $user->getPlainPassword()) {
            $password = $this->userPasswordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($password);
        }
    }
}