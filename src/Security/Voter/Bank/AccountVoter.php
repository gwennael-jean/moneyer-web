<?php

namespace App\Security\Voter\Bank;

use App\DBAL\Types\Bank\AccountShareType;
use App\Entity\Bank\Account;
use App\Entity\Bank\AccountShare;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccountVoter extends Voter
{
    const VIEW = 'view';

    const EDIT = 'edit';

    const SHARE = 'share';

    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::SHARE, self::DELETE])
            && $subject instanceof Account;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW: return $this->isOwner($subject, $user) || $this->canView($subject, $user);
            case self::EDIT: return $this->isOwner($subject, $user) || $this->canEdit($subject, $user);
            case self::SHARE:
            case self::DELETE: return $this->isOwner($subject, $user);
        }

        return false;
    }

    private function isOwner(Account $account, User $user): bool
    {
        return null === $account->getId() || $account->getOwner() === $user;
    }

    private function canView(Account $account, User $user): bool
    {
        return $this->canEdit($account, $user) || !$account->getAccountShares()
            ->filter(fn(AccountShare $accountShare) => $accountShare->getUser() === $user && $accountShare->isType(AccountShareType::VIEW))
            ->isEmpty();
    }

    private function canEdit(Account $account, User $user): bool
    {
        return !$account->getAccountShares()
            ->filter(fn(AccountShare $accountShare) => $accountShare->getUser() === $user && $accountShare->isType(AccountShareType::EDIT))
            ->isEmpty();
    }
}
