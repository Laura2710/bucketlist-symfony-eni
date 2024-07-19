<?php

namespace App\Security\Voter;

use App\Entity\Wish;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class WishVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'POST_DELETE';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Wish;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);

            case self::VIEW:
                return $this->canView($subject, $user);

            case self::DELETE:
                return $this->canDelete($subject, $user);
        }

        return false;
    }

    private function canEdit(Wish $wish, UserInterface $user): bool
    {
        return $wish->getAuthor() === $user->getUserIdentifier();
    }

    private function canView(Wish $wish, UserInterface $user): bool
    {
        return true; // Supposons que tous les utilisateurs peuvent voir tous les souhaits
    }

    private function canDelete(Wish $wish, UserInterface $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $wish->getAuthor() == $user->getUserIdentifier();
    }
}
