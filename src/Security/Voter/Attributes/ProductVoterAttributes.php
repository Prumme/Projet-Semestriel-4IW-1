<?php

namespace App\Security\Voter\Attributes;

class ProductVoterAttributes extends VoterAttributes
{
    const CAN_EDIT_PRODUCT = 'CAN_EDIT_PRODUCT';
    const CAN_VIEW_PRODUCT = 'CAN_VIEW_PRODUCT';
    const CAN_DELETE_PRODUCT = 'CAN_DELETE_PRODUCT';
}