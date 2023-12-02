<?php

namespace App\Security\Voter;

use App\Entity\Company;
use App\Entity\User;
use App\Security\AuthentificableRoles;
use App\Security\Voter\Attributes\CompanyVoterAttributes;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CompanyVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function supports($attr, $subject) : bool
    {
        return CompanyVoterAttributes::has($attr)  && $subject instanceof Company;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token) : bool
    {
        /**
         * @type null|User $user
         */
        $user = $token->getUser();
        if(!$user instanceof User) return false;

        switch ($attribute) {
            case CompanyVoterAttributes::CAN_VIEW_COMPANY:
                return $this->canView($subject, $user);
            case CompanyVoterAttributes::CAN_EDIT_COMPANY:
                return $this->canEdit($subject, $user);
            case CompanyVoterAttributes::CAN_DELETE_COMPANY:
                return $user->hasRole(AuthentificableRoles::ROLE_SUPER_ADMIN);
        }
        return false;
    }

    private function canView(Company $company, User $user) : bool
    {
        return $user->getCompany() === $company;
    }

    private function canEdit(Company $company, User $user) : bool
    {
        if(!$user->hasRole(AuthentificableRoles::ROLE_COMPANY_ADMIN)) return false;
        return $user->getCompany() === $company;
    }

}