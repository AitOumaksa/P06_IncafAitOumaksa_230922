<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public const EDIT = 'edit_comment';
    public const DELETE = 'delete_comment';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $comment): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $comment instanceof \App\Entity\Comment;
    }

    protected function voteOnAttribute(string $attribute, $comment, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if(null === $comment->getUser()){

            return false;
        } 

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                return $this->canEdit($comment, $user);
                break;
            case self::DELETE:
                // logic to determine if the user can VIEW
                return $this->canDelete($comment, $user);
                break;
        }

        return false;
    }

    private function canEdit(Comment $comment, User $user)
    {
        return $user === $comment->getUser();
    }

    private function canDelete(Comment $comment, User $user)
    {
        if($this->security->isGranted('ROLE_ADMIN')|| $user === $comment->getUser()) return true;
        return false;
    }
}
