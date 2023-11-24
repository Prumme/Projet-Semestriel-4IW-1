<?php

namespace App\Security\Voter\Attributes;

class UserVoterAttributes extends VoterAttributes
{
    const CAN_EDIT_USER = 'CAN_EDIT_USER';
    const CAN_VIEW_USER = 'CAN_VIEW_USER';
    const CAN_DELETE_USER = 'CAN_DELETE_USER';
}