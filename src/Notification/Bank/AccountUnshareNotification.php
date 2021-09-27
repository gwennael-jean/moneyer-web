<?php

namespace App\Notification\Bank;

use App\Entity\Bank\AccountShare;
use App\Entity\User;
use App\Notification\UserRecipient;
use Symfony\Component\Notifier\Exception\LogicException;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class AccountUnshareNotification extends Notification implements EmailNotificationInterface
{
    private AccountShare $accountShare;

    private User $sharer;

    public function __construct(AccountShare $accountShare, User $sharer)
    {
        $this->accountShare = $accountShare;
        $this->sharer = $sharer;

        parent::__construct('Account unshared');
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        if (!$recipient instanceof UserRecipient) {
            throw new LogicException("The first parameter must be instance of " . UserRecipient::class);
        }

        $message = EmailMessage::fromNotification($this, $recipient, $transport);

        $message->getMessage()
            ->htmlTemplate('emails/bank/account-unshare.html.twig')
            ->context([
                'accountShare' => $this->accountShare,
                'sharer' => $this->sharer,
            ])
        ;

        return $message;
    }

}
