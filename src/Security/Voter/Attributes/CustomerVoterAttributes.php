<?php

namespace App\Security\Voter\Attributes;

class CustomerVoterAttributes extends VoterAttributes
{
    const CAN_EDIT_CUSTOMER = 'CAN_EDIT_CUSTOMER';
    const CAN_VIEW_CUSTOMER = 'CAN_VIEW_CUSTOMER';
    const CAN_DELETE_CUSTOMER = 'CAN_DELETE_CUSTOMER';
}