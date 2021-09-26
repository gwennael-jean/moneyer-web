<?php

namespace App\Notification;

use App\Entity\User;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class UserRecipient implements EmailRecipientInterface
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getEmail(): string
    {
        return $this->user->getEmail();
    }

    public function isNewUser(): bool
    {
        return null === $this->user->getPassword();
    }
}
