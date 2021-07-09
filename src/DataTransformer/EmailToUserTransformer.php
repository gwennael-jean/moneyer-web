<?php

namespace App\DataTransformer;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EmailToUserTransformer implements DataTransformerInterface
{
    public function __construct(
        private UserRepository $userRepository
    )
    {
    }

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        $user = $this->userRepository->findOneByEmail($value);

        if (null === $user) {
            $user = $this->emailToUser($value);
        }

        return $user;
    }

    private function emailToUser(string $email): User
    {
        $user = (new User())
            ->setEmail($email);

        $name = substr($email, 0, strpos($email, '@'));

        if (str_contains($name, '.')) {
            $user
                ->setFirstname(ucfirst(strtolower(substr($name, 0, strpos($name, '.')))))
                ->setLastname(ucfirst(strtolower(substr($name, strpos($name, '.') + 1))));
        } else {

            $user
                ->setLastname(ucfirst(strtolower($name)));
        }

        return $user;
    }
}