<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Security\AuthentificableRoles;
use App\Security\Voter\Attributes\CategoryVoterAttributes;
use App\Security\Voter\Attributes\ProductVoterAttributes;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CategoryVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function supports($attr, $subject) : bool
    {

        return CategoryVoterAttributes::has($attr) && $subject instanceof Category;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token) : bool
    {
        /**
         * @type null|User $user
         */
        $user = $token->getUser();
        if(!$user instanceof User) return false;

        switch ($attribute) {
            case CategoryVoterAttributes::CAN_VIEW_CATEGORY:
                return $this->canView($subject, $user);
            case CategoryVoterAttributes::CAN_EDIT_CATEGORY:
                return $this->canEdit($subject, $user);
            case CategoryVoterAttributes::CAN_DELETE_CATEGORY:
                return $this->canDelete($subject, $user);
        }
        return false;
    }

    private function canView(Category $category, User $user) : bool
    {
        if($this->security->isGranted(AuthentificableRoles::ROLE_SUPER_ADMIN)) return true;
        return $user->getCompany() === $category->getCompany();
    }

    private function canEdit(Category $category, User $user) : bool
    {
        if(!$this->security->isGranted(AuthentificableRoles::ROLE_USER)) return false;
        return $this->canView($category, $user);
    }

    private function canDelete(Category $category, User $user) : bool
    {
        if(!$this->security->isGranted(AuthentificableRoles::ROLE_COMPANY_ADMIN)) return false;
        return $this->canView($category, $user);
    }
}