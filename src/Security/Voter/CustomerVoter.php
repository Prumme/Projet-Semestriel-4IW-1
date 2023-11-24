<?php

namespace App\Security\Voter;

use App\Entity\Customer;
use App\Entity\User;
use App\Security\AuthentificableRoles;
use App\Security\Voter\Attributes\CustomerVoterAttributes;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CustomerVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function supports($attr, $subject) : bool
    {

        return CustomerVoterAttributes::has($attr) && $subject instanceof Customer;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token) : bool
    {
        /**
         * @type null|User $user
         */
        $user = $token->getUser();
        if(!$user instanceof User) return false;

        switch ($attribute) {
            case CustomerVoterAttributes::CAN_VIEW_CUSTOMER:
                return $this->canView($subject, $user);
            case CustomerVoterAttributes::CAN_EDIT_CUSTOMER:
                return $this->canEdit($subject, $user);
            case CustomerVoterAttributes::CAN_DELETE_CUSTOMER:
                return $this->canDelete($subject, $user);
        }
        return false;
    }

    private function canView(Customer $customer, User $user) : bool
    {
        return $user->getCompany() === $customer->getReferenceCompany();
    }

    private function canEdit(Customer $customer, User $user) : bool
    {
        if(!$user->hasRole(AuthentificableRoles::ROLE_COMPANY_ADMIN)) return false;
        return $this->canView($customer, $user);
    }

    private function canDelete(Customer $customer, User $user) : bool
    {
        if(!$user->hasRole(AuthentificableRoles::ROLE_COMPANY_ADMIN)) return false;
        return $this->canView($customer, $user);
    }
}