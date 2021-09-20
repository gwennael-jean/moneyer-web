<?php

namespace App\Security\Voter\Bank;

use App\DBAL\Types\Bank\AccountShareType;
use App\Entity\Bank\Resource;
use App\Entity\Bank\AccountShare;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceVoter extends Voter
{
    const VIEW = 'view';

    const EDIT = 'edit';

    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Resource;
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
            case self::DELETE: return $this->isCreatedBy($subject, $user) || $this->isOwner($subject, $user);
        }

        return false;
    }

    private function isCreatedBy(Resource $resource, User $user): bool
    {
        return null === $resource->getId() || $resource->getAccount()->getCreatedBy() === $user;
    }

    private function isOwner(Resource $resource, User $user): bool
    {
        return null === $resource->getId() || $resource->getAccount()->getOwner() === $user;
    }

    private function canView(Resource $resource, User $user): bool
    {
        return $this->canEdit($resource, $user) || !$resource->getAccount()->getAccountShares()
            ->filter(fn(AccountShare $resourceShare) => $resourceShare->getUser() === $user && $resourceShare->isType(AccountShareType::VIEW))
            ->isEmpty();
    }

    private function canEdit(Resource $resource, User $user): bool
    {
        return !$resource->getAccount()->getAccountShares()
            ->filter(fn(AccountShare $resourceShare) => $resourceShare->getUser() === $user && $resourceShare->isType(AccountShareType::EDIT))
            ->isEmpty();
    }
}
