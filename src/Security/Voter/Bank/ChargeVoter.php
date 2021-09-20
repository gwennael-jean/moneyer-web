<?php

namespace App\Security\Voter\Bank;

use App\DBAL\Types\Bank\AccountShareType;
use App\Entity\Bank\Charge;
use App\Entity\Bank\AccountShare;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChargeVoter extends Voter
{
    const VIEW = 'view';

    const EDIT = 'edit';

    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Charge;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW: return $this->isCreatedBy($subject, $user) || $this->isOwner($subject, $user) || $this->canView($subject, $user);
            case self::EDIT: return $this->isCreatedBy($subject, $user) || $this->isOwner($subject, $user) || $this->canEdit($subject, $user);
            case self::DELETE: return $this->isCreatedBy($subject, $user) || $this->isOwner($subject, $user) || $this->canEdit($subject, $user);
        }

        return false;
    }

    private function isCreatedBy(Charge $charge, User $user): bool
    {
        return null === $charge->getId() || $charge->getAccount()->getCreatedBy() === $user;
    }

    private function isOwner(Charge $charge, User $user): bool
    {
        return null === $charge->getId() || $charge->getAccount()->getOwner() === $user;
    }

    private function canView(Charge $charge, User $user): bool
    {
        return $this->canEdit($charge, $user) || !$charge->getAccount()->getAccountShares()
            ->filter(fn(AccountShare $chargeShare) => $chargeShare->getUser() === $user && $chargeShare->isType(AccountShareType::VIEW))
            ->isEmpty();
    }

    private function canEdit(Charge $charge, User $user): bool
    {
        return !$charge->getAccount()->getAccountShares()
            ->filter(fn(AccountShare $chargeShare) => $chargeShare->getUser() === $user && $chargeShare->isType(AccountShareType::EDIT))
            ->isEmpty();
    }
}
