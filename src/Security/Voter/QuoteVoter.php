<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Quote;
use App\Entity\Company;
use App\Security\AuthentificableRoles;
use Symfony\Bundle\SecurityBundle\Security;
use App\Security\Voter\Attributes\QuoteVoterAttributes;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Security\Voter\Attributes\CompanyVoterAttributes;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class QuoteVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attr, $subject): bool
    {
        return QuoteVoterAttributes::has($attr) && $subject instanceof Quote;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) return false;

        return $this->canManage($subject, $user);
    }

    /**
     * Method canView   
     * 
     * @param Quote $quote
     * @param User $user
     * 
     * @return bool
     */
    public function canManage(Quote $quote, User $user): bool
    {
        $company = $quote->getOwner()->getCompany();
        return $this->security->isGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, $company);
    }
}
