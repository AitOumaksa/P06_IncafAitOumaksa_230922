<?php

namespace App\Security\Voter;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickVoter extends Voter
{
    public const EDIT = 'edit_trick';
    public const DELETE = 'delete_trick';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $trick): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $trick instanceof \App\Entity\Trick;
    }

    protected function voteOnAttribute(string $attribute, $trick, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if(null === $trick->getUser()){

            return false;
        } 
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                return $this->canEdit($trick, $user);
                break;
            case self::DELETE:
                // logic to determine if the user can VIEW
                return $this->canDelete($trick, $user);
                break;
        }

        return false;
    }

    private function canEdit(Trick $trick, User $user)
    {
        return $user === $trick->getUser();
    }

    private function canDelete(Trick $trick, User $user)
    {
        if($this->security->isGranted('ROLE_ADMIN')|| $user === $trick->getUser()) return true;
        return false;
    }
}
