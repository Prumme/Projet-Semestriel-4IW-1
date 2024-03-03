<?php

namespace App\Security\Voter;

use App\Entity\Product;
use App\Entity\User;
use App\Security\AuthentificableRoles;
use App\Security\Voter\Attributes\ProductVoterAttributes;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function supports($attr, $subject) : bool
    {

        return ProductVoterAttributes::has($attr) && $subject instanceof Product;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token) : bool
    {
        /**
         * @type null|User $user
         */
        $user = $token->getUser();
        if(!$user instanceof User) return false;

        switch ($attribute) {
            case ProductVoterAttributes::CAN_VIEW_PRODUCT:
                return $this->canView($subject, $user);
            case ProductVoterAttributes::CAN_EDIT_PRODUCT:
                return $this->canEdit($subject, $user);
            case ProductVoterAttributes::CAN_DELETE_PRODUCT:
                return $this->canDelete($subject, $user);
        }
        return false;
    }

    private function canView(Product $product, User $user) : bool
    {
        return $user->getCompany() === $product->getCompany();
    }

    private function canEdit(Product $product, User $user) : bool
    {
        if(!$this->security->isGranted(AuthentificableRoles::ROLE_USER)) return false;
        return $this->canView($product, $user);
    }

    private function canDelete(Product $product, User $user) : bool
    {
        if(!$this->security->isGranted(AuthentificableRoles::ROLE_USER)) return false;
        if($user->getId() !== $product->getUserId()->getId()) return false;
        return $this->canView($product, $user);
    }
}