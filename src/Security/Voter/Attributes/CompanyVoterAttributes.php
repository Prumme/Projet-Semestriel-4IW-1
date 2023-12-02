<?php

namespace App\Security\Voter\Attributes;

class CompanyVoterAttributes extends VoterAttributes
{
    const CAN_EDIT_COMPANY = 'CAN_EDIT_COMPANY';
    const CAN_VIEW_COMPANY = 'CAN_VIEW_COMPANY';
    const CAN_DELETE_COMPANY = 'CAN_DELETE_COMPANY';
}