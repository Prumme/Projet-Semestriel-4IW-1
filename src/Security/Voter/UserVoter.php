<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Security\AuthentificableRoles;
use App\Security\Voter\Attributes\UserVoterAttributes;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function supports($attr, $subject) : bool
    {
        return UserVoterAttributes::has($attr)  && $subject instanceof User;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token) : bool
    {
        /**
         * @type null|User $user
         */
        $user = $token->getUser();
        if(!$user instanceof User) return false;

        switch ($attribute) {
            case UserVoterAttributes::CAN_VIEW_USER:
                return $this->canView($subject, $user);
            case UserVoterAttributes::CAN_EDIT_USER:
                return $this->canEdit($subject, $user);
            case UserVoterAttributes::CAN_DELETE_USER:
                return $this->canDelete($subject, $user);
        }
        return false;
    }

    private function canView(User $resource, User $user) : bool
    {

        return $user->getCompany() === $resource->getCompany();
    }

    private function canEdit(User $resource, User $user) : bool
    {
        if($user === $resource) return true;
        if(!$user->hasRole(AuthentificableRoles::ROLE_COMPANY_ADMIN)) return false;
        return $this->canView($resource, $user);
    }

    private function canDelete(User $resource, User $user) : bool
    {
        if($user === $resource) return false;
        return $this->canEdit($resource, $user);
    }

}