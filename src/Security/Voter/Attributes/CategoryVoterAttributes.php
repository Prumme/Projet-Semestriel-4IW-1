<?php

namespace App\Security\Voter\Attributes;

class CategoryVoterAttributes extends VoterAttributes
{
    const CAN_EDIT_CATEGORY = 'CAN_EDIT_CATEGORY';
    const CAN_VIEW_CATEGORY = 'CAN_VIEW_CATEGORY';
    const CAN_DELETE_CATEGORY = 'CAN_DELETE_CATEGORY';
}
