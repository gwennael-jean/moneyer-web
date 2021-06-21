<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class UserListener
{
    private PasswordHasherInterface $hasher;

    public function __construct(PasswordHasherFactoryInterface $passwordHasherFactory)
    {
        $this->hasher = $passwordHasherFactory->getPasswordHasher(User::class);
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
            $password = $this->hasher->hash($user->getPlainPassword());
            $user->setPassword($password);
        }
    }
}